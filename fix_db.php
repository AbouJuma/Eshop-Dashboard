<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

$tables = [
    'orders',
    'order_eshops',
    'bookings',
    'booking_sub_services',
    'addresses'
];

foreach ($tables as $table) {
    try {
        // Delete rows with id 0 as they block auto increment
        DB::table($table)->where('id', 0)->delete();
        DB::statement("ALTER TABLE {$table} MODIFY id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT");
        echo "Fixed {$table} auto_increment (BIGINT).\n";
    } catch (\Exception $e) {
        try {
            DB::statement("ALTER TABLE {$table} MODIFY id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
            echo "Fixed {$table} auto_increment (INT).\n";
        } catch (\Exception $e2) {
            echo "Error on {$table}: " . $e->getMessage() . "\n" . $e2->getMessage() . "\n";
        }
    }
}
