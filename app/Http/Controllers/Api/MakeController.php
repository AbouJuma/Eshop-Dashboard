<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\MakeResource;

use App\Models\Make;

class MakeController extends BaseController
{
    public function index()
    {
        $makes = Make::with("vehicleModels")->get();

        if (!$makes) return $this->sendError('NOT_FOUND', 404);
        return $this->sendResponse(MakeResource::collection($makes), 'RETRIEVE_SUCCESS');
    }
}
