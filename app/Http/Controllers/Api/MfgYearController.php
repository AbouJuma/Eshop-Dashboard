<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\MfgYearResource;
use App\Models\MfgYear;

class MfgYearController extends BaseController
{
    public function index()
    {
        $years = MfgYear::all();

        if (!$years) return $this->sendError('NOT_FOUND', 404);
        return $this->sendResponse(MfgYearResource::collection($years), 'RETRIEVE_SUCCESS');
    }
}
