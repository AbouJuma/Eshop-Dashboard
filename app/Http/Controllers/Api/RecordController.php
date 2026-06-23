<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\RecordResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\BookingSubServiceResource;

use App\Models\BookingSubService;
use App\Models\Booking;
use App\Models\Order;
use App\Services\NotificationService;

class RecordController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page = 30;

        // for only the authenticated user
        $client = auth()->user();
        
        // Get bookings with relationships
        $bookings = Booking::with('client', 'address', 'services')
            ->where('user_id', $client->id)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'location' => $booking->address ? $booking->address->location : 'N/A',
                    'grandTotal' => $booking->grand_total,
                    'finalTotal' => $booking->final_total,
                    'appointmentDate' => $booking->date,
                    'appointmentTime' => date('h:i:s A', strtotime($booking->from)) . ' - ' . date('h:i:s A', strtotime($booking->to)),
                    'client' => $booking->client ? new UserResource($booking->client) : null,
                    'sub_services' => $booking->bookingSubServices ? BookingSubServiceResource::collection($booking->bookingSubServices) : [],
                    'record_from' => 'booking',
                    'reference_number' => $booking->reference_number,
                    'unsatisfied_reason' => $booking->unsatisfied_reason,
                    'created_at' => $booking->created_at,
                ];
            });

        // Get orders with relationships
        $orders = Order::with('products')
            ->where('user_id', $client->id)
            ->get()
            ->map(function ($order) {
                // Transform order products to match SubService structure for frontend
                $subServices = [];
                if ($order->products) {
                    foreach ($order->products as $product) {
                        $subServices[] = [
                            'id' => $product->id,
                            'title' => $product->p_name ?? 'Product',
                            'price' => $product->p_price ?? '0',
                            'description' => $product->p_name ?? 'Product',
                            'image' => $product->p_image ?? null,
                        ];
                    }
                }
                return [
                    'id' => $order->id,
                    'status' => $order->status ?? 'pending',
                    'location' => 'Order Delivery',
                    'grandTotal' => $order->amount,
                    'finalTotal' => $order->amount,
                    'appointmentDate' => $order->created_at->format('Y-m-d'),
                    'appointmentTime' => $order->created_at->format('h:i:s A'),
                    'client' => $order->user ? new UserResource($order->user) : null,
                    'sub_services' => $subServices,
                    'record_from' => 'order',
                    'reference_number' => $order->reference_no,
                    'unsatisfied_reason' => $order->unsatisfied_reason,
                    'created_at' => $order->created_at,
                ];
            });

        // Combine and sort by created_at (newest first)
        $allRecords = $bookings->concat($orders)
            ->sortByDesc('created_at')
            ->values();

        // Manual pagination
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $per_page;
        $paginatedRecords = $allRecords->slice($offset, $per_page);
        
        $paginationData = [
            'current_page' => $currentPage,
            'data' => $paginatedRecords->values(),
            'first_page_url' => request()->url() . '?page=1',
            'from' => $offset + 1,
            'last_page' => ceil($allRecords->count() / $per_page),
            'last_page_url' => request()->url() . '?page=' . ceil($allRecords->count() / $per_page),
            'next_page_url' => $currentPage < ceil($allRecords->count() / $per_page) ? request()->url() . '?page=' . ($currentPage + 1) : null,
            'path' => request()->url(),
            'per_page' => $per_page,
            'prev_page_url' => $currentPage > 1 ? request()->url() . '?page=' . ($currentPage - 1) : null,
            'to' => $offset + $paginatedRecords->count(),
            'total' => $allRecords->count(),
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'RETRIEVE_SUCCESS',
            'status_code' => 200,
            'data' => $paginationData
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$record = Booking::find($id)) return $this->sendError('NOT_FOUND', 404);
        else return $this->sendResponse(new RecordResource($record), 'RETRIEVE_SUCCESS')->response()->setStatusCode(200);
    }

    // Get records by type
    public function getRecordsByType($type)
    {
        $per_page = 100;

        // for only the authenticated user
        $client = auth()->user();
        $records = Booking::where('user_id', $client->id)->where('type', $type)->paginate($per_page);
        return $this->sendResponse(RecordResource::collection($records), 'RETRIEVE_SUCCESS');
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
        //delete record if exists and status is pending

        if (!$record = Booking::find($id)) return $this->sendError('NOT_FOUND', 404);
        else {
        if ($record->status != 'pending') {
            return $this->sendError('RECORD_NOT_PENDING', 400);
        }
        $record->delete();
        return $this->sendResponse(new RecordResource($record), 'DELETE_SUCCESS');
        }
    }

    /**
     * Cancel a record (booking or order) with reason
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $record
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $record)
    {
        // Log the incoming request for debugging
        \Log::info('Cancel request received', ['id' => $record, 'data' => $request->all(), 'user' => auth()->user() ? auth()->user()->id : 'not authenticated']);

        $request->validate([
            'cancellation_reason' => 'required|string|max:1000'
        ]);

        // Use record_from parameter if provided to determine which table to query
        $recordFrom = $request->input('record_from');

        if ($recordFrom === 'order') {
            $booking = \App\Models\Order::find($record);
            $type = 'order';
        } elseif ($recordFrom === 'booking') {
            $booking = Booking::find($record);
            $type = 'booking';
        } else {
            // Try to find as booking first (fallback for backward compatibility)
            $booking = Booking::find($record);
            $type = 'booking';

            // If not found as booking, try as order
            if (!$booking) {
                $booking = \App\Models\Order::find($record);
                $type = 'order';
            }
        }

        if (!$booking) {
            \Log::error('Record not found for cancellation', ['id' => $record]);
            return response()->json([
                'status' => 'error',
                'message' => 'Record not found',
                'status_code' => 404
            ], 404);
        }

        // Check if record can be cancelled (allow pending, accepted, confirmed, completed)
        if (!in_array($booking->status, ['pending', 'accepted', 'confirmed', 'completed'])) {
            \Log::error('Record cannot be cancelled', ['id' => $record, 'status' => $booking->status]);
            return response()->json([
                'status' => 'error',
                'message' => 'This record cannot be cancelled. Current status: ' . $booking->status,
                'status_code' => 400
            ], 400);
        }

        $booking->status = 'cancelled';
        $booking->cancellation_reason = $request->cancellation_reason;
        $booking->save();

        \Log::info('Record cancelled successfully', ['id' => $record, 'type' => $type]);

        // Send notification for cancellation
        if ($booking->user_id) {
            $client = \App\Models\User::find($booking->user_id);
            if ($client) {
                if ($type === 'booking') {
                    NotificationService::bookingCancelled($client->id, $booking->id, $booking->reference_number);
                } else {
                    NotificationService::orderStatusChanged($client->id, $booking->reference_no, 'cancelled', 'Your order has been cancelled');
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'CANCEL_SUCCESS',
            'status_code' => 200,
            'data' => $booking
        ], 200);
    }
}
