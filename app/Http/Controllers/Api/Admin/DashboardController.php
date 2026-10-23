<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\CarMaintenance;
use App\Models\Order;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Summary figures for the order, booking, roadside and car maintenance dashboards.
     * Optional date_from / date_to (Y-m-d) limit every figure except "today" and "this month".
     */
    public function index(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $businessId = $request->user()->business_id;
        $monthStart = now()->startOfMonth();
        $completed = ['completed', 'satisfied', 'not_satisfied'];

        $inRange = function ($query, string $column = 'created_at') use ($request) {
            if ($request->filled('date_from')) {
                $query->whereDate($column, '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate($column, '<=', $request->date_to);
            }

            return $query;
        };

        $countByStatus = fn ($model) => $inRange($model::query())
            ->selectRaw("COALESCE(status, 'pending') as status, COUNT(*) as total")
            ->groupByRaw("COALESCE(status, 'pending')")
            ->pluck('total', 'status');

        $orderCounts = $countByStatus(Order::class);
        $bookingCounts = $countByStatus(Booking::class);
        $roadsideCounts = $countByStatus(ServiceRequest::class);

        $maintenance = fn () => $inRange(CarMaintenance::where('business_id', $businessId), 'service_date');

        return response()->json([
            'success' => true,
            'data' => [
                'range' => ['date_from' => $request->date_from, 'date_to' => $request->date_to],
                'orders' => [
                    'total' => (int) $orderCounts->sum(),
                    'today' => Order::whereDate('created_at', today())->count(),
                    'by_status' => $orderCounts,
                    'revenue' => (float) $inRange(Order::whereIn('status', $completed))->sum('amount'),
                    'revenue_this_month' => (float) Order::whereIn('status', $completed)
                        ->where('created_at', '>=', $monthStart)->sum('amount'),
                ],
                'bookings' => [
                    'total' => (int) $bookingCounts->sum(),
                    'today' => Booking::whereDate('created_at', today())->count(),
                    'by_status' => $bookingCounts,
                    'revenue' => (float) $inRange(Booking::whereIn('status', $completed))->sum('grand_total'),
                    'revenue_this_month' => (float) Booking::whereIn('status', $completed)
                        ->where('created_at', '>=', $monthStart)->sum('grand_total'),
                ],
                'roadside' => [
                    'total' => (int) $roadsideCounts->sum(),
                    'today' => ServiceRequest::whereDate('created_at', today())->count(),
                    'by_status' => $roadsideCounts,
                    'revenue' => (float) $inRange(ServiceRequest::where('status', 'completed'))->sum('price'),
                    'revenue_this_month' => (float) ServiceRequest::where('status', 'completed')
                        ->where('created_at', '>=', $monthStart)->sum('price'),
                ],
                'car_maintenance' => [
                    'records' => $maintenance()->count(),
                    'vehicles' => Car::where('business_id', $businessId)->count(),
                    'this_month' => CarMaintenance::where('business_id', $businessId)
                        ->where('service_date', '>=', $monthStart)->count(),
                    'cost_this_month' => (float) CarMaintenance::where('business_id', $businessId)
                        ->where('service_date', '>=', $monthStart)->sum('cost'),
                    'cost' => (float) $maintenance()->sum('cost'),
                ],
            ],
        ]);
    }
}
