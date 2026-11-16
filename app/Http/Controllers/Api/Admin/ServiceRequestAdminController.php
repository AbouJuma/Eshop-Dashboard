<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\MobileAdmin;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCall;
use App\Services\Attachments;
use App\Services\ServiceRequestCalls;
use App\Services\ServiceRequestChat;
use App\Services\ServiceRequestNotifier;
use Illuminate\Http\Request;

/**
 * Admin / mechanic side of on-demand roadside service requests.
 */
class ServiceRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->visible($request)->latest();

        $status = $request->input('status');
        if ($status === 'active') {
            $query->whereIn('status', ServiceRequest::ACTIVE_STATUSES);
        } elseif ($status) {
            $query->where('status', $status);
        }
        if ($request->boolean('mine')) {
            $query->where('assigned_to', $request->user()->id);
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
        // only people who can take (or supervise) jobs are alerted about new requests
        $pending = MobileAdmin::can($request->user(), 'roadside.accept', 'roadside.view_all')
            ? ServiceRequest::with(['client', 'mechanic'])->where('status', 'pending')->latest()->limit(20)->get()
            : collect();

        return response()->json([
            'success' => true,
            'pending_count' => $pending->count(),
            'pending' => $pending->map->toApiArray(),
            'my_active_count' => ServiceRequest::where('assigned_to', $request->user()->id)
                ->whereIn('status', ['accepted', 'on_the_way', 'arrived', 'in_progress'])->count(),
            // a customer is video-calling me (backup for when the push does not arrive)
            'incoming_call' => $this->incomingCallForMe($request),
        ]);
    }

    private function incomingCallForMe(Request $request): ?array
    {
        $call = ServiceRequestCall::where('status', 'ringing')
            ->where('caller_type', 'customer')
            ->where('created_at', '>=', now()->subSeconds(ServiceRequestCall::RING_SECONDS))
            ->whereHas('serviceRequest', fn ($q) => $q->where('assigned_to', $request->user()->id))
            ->latest('id')->first();

        return $call ? [
            'id' => $call->id,
            'service_request_id' => $call->service_request_id,
            'caller_name' => $call->callerName(),
        ] : null;
    }

    public function show(Request $request, $id)
    {
        $serviceRequest = $this->find($request, $id);

        return response()->json(['success' => true, 'data' => $serviceRequest->toApiArray() + [
            'unread_messages' => ServiceRequestChat::unreadFor($serviceRequest, 'admin'),
            'incoming_call' => $serviceRequest->assigned_to == $request->user()->id
                ? ServiceRequestCalls::incoming($serviceRequest, 'admin') : null,
            'call_ice_servers' => ServiceRequestCall::iceServers(),
        ]]);
    }

    public function accept(Request $request, $id)
    {
        // atomic: only one admin can take a pending request
        $taken = ServiceRequest::where('id', $id)->where('status', 'pending')->update([
            'status' => 'accepted',
            'assigned_to' => $request->user()->id,
            'accepted_at' => now(),
        ]);

        $serviceRequest = $this->find($request, $id);

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
        $serviceRequest = $this->find($request, $id);

        if (! MobileAdmin::can($request->user(), 'roadside.manage')) {
            return response()->json(['success' => false, 'message' => 'Your role does not allow declining requests'], 422);
        }
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
        $serviceRequest = $this->find($request, $id);
        if ($denied = $this->denyIfNotMine($request, $serviceRequest)) {
            return $denied;
        }
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
        $serviceRequest = $this->find($request, $id);
        if ($denied = $this->denyIfNotMine($request, $serviceRequest)) {
            return $denied;
        }

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
        $serviceRequest = $this->find($request, $id);

        if (! in_array($serviceRequest->status, ['accepted', 'on_the_way', 'arrived', 'in_progress'])) {
            return response()->json(['success' => false, 'message' => 'This request is not active', 'status' => $serviceRequest->status], 422);
        }
        if ($serviceRequest->assigned_to && $serviceRequest->assigned_to != $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'This job is assigned to another mechanic'], 409);
        }

        $serviceRequest->update([
            'mechanic_lat' => $request->latitude,
            'mechanic_lng' => $request->longitude,
            'mechanic_location_at' => now(),
        ]);

        return response()->json(['success' => true, 'status' => $serviceRequest->status]);
    }

    public function messages(Request $request, $id)
    {
        return response()->json(ServiceRequestChat::list($this->find($request, $id), 'admin', $request));
    }

    public function sendMessage(Request $request, $id)
    {
        $serviceRequest = $this->find($request, $id);
        if ($denied = $this->denyIfNotMine($request, $serviceRequest)) {
            return $denied;
        }
        [$payload, $code] = ServiceRequestChat::send($serviceRequest, 'admin', $request->user()->id, $request);

        return response()->json($payload, $code);
    }

    // ---- video call with the customer (the assigned mechanic, or a supervisor) ----

    public function startCall(Request $request, $id)
    {
        $serviceRequest = $this->find($request, $id);
        if ($denied = $this->denyIfNotMine($request, $serviceRequest)) {
            return $denied;
        }

        return $this->reply(ServiceRequestCalls::start($serviceRequest, 'admin', $request->user()->id, $request));
    }

    public function showCall(Request $request, $id, $call)
    {
        return $this->reply(ServiceRequestCalls::show($this->find($request, $id), 'admin', $call));
    }

    public function answerCall(Request $request, $id, $call)
    {
        $serviceRequest = $this->find($request, $id);
        if ($denied = $this->denyIfNotMine($request, $serviceRequest)) {
            return $denied;
        }

        return $this->reply(ServiceRequestCalls::answer($serviceRequest, 'admin', $request->user()->id, $call, $request));
    }

    public function endCall(Request $request, $id, $call)
    {
        return $this->reply(ServiceRequestCalls::end($this->find($request, $id), 'admin', $call, $request->boolean('decline')));
    }

    // ---- reference photos / videos (anyone who can see the job may look; those working it may add) ----

    public function attachments(Request $request, $id)
    {
        return $this->reply(Attachments::list('service_request', $this->find($request, $id)->id, $request->user()->id, true));
    }

    public function uploadAttachment(Request $request, $id)
    {
        $serviceRequest = $this->find($request, $id);
        if ($denied = $this->denyIfNotMine($request, $serviceRequest)) {
            return $denied;
        }

        return $this->reply(Attachments::upload('service_request', $serviceRequest->id, $request->user()->id, true, $request));
    }

    public function deleteAttachment(Request $request, $id, $attachment)
    {
        $serviceRequest = $this->find($request, $id);
        if ($denied = $this->denyIfNotMine($request, $serviceRequest)) {
            return $denied;
        }

        return $this->reply(Attachments::delete('service_request', $serviceRequest->id, $attachment, $request->user()->id, true));
    }

    private function reply(array $result)
    {
        [$payload, $code] = $result;

        return response()->json($payload, $code);
    }

    /**
     * Requests this user may see: with roadside.view_all everything; otherwise requests still waiting
     * to be accepted (when they may accept, so everyone can take them) plus their own jobs.
     */
    private function visible(Request $request)
    {
        $query = ServiceRequest::with(['client', 'mechanic']);
        $user = $request->user();

        if (! MobileAdmin::can($user, 'roadside.view_all')) {
            $canAccept = MobileAdmin::can($user, 'roadside.accept');
            $query->where(fn ($q) => $q->where('assigned_to', $user->id)
                ->when($canAccept, fn ($q) => $q->orWhere('status', 'pending')));
        }

        return $query;
    }

    private function find(Request $request, $id): ServiceRequest
    {
        return $this->visible($request)->findOrFail($id);
    }

    /** Only supervisors (roadside.manage) work on other people's jobs. 409, not 403: the app treats 403 as signed out. */
    private function denyIfNotMine(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->assigned_to != $request->user()->id && ! MobileAdmin::can($request->user(), 'roadside.manage')) {
            return response()->json(['success' => false, 'message' => 'This job is assigned to another mechanic'], 409);
        }

        return null;
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
