<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\MakeResource;

use App\Models\Make;
use App\Models\VehicleModel;

class MakeController extends BaseController
{
    public function index()
    {
        $makes = Make::with("vehicleModels")->get();

        // Pre-compute every model's year range in one query instead of one query per model
        $ranges = VehicleModel::allYearRanges();
        foreach ($makes as $make) {
            foreach ($make->vehicleModels as $model) {
                $model->setAttribute('year_range', $ranges[$model->id] ?? ['min' => 0, 'max' => 0]);
            }
        }

        if (!$makes) return $this->sendError('NOT_FOUND', 404);
        return $this->sendResponse(MakeResource::collection($makes), 'RETRIEVE_SUCCESS');
    }
}
