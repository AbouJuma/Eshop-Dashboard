<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Debugging OTP verification process...\n\n";

// Test data from your request
$phone = '255682676819';
$otp = '371233';

echo "=== TESTING OTP VERIFICATION ===\n";
echo "Phone: {$phone}\n";
echo "OTP: {$otp}\n\n";

// 1. Check if OTP exists in database
$dbOtp = \App\Models\OTP::where('phone', $phone)->where('otp', $otp)->first();
if ($dbOtp) {
    echo "✅ OTP found in database:\n";
    echo "- ID: " . $dbOtp->id . "\n";
    echo "- Phone: " . $dbOtp->phone . "\n";
    echo "- OTP: " . $dbOtp->otp . "\n";
    echo "- Created: " . $dbOtp->created_at . "\n";
    echo "- Updated: " . $dbOtp->updated_at . "\n";
} else {
    echo "❌ OTP NOT found in database\n";
    
    // Check if phone exists with different OTP
    $phoneExists = \App\Models\OTP::where('phone', $phone)->first();
    if ($phoneExists) {
        echo "⚠️  Phone exists but with different OTP:\n";
        echo "- Phone: " . $phoneExists->phone . "\n";
        echo "- OTP in DB: " . $phoneExists->otp . "\n";
        echo "- Your OTP: {$otp}\n";
    } else {
        echo "❌ Phone not found in OTP table\n";
    }
}

echo "\n=== TESTING OTP EXPIRATION ===\n";
if ($dbOtp) {
    $now = now();
    $created = $dbOtp->created_at;
    $diffInMinutes = $now->diffInMinutes($created);
    $diffInHours = $now->diffInHours($created);
    
    echo "Current time: " . $now . "\n";
    echo "OTP created: " . $created . "\n";
    echo "Difference: {$diffInMinutes} minutes ({$diffInHours} hours)\n";
    
    // Check if OTP is expired (older than 1 hour)
    if ($diffInHours >= 1) {
        echo "⚠️  OTP might be expired (older than 1 hour)\n";
    } else {
        echo "✅ OTP is fresh (less than 1 hour old)\n";
    }
}

echo "\n=== TESTING OTPTrait VERIFY METHOD ===\n";
try {
    $controller = new \App\Http\Controllers\PassportAuthController();
    
    // Test the verify method directly
    $verifyResult = $controller->verify($phone, $otp);
    echo "Verify method result: " . ($verifyResult ? 'TRUE' : 'FALSE') . "\n";
    
    if (!$verifyResult) {
        echo "❌ Verify method returned FALSE\n";
        echo "This means the OTP verification logic failed\n";
        
        // Let's check what the verify method does
        echo "\n=== DEBUGGING VERIFY METHOD LOGIC ===\n";
        
        // Check if deleteExpiredOTP is causing issues
        echo "The verify method calls deleteExpiredOTP() first\n";
        echo "This might be deleting your OTP before verification\n";
        
        // Check current OTP count
        $beforeCount = \App\Models\OTP::count();
        echo "OTP count before verify: {$beforeCount}\n";
        
        // Call deleteExpiredOTP to see what happens
        $controller->deleteExpiredOTP();
        
        $afterCount = \App\Models\OTP::count();
        echo "OTP count after deleteExpiredOTP: {$afterCount}\n";
        
        if ($afterCount < $beforeCount) {
            echo "⚠️  deleteExpiredOTP() deleted some OTPs\n";
            echo "Your OTP might have been deleted as expired\n";
        }
        
        // Check if your OTP still exists
        $otpStillExists = \App\Models\OTP::where('phone', $phone)->where('otp', $otp)->first();
        if ($otpStillExists) {
            echo "✅ Your OTP still exists after cleanup\n";
        } else {
            echo "❌ Your OTP was deleted by cleanup\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error testing verify method: " . $e->getMessage() . "\n";
}

echo "\n=== POSSIBLE ISSUES ===\n";
echo "1. OTP format mismatch (spaces, special chars)\n";
echo "2. OTP expiration (older than 1 hour)\n";
echo "3. deleteExpiredOTP() removing your OTP\n";
echo "4. Phone number format (+ vs no +)\n";
echo "5. Case sensitivity issues\n";

echo "\n=== SOLUTIONS ===\n";
echo "1. Use fresh OTP (less than 1 hour old)\n";
echo "2. Check phone format consistency\n";
echo "3. Verify OTP is not being deleted by cleanup\n";
echo "4. Test immediately after OTP generation\n";
