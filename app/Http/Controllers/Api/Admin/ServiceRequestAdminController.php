<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestNotifier;
use Illuminate\Http\Request;

/**
 * Admin / mechanic side of on-demand roadside service requests.
 */
class ServiceRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceRequest::with(['client', 'mechanic'])->latest();

        $status = $request->input('status');
        if ($status === 'active') {
            $query->whereIn('status', ServiceRequest::ACTIVE_STATUSES);
        } elseif ($status) {
            $query->where('status', $status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->paginate(min(max((int) $request->input('per_page', 20), 1), 100));
        $requests->getCollection()->transform(fn ($r) => $r->toApiArray());

        return response()->json(['success' => true] + $requests->toArray());
    }

    /**
     * Lightweight check the admin app calls every few seconds to raise the alarm for new requests.
     */
    public function poll(Request $request)
    {
        $pending = ServiceRequest::with(['client', 'mechanic'])->where('status', 'pending')->latest()->limit(20)->get();

        return response()->json([
            'success' => true,
            'pending_count' => $pending->count(),
            'pending' => $pending->map->toApiArray(),
            'my_active_count' => ServiceRequest::where('assigned_to', $request->user()->id)
                ->whereIn('status', ['accepted', 'on_the_way', 'arrived', 'in_progress'])->count(),
        ]);
    }

    public function show($id)
    {
        return response()->json(['success' => true, 'data' => $this->find($id)->toApiArray()]);
    }

    public function accept(Request $request, $id)
    {
        // atomic: only one admin can take a pending request
        $taken = ServiceRequest::where('id', $id)->where('status', 'pending')->update([
            'status' => 'accepted',
            'assigned_to' => $request->user()->id,
            'accepted_at' => now(),
        ]);

        $serviceRequest = $this->find($id);

        if (! $taken) {
            return response()->json([
                'success' => false,
                'message' => 'This request is no longer pending (it may have been taken by someone else).',
                'data' => $serviceRequest->toApiArray(),
            ], 409);
        }

        ServiceRequestNotifier::statusChanged($serviceRequest);

        return $this->respondWithRequest($serviceRequest, 'Request accepted');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:1000']);
        $serviceRequest = $this->find($id);

        if ($serviceRequest->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Only pending requests can be declined'], 422);
        }

        $serviceRequest->update(['status' => 'rejected', 'cancelled_by' => 'admin', 'cancellation_reason' => $request->reason]);
        ServiceRequestNotifier::statusChanged($serviceRequest);

        return $this->respondWithRequest($serviceRequest, 'Request declined');
    }

    /** Move an accepted job forward: on_the_way → arrived → in_progress → completed. */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);
        $serviceRequest = $this->find($id);
        $next = $request->status;

        if (! in_array($next, ServiceRequest::TRANSITIONS[$serviceRequest->status] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => "Cannot change status from {$serviceRequest->status} to {$next}",
            ], 422);
        }

        $changes = ['status' => $next];
        if ($next === 'on_the_way') {
            $changes['started_at'] = now();
        } elseif ($next === 'arrived') {
            $changes['arrived_at'] = now();
        } elseif ($next === 'completed') {
            $changes['completed_at'] = now();
            if ($request->filled('price')) {
                $changes['price'] = $request->price;
            }
        }
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $changes += [
                'mechanic_lat' => $request->latitude,
                'mechanic_lng' => $request->longitude,
                'mechanic_location_at' => now(),
            ];
        }
        // whoever moves the job forward becomes the mechanic if nobody was assigned
        if (! $serviceRequest->assigned_to) {
            $changes['assigned_to'] = $request->user()->id;
        }

        $serviceRequest->update($changes);
        ServiceRequestNotifier::statusChanged($serviceRequest->fresh(['client', 'mechanic']));

        return $this->respondWithRequest($serviceRequest, 'Status updated');
    }

    public function cancel(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:1000']);
        $serviceRequest = $this->find($id);

        if (! $serviceRequest->isActive()) {
            return response()->json(['success' => false, 'message' => 'This request is already closed'], 422);
        }

        $serviceRequest->update(['status' => 'cancelled', 'cancelled_by' => 'admin', 'cancellation_reason' => $request->reason]);
        ServiceRequestNotifier::statusChanged($serviceRequest);

        return $this->respondWithRequest($serviceRequest, 'Request cancelled');
    }

    /** Live GPS of the mechanic while on the job; the customer's map follows it. */
    public function location(Request $request, $id)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
        $serviceRequest = $this->find($id);

        if (! in_array($serviceRequest->status, ['accepted', 'on_the_way', 'arrived', 'in_progress'])) {
            return response()->json(['success' => false, 'message' => 'This request is not active', 'status' => $serviceRequest->status], 422);
        }
        if ($serviceRequest->assigned_to && $serviceRequest->assigned_to != $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'This job is assigned to another mechanic'], 403);
        }

        $serviceRequest->update([
            'mechanic_lat' => $request->latitude,
            'mechanic_lng' => $request->longitude,
            'mechanic_location_at' => now(),
        ]);

        return response()->json(['success' => true, 'status' => $serviceRequest->status]);
    }

    private function find($id): ServiceRequest
    {
        return ServiceRequest::with(['client', 'mechanic'])->findOrFail($id);
    }

    private function respondWithRequest(ServiceRequest $serviceRequest, string $message)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $serviceRequest->fresh(['client', 'mechanic'])->toApiArray(),
        ]);
    }
}
