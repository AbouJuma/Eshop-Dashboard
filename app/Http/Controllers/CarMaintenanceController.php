<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarMaintenance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CarMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        
        // Query for service history logs
        $query = CarMaintenance::with('car')->where('business_id', $business_id);

        if ($request->filled('car_plate_number')) {
            $query->whereHas('car', function ($q) use ($request) {
                $q->where('car_plate_number', 'like', '%' . $request->car_plate_number . '%');
            });
        }

        if ($request->filled('owner_name')) {
            $query->whereHas('car', function ($q) use ($request) {
                $q->where('owner_name', 'like', '%' . $request->owner_name . '%');
            });
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('service_date', [$request->start_date, $request->end_date]);
        }

        $maintenances = $query->orderBy('service_date', 'desc')->get();

        // Fetch registered vehicles for the second tab
        $cars = Car::where('business_id', $business_id)
            ->withCount('maintenances')
            ->orderBy('car_plate_number', 'asc')
            ->get();

        return view('car_maintenance.index', compact('maintenances', 'cars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        
        // If a specific car ID is selected, get it
        $selected_car = null;
        if ($request->filled('car_id')) {
            $selected_car = Car::where('business_id', $business_id)->findOrFail($request->car_id);
        }

        // Get all registered cars to choose from
        $cars = Car::where('business_id', $business_id)->orderBy('car_plate_number', 'asc')->get();

        return view('car_maintenance.create', compact('cars', 'selected_car'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');

        // Dynamic validation
        $rules = [
            'service_type' => 'required|string|in:normal,spares,both',
            'service_date' => 'required|date',
            'details' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
        ];

        // Milages are only required if it's not "spares only" service type
        if ($request->input('service_type') !== 'spares') {
            $rules['serviced_kilometer'] = 'required|integer|min:0';
            $rules['next_service_kilometer'] = 'required|integer|min:0';
        } else {
            $rules['serviced_kilometer'] = 'nullable|integer|min:0';
            $rules['next_service_kilometer'] = 'nullable|integer|min:0';
        }

        // Determine if using existing car or new car
        $is_existing = $request->input('vehicle_selection_type') === 'existing' || $request->filled('car_id');

        if ($is_existing) {
            $rules['car_id'] = 'required|integer';
        } else {
            $rules['car_plate_number'] = 'required|string|max:50';
            $rules['car_chassis_number'] = 'nullable|string|max:100';
            $rules['car_brand'] = 'nullable|string|max:100';
            $rules['car_model'] = 'nullable|string|max:100';
            $rules['car_year'] = 'nullable|string|max:4';
            $rules['owner_name'] = 'required|string|max:150';
            $rules['owner_phone'] = 'nullable|string|max:30';
            $rules['owner_email'] = 'nullable|email|max:150';
        }

        $request->validate($rules);

        $car_id = null;

        if ($is_existing) {
            $car_id = $request->input('car_id');
            // Verify ownership
            Car::where('business_id', $business_id)->findOrFail($car_id);
        } else {
            // Check if plate number already exists for this business
            $clean_plate = strtoupper(trim($request->input('car_plate_number')));
            $existing_car = Car::where('business_id', $business_id)
                ->where('car_plate_number', $clean_plate)
                ->first();

            if ($existing_car) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['car_plate_number' => 'A vehicle with plate number "' . $clean_plate . '" already exists. Please select "Existing Vehicle" option instead.']);
            }

            // Check if chassis number already exists for this business (if provided)
            $clean_chassis = null;
            if ($request->filled('car_chassis_number')) {
                $clean_chassis = strtoupper(trim($request->input('car_chassis_number')));
                $existing_chassis = Car::where('business_id', $business_id)
                    ->where('car_chassis_number', $clean_chassis)
                    ->first();

                if ($existing_chassis) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['car_chassis_number' => 'A vehicle with chassis number "' . $clean_chassis . '" already exists.']);
                }
            }

            // Create new Car
            $car = Car::create([
                'business_id' => $business_id,
                'car_plate_number' => $clean_plate,
                'car_chassis_number' => $clean_chassis,
                'car_brand' => $request->input('car_brand'),
                'car_model' => $request->input('car_model'),
                'car_year' => $request->input('car_year'),
                'owner_name' => $request->input('owner_name'),
                'owner_phone' => $request->input('owner_phone'),
                'owner_email' => $request->input('owner_email')
            ]);
            $car_id = $car->id;
        }

        // Save Maintenance record
        CarMaintenance::create([
            'business_id' => $business_id,
            'car_id' => $car_id,
            'service_date' => $request->input('service_date'),
            'serviced_kilometer' => $request->input('service_type') !== 'spares' ? $request->input('serviced_kilometer') : null,
            'next_service_kilometer' => $request->input('service_type') !== 'spares' ? $request->input('next_service_kilometer') : null,
            'service_type' => $request->input('service_type'),
            'details' => $request->input('details'),
            'cost' => $request->input('cost') ?: 0
        ]);

        return redirect()->route('car-maintenance.index')
            ->with('status', [
                'success' => true,
                'msg' => 'Service history record added successfully!'
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('business.id');
        $maintenance = CarMaintenance::where('business_id', $business_id)->with('car')->findOrFail($id);

        return view('car_maintenance.show', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $maintenance = CarMaintenance::where('business_id', $business_id)->with('car')->findOrFail($id);

        return view('car_maintenance.edit', compact('maintenance'));
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
        $business_id = request()->session()->get('business.id');
        $maintenance = CarMaintenance::where('business_id', $business_id)->findOrFail($id);

        // Validation for editing service info
        $rules = [
            'service_type' => 'required|string|in:normal,spares,both',
            'service_date' => 'required|date',
            'details' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
        ];

        // Milages are only required if it's not "spares only" service type
        if ($request->input('service_type') !== 'spares') {
            $rules['serviced_kilometer'] = 'required|integer|min:0';
            $rules['next_service_kilometer'] = 'required|integer|min:0';
        } else {
            $rules['serviced_kilometer'] = 'nullable|integer|min:0';
            $rules['next_service_kilometer'] = 'nullable|integer|min:0';
        }

        $request->validate($rules);

        $maintenance->update([
            'service_date' => $request->input('service_date'),
            'serviced_kilometer' => $request->input('service_type') !== 'spares' ? $request->input('serviced_kilometer') : null,
            'next_service_kilometer' => $request->input('service_type') !== 'spares' ? $request->input('next_service_kilometer') : null,
            'service_type' => $request->input('service_type'),
            'details' => $request->input('details'),
            'cost' => $request->input('cost') ?: 0
        ]);

        return redirect()->route('car-maintenance.index')
            ->with('status', [
                'success' => true,
                'msg' => 'Service history record updated successfully!'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('business.id');
        $maintenance = CarMaintenance::where('business_id', $business_id)->findOrFail($id);
        
        $maintenance->delete();

        return redirect()->route('car-maintenance.index')
            ->with('status', [
                'success' => true,
                'msg' => 'Service history record deleted successfully!'
            ]);
    }

    /**
     * Export report to PDF.
     */
    public function exportPdf(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        $query = CarMaintenance::with('car')->where('business_id', $business_id);

        if ($request->filled('car_plate_number')) {
            $query->whereHas('car', function ($q) use ($request) {
                $q->where('car_plate_number', 'like', '%' . $request->car_plate_number . '%');
            });
        }
        if ($request->filled('owner_name')) {
            $query->whereHas('car', function ($q) use ($request) {
                $q->where('owner_name', 'like', '%' . $request->owner_name . '%');
            });
        }
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('service_date', [$request->start_date, $request->end_date]);
        }

        $records = $query->orderBy('service_date', 'desc')->get();

        $pdf = Pdf::loadView('car_maintenance.pdf', compact('records', 'request'));
        return $pdf->download('car_maintenance_report.pdf');
    }

    /**
     * Show printable sticker/label.
     */
    public function printLabel($id)
    {
        $business_id = request()->session()->get('business.id');
        $record = CarMaintenance::with('car')->where('business_id', $business_id)->findOrFail($id);

        return view('car_maintenance.print_label', compact('record'));
    }
}
