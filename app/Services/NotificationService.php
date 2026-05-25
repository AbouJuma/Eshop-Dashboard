<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public static function create($userId, $title, $content, $actionTime = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'action_time' => $actionTime ?? now(),
        ]);
    }

    /**
     * Create notification for multiple users
     */
    public static function createBulk($userIds, $title, $content, $actionTime = null)
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => $title,
                'content' => $content,
                'action_time' => $actionTime ?? now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return Notification::insert($notifications);
    }

    /**
     * Create notification for user by phone
     */
    public static function createByPhone($phone, $title, $content, $actionTime = null)
    {
        $user = User::where('phone', $phone)->first();
        if ($user) {
            return self::create($user->id, $title, $content, $actionTime);
        }
        return null;
    }

    /**
     * Common notification templates
     */
    public static function orderCreated($userId, $orderNumber)
    {
        return self::create($userId, 'Order Created', "Your order #{$orderNumber} has been created successfully.");
    }

    public static function orderShipped($userId, $orderNumber)
    {
        return self::create($userId, 'Order Shipped', "Your order #{$orderNumber} has been shipped and is on its way.");
    }

    public static function orderDelivered($userId, $orderNumber)
    {
        return self::create($userId, 'Order Delivered', "Your order #{$orderNumber} has been delivered successfully.");
    }

    public static function paymentReceived($userId, $amount)
    {
        return self::create($userId, 'Payment Received', "We have received your payment of {$amount}.");
    }

    public static function bookingConfirmed($userId, $bookingId, $referenceNumber = null)
    {
        $refNumber = $referenceNumber ?? $bookingId;
        return self::create($userId, 'Booking Confirmed', "Your booking #{$refNumber} has been confirmed.");
    }

    public static function bookingCancelled($userId, $bookingId, $referenceNumber = null)
    {
        $refNumber = $referenceNumber ?? $bookingId;
        return self::create($userId, 'Booking Cancelled', "Your booking #{$refNumber} has been cancelled.");
    }

    public static function bookingCompleted($userId, $bookingId, $referenceNumber = null)
    {
        $refNumber = $referenceNumber ?? $bookingId;
        return self::create($userId, 'Booking Completed', "Your booking #{$refNumber} has been completed successfully.");
    }
}
