<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Balance Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for SMS balance tracking and reminder notifications
    |
    */

    'balance_thresholds' => [
        'warning' => 100,  // Send warning when balance reaches 100
        'critical' => 50,   // Send critical alert when balance reaches 50
    ],

    'notification_numbers' => [
        '06267619', // Phone number to send balance notifications to
        '0787011402', // Additional recipient
        '0684551070', // Additional recipient
        '0788753599', // Additional recipient
    ],

    'reminder_messages' => [
        'warning' => 'D-WORLD SMS Balance Warning: Your SMS balance has reached {balance} messages. Please recharge soon to avoid service interruption.',
        'critical' => 'D-WORLD SMS Balance Critical: Your SMS balance has reached {balance} messages. Immediate recharge required to continue SMS services.',
    ],

    'balance_tracking' => [
        'enabled' => true,
        'current_balance' => 500, // Update this to your actual Beems balance
        'log_usage' => true,
    ],

    'beem_api' => [
        'balance_check_endpoint' => 'https://apisms.beem.africa/public/v1/vendors/balance',
        'use_real_balance' => true, // Enable real balance checking
        'api_key' => '48a8c40076933db5',
        'secret_key' => 'ZjE4NjQxMzBhODIwMmQzNjZjMWE5YjJmODY3YzEyZmM0NzliODI1NDE3Y2U0NjAzYmUyOWE3NWU4ODcxYzVkYg==',
    ],
];
