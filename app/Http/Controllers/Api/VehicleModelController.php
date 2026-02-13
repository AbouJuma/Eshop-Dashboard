<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\VehicleModelResource;
use App\Models\VehicleModel;

class VehicleModelController extends Controller
{
    public function index()
    {
        $models = VehicleModel::with("make", "years")->get();

        if (!$models) return $this->sendError('NOT_FOUND', 404);
        return $this->sendResponse(VehicleModelResource::collection($models), 'RETRIEVE_SUCCESS');
    }
}
