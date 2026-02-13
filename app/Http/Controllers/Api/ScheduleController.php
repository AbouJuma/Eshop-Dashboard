<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Schedule;

use App\Http\Resources\ScheduleResource;

class ScheduleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedules = Schedule::latest('date')->limit(4)->get(); // for trial
        // $schedules = Schedule::where('date', '>=', date('Y-m-d'))->paginate($per_page); // for production
        return $this->sendResponse(ScheduleResource::collection($schedules), 'RETRIEVE_SUCCESS');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$schedule = Schedule::find($id)) return $this->sendError('NOT_FOUND', 404);
        else return $this->sendResponse(new ScheduleResource($schedule), 'RETRIEVE_SUCCESS')->response()->setStatusCode(200);
    }
}
