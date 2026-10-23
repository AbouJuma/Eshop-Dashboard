<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestNotifier;
use Illuminate\Http\Request;

/**
 * Customer side of on-demand roadside service (request a mechanic to my location).
 */
class ServiceRequestController extends Controller
{
    public function types()
    {
        return response()->json([
            'success' => true,
            'data' => collect(ServiceRequest::SERVICE_TYPES)
                ->map(fn ($label, $key) => ['key' => $key, 'label' => $label])->values(),
        ]);
    }

    public function index(Request $request)
    {
        $requests = ServiceRequest::with(['client', 'mechanic'])
            ->where('user_id', $request->user()->id)
            ->latest()->limit(50)->get()
            ->map->toApiArray();

        return response()->json(['success' => true, 'data' => $requests]);
    }

    /** The customer's running request, if any (used to resume tracking). */
    public function active(Request $request)
    {
        $active = ServiceRequest::with(['client', 'mechanic'])
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ServiceRequest::ACTIVE_STATUSES)
            ->latest()->first();

        return response()->json(['success' => true, 'data' => $active ? $active->toApiArray() : null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_type' => 'required|in:'.implode(',', array_keys(ServiceRequest::SERVICE_TYPES)),
            'description' => 'nullable|string|max:2000',
            'vehicle' => 'nullable|string|max:191',
            'contact_phone' => 'nullable|string|max:30',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:191',
        ]);

        $userId = $request->user()->id;

        $existing = ServiceRequest::where('user_id', $userId)
            ->whereIn('status', ServiceRequest::ACTIVE_STATUSES)->latest()->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active request. Cancel it before making a new one.',
                'data' => $existing->load(['client', 'mechanic'])->toApiArray(),
            ], 409);
        }

        $serviceRequest = ServiceRequest::create([
            'reference' => ServiceRequest::generateReference(),
            'user_id' => $userId,
            'service_type' => $data['service_type'],
            'description' => $data['description'] ?? null,
            'vehicle' => $data['vehicle'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'customer_lat' => $data['latitude'],
            'customer_lng' => $data['longitude'],
            'address' => $data['address'] ?? null,
            'status' => 'pending',
        ]);

        ServiceRequestNotifier::newRequest($serviceRequest);

        return response()->json([
            'success' => true,
            'message' => 'Request sent. We are finding a mechanic for you.',
            'data' => $serviceRequest->load(['client', 'mechanic'])->toApiArray(),
        ], 201);
    }

    public function show(Request $request, $id)
    {
        return response()->json(['success' => true, 'data' => $this->find($request, $id)->toApiArray()]);
    }

    public function cancel(Request $request, $id)
    {
        $request->validate(['reason' => 'nullable|string|max:1000']);
        $serviceRequest = $this->find($request, $id);

        if (! in_array($serviceRequest->status, ['pending', 'accepted', 'on_the_way'])) {
            return response()->json(['success' => false, 'message' => 'This request can no longer be cancelled'], 422);
        }

        $serviceRequest->update([
            'status' => 'cancelled',
            'cancelled_by' => 'customer',
            'cancellation_reason' => $request->reason ?: 'Cancelled by customer',
        ]);

        ServiceRequestNotifier::customerCancelled($serviceRequest);

        return response()->json(['success' => true, 'message' => 'Request cancelled', 'data' => $serviceRequest->toApiArray()]);
    }

    private function find(Request $request, $id): ServiceRequest
    {
        return ServiceRequest::with(['client', 'mechanic'])
            ->where('user_id', $request->user()->id)->findOrFail($id);
    }
}
