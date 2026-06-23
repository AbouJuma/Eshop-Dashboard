<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Traits\OTPTrait;
use App\Models\OTP;
use Illuminate\Support\Facades\Log;


class OTPController extends BaseController
{
    use OTPTrait;

    function index(Request $request)
    {
        // Request validation
        $request->validate([
            'phone' => 'required|string|min:10|max:20'
        ]);

        try {
            // Clean and format phone number
            $phone = str_replace(['+', '-', ' ', '(', ')'], '', $request->phone);
            
            // Ensure phone starts with country code format (without + for database)
            if (substr($phone, 0, 3) === '255') {
                // Already in correct format
                $dbPhone = $phone;
            } elseif (substr($phone, 0, 1) === '0') {
                // Convert from 0... to 255...
                $dbPhone = '255' . substr($phone, 1);
            } else {
                // Assume it's missing country code
                $dbPhone = '255' . $phone;
            }
            
            // Check if user already exists
            $existingUser = \App\Models\User::where('phone', $dbPhone)
                            ->orWhere('phone', $request->phone)
                            ->orWhere('phone', $phone)
                            ->first();

            if ($existingUser) {
                // Update FCM token if provided
                if ($request->has('fcm_token') && $request->fcm_token) {
                    \Log::info('Before save - FCM token in user object', ['user_id' => $existingUser->id, 'current_fcm_token' => $existingUser->fcm_token, 'new_fcm_token' => $request->fcm_token]);
                    $existingUser->fcm_token = $request->fcm_token;
                    $saved = $existingUser->save();
                    \Log::info('After save - FCM token in user object', ['user_id' => $existingUser->id, 'saved' => $saved, 'fcm_token_after_save' => $existingUser->fcm_token]);

                    // Refresh from database to verify
                    $existingUser->refresh();
                    \Log::info('After refresh from database - FCM token', ['user_id' => $existingUser->id, 'fcm_token_from_db' => $existingUser->fcm_token]);

                    \Log::info('FCM token updated for existing user (OTP auto-login)', ['user_id' => $existingUser->id, 'fcm_token' => substr($request->fcm_token, 0, 20) . '...']);
                } else {
                    \Log::warning('FCM token not provided in OTP request', ['user_id' => $existingUser->id]);
                }

                $token = $existingUser->createToken('ClientAuthToken')->accessToken;
                return response()->json([
                    'success' => true,
                    'is_existing' => true,
                    'token' => $token,
                    'client_id' => $existingUser->id,
                    'username' => $existingUser->username,
                    'message' => 'User exists, bypassing OTP'
                ]);
            }

            // Check if OTP already exists for this phone number
            $existingOTP = OTP::where('phone', $dbPhone)->first();
            
            if ($existingOTP) {
                // Update existing OTP with new code (same record, same ID)
                $existingOTP->otp = $this->generateOTP();
                $existingOTP->save(); // SMS will be sent automatically via model event
                
                return response()->json([
                    'success' => true,
                    'message' => 'OTP updated and SMS sent successfully',
                    'action' => 'updated',
                    'otp_id' => $existingOTP->id,
                    'phone' => $dbPhone
                ]);
            } else {
                // Create new OTP for first time only
                $otp = new OTP();
                $otp->otp = $this->generateOTP();
                $otp->phone = $dbPhone;
                $otp->save(); // SMS will be sent automatically via model event
                
                return response()->json([
                    'success' => true,
                    'message' => 'OTP created and SMS sent successfully',
                    'action' => 'created',
                    'otp_id' => $otp->id,
                    'phone' => $dbPhone
                ]);
            }
            
        } catch (\Throwable $th) {
            Log::error('OTP Generation Error: ' . $th->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate OTP: ' . $th->getMessage()
            ], 500);
        }
    }

    
}
