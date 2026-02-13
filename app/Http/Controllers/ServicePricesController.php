<?php

namespace App\Http\Controllers;

use App\Models\ServiceVehiclePrice;
use App\Models\SubService;
use App\Models\Make;
use App\Models\VehicleModel;
use Illuminate\Http\Request;

class ServicePricesController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceVehiclePrice::with(['subService', 'make', 'model'])->latest();
        
        // Filter by service
        if ($request->filled('sub_service_id')) {
            $query->where('sub_service_id', $request->sub_service_id);
        }
        
        // Filter by make
        if ($request->filled('make_id')) {
            $query->where('make_id', $request->make_id);
        }
        
        // Filter by model
        if ($request->filled('model_id')) {
            $query->where('model_id', $request->model_id);
        }
        
        // Get paginated results
        $servicePrices = $query->paginate(50);
        
        // Get filter options
        $subServices = SubService::where('status', 1)->orderBy('title')->get();
        $makes = Make::orderBy('name')->get();
        $models = VehicleModel::orderBy('name')->get();

        return view('service-prices.index', compact('servicePrices', 'subServices', 'makes', 'models'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_service_id' => 'required|exists:sub_services,id',
            'make_id' => 'required|exists:makes,id',
            'model_id' => 'required|exists:models,id',
            'year_from' => 'required|integer|min:1900|max:' . date('Y'),
            'year_to' => 'required|integer|min:1900|max:' . date('Y'),
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100'
        ]);

        ServiceVehiclePrice::create($request->all());

        return redirect()->back()->with('success', 'Service price added successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'sub_service_id' => 'required|exists:sub_services,id',
            'make_id' => 'required|exists:makes,id',
            'model_id' => 'required|exists:models,id',
            'year_from' => 'required|integer|min:1900|max:' . date('Y'),
            'year_to' => 'required|integer|min:1900|max:' . date('Y'),
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100'
        ]);

        $servicePrice = ServiceVehiclePrice::findOrFail($id);
        $servicePrice->update($request->all());

        return redirect()->back()->with('success', 'Service price updated successfully');
    }

    public function destroy($id)
    {
        $servicePrice = ServiceVehiclePrice::findOrFail($id);
        $servicePrice->delete();

        return redirect()->back()->with('success', 'Service price deleted successfully');
    }
}
