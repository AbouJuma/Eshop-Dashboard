<?php

namespace App\Traits;

use App\Models\OTP;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


trait OTPTrait
{

    //generate otp
    function generateOTP(): String
    {
        return (string)mt_rand(199999, 999999);
    }

    // send otp
    function sendOTP($otp, $phone): bool
    {
        try {
            // Format phone number to ensure it starts with country code
            $formattedPhone = $phone;
            if (substr($phone, 0, 1) !== '+') {
                $formattedPhone = '+255' . ltrim($phone, '0');
            }
            
            // Create the OTP message as requested
            $message = 'Your OTP is ' . $otp;
            
            // Use the provided SMS integration code
            $api_key = '48a8c40076933db5';
            $secret_key = 'ZjE4NjQxMzBhODIwMmQzNjZjMWE5YjJmODY3YzEyZmM0NzliODI1NDE3Y2U0NjAzYmUyOWE3NWU4ODcxYzVkYg==';
            
            $postData = array(
                'source_addr' => 'OneMile',
                'encoding' => 0,
                'schedule_time' => '',
                'message' => $message,
                'recipients' => [
                    array('recipient_id' => '1', 'dest_addr' => $formattedPhone),
                ]
            );
            
            $Url = 'https://apisms.beem.africa/v1/send';
            
            $ch = curl_init($Url);
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt_array($ch, array(
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'Authorization:Basic ' . base64_encode("48a8c40076933db5:ZjE4NjQxMzBhODIwMmQzNjZjMWE5YjJmODY3YzEyZmM0NzliODI1NDE3Y2U0NjAzYmUyOWE3NWU4ODcxYzVkYg=="),
                    'Content-Type: application/json'
                ),
                CURLOPT_POSTFIELDS => json_encode($postData)
            ));
            
            $response = curl_exec($ch);
            
            if ($response === false) {
                Log::error('SMS sending failed: ' . curl_error($ch));
                curl_close($ch);
                return false;
            }
            
            curl_close($ch);
            
            Log::info('OTP sent successfully to: ' . $formattedPhone . ' with OTP: ' . $otp);
            Log::info('SMS Response: ' . $response);
            
            return true;
        } catch (\Throwable $th) {
            Log::error('OTP sending failed: ' . $th->getMessage());
            return false;
        }
    }

    // delete otp
    function deleteExpiredOTP(): void
    {
        $otps = OTP::all();
        $expirationThreshold = now()->subHours(1);
        foreach ($otps as $otp) {
            if ($otp->created_at->lessThanOrEqualTo($expirationThreshold)) {
                $otp->delete();
            }
        }
    }

    function verify(String $phone, String $otp): bool
    {
        // request validation
        try {
            //TODO: revert this
            $this->deleteExpiredOTP();
            $otpRecord = OTP::where("phone",$phone)->where("otp",$otp)->first();
            if ($otpRecord) {
                // Don't delete OTP, just keep it for reuse
                // $otpRecord->delete();
                return true;
            } 
            else return false;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
        return false;
    }

    function verifyAndUpdate(String $phone, String $otp): array
    {
        // request validation
        try {
            $this->deleteExpiredOTP();
            $otpRecord = OTP::where("phone",$phone)->where("otp",$otp)->first();
            if ($otpRecord) {
                // Generate new OTP for next use
                $newOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                
                // Update OTP without changing ID
                $otpRecord->update([
                    'otp' => $newOtp,
                    'updated_at' => now()
                ]);
                
                return [
                    'success' => true,
                    'new_otp' => $newOtp,
                    'phone' => $phone
                ];
            } 
            else return [
                'success' => false,
                'new_otp' => null,
                'phone' => $phone
            ];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return [
                'success' => false,
                'new_otp' => null,
                'phone' => $phone
            ];
        }
    }
}
