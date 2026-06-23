<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

DB::statement("ALTER TABLE booking MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");
DB::statement("ALTER TABLE orders MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");

DB::table('booking')->where('status', '')->update(['status' => 'satisfied']);
DB::table('orders')->where('status', '')->update(['status' => 'satisfied']);

// ALSO wait, the auto-increment on bookings!
// In the schema above, `id` was int(10) unsigned NOT NULL without AUTO_INCREMENT!
try {
    DB::statement("ALTER TABLE booking MODIFY id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
} catch (\Exception $e) {
    echo "AI Error: " . $e->getMessage() . "\n";
}

echo "Database updated successfully.";
