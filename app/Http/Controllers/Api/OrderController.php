<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\NotificationService;
use App\Models\Product;
use App\Models\OrderProduct;
use App\Models\User;

use App\Http\Resources\OrderResource;
use App\Models\OrderEshop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Address;
use App\Http\Integration\Beem\BeemSMSController;

class OrderController extends BaseController
{
    /**
     * Send SMS using Beem API
     *
     * @param string $message
     * @return bool
     */
    private function sendBeemSMS($message)
    {
        $api_key = '48a8c40076933db5';
        $secret_key = 'ZjE4NjQxMzBhODIwMmQzNjZjMWE5YjJmODY3YzEyZmM0NzliODI1NDE3Y2U0NjAzYmUyOWE3NWU4ODcxYzVkYg==';
        
        // Define recipients (you can modify this based on your needs)
        $recipients = [
            array('recipient_id' => '1', 'dest_addr' => '255787011402'),
            array('recipient_id' => '2', 'dest_addr' => '255684551070'),
            array('recipient_id' => '3', 'dest_addr' => '255788753599'),
            array('recipient_id' => '4', 'dest_addr' => '255682676819'),
        ];
        
        $postData = array(
            'source_addr' => 'OneMile',
            'encoding' => 0,
            'schedule_time' => '',
            'message' => $message,
            'recipients' => $recipients
        );
        
        $Url = 'https://apisms.beem.africa/v1/send';
        
        $ch = curl_init($Url);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization:Basic ' . base64_encode("48a8c40076933db5:ZjE4NjQxMzBhODIwMmQzNjZjMWE5YjJmODY3YzEyZmM0NzliODI1NDE3Y2U0NjAzYmUyOWE3NWU4ODcxYzVkYg=="),
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));
        
        $response = curl_exec($ch);
        
        if ($response === false) {
            Log::error('SMS sending failed: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }
        
        curl_close($ch);
        
        // Log the response for debugging
        Log::info('SMS response: ' . $response);
        
        return true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page = 15;

        $client = auth()->user();

        if (!$client) return $this->sendError('NOT_LOGGED_IN');

        $orders = Order::with('products')->where('user_id', $client->id)->paginate($per_page);

        return $this->sendResponse(OrderResource::collection($orders), 'RETRIEVE_SUCCESS');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

Log::alert($request->all());
        // var_dump($request->all()); die;
        // $validator = Validator::make($request->all(), [
        //     "user_id" => "required|integer",
        //     "services" => "required",
        //     "address" => "required",
        //     "vehicleData" => "required",
        //     "grandTotal" => "required",
        //     "appointmentDate" => "required",
        //     "appointmentTime" => "required",
        //     "fcmToken" => "required"
        // ]);

        // if ($validator->fails()) {
        //     return $this->sendError('VALIDATION_FAILED', $validator->errors(), 404);
        // }
        $client_id = $request->user_id ? $request->user_id : auth()->user()->id;
        
        // Get user for later use
        $user = User::find($client_id);
        
        //update user name if set
        if ($request->name != null) {
            $user->username = $request->name;
            $user->save();
        }

        //location
        $address = Address::updateOrCreate(
            ['location' => $request->address['location'], 'type' => 'delivery'],
            [
                'location' => $request->address['location'], 
                'type' => 'delivery',
                'additional_info' => $request->description ?? NULL
            ]
        );

        //order
        $order = new Order();
        try {
            $order->amount = $request->cartTotal;
            $order->user_id = $client_id;
            $order->reference_no = rand(99999,999999);
            $order->address_id = $address->id;
            $order->save();
Log::alert($request->products);
            foreach ($request->products as $product) {
                $product = explode("|",$product);
                Log::alert($product);
                $orderEshop = new OrderEshop();
                $orderEshop->order_id = $order->id;
                $orderEshop->p_name = $product[0];
                $orderEshop->p_image = $product[1];
                $orderEshop->p_price = $product[2];
                $orderEshop->p_quantity = $product[3];
                $orderEshop->p_sku = $product[4];
                $orderEshop->save();
            }

            if($order != null){
                $message = "Order ".$order->reference_no. " is created by ". $user->username. " just now";
                $this->sendBeemSMS($message);
                
                // Create notification for the user
                NotificationService::orderCreated($user->id, $order->reference_no);
            }
        
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->sendError('FAILED', $th->getMessage(), 500);

        }

        // return $this->sendResponse(new RecordResource($booking), 'CREATE_SUCCESS')->response()->setStatusCode(200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$order = Order::find($id)) return $this->sendError('NOT_FOUND', 404);
        else return $this->sendResponse(new OrderResource($order), 'RETRIEVE_SUCCESS')->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Update order status to 'satisfied' when customer receives and is satisfied.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function satisfy($id)
    {
        try {
            $order = Order::find($id);
            
            if (!$order) {
                return $this->sendError('NOT_FOUND', 'Order not found', 404);
            }
            
            // Optionally, verify that the order belongs to the authenticated user
            if (auth()->check() && $order->user_id !== auth()->user()->id) {
                return $this->sendError('UNAUTHORIZED', 'You are not authorized to update this order', 403);
            }
            
            $order->status = 'satisfied';
            $order->save();
            
            return $this->sendResponse(new OrderResource($order), 'Order status updated to Customer Satisfied successfully')->response()->setStatusCode(200);
            
        } catch (\Exception $e) {
            Log::error('Error updating order to satisfied: ' . $e->getMessage());
            return $this->sendError('FAILED', 'Error updating order status: ' . $e->getMessage(), 500);
        }
    }
}
