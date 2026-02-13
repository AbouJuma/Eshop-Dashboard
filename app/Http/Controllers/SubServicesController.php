<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubService;
use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\Make;
use App\Models\VehicleModel;

class SubServicesController extends Controller
{
    public function index(Request $request)
    {
        $query = SubService::with('service', 'serviceVehiclePrices');
        
        // Filter by service if provided
        if ($request->has('service_id') && $request->service_id) {
            $query->where('service_id', $request->service_id);
        }
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            });
        }
        
        $subServices = $query->latest()->paginate(20);
        $services = Service::where('status', 1)->get();
        
        return view('sub-services.index', compact('subServices', 'services'));
    }

    public function create()
    {
        $services = Service::where('status', 1)->get();
        $makes = Make::all();
        $models = VehicleModel::all();
        
        return view('sub-services.create', compact('services', 'makes', 'models'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_id' => 'required|exists:services,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discount' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'vehicle_prices' => 'required|array|min:1',
            'vehicle_prices.*.make_id' => 'required|exists:makes,id',
            'vehicle_prices.*.model_id' => 'required|exists:vehicle_models,id',
            'vehicle_prices.*.year_from' => 'required|integer|min:1900|max:' . date('Y'),
            'vehicle_prices.*.year_to' => 'required|integer|min:1900|max:' . date('Y'),
            'vehicle_prices.*.price' => 'required|numeric|min:0',
        ]);

        // Create sub-service
        $subService = new SubService();
        $subService->title = $request->title;
        $subService->description = $request->description;
        $subService->service_id = $request->service_id;
        $subService->discount = $request->discount;
        $subService->status = $request->status == 'active' ? 1 : 0;
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/sub_services'), $imageName);
            $subService->image = 'sub_services/' . $imageName;
        }
        
        $subService->save();
        
        // Create vehicle prices
        foreach ($request->vehicle_prices as $priceData) {
            ServiceVehiclePrice::create([
                'sub_service_id' => $subService->id,
                'make_id' => $priceData['make_id'],
                'model_id' => $priceData['model_id'],
                'year_from' => $priceData['year_from'],
                'year_to' => $priceData['year_to'],
                'price' => $priceData['price'],
                'discount' => $priceData['discount'] ?? null,
            ]);
        }
        
        return redirect()->route('sub-services.index')
            ->with('success', 'Sub-service created successfully!');
    }

    public function edit($id)
    {
        $subService = SubService::with('serviceVehiclePrices')->findOrFail($id);
        $services = Service::where('status', 1)->get();
        $makes = Make::all();
        $models = VehicleModel::all();
        
        return view('sub-services.edit', compact('subService', 'services', 'makes', 'models'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_id' => 'required|exists:services,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discount' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'vehicle_prices' => 'required|array|min:1',
            'vehicle_prices.*.make_id' => 'required|exists:makes,id',
            'vehicle_prices.*.model_id' => 'required|exists:vehicle_models,id',
            'vehicle_prices.*.year_from' => 'required|integer|min:1900|max:' . date('Y'),
            'vehicle_prices.*.year_to' => 'required|integer|min:1900|max:' . date('Y'),
            'vehicle_prices.*.price' => 'required|numeric|min:0',
        ]);

        $subService = SubService::findOrFail($id);
        $subService->title = $request->title;
        $subService->description = $request->description;
        $subService->service_id = $request->service_id;
        $subService->discount = $request->discount;
        $subService->status = $request->status == 'active' ? 1 : 0;
        
        if ($request->hasFile('image')) {
            // Delete old image
            if ($subService->image && file_exists(public_path('storage/' . $subService->image))) {
                unlink(public_path('storage/' . $subService->image));
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/sub_services'), $imageName);
            $subService->image = 'sub_services/' . $imageName;
        }
        
        $subService->save();
        
        // Delete old vehicle prices and create new ones
        $subService->serviceVehiclePrices()->delete();
        
        foreach ($request->vehicle_prices as $priceData) {
            ServiceVehiclePrice::create([
                'sub_service_id' => $subService->id,
                'make_id' => $priceData['make_id'],
                'model_id' => $priceData['model_id'],
                'year_from' => $priceData['year_from'],
                'year_to' => $priceData['year_to'],
                'price' => $priceData['price'],
                'discount' => $priceData['discount'] ?? null,
            ]);
        }
        
        return redirect()->route('sub-services.index')
            ->with('success', 'Sub-service updated successfully!');
    }

    public function destroy($id)
    {
        $subService = SubService::findOrFail($id);
        
        // Delete image if exists
        if ($subService->image && file_exists(public_path('storage/' . $subService->image))) {
            unlink(public_path('storage/' . $subService->image));
        }
        
        // Delete related vehicle prices
        $subService->serviceVehiclePrices()->delete();
        
        $subService->delete();
        
        return redirect()->route('sub-services.index')
            ->with('success', 'Sub-service deleted successfully!');
    }

    public function getModelsByMake($makeId)
    {
        $models = VehicleModel::where('make_id', $makeId)->get();
        return response()->json($models);
    }
}
