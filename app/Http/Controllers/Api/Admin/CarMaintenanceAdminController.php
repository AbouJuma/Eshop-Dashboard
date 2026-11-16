<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarMaintenance;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Mobile admin equivalent of CarMaintenanceController (web), scoped to the admin's business.
 */
class CarMaintenanceAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = CarMaintenance::with(['car', 'creator'])->where('business_id', $request->user()->business_id);

        if ($request->filled('search')) {
            $search = '%'.$request->search.'%';
            $query->whereHas('car', fn ($q) => $q->where('car_plate_number', 'like', $search)
                ->orWhere('owner_name', 'like', $search));
        }
        if ($request->filled('car_id')) {
            $query->where('car_id', $request->car_id);
        }
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('service_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('service_date', '<=', $request->date_to);
        }

        $records = $query->orderBy('service_date', 'desc')->orderBy('id', 'desc')
            ->paginate(min(max((int) $request->input('per_page', 20), 1), 100));
        $records->getCollection()->transform(fn ($record) => $this->recordPayload($record));

        return response()->json(['success' => true] + $records->toArray());
    }

    public function cars(Request $request)
    {
        $query = Car::where('business_id', $request->user()->business_id)->withCount('maintenances');

        if ($request->filled('search')) {
            $search = '%'.$request->search.'%';
            $query->where(fn ($q) => $q->where('car_plate_number', 'like', $search)
                ->orWhere('owner_name', 'like', $search)
                ->orWhere('owner_phone', 'like', $search));
        }

        $cars = $query->orderBy('car_plate_number')->get()->map(fn ($car) => $this->carPayload($car));

        return response()->json(['success' => true, 'data' => $cars]);
    }

    public function show(Request $request, $id)
    {
        return response()->json(['success' => true, 'data' => $this->recordPayload($this->findRecord($request, $id))]);
    }

    public function store(Request $request)
    {
        $businessId = $request->user()->business_id;
        $isExisting = $request->filled('car_id');

        $rules = $this->serviceRules($request);
        if ($isExisting) {
            $rules['car_id'] = 'required|integer';
        } else {
            $rules += [
                'car_plate_number' => 'required|string|max:50',
                'car_chassis_number' => 'nullable|string|max:100',
                'car_brand' => 'nullable|string|max:100',
                'car_model' => 'nullable|string|max:100',
                'car_year' => 'nullable|string|max:4',
                'owner_name' => 'required|string|max:150',
                'owner_phone' => 'nullable|string|max:30',
                'owner_email' => 'nullable|email|max:150',
            ];
        }
        $request->validate($rules);

        if ($isExisting) {
            $carId = Car::where('business_id', $businessId)->findOrFail($request->car_id)->id;
        } else {
            $plate = strtoupper(trim($request->car_plate_number));
            if (Car::where('business_id', $businessId)->where('car_plate_number', $plate)->exists()) {
                throw ValidationException::withMessages([
                    'car_plate_number' => 'A vehicle with plate number "'.$plate.'" already exists. Select it as an existing vehicle instead.',
                ]);
            }

            $chassis = $request->filled('car_chassis_number') ? strtoupper(trim($request->car_chassis_number)) : null;
            if ($chassis && Car::where('business_id', $businessId)->where('car_chassis_number', $chassis)->exists()) {
                throw ValidationException::withMessages([
                    'car_chassis_number' => 'A vehicle with chassis number "'.$chassis.'" already exists.',
                ]);
            }

            $carId = Car::create([
                'business_id' => $businessId,
                'car_plate_number' => $plate,
                'car_chassis_number' => $chassis,
                'car_brand' => $request->car_brand,
                'car_model' => $request->car_model,
                'car_year' => $request->car_year,
                'owner_name' => $request->owner_name,
                'owner_phone' => $request->owner_phone,
                'owner_email' => $request->owner_email,
            ])->id;
        }

        $record = CarMaintenance::create(['business_id' => $businessId, 'car_id' => $carId, 'created_by' => $request->user()->id] + $this->serviceData($request));

        return response()->json([
            'success' => true,
            'message' => 'Service history record added successfully',
            'data' => $this->recordPayload($record->load('car')),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = $this->findRecord($request, $id);
        $request->validate($this->serviceRules($request));

        $record->update($this->serviceData($request));

        return response()->json([
            'success' => true,
            'message' => 'Service history record updated successfully',
            'data' => $this->recordPayload($record->fresh('car')),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->findRecord($request, $id)->delete();

        return response()->json(['success' => true, 'message' => 'Service history record deleted successfully']);
    }

    private function findRecord(Request $request, $id): CarMaintenance
    {
        return CarMaintenance::with(['car', 'creator'])->where('business_id', $request->user()->business_id)->findOrFail($id);
    }

    private function serviceRules(Request $request): array
    {
        return CarMaintenance::serviceRules($request); // mileage required for oil changes only
    }

    private function serviceData(Request $request): array
    {
        return CarMaintenance::serviceData($request);
    }

    private function recordPayload(CarMaintenance $record): array
    {
        return [
            'id' => $record->id,
            'service_date' => optional($record->service_date)->format('Y-m-d'),
            'service_type' => $record->service_type,
            'serviced_kilometer' => $record->serviced_kilometer,
            'next_service_kilometer' => $record->next_service_kilometer,
            'next_service_date' => optional($record->next_service_date)->format('Y-m-d'),
            'type_label' => $record->type_label,
            'receipt_number' => $record->receipt_number,
            'recorded_by' => $record->creator ? trim($record->creator->first_name.' '.$record->creator->last_name) : null,
            // signed link (2 h) to the printable next-service card / receipt, opened in the phone's browser
            'print_url' => \App\Http\Controllers\CarMaintenanceController::sharedPrintUrl($record),
            'default_document' => $record->defaultDocument(),
            'details' => $record->details,
            'cost' => (float) $record->cost,
            'car' => $record->car ? $this->carPayload($record->car) : null,
        ];
    }

    private function carPayload(Car $car): array
    {
        return [
            'id' => $car->id,
            'car_plate_number' => $car->car_plate_number,
            'car_chassis_number' => $car->car_chassis_number,
            'car_brand' => $car->car_brand,
            'car_model' => $car->car_model,
            'car_year' => $car->car_year,
            'owner_name' => $car->owner_name,
            'owner_phone' => $car->owner_phone,
            'owner_email' => $car->owner_email,
            'maintenances_count' => $car->maintenances_count,
        ];
    }
}
