<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CarMaintenance;
use App\Models\Car;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Summary figures for the order, booking and car maintenance dashboards.
     */
    public function index(Request $request)
    {
        $businessId = $request->user()->business_id;
        $monthStart = now()->startOfMonth();

        $orderCounts = Order::selectRaw("COALESCE(status, 'pending') as status, COUNT(*) as total")
            ->groupByRaw("COALESCE(status, 'pending')")->pluck('total', 'status');
        $bookingCounts = Booking::selectRaw("COALESCE(status, 'pending') as status, COUNT(*) as total")
            ->groupByRaw("COALESCE(status, 'pending')")->pluck('total', 'status');

        $completedOrderStatuses = ['completed', 'satisfied', 'not_satisfied'];
        $completedBookingStatuses = ['completed', 'satisfied', 'not_satisfied'];

        $maintenance = CarMaintenance::where('business_id', $businessId);

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => [
                    'total' => (int) $orderCounts->sum(),
                    'today' => Order::whereDate('created_at', today())->count(),
                    'by_status' => $orderCounts,
                    'revenue' => (float) Order::whereIn('status', $completedOrderStatuses)->sum('amount'),
                    'revenue_this_month' => (float) Order::whereIn('status', $completedOrderStatuses)
                        ->where('created_at', '>=', $monthStart)->sum('amount'),
                ],
                'bookings' => [
                    'total' => (int) $bookingCounts->sum(),
                    'today' => Booking::whereDate('created_at', today())->count(),
                    'by_status' => $bookingCounts,
                    'revenue' => (float) Booking::whereIn('status', $completedBookingStatuses)->sum('grand_total'),
                    'revenue_this_month' => (float) Booking::whereIn('status', $completedBookingStatuses)
                        ->where('created_at', '>=', $monthStart)->sum('grand_total'),
                ],
                'car_maintenance' => [
                    'records' => (clone $maintenance)->count(),
                    'vehicles' => Car::where('business_id', $businessId)->count(),
                    'this_month' => (clone $maintenance)->where('service_date', '>=', $monthStart)->count(),
                    'cost_this_month' => (float) (clone $maintenance)->where('service_date', '>=', $monthStart)->sum('cost'),
                ],
            ],
        ]);
    }
}
