<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SMSBalanceService;

class SetSMSBalanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:set-balance {balance : The new SMS balance}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the SMS balance to a specific value';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $balance = $this->argument('balance');
        
        if (!is_numeric($balance) || $balance < 0) {
            $this->error('Balance must be a positive number.');
            return Command::FAILURE;
        }
        
        try {
            $smsService = new SMSBalanceService();
            $oldBalance = $smsService->getCurrentBalance();
            $smsService->resetBalance($balance);
            
            $this->info("SMS balance updated from {$oldBalance} to {$balance}");
            
            // Check if reminders need to be sent
            $smsService->checkAndSendReminders($balance);
            
        } catch (\Exception $e) {
            $this->error('Failed to set SMS balance: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}
