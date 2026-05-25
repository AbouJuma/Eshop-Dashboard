<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use Modules\Connector\Http\Controllers\Api\ProductController;
use App\Http\Controllers\MobileAPIController;

use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\FeedBackController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\RecordController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\MakeController;
use App\Http\Controllers\Api\OTPController;

use App\Http\Middleware\WithoutLinks;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('api')->group(function(){
    Route::get('/user', function (Request $request) {return $request->user();});

    // PRODUCTS
    Route::get('/products/categories', [ProductController::class, 'categories']);
        // Route::get('/products/dol-shop', [ProductController::class, 'categories']);

    /** Get DOL SPARE SHOP NJIA YA NG'OMBE products by categories */
    Route::get('/products/dol-shop', [ProductController::class, 'getDolShopProductsByCategories']);
        // Route::get('/products/categories', [ProductController::class, 'getDolShopProductsByCategories']);

    /** Get all business locations */
    Route::get('/locations', [ProductController::class, 'getAllLocations']);
    
    /** Get products by location ID */
    Route::get('/products/location/{location_id}', [ProductController::class, 'getProductsByLocation']);
    
    /** Get products by location name */
    Route::get('/products/location-name/{location_name}', [ProductController::class, 'getProductsByLocationName']);

   


});



// Route::get('/getbooking', [MobileAPIController::class, 'getbooking']);
// Route::get('/deliveries', [MobileAPIController::class, 'deliveries']);
// Route::get('/make', [MobileAPIController::class, 'make']);
// Route::get('/inventory', [MobileAPIController::class, 'inventory']);
// Route::get('/order_eshops', [MobileAPIController::class, 'order_eshops']);
// Route::get('/orders', [MobileAPIController::class, 'orders']);
// Route::get('/product_categories', [MobileAPIController::class, 'product_categories']);
// Route::get('/otps', [MobileAPIController::class, 'otps']);
// Route::get('/products', [MobileAPIController::class, 'products']);
// Route::get('/services', [MobileAPIController::class, 'services']);
// Route::get('/schedules', [MobileAPIController::class, 'schedules']);
// Route::get('/models', [MobileAPIController::class, 'models']);




Route::post('auth', [\App\Http\Controllers\PassportAuthController::class, 'authenticate']);
Route::post('login', [\App\Http\Controllers\PassportAuthController::class, 'login']);

Route::post('otp', [OTPController::class, 'index']);
// Route::post('otp-verify', [OTPController::class, 'verify']);
/** User routes */
Route::apiResource('users', UserController::class);

Route::get('makes', [MakeController::class, 'index']);
Route::post('feedback', [FeedBackController::class, 'send']);
Route::post('delete-account', [UserController::class, 'delete']);

/** Vehicle Routes */
Route::apiResource('vehicles', VehicleController::class);

/** Get Client's Vehicle routes */
Route::get('vehicles/client/{user}', [VehicleController::class, 'clientVehicles']);

/** Service - Bookings routes */
Route::apiResource('booking', BookingController::class);

Route::post('shop-order', [OrderController::class,'store'] );

/** Service routes */
Route::apiResource('services', ServiceController::class);

/** Get all schedules */
Route::apiResource('schedules', ScheduleController::class)->only(['index', 'show']);


/** Get products in their categories */
Route::get('products/categories', [ProductController::class, 'getProductInCategories']);

/** Get products by category */
Route::get('products/categories/{category}', [ProductController::class, 'getProductsByCategory']);

/** Get product by id  */
Route::get('products/{product}', [ProductController::class, 'show']);

/** Product routes */
Route::apiResource('products', ProductController::class);


Route::middleware('withoutlink')->group(function () {
    //all sub services
    Route::post('sub-services', [ServiceController::class, 'subServices']);
    /** Search Routes */
    Route::get('search/sub_services', [ServiceController::class, 'search']);
    

});


Route::middleware(['auth:api', 'withoutlink'])->group(function () {
    //all notifcations
    Route::get('notifications', [UserController::class, 'notifications']);

    /** Question routes */
    Route::apiResource('questions', QuestionController::class);

    /** Records routes */
    Route::apiResource('records', RecordController::class)->only(['index', 'show']);

    /** Order routes */
    Route::apiResource('orders', OrderController::class);
    
    /** Order Satisfied route */
    Route::post('orders/{order}/satisfy', [OrderController::class, 'satisfy']);

    /** Get records by type */
    Route::get('records/type/{type}', [RecordController::class, 'getRecordsByType']);
});

Route::middleware('auth:api')->group(function () {
    /** delete record by id */
    Route::delete('records/{record}', [RecordController::class, 'destroy']);
});