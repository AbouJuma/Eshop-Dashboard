<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Creating OTP for your new test...\n\n";

$phone = '255682676819';  // Without + sign
$otp = '371233';

echo "=== CHECKING EXISTING OTP ===\n";
$existingOtp = \App\Models\OTP::where('phone', $phone)
    ->where('otp', $otp)
    ->first();

if ($existingOtp) {
    echo "✅ OTP already exists:\n";
    echo "- Phone: " . $existingOtp->phone . "\n";
    echo "- OTP: " . $existingOtp->otp . "\n";
    echo "- Created: " . $existingOtp->created_at . "\n";
} else {
    echo "❌ OTP not found, creating it...\n";
    
    // Create/update the OTP
    $otpRecord = \App\Models\OTP::updateOrCreate(
        ['phone' => $phone], // Search by phone
        [
            'otp' => $otp,
            'updated_at' => now()
        ]
    );
    
    echo "✅ OTP created successfully:\n";
    echo "- Phone: " . $otpRecord->phone . "\n";
    echo "- OTP: " . $otpRecord->otp . "\n";
    echo "- Created: " . $otpRecord->created_at . "\n";
}

echo "\n=== YOUR TEST REQUEST ===\n";
echo "Now you can use this JSON:\n\n";
echo "{\n";
echo "  \"fname\": \"John1\",\n";
echo "  \"mname\": \"Doe1\",\n";
echo "  \"sname\": \"Smith1\",\n";
echo "  \"username\": \"johnsmith1\",\n";
echo "  \"phone\": \"{$phone}\",\n";
echo "  \"otp\": \"{$otp}\",\n";
echo "  \"email\": \"john.smith@example.com\",\n";
echo "  \"password\": \"password123\"\n";
echo "}\n\n";

echo "=== cURL COMMAND ===\n";
echo "curl -X POST http://127.0.0.1:8000/api/auth \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -H \"Accept: application/json\" \\\n";
echo "  -d '{\n";
echo "    \"fname\": \"John1\",\n";
echo "    \"mname\": \"Doe1\",\n";
echo "    \"sname\": \"Smith1\",\n";
echo "    \"username\": \"johnsmith1\",\n";
echo "    \"phone\": \"{$phone}\",\n";
echo "    \"otp\": \"{$otp}\",\n";
echo "    \"email\": \"john.smith@example.com\",\n";
echo "    \"password\": \"password123\"\n";
echo "  }'\n\n";

echo "=== EXPECTED RESPONSE ===\n";
echo "{\n";
echo "  \"token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIs...\",\n";
echo "  \"client_id\": new_user_id\n";
echo "}\n\n";

echo "Ready to test! The OTP is now in your database.\n";
