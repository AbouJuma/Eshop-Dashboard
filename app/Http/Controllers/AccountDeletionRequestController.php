<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountDeletionRequestConfirmation;
use App\Mail\AccountDeletionProcessed;

class AccountDeletionRequestController extends Controller
{
    /**
     * Show account deletion request form
     */
    public function showRequestForm()
    {
        return view('account-deletion.request');
    }

    /**
     * Process account deletion request
     */
    public function processRequest(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|max:255',
            'reason' => 'required|string|max:500',
        ]);

        $identifier = $request->identifier;
        
        // Check if identifier is email or phone number
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
        
        // Find user by email or phone
        if ($isEmail) {
            $user = User::where('email', $identifier)->first();
            $email = $identifier;
            $phone = $user ? $user->phone : null;
        } else {
            // Clean phone number but don't modify format - use as-is
            $phone = str_replace(['+', '-', ' ', '(', ')'], '', $identifier);
            
            // Try exact formats that might be in database
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
            
            $email = $user ? $user->email : null;
            $phone = $user ? $user->phone : $identifier;
        }
        
        if (!$user) {
            return back()->with('error', 'No account found with this email address or phone number.');
        }

        // Check if request already exists
        $existingRequest = AccountDeletionRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        Log::info('Checking existing request', [
            'user_id' => $user->id,
            'existing_request' => $existingRequest ? 'found' : 'not found',
            'user_email' => $user->email,
            'user_phone' => $user->phone
        ]);

        if ($existingRequest) {
            return back()->with('error', 'You already have a pending deletion request. Please wait for admin review.');
        }

        try {
            $displayName = $user->name
                ?? $user->username
                ?? trim((string) ($user->fname ?? '') . ' ' . (string) ($user->sname ?? ''))
                ?? $user->email
                ?? $user->phone
                ?? 'User';

            if (is_string($displayName)) {
                $displayName = trim($displayName);
            }

            if (!$displayName) {
                $displayName = 'User';
            }

            Log::info('Creating deletion request', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_phone' => $user->phone,
                'request_email' => $email,
                'request_phone' => $phone,
                'reason' => $request->reason
            ]);

            // Create deletion request
            $deletionRequest = AccountDeletionRequest::create([
                'user_id' => $user->id,
                'email' => $user->email ?? $email,
                'name' => $displayName,
                'phone' => $user->phone,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            Log::info('Deletion request created', [
                'request_id' => $deletionRequest->id,
                'user_id' => $user->id,
                'status' => $deletionRequest->status
            ]);

            // Send confirmation email
            try {
                if ($user->email) {
                    Mail::to($user->email)->send(new AccountDeletionRequestConfirmation($user, $deletionRequest));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send request confirmation email', [
                    'user_id' => $user->id,
                    'request_id' => $deletionRequest->id,
                    'error' => $e->getMessage()
                ]);
            }

            return back()->with('success', 'Your deletion request has been submitted successfully. Our admin team will review your request within 24-48 hours.');
            
        } catch (\Exception $e) {
            Log::error('Failed to create deletion request', [
                'identifier' => $identifier,
                'user_id' => $user->id ?? null,
                'email' => $email,
                'phone' => $phone,
                'error' => $e->getMessage(),
                'exception' => get_class($e)
            ]);
            
            return back()->with('error', 'Failed to process your request. Please try again later.');
        }
    }

    /**
     * Show privacy policy page
     */
    public function showPrivacyPolicy()
    {
        return view('account-deletion.privacy-policy');
    }

    /**
     * Show admin dashboard for deletion requests
     */
    public function adminIndex()
    {
        $requests = AccountDeletionRequest::with(['user', 'processedByAdmin'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('account-deletion.admin-index', compact('requests'));
    }

    /**
     * Process deletion request (admin action)
     */
    public function adminProcess(Request $request, $id)
    {
        Log::info('adminProcess method called', [
            'id' => $id,
            'all_request_data' => $request->all()
        ]);

        // Validate CSRF token
        if (!$request->has('_token') || !hash_equals(csrf_token(), $request->_token)) {
            Log::error('CSRF token mismatch');
            return back()->with('error', 'Invalid request. Please try again.');
        }

        // Validate action
        if (!in_array($request->action, ['approve', 'reject'])) {
            Log::error('Invalid action', ['action' => $request->action]);
            return back()->with('error', 'Invalid action specified.');
        }

        $deletionRequest = AccountDeletionRequest::findOrFail($id);
        $user = $deletionRequest->user;

        if (!$user) {
            Log::error('User not found for deletion request', [
                'request_id' => $id,
                'user_id' => $deletionRequest->user_id
            ]);
            return back()->with('error', 'User account not found for this request.');
        }

        // Debug logging
        Log::info('Processing deletion request', [
            'id' => $id,
            'action' => $request->action,
            'admin_notes' => $request->admin_notes,
            'auth_id' => auth()->id(),
            'request_data' => $request->all(),
            'method' => $request->method()
        ]);

        // Validate CSRF token
        if (!$request->has('_token') || !hash_equals(csrf_token(), $request->_token)) {
            Log::error('CSRF token mismatch');
            return back()->with('error', 'Invalid request. Please try again.');
        }

        // Validate action
        if (!in_array($request->action, ['approve', 'reject'])) {
            Log::error('Invalid action', ['action' => $request->action]);
            return back()->with('error', 'Invalid action specified.');
        }

        $deletionRequest = AccountDeletionRequest::findOrFail($id);
        $user = $deletionRequest->user;

        if (!$user) {
            return back()->with('error', 'User account not found.');
        }

        try {
            Log::info('Starting action processing', [
                'action' => $request->action,
                'user_id' => $user->id,
                'user_exists' => $user ? true : false,
                'request_id' => $deletionRequest->id
            ]);

            if ($request->action === 'approve') {
                // Delete user account
                Log::info('Approving request - deleting user', ['user_id' => $user->id]);
                $user->delete();
                $deletionRequest->status = 'inactive';
                $deletionRequest->admin_notes = $request->admin_notes ?? 'Account approved by admin';
                
                // Send approval email
                Mail::to($deletionRequest->email)->send(new AccountDeletionProcessed($user, $deletionRequest, 'approved'));
                
            } else {
                // Reject request
                Log::info('Rejecting request', ['user_id' => $user->id]);
                $deletionRequest->status = 'active';
                $deletionRequest->admin_notes = $request->admin_notes ?? 'Account rejected by admin';
                
                // Send rejection email
                Mail::to($deletionRequest->email)->send(new AccountDeletionProcessed($user, $deletionRequest, 'rejected'));
            }

            Log::info('Saving deletion request', [
                'status' => $deletionRequest->status,
                'processed_at' => now(),
                'processed_by' => auth()->id()
            ]);

            $deletionRequest->processed_at = now();
            $deletionRequest->processed_by = auth()->id();
            $saved = $deletionRequest->save();
            
            Log::info('Deletion request saved', [
                'saved' => $saved,
                'final_status' => $deletionRequest->status
            ]);

            return back()->with('success', "Deletion request has been {$request->action}d successfully.");
            
        } catch (\Exception $e) {
            Log::error('Failed to process deletion request', [
                'request_id' => $id,
                'action' => $request->action,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to process request. Please try again.');
        }
    }
}
