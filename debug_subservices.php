<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ServiceVehiclePrice;
use App\Models\SubService;

echo "Debugging sub-services API issue...\n\n";

$params = [
    'make_id' => 101,
    'model_id' => 101,
    'year' => 2008,
    'service_id' => [42]
];

echo "=== CHECKING PARAMETERS ===\n";
echo "Make ID: " . $params['make_id'] . "\n";
echo "Model ID: " . $params['model_id'] . "\n";
echo "Year: " . $params['year'] . "\n";
echo "Service ID: " . implode(', ', $params['service_id']) . "\n\n";

echo "=== CHECKING SERVICE VEHICLE PRICES TABLE ===\n";
$totalRecords = DB::table('service_vehicle_prices')->count();
echo "Total records in service_vehicle_prices: " . $totalRecords . "\n";

$matchingMakeModel = DB::table('service_vehicle_prices')
    ->where('make_id', $params['make_id'])
    ->where('model_id', $params['model_id'])
    ->count();
echo "Records with make_id=101 AND model_id=101: " . $matchingMakeModel . "\n";

$matchingYear = DB::table('service_vehicle_prices')
    ->where('make_id', $params['make_id'])
    ->where('model_id', $params['model_id'])
    ->where('year_from', '<=', $params['year'])
    ->where('year_to', '>=', $params['year'])
    ->count();
echo "Records matching year range: " . $matchingYear . "\n\n";

echo "=== CHECKING SUB-SERVICES ===\n";
$subServicesIds = null;
if (!empty($params['service_id'])) {
    $subServicesIds = SubService::whereIn('service_id', $params['service_id'])->pluck('id');
    echo "Sub-service IDs for service_id [42]: " . $subServicesIds->implode(', ') . "\n";
    echo "Count: " . $subServicesIds->count() . "\n";
} else {
    echo "No service_id filter\n";
}

echo "\n=== FINAL QUERY SIMULATION ===\n";
$query = DB::table('service_vehicle_prices');

if ($subServicesIds && $subServicesIds->isNotEmpty()) {
    $query->whereIn('sub_service_id', $subServicesIds);
    echo "Applied sub_service_id filter: " . $subServicesIds->implode(', ') . "\n";
}

$query->where('make_id', $params['make_id'])
    ->where('model_id', $params['model_id'])
    ->where('year_from', '<=', $params['year'])
    ->where('year_to', '>=', $params['year']);

$results = $query->get();
echo "Final query results count: " . $results->count() . "\n";

if ($results->isNotEmpty()) {
    echo "Sample results:\n";
    foreach ($results->take(3) as $result) {
        echo "- Sub Service ID: " . $result->sub_service_id . ", Price: " . $result->price . "\n";
    }
} else {
    echo "❌ No results found with these parameters!\n";
}

echo "\n=== TESTING DIFFERENT PARAMETERS ===\n";
// Test with just make_id and model_id
$testResults = DB::table('service_vehicle_prices')
    ->where('make_id', $params['make_id'])
    ->where('model_id', $params['model_id'])
    ->limit(5)
    ->get();

echo "Records with make_id=101, model_id=101 (any year):\n";
foreach ($testResults as $result) {
    echo "- Sub Service ID: " . $result->sub_service_id . ", Year: " . $result->year_from . "-" . $result->year_to . ", Price: " . $result->price . "\n";
}

echo "\nDebug completed!\n";
