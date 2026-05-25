<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountDeletionConfirmation;

class UserAccountDeletionController extends Controller
{
    /**
     * Show the account deletion form
     */
    public function showDeletionForm()
    {
        return view('account-deletion.index');
    }

    /**
     * Process the account deletion request
     */
    public function processDeletion(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|max:255',
            'confirmation' => 'required|string|in:DELETE',
        ]);

        $identifier = $request->identifier;
        
        // Check if identifier is email or phone number
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
        
        // Find user by email or phone
        if ($isEmail) {
            $user = User::where('email', $identifier)->first();
        } else {
            // Clean phone number but don't modify format - use as-is
            $phone = str_replace(['+', '-', ' ', '(', ')'], '', $identifier);
            
            // Try the exact formats that might be in database
            $possibleFormats = [
                $phone,                                    // Original input (cleaned)
                '+' . $phone,                             // Add + if missing
                '255' . $phone,                            // Add 255 prefix
                '+255' . $phone,                           // Add +255 prefix
                '0' . substr($phone, 1),                 // Convert 0... to 255...
                substr($phone, 1),                          // Remove first digit if it's 0
                ltrim($phone, '+'),                         // Remove leading +
                ltrim($phone, '255'),                         // Remove leading 255
                '255' . ltrim($phone, '+'),                // Add 255 if missing
                '255' . ltrim($phone, '0'),                 // Handle 0... format
            ];
            
            // Try each format until we find a match
            $user = null;
            foreach ($possibleFormats as $format) {
                $user = User::where('phone', $format)->first();
                if ($user) {
                    break;
                }
            }
        }
        
        if (!$user) {
            return back()->with('error', 'No account found with this email address or phone number.');
        }

        // Check if account is already inactive
        if ($user->status === 'inactive') {
            return back()->with('error', 'This account is already inactive and cannot be processed further.');
        }

        try {
            // Option 1: Soft delete (suspend the account)
            $user->status = 'inactive';
            $user->save();
            
            // Option 2: Hard delete (completely remove)
            // $user->delete();
            
            // Log the deletion
            Log::info('User account suspended', [
                'user_id' => $user->id,
                'email' => $user->email,
                'action' => 'suspended',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            // Send confirmation email
            try {
                Mail::to($user->email)->send(new AccountDeletionConfirmation($user));
            } catch (\Exception $e) {
                Log::error('Failed to send deletion confirmation email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }

            return back()->with('success', 'Account has been deactivated successfully. ');
            
        } catch (\Exception $e) {
            Log::error('Failed to suspend user account', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to process your request. Please try again later.');
        }
    }
}
