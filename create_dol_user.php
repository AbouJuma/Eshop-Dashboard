<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "Creating user for DOL Business (ID 2)...\n";

try {
    // User details
    $userData = [
        'username' => 'doladmin',
        'email' => 'doladmin@dadisonestop.com',
        'business_id' => 2, // DADIS ONE STOP GARAGE
        'first_name' => 'DOL',
        'last_name' => 'Admin',
        'password' => Hash::make('doladmin123'), // Plain password: doladmin123
        'status' => 'active',
        'user_type' => 'user',
        'contact_no' => '+255123456789',
        'created_at' => now(),
        'updated_at' => now()
    ];

    // Check if user already exists
    $existingUser = DB::table('users')->where('email', $userData['email'])->first();
    if ($existingUser) {
        echo "User with email " . $userData['email'] . " already exists (ID: " . $existingUser->id . ")\n";
        echo "Updating password...\n";
        
        // Update existing user
        DB::table('users')->where('email', $userData['email'])->update([
            'password' => $userData['password'],
            'business_id' => 2,
            'updated_at' => now()
        ]);
        
        $user = $existingUser;
    } else {
        // Insert new user
        $userId = DB::table('users')->insertGetId($userData);
        echo "Created new user with ID: " . $userId . "\n";
        
        $user = DB::table('users')->where('id', $userId)->first();
    }

    echo "\n=== USER CREATED SUCCESSFULLY ===\n";
    echo "Username: " . $user->username . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Business ID: " . $user->business_id . " (DADIS ONE STOP GARAGE)\n";
    echo "Plain Password: doladmin123\n";
    echo "Hashed Password: " . $user->password . "\n";
    echo "Status: " . $user->status . "\n";
    echo "User Type: " . $user->user_type . "\n";
    
    echo "\n=== LOGIN CREDENTIALS ===\n";
    echo "Email: doladmin@dadisonestop.com\n";
    echo "Password: doladmin123\n";
    
    echo "\n=== ACCESSIBLE LOCATIONS ===\n";
    $locations = DB::table('business_locations')->where('business_id', 2)->get();
    foreach ($locations as $location) {
        echo "- Location ID: " . $location->id . " Name: " . $location->name . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nUser creation completed!\n";
