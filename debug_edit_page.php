<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Debugging Edit Page Data Loading...\n\n";

// Get a sample sub-service with vehicle prices
$subService = \App\Models\SubService::with('serviceVehiclePrices')->first();

if ($subService) {
    echo "=== SUB SERVICE INFO ===\n";
    echo "ID: " . $subService->id . "\n";
    echo "Title: " . $subService->title . "\n";
    echo "Service ID: " . $subService->service_id . "\n";
    echo "Description: " . ($subService->description ?? 'NULL') . "\n";
    echo "Discount: " . ($subService->discount ?? 'NULL') . "\n";
    echo "Status: " . ($subService->status ? 'Active' : 'Inactive') . "\n";
    echo "Image: " . ($subService->image ?? 'NULL') . "\n\n";
    
    echo "=== VEHICLE PRICES ===\n";
    $prices = $subService->serviceVehiclePrices;
    echo "Count: " . $prices->count() . "\n\n";
    
    if ($prices->isNotEmpty()) {
        foreach ($prices as $index => $price) {
            echo "Price " . ($index + 1) . ":\n";
            echo "- Make ID: " . $price->make_id . "\n";
            echo "- Model ID: " . $price->model_id . "\n";
            echo "- Year From: " . $price->year_from . "\n";
            echo "- Year To: " . $price->year_to . "\n";
            echo "- Price: " . $price->price . "\n";
            echo "- Discount: " . ($price->discount ?? 'NULL') . "\n";
            echo "---\n";
        }
    } else {
        echo "❌ No vehicle prices found for this sub-service!\n";
    }
    
    echo "\n=== CHECKING MAKES AND MODELS ===\n";
    $makes = \App\Models\Make::all();
    $models = \App\Models\VehicleModel::all();
    echo "Makes count: " . $makes->count() . "\n";
    echo "Models count: " . $models->count() . "\n";
    
} else {
    echo "❌ No sub-services found in database!\n";
}

echo "\n=== EDIT PAGE URL ===\n";
echo "Edit URL: http://127.0.0.1:8000/sub-services/" . ($subService->id ?? '1') . "/edit\n";

echo "\nDebug completed!\n";
