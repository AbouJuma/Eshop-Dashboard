<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\RecordResource;

use App\Models\BookingSubService;
use App\Models\Booking;

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
        
        $records = Booking::with('client', 'address', 'services')->where('user_id', $client->id)->latest()->paginate($per_page);

        return $this->sendResponse(RecordResource::collection($records), 'RETRIEVE_SUCCESS');
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
}
