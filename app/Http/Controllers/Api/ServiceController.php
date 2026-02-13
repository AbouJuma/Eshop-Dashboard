<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Livewire\Service\SubServices;
use Illuminate\Http\Request;

use App\Http\Resources\ServiceResource;
use App\Http\Resources\ServiceSearchResource;
use App\Http\Resources\ServicesResource;
use App\Models\Service;
use App\Models\SubService;
use App\Models\ServiceVehiclePrice;
use App\Http\Resources\ServiceVehiclePriceResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Spatie\QueryBuilder\QueryBuilder as Filter;

class ServiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::where('status', 1)->get();
        return ServicesResource::collection($services);
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


        //get service from service vehicle price
        $service = Service::where('id', $id)->where('status', 1)->first();
        if (!$service) return $this->sendError('NOT_FOUND', null, 404);
        else return $this->sendResponse(new ServiceResource($service), 'RETRIEVE_SUCCESS')->response()->setStatusCode(200);
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
     * Search for a name
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $per_page = 50;

        $builder = SubService::with('serviceVehiclePrices');
        if ($request->has('q')) {
            $builder = $builder->where('title', 'LIKE', '%' . $request->q . '%')
                ->orWhere('description', 'LIKE', '%' . $request->q . '%');
        }

        $services = Filter::for($builder)
            ->orderBy('updated_at', 'DESC')
            ->paginate($per_page);

        if (!$services) {
            return $this->sendError('NOT_FOUND', null, 404);
        }

        return $this->sendResponse(ServiceSearchResource::collection($services), 'RETRIEVE_SUCCESS');
    }

    /**
     * Search for a name
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function subServices(Request $request)
    {
        $subServicesIds = null;
    
        if ($request->service_id != "") {
            $subServicesIds = SubService::where('service_id', $request->service_id)->pluck('id');
        }
    
        $serviceVehiclePriceQuery = ServiceVehiclePrice::when($subServicesIds, function ($query, $subServicesIds) {
            return $query->whereIn('sub_service_id', $subServicesIds);
        });
    
        if (!$request->filled('year')) {
            $request->merge(['year' => '2000']);
        }
    
        $serviceVehiclePrice = $serviceVehiclePriceQuery
            ->when($request->has('make_id') && $request->has('model_id'), function ($query) use ($request) {
                return $query
                    ->where('make_id', $request->make_id)
                    ->where('model_id', $request->model_id)
                    ->where('year_from', '<=', $request->year)
                    ->where('year_to', '>=', $request->year);
            })
            ->select('sub_service_id', 'price', 'discount', DB::raw('MAX(id) as max_id'))
            ->groupBy('sub_service_id', 'price', 'discount')
            ->orderBy('max_id', 'desc')
            ->paginate(50);
        
        // Fix pagination issue: if requesting a page beyond available results, reset to page 1
        if ($serviceVehiclePrice->currentPage() > $serviceVehiclePrice->lastPage() && $serviceVehiclePrice->lastPage() > 0) {
            $request->merge(['page' => 1]);
            $serviceVehiclePrice = $serviceVehiclePriceQuery
                ->when($request->has('make_id') && $request->has('model_id'), function ($query) use ($request) {
                    return $query
                        ->where('make_id', $request->make_id)
                        ->where('model_id', $request->model_id)
                        ->where('year_from', '<=', $request->year)
                        ->where('year_to', '>=', $request->year);
                })
                ->select('sub_service_id', 'price', 'discount', DB::raw('MAX(id) as max_id'))
                ->groupBy('sub_service_id', 'price', 'discount')
                ->orderBy('max_id', 'desc')
                ->paginate(50);
        }
    
        return $this->sendResponse(ServiceVehiclePriceResource::collection($serviceVehiclePrice), 'RETRIEVE_SUCCESS');
    }
    
    
}
