<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\VehicleResource;
use App\Http\Resources\MakeResource;
use App\Models\Vehicle;
use App\Models\Make;
use App\Models\User;

use Spatie\QueryBuilder\QueryBuilder as Filter;

class VehicleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page = 100;
        return $this->sendResponse(VehicleResource::collection(Vehicle::paginate($per_page)), 'RETRIEVE_SUCCESS');
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

    public function clientVehicles(User $user)
    {
        $per_page = 100;
        return $this->sendResponse(VehicleResource::collection(Vehicle::where('user_id', $user->id)->paginate($per_page)), 'RETRIEVE_SUCCESS');
    }
}
