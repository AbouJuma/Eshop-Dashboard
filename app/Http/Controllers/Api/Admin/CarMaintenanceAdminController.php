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
        $query = CarMaintenance::with('car')->where('business_id', $request->user()->business_id);

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
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('service_date', [$request->start_date, $request->end_date]);
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

        $record = CarMaintenance::create(['business_id' => $businessId, 'car_id' => $carId] + $this->serviceData($request));

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
        return CarMaintenance::with('car')->where('business_id', $request->user()->business_id)->findOrFail($id);
    }

    /** Mileage is only required when the service is not "spares only" (same rule as the web portal). */
    private function serviceRules(Request $request): array
    {
        $mileage = $request->input('service_type') !== 'spares' ? 'required|integer|min:0' : 'nullable|integer|min:0';

        return [
            'service_type' => 'required|string|in:normal,spares,both',
            'service_date' => 'required|date',
            'details' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'serviced_kilometer' => $mileage,
            'next_service_kilometer' => $mileage,
        ];
    }

    private function serviceData(Request $request): array
    {
        $isSpares = $request->service_type === 'spares';

        return [
            'service_date' => $request->service_date,
            'service_type' => $request->service_type,
            'serviced_kilometer' => $isSpares ? null : $request->serviced_kilometer,
            'next_service_kilometer' => $isSpares ? null : $request->next_service_kilometer,
            'details' => $request->details,
            'cost' => $request->cost ?: 0,
        ];
    }

    private function recordPayload(CarMaintenance $record): array
    {
        return [
            'id' => $record->id,
            'service_date' => optional($record->service_date)->format('Y-m-d'),
            'service_type' => $record->service_type,
            'serviced_kilometer' => $record->serviced_kilometer,
            'next_service_kilometer' => $record->next_service_kilometer,
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
