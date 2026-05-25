<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\SMSBalanceService;

class CheckSMSBalanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $smsService = new SMSBalanceService();
            $currentBalance = $smsService->getCurrentBalance();
            $status = $smsService->getBalanceStatus();
            
            Log::info("SMS Balance Check - Current Balance: {$currentBalance}, Status: {$status}");
            
            // Check and send reminders if needed
            $smsService->checkAndSendReminders($currentBalance);
            
        } catch (\Exception $e) {
            Log::error("SMS Balance Check Job failed: " . $e->getMessage());
        }
    }
}
