<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

$tables = ['orders', 'order_eshops', 'bookings', 'booking_sub_services'];
foreach ($tables as $table) {
    $deleted = DB::table($table)->where('id', 0)->delete();
    if ($table == 'booking_sub_services') {
        DB::table($table)->where('booking_id', 0)->delete();
    }
    if ($table == 'order_eshops') {
        DB::table($table)->where('order_id', 0)->delete();
    }
    echo "Deleted $deleted rows with id=0 from $table.\n";
    
    // Ensure AI is set
    try {
        DB::statement("ALTER TABLE {$table} MODIFY id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT");
    } catch (\Exception $e) {
        try {
            DB::statement("ALTER TABLE {$table} MODIFY id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
        } catch (\Exception $e2) {
            echo "Error setting AI on $table\n";
        }
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Cleanup complete.";
