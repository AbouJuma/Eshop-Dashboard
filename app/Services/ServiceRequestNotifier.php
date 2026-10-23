<?php

namespace App\Services;

use App\Http\Middleware\MobileAdmin;
use App\Models\ServiceRequest;
use App\User;

/**
 * Push notifications for roadside service requests.
 * Sent after the HTTP response so the customer/admin is not kept waiting on FCM.
 */
class ServiceRequestNotifier
{
    /** Alert every admin signed in to the mobile admin panel. */
    public static function newRequest(ServiceRequest $serviceRequest): void
    {
        $title = '🚨 New roadside request';
        $body = (ServiceRequest::SERVICE_TYPES[$serviceRequest->service_type] ?? 'Service')
            .($serviceRequest->address ? ' at '.$serviceRequest->address : '')
            .' — tap to respond';
        $data = ['type' => 'service_request_alert', 'service_request_id' => $serviceRequest->id, 'audience' => 'admin'];

        dispatch(function () use ($title, $body, $data) {
            foreach (self::adminRecipients() as $admin) {
                NotificationService::sendFCMNotification($admin->id, $title, $body, $data, true);
            }
        })->afterResponse();
    }

    public static function statusChanged(ServiceRequest $serviceRequest): void
    {
        $message = ServiceRequest::CUSTOMER_MESSAGES[$serviceRequest->status] ?? null;
        if (! $message || ! $serviceRequest->user_id) {
            return;
        }

        [$title, $body] = $message;
        $body = str_replace(':ref', $serviceRequest->reference, $body);
        if ($serviceRequest->status === 'cancelled' && $serviceRequest->cancellation_reason) {
            $body .= ' Reason: '.$serviceRequest->cancellation_reason;
        }
        $userId = $serviceRequest->user_id;

        $data = ['type' => 'service_request', 'service_request_id' => $serviceRequest->id, 'audience' => 'customer'];

        dispatch(fn () => NotificationService::create($userId, $title, $body, null, $data))->afterResponse();
    }

    public static function customerCancelled(ServiceRequest $serviceRequest): void
    {
        if (! $serviceRequest->assigned_to) {
            return;
        }
        $adminId = $serviceRequest->assigned_to;
        $body = "Request {$serviceRequest->reference} was cancelled by the customer.";

        dispatch(fn () => NotificationService::sendFCMNotification($adminId, 'Request cancelled', $body, [
            'type' => 'service_request',
            'service_request_id' => $serviceRequest->id,
            'audience' => 'admin',
        ]))->afterResponse();
    }

    /** Admins with a device token that have signed in to the mobile admin panel. */
    private static function adminRecipients()
    {
        return User::whereNotNull('fcm_token')->where('fcm_token', '!=', '')
            ->whereHas('tokens', fn ($q) => $q->where('name', MobileAdmin::TOKEN_NAME)->where('revoked', false))
            ->get()
            ->filter(fn ($user) => MobileAdmin::isAdmin($user));
    }
}
