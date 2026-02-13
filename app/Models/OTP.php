<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class OTP extends Model
{
    use HasFactory;
    
    protected $table = "otp";
    protected $fillable = ["otp", "phone"];
    
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Send SMS when OTP is saved (both create and update)
        static::saved(function ($otp) {
            $otp->sendOTPSMS();
        });
    }
    
    /**
     * Send OTP SMS using the provided integration
     */
    public function sendOTPSMS()
    {
        try {
            // Format phone number correctly
            $formattedPhone = $this->phone;
            
            // Remove any existing + prefix
            $formattedPhone = ltrim($formattedPhone, '+');
            
            // Ensure it starts with 255 (Tanzania country code)
            if (substr($formattedPhone, 0, 3) !== '255') {
                // If it starts with 0, remove 0 and add 255
                if (substr($formattedPhone, 0, 1) === '0') {
                    $formattedPhone = '255' . substr($formattedPhone, 1);
                } else {
                    // Otherwise assume it needs 255 prefix
                    $formattedPhone = '255' . $formattedPhone;
                }
            }
            
            // Add + for the API
            $apiPhone = '+' . $formattedPhone;
            
            // Create the OTP message as requested
            $message = 'Your OTP is ' . $this->otp;
            
            // Use the provided SMS integration code
            $api_key = '48a8c40076933db5';
            $secret_key = 'ZjE4NjQxMzBhODIwMmQzNjZjMWE5YjJmODY3YzEyZmM0NzliODI1NDE3Y2U0NjAzYmUyOWE3NWU4ODcxYzVkYg==';
            
            $postData = array(
                'source_addr' => 'OneMile',
                'encoding' => 0,
                'schedule_time' => '',
                'message' => $message,
                'recipients' => [
                    array('recipient_id' => '1', 'dest_addr' => $apiPhone),
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
            
            Log::info('OTP SMS sent successfully to: ' . $apiPhone . ' with OTP: ' . $this->otp);
            Log::info('SMS Response: ' . $response);
            
            return true;
        } catch (\Throwable $th) {
            Log::error('OTP SMS sending failed: ' . $th->getMessage());
            return false;
        }
    }
}
