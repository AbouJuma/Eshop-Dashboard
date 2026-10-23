<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Order;
use App\Models\User;
use InvalidArgumentException;

/**
 * Status transitions for D-World bookings and shop orders.
 * Shared by the web portal (BookingViewController / OrdersController)
 * and the mobile admin API so both behave identically.
 */
class DWorldStatusService
{
    const BOOKING_STATUSES = ['pending', 'accepted', 'completed', 'cancelled', 'denied', 'satisfied', 'not_satisfied'];

    const ORDER_STATUSES = ['pending', 'confirmed', 'completed', 'cancelled', 'denied', 'satisfied', 'not_satisfied'];

    /** Statuses after which no further admin action is possible. */
    const FINAL_STATUSES = ['completed', 'cancelled', 'satisfied', 'not_satisfied'];

    const BOOKING_MESSAGES = [
        'accepted' => 'Your booking has been accepted',
        'completed' => 'Your booking has been completed',
        'cancelled' => 'Your booking has been cancelled',
        'denied' => 'Your booking has been denied',
    ];

    const ORDER_MESSAGES = [
        'confirmed' => 'Your order has been confirmed',
        'completed' => 'Your order has been completed',
        'cancelled' => 'Your order has been cancelled',
        'denied' => 'Your order has been denied',
        'shipped' => 'Your order has been shipped',
        'delivered' => 'Your order has been delivered',
    ];

    /**
     * @throws InvalidArgumentException on invalid status or missing cancellation reason
     */
    public static function updateBookingStatus(Booking $booking, string $status, $grandTotal = null, $cancellationReason = null): Booking
    {
        $status = strtolower(trim($status));

        if (! in_array($status, self::BOOKING_STATUSES)) {
            throw new InvalidArgumentException('Invalid status provided');
        }

        if ($status === 'accepted' && $grandTotal !== null && $grandTotal !== '') {
            if (! is_numeric($grandTotal) || $grandTotal < 0) {
                throw new InvalidArgumentException('Grand total must be a positive number');
            }
            $booking->grand_total = $grandTotal;
            $booking->final_total = $grandTotal; // App uses final_total for the "Grand Total" display
        }

        if ($status === 'cancelled') {
            if (trim((string) $cancellationReason) === '') {
                throw new InvalidArgumentException('Cancellation reason is required');
            }
            $booking->cancellation_reason = $cancellationReason;
        }

        $booking->status = $status;
        $booking->save();

        if ($booking->user_id && isset(self::BOOKING_MESSAGES[$status]) && User::find($booking->user_id)) {
            NotificationService::bookingStatusChanged($booking->user_id, $booking->id, $booking->reference_number, $status, self::BOOKING_MESSAGES[$status]);
        }

        return $booking;
    }

    /**
     * @throws InvalidArgumentException on invalid status
     */
    public static function updateOrderStatus(Order $order, string $status, $cancellationReason = null): Order
    {
        $status = strtolower(trim($status));

        if (! in_array($status, self::ORDER_STATUSES)) {
            throw new InvalidArgumentException('Invalid status provided');
        }

        $order->status = $status;

        if ($status === 'cancelled' && trim((string) $cancellationReason) !== '') {
            $order->cancellation_reason = $cancellationReason;
        }

        $order->save();

        if ($order->user_id && isset(self::ORDER_MESSAGES[$status]) && User::find($order->user_id)) {
            NotificationService::orderStatusChanged($order->user_id, $order->reference_no, $status, self::ORDER_MESSAGES[$status]);
        }

        return $order;
    }
}
