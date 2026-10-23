<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestMessage;
use Illuminate\Http\Request;

/**
 * Customer <-> mechanic chat on a roadside request, shared by the customer and admin APIs.
 */
class ServiceRequestChat
{
    /** Chat is open from the moment a mechanic accepts until the job is finished. */
    const OPEN_STATUSES = ['accepted', 'on_the_way', 'arrived', 'in_progress'];

    /**
     * Messages after `after_id` (for polling). Marks the other side's messages as read.
     */
    public static function list(ServiceRequest $serviceRequest, string $viewer, Request $request): array
    {
        $afterId = (int) $request->input('after_id', 0);

        $messages = ServiceRequestMessage::where('service_request_id', $serviceRequest->id)
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->limit(200)
            ->get();

        ServiceRequestMessage::where('service_request_id', $serviceRequest->id)
            ->where('sender_type', '!=', $viewer)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return [
            'success' => true,
            'chat_open' => in_array($serviceRequest->status, self::OPEN_STATUSES),
            'status' => $serviceRequest->status,
            'data' => $messages->map->toApiArray()->values(),
        ];
    }

    /**
     * @return array [payload, http status]
     */
    public static function send(ServiceRequest $serviceRequest, string $senderType, int $senderId, Request $request): array
    {
        $request->validate(['body' => 'required|string|max:2000']);

        if (! in_array($serviceRequest->status, self::OPEN_STATUSES)) {
            return [['success' => false, 'message' => 'Chat is only available while the job is active.'], 422];
        }

        $message = ServiceRequestMessage::create([
            'service_request_id' => $serviceRequest->id,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'body' => trim($request->body),
        ]);

        self::notify($serviceRequest, $senderType, $message->body);

        return [['success' => true, 'data' => $message->toApiArray()], 201];
    }

    public static function unreadFor(ServiceRequest $serviceRequest, string $viewer): int
    {
        return ServiceRequestMessage::where('service_request_id', $serviceRequest->id)
            ->where('sender_type', '!=', $viewer)
            ->whereNull('read_at')
            ->count();
    }

    private static function notify(ServiceRequest $serviceRequest, string $senderType, string $body): void
    {
        $recipientId = $senderType === 'customer' ? $serviceRequest->assigned_to : $serviceRequest->user_id;
        if (! $recipientId) {
            return;
        }

        $title = $senderType === 'customer'
            ? 'Message from '.(optional($serviceRequest->client)->username ?? 'customer')
            : 'Message from your mechanic';
        $data = [
            'type' => 'service_request_chat',
            'service_request_id' => $serviceRequest->id,
            'audience' => $senderType === 'customer' ? 'admin' : 'customer',
        ];
        $preview = mb_strimwidth($body, 0, 120, '…');

        dispatch(fn () => NotificationService::sendFCMNotification($recipientId, $title, $preview, $data))->afterResponse();
    }
}
