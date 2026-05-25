<?php

namespace App\Http\Controllers;

use App\Services\SMSBalanceService;
use Illuminate\Http\Request;

class SMSBalanceController extends Controller
{
    protected $smsBalanceService;

    public function __construct(SMSBalanceService $smsBalanceService)
    {
        $this->smsBalanceService = $smsBalanceService;
    }

    /**
     * Get SMS balance data for API
     */
    public function getBalance(Request $request)
    {
        try {
            $balance = $this->smsBalanceService->getCurrentBalance();
            $status = $this->smsBalanceService->getBalanceStatus();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'balance' => $balance,
                    'status' => $status,
                    'formatted_balance' => number_format($balance) . ' SMS',
                    'warning_threshold' => config('sms_balance.balance_thresholds.warning', 100),
                    'critical_threshold' => config('sms_balance.balance_thresholds.critical', 50),
                    'notification_numbers' => config('sms_balance.notification_numbers', []),
                    'notification_count' => count(config('sms_balance.notification_numbers', [])),
                ],
                'message' => 'SMS balance retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve SMS balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manual balance check endpoint
     */
    public function checkBalance(Request $request)
    {
        try {
            $this->smsBalanceService->checkAndSendReminders();
            
            return response()->json([
                'success' => true,
                'message' => 'SMS balance check completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SMS balance check failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update balance manually (admin only)
     */
    public function updateBalance(Request $request)
    {
        $request->validate([
            'balance' => 'required|integer|min:0'
        ]);

        try {
            $this->smsBalanceService->resetBalance($request->balance);
            
            return response()->json([
                'success' => true,
                'message' => 'SMS balance updated successfully',
                'data' => [
                    'new_balance' => $request->balance
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update SMS balance: ' . $e->getMessage()
            ], 500);
        }
    }
}
