<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use App\Http\Integration\Beem\BeemSMSController;

class SMSBalanceService
{
    /**
     * Get current SMS balance from Beems API
     */
    public function getCurrentBalance()
    {
        if (Config::get('sms_balance.beem_api.use_real_balance', false)) {
            return $this->getRealBalance();
        }
        
        return Cache::get('sms_balance', Config::get('sms_balance.balance_tracking.current_balance', 1000));
    }

    /**
     * Get real balance from Beems API
     */
    private function getRealBalance()
    {
        try {
            // Use OneMile API credentials (same as booking/order SMS)
            $api_key = Config::get('sms_balance.beem_api.api_key');
            $secret_key = Config::get('sms_balance.beem_api.secret_key');
            $endpoint = Config::get('sms_balance.beem_api.balance_check_endpoint');

            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPGET, true); // Use GET method as per your example
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . base64_encode("$api_key:$secret_key"),
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response !== false && $httpCode === 200) {
                $data = json_decode($response, true);
                $balance = $this->extractBalanceFromResponse($data);
                
                // Cache the real balance for 1 hour
                Cache::put('sms_balance', $balance, now()->addHour());
                
                if (Config::get('sms_balance.balance_tracking.log_usage', true)) {
                    Log::info("Real SMS Balance from Beems API: {$balance}");
                }
                
                return $balance;
            } else {
                Log::error("Beems API balance check failed. HTTP Code: {$httpCode}, Response: {$response}");
                // Fallback to cached balance
                return Cache::get('sms_balance', Config::get('sms_balance.balance_tracking.current_balance', 1000));
            }
        } catch (\Exception $e) {
            Log::error("Exception while checking Beems balance: " . $e->getMessage());
            // Fallback to cached balance
            return Cache::get('sms_balance', Config::get('sms_balance.balance_tracking.current_balance', 1000));
        }
    }

    /**
     * Extract balance from Beems API response
     */
    private function extractBalanceFromResponse($data)
    {
        // Handle Beems API response format for balance endpoint
        if (isset($data['data']['credit_balance'])) {
            return (int) $data['data']['credit_balance'];
        } elseif (isset($data['data']['balance'])) {
            return (int) $data['data']['balance'];
        } elseif (isset($data['balance'])) {
            return (int) $data['balance'];
        } elseif (isset($data['credits'])) {
            return (int) $data['credits'];
        } elseif (isset($data['available_sms'])) {
            return (int) $data['available_sms'];
        } elseif (isset($data['sms_balance'])) {
            return (int) $data['sms_balance'];
        }
        
        Log::warning("Could not extract balance from Beems API response: " . json_encode($data));
        return Config::get('sms_balance.balance_tracking.current_balance', 1000);
    }

    /**
     * Update SMS balance
     */
    public function updateBalance($newBalance)
    {
        Cache::forever('sms_balance', $newBalance);
        
        if (Config::get('sms_balance.balance_tracking.log_usage', true)) {
            Log::info("SMS Balance updated to: {$newBalance}");
        }
    }

    /**
     * Deduct SMS usage
     */
    public function deductUsage($messagesCount = 1)
    {
        $currentBalance = $this->getCurrentBalance();
        $newBalance = $currentBalance - $messagesCount;
        
        if ($newBalance < 0) {
            $newBalance = 0;
        }
        
        $this->updateBalance($newBalance);
        
        // Check if we need to send reminders
        $this->checkAndSendReminders($newBalance);
        
        return $newBalance;
    }

    /**
     * Check balance and send reminders if needed
     */
    public function checkAndSendReminders($balance = null)
    {
        if (!Config::get('sms_balance.balance_tracking.enabled', true)) {
            return;
        }

        $balance = $balance ?? $this->getCurrentBalance();
        $thresholds = Config::get('sms_balance.balance_thresholds');
        $notificationNumbers = Config::get('sms_balance.notification_numbers', []);
        
        // Check warning threshold (100)
        if ($balance <= $thresholds['warning'] && $balance > $thresholds['critical']) {
            $this->sendReminder('warning', $balance, $notificationNumbers);
        }
        
        // Check critical threshold (50)
        if ($balance <= $thresholds['critical']) {
            $this->sendReminder('critical', $balance, $notificationNumbers);
        }
    }

    /**
     * Send balance reminder notification
     */
    private function sendReminder($type, $balance, $phoneNumbers)
    {
        $cacheKey = "sms_reminder_{$type}_{$balance}";
        
        // Prevent sending multiple reminders for the same balance level
        if (Cache::has($cacheKey)) {
            return;
        }

        $message = Config::get("sms_balance.reminder_messages.{$type}");
        $message = str_replace('{balance}', $balance, $message);

        foreach ($phoneNumbers as $phoneNumber) {
            try {
                BeemSMSController::send($phoneNumber, $message, $phoneNumber);
                Log::info("SMS {$type} reminder sent to {$phoneNumber}. Balance: {$balance}");
            } catch (\Exception $e) {
                Log::error("Failed to send SMS {$type} reminder to {$phoneNumber}: " . $e->getMessage());
            }
        }

        // Cache the reminder for 24 hours to prevent spam
        Cache::put($cacheKey, true, now()->addHours(24));
    }

    /**
     * Get balance status
     */
    public function getBalanceStatus()
    {
        $balance = $this->getCurrentBalance();
        $thresholds = Config::get('sms_balance.balance_thresholds');

        if ($balance <= $thresholds['critical']) {
            return 'critical';
        } elseif ($balance <= $thresholds['warning']) {
            return 'warning';
        } else {
            return 'good';
        }
    }

    /**
     * Reset balance (for testing or manual updates)
     */
    public function resetBalance($newBalance)
    {
        $this->updateBalance($newBalance);
        Log::info("SMS Balance manually reset to: {$newBalance}");
    }
}
