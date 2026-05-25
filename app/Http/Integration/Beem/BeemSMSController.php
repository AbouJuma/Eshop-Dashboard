<?php

namespace App\Http\Integration\Beem;
use App\Http\Integration\Beem\Constants;
use Illuminate\Support\Facades\Log;
use App\Services\SMSBalanceService;

class BeemSMSController
{
    static function send($phone, $message, $client_id)
    {
        // Use OneMile credentials (same as booking/order SMS)
        $api_key = '48a8c40076933db5';
        $secret_key = 'ZjE4NjQxMzBhODIwMmQzNjZjMWE5YjJmODY3YzEyZmM0NzliODI1NDE3Y2U0NjAzYmUyOWE3NWU4ODcxYzVkYg==';

        $postData = array(
            'source_addr' => 'OneMile',
            'encoding' => 0,
            'schedule_time' => '',
            'message' => $message,
            'recipients' => [array('recipient_id' => $client_id, 'dest_addr' => $phone)]
        );

        $Url = Constants::BASE_URL;

        $ch = curl_init($Url);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization:Basic ' . base64_encode("$api_key:$secret_key"),
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        $response = curl_exec($ch);
        if ($response === FALSE) {
            Log::error("Curl Error: " . curl_error($ch));
        } else {
            Log::info("SMS sent successfully to {$phone}: " . $response);
            
            // Track SMS usage
            try {
                $smsBalanceService = new SMSBalanceService();
                $smsBalanceService->deductUsage(1);
            } catch (\Exception $e) {
                Log::error("Failed to track SMS usage: " . $e->getMessage());
            }
        }
        curl_close($ch);
        return;
    }

    static function sendToMany($message,$phone_array=null){
        $phone_array = $phone_array != null ? $phone_array : Constants::PHONE_ARRAY;
        foreach($phone_array as $item){
            try {
                BeemSMSController::send($item,$message,$item);
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }
    }


    
}
