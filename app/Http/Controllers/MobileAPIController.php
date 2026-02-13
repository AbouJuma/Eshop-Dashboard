<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileAPIController extends Controller
{
    public function getbooking()
    {
        $booking_list = DB::table('booking')->get();
        return response()->json($booking_list);
    }


    public function deliveries()
    {
        $deliveries = DB::table('deliveries')->get();
        return response()->json($booking_list);
    }

     public function make()
    {
        $make = DB::table('make')->get();
        return response()->json($make);
    }
    
     public function inventory()
    {
        $inventory = DB::table('inventory')->get();
        return response()->json($inventory);
    }
    

     public function orders()
    {
        $orders = DB::table('orders')->get();
        return response()->json($orders);
    }

     public function order_eshops()
    {
        $order_eshops = DB::table('order_eshops')->get();
        return response()->json($order_eshops);
    }
    
    public function  products()
    {
        $products = DB::table('products')->get();
        return response()->json($products);
    }

    public function product_categories()
    {
        $product_categories = DB::table('product_categories')->get();
        return response()->json($product_categories);
    }
    
   public function otps()
    {
        $otps = DB::table('otps')->get();
        return response()->json($otps);
    }

      public function services()
    {
        $services = DB::table('services')->get();
        return response()->json($services);
    }

    public function schedules()
    {
        $schedules = DB::table('schedules')->get();
        return response()->json($schedules);
    }

     public function models()
    {
        $models = DB::table('models')->get();
        return response()->json($models);
    }
}