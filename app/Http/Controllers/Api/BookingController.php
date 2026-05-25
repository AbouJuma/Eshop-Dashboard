<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\Booking;
use App\Models\SubService;
use App\Models\BookingSubService;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Make;
use App\Models\VehicleModel;
use App\Http\Integration\Beem\BeemSMSController;
use App\Services\NotificationService;

use App\Http\Resources\RecordResource;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingController extends BaseController
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
        
        // Define recipients (same as OrderController)
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
        
        // Log response for debugging
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
        // won't be implemented
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|integer",
            "services" => "required",
            "address" => "required",
            "vehicleData" => "required",
            "grandTotal" => "required",
            "appointmentDate" => "required",
            "appointmentTime" => "required",
            // "fcmToken" => "required" //iOS notification
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_FAILED', $validator->errors(), 404);
        }

        Log::info($request->all());

        // check if user is logged in
        $client_id = $request->user_id ? $request->user_id : auth()->user()->id;

        //update user name if set
        if($request->name != null){
            $user = User::find($client_id);
            $user->username = $request->name;
            $user->save();
        }

        $address = Address::updateOrCreate(
            ['location' => $request->address['location'], 'type' => 'booking'],
            [
                'location' => $request->address['location'], 
                'type' => 'booking',
                'additional_info' => $request->description ?? NULL
            ]
        );

        // var_dump($request->services); die;

        // TODO:: Look for a finer way to create a new booking
        $booking = new Booking();
        try {
            if($address) {
                DB::transaction(function () use ($request, $client_id, $address, $booking) {
                    // check if the given amount is equal to the sum of the services

                   

                    $booking->user_id = $client_id;
                    $booking->address_id = $address->id;
                    $booking->reference_number = $request->appointmentID;

                    // explode values of time to get required values for from and to
                    $explodedValues = explode('-', $request->appointmentTime);
                    $from = new \DateTime($explodedValues[0]);
                    $to = new \DateTime($explodedValues[1]);


                    $booking->from = $from->format('H:i:s');
                    $booking->to = $to->format('H:i:s');
                    $date = new \DateTime($request->appointmentDate);

                    $booking->date = $date;
                    // $booking->grand_total = $this->getGrandTotal($request->services, $request->grandTotal);
                    $booking->grand_total = $request->grandTotal;
                    $booking->status = "pending";
                    $booking->vehicle = $request->vehicleName;
                    $booking->save();
                    
                    $client = User::find($booking->user_id);
                    if($booking != null && $client != null && $request->fcmToken != null ){
                        $client->fcm_token = $request->fcmToken;
                        $client->save();
                    }

                    // save required booking - services
                    $service = $request->services;

                    for($i = 0; $i < count($service); $i++) {
                        $sub_service_id = explode('-', $service[$i])[0];
                        $bookingSubService = new BookingSubService;
                        $bookingSubService->booking_id = $booking->id;
                        $bookingSubService->sub_service_id = $sub_service_id;
                        $bookingSubService->save();
                    }

                    if($booking != null){
                        $message = "Service Booking ".$booking->id. " is created by ". $client->username. " just now";
                        $this->sendBeemSMS($message);
                        
                        // Create notification for the user with reference number
                        NotificationService::bookingConfirmed($client->id, $booking->id, $booking->reference_number);
                    }

                });
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->sendError('FAILED', $th->getMessage(), 500);
        }

        // return response
        return $this->sendResponse(new RecordResource($booking), 'CREATE_SUCCESS')->response()->setStatusCode(200);
    }

    public function getGrandTotal(Array $services, $total)
    {
        $grandTotal = 0;
        for($i = 0; $i < count($services); $i++) {
            $service_id = explode('-', $services[$i])[0];
            $service = SubService::findOrFail($service_id);
            $grandTotal += $service->price;
        }

        if($grandTotal != $total) {
            return $grandTotal;
        } else {
            return $total;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        // get the request
        // what is the action to be performed?
        // action available to the client is only cancel and it should be checked if the specific booking has been accepted or not
        // if rejected, cancel option is invalid
        // else, proceed to update the status of the booking

        // return the new status
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
}
