<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SMSBalanceService;
use App\Jobs\CheckSMSBalanceJob;

class CheckSMSBalanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:check-balance {--force : Force check even if recently checked}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SMS balance and send reminder notifications if needed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking SMS balance...');
        
        try {
            $smsService = new SMSBalanceService();
            $currentBalance = $smsService->getCurrentBalance();
            $status = $smsService->getBalanceStatus();
            
            $this->info("Current SMS Balance: {$currentBalance}");
            $this->info("Balance Status: {$status}");
            
            // Check and send reminders if needed
            $smsService->checkAndSendReminders($currentBalance);
            
            if ($status === 'critical') {
                $this->warn('CRITICAL: SMS balance is critically low!');
            } elseif ($status === 'warning') {
                $this->warn('WARNING: SMS balance is low.');
            } else {
                $this->info('SMS balance is healthy.');
            }
            
            $this->info('SMS balance check completed successfully.');
            
        } catch (\Exception $e) {
            $this->error('SMS balance check failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}
