<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestCall;
use Illuminate\Http\Request;

/**
 * Video calls between the customer ("customer") and the company / mechanic ("admin") on a roadside job.
 * Shared by the customer and admin APIs; every method returns [payload, http status].
 */
class ServiceRequestCalls
{
    /** Calls are possible from the moment a mechanic accepts until the job is finished (same as chat). */
    const OPEN_STATUSES = ServiceRequestChat::OPEN_STATUSES;

    public static function start(ServiceRequest $serviceRequest, string $side, int $userId, Request $request): array
    {
        $request->validate([
            'offer' => 'required|array',
            'offer.type' => 'required|in:offer',
            'offer.sdp' => 'required|string|max:60000',
        ]);

        if (! in_array($serviceRequest->status, self::OPEN_STATUSES)) {
            return [['success' => false, 'message' => 'Video calls are available once a mechanic has accepted the job and until it is finished.'], 422];
        }
        if ($side === 'customer' && ! $serviceRequest->assigned_to) {
            return [['success' => false, 'message' => 'No mechanic is assigned to this job yet.'], 422];
        }

        // one call at a time per job: a new call replaces an old one that is still open
        ServiceRequestCall::where('service_request_id', $serviceRequest->id)
            ->whereIn('status', ['ringing', 'active'])
            ->update(['status' => 'ended', 'ended_at' => now(), 'ended_by' => $side]);

        $call = ServiceRequestCall::create([
            'service_request_id' => $serviceRequest->id,
            'caller_type' => $side,
            'caller_id' => $userId,
            'status' => 'ringing',
            'offer' => json_encode($request->input('offer')),
        ]);

        self::ring($serviceRequest, $call);

        return [['success' => true, 'data' => $call->toApiArray($side)], 201];
    }

    public static function show(ServiceRequest $serviceRequest, string $side, $callId): array
    {
        $call = self::find($serviceRequest, $callId)->expireIfUnanswered();

        return [['success' => true, 'data' => $call->toApiArray($side)], 200];
    }

    /** The open call ringing for this side, if any (the apps check it while a job screen is open). */
    public static function incoming(ServiceRequest $serviceRequest, string $side): ?array
    {
        $call = ServiceRequestCall::where('service_request_id', $serviceRequest->id)
            ->where('status', 'ringing')
            ->where('caller_type', '!=', $side)
            ->latest('id')->first();

        if (! $call || ! $call->expireIfUnanswered()->isOpen()) {
            return null;
        }

        return ['id' => $call->id, 'caller_name' => $call->callerName()];
    }

    public static function answer(ServiceRequest $serviceRequest, string $side, int $userId, $callId, Request $request): array
    {
        $request->validate([
            'answer' => 'required|array',
            'answer.type' => 'required|in:answer',
            'answer.sdp' => 'required|string|max:60000',
        ]);
        $call = self::find($serviceRequest, $callId)->expireIfUnanswered();

        if ($call->caller_type === $side) {
            return [['success' => false, 'message' => 'You cannot answer your own call.'], 422];
        }
        if ($call->status !== 'ringing') {
            return [['success' => false, 'message' => self::closedMessage($call), 'data' => $call->toApiArray($side)], 409];
        }

        $call->update([
            'status' => 'active',
            'answer' => json_encode($request->input('answer')),
            'answered_at' => now(),
            'answered_by' => $userId,
        ]);

        return [['success' => true, 'data' => $call->toApiArray($side)], 200];
    }

    /** Hang up (either side) or decline (the side being called, while it rings). */
    public static function end(ServiceRequest $serviceRequest, string $side, $callId, bool $decline = false): array
    {
        $call = self::find($serviceRequest, $callId)->expireIfUnanswered();

        if ($call->isOpen()) {
            $wasRinging = $call->status === 'ringing';
            $isCaller = $call->caller_type === $side;
            $status = $wasRinging && $decline && ! $isCaller ? 'declined' : 'ended';
            $call->update(['status' => $status, 'ended_at' => now(), 'ended_by' => $side]);
            // the caller gave up before anyone answered: stop the other phone ringing
            // (the caller's own app sees "declined" on its next check, no push needed)
            if ($wasRinging && $isCaller) {
                self::cancelRinging($serviceRequest, $call);
            }
        }

        return [['success' => true, 'data' => $call->toApiArray($side)], 200];
    }

    private static function find(ServiceRequest $serviceRequest, $callId): ServiceRequestCall
    {
        return ServiceRequestCall::where('service_request_id', $serviceRequest->id)->findOrFail($callId);
    }

    private static function closedMessage(ServiceRequestCall $call): string
    {
        switch ($call->status) {
            case 'missed':
                return 'The call was not answered in time.';
            case 'active':
                return 'The call was already answered.';
            default:
                return 'The caller hung up.';
        }
    }

    /** Who should ring: the customer when the company calls, the assigned mechanic when the customer calls. */
    private static function recipient(ServiceRequest $serviceRequest, ServiceRequestCall $call): ?int
    {
        return $call->caller_type === 'customer' ? $serviceRequest->assigned_to : $serviceRequest->user_id;
    }

    private static function ring(ServiceRequest $serviceRequest, ServiceRequestCall $call): void
    {
        $recipientId = self::recipient($serviceRequest, $call);
        if (! $recipientId) {
            return;
        }
        $caller = $call->callerName();
        $data = [
            'type' => 'video_call',
            'call_id' => $call->id,
            'service_request_id' => $serviceRequest->id,
            'audience' => $call->caller_type === 'customer' ? 'admin' : 'customer',
            'caller_name' => $caller,
            'reference' => $serviceRequest->reference,
            'sent_at' => now()->timestamp,
        ];

        // data-only: the app shows its own full-screen "incoming video call" with a ringtone
        dispatch(fn () => NotificationService::sendFCMNotification(
            $recipientId, 'Incoming video call', $caller.' · '.$serviceRequest->reference, $data, true
        ))->afterResponse();
    }

    /** Tell the other phone to stop ringing (call declined or the caller hung up first). */
    private static function cancelRinging(ServiceRequest $serviceRequest, ServiceRequestCall $call): void
    {
        $recipientId = self::recipient($serviceRequest, $call);
        if (! $recipientId) {
            return;
        }
        $data = [
            'type' => 'video_call_ended',
            'call_id' => $call->id,
            'service_request_id' => $serviceRequest->id,
            'audience' => $call->caller_type === 'customer' ? 'admin' : 'customer',
            'caller_name' => $call->callerName(),
            'status' => $call->status,
        ];

        dispatch(fn () => NotificationService::sendFCMNotification($recipientId, 'Call ended', '', $data, true))->afterResponse();
    }
}
