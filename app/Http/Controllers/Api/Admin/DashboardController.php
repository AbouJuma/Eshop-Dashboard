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
     * The signed-in user's own figures (every app account, whatever its role):
     * roadside jobs they took and service records they entered. Optional date_from / date_to.
     */
    public function mine(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);
        $user = $request->user();

        $inRange = function ($query, string $column = 'created_at') use ($request) {
            if ($request->filled('date_from')) {
                $query->whereDate($column, '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate($column, '<=', $request->date_to);
            }

            return $query;
        };

        $jobs = fn () => $inRange(ServiceRequest::where('assigned_to', $user->id), 'accepted_at');
        $byStatus = $jobs()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $arrivalMinutes = $jobs()->whereNotNull('arrived_at')->whereNotNull('accepted_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, accepted_at, arrived_at)) as m')->value('m');

        $records = fn () => $inRange(CarMaintenance::where('business_id', $user->business_id)->where('created_by', $user->id), 'service_date');

        return response()->json([
            'success' => true,
            'data' => [
                'range' => ['date_from' => $request->date_from, 'date_to' => $request->date_to],
                'roadside' => [
                    'total' => (int) $byStatus->sum(),
                    'by_status' => $byStatus,
                    'active' => ServiceRequest::where('assigned_to', $user->id)
                        ->whereIn('status', ['accepted', 'on_the_way', 'arrived', 'in_progress'])->count(),
                    'completed' => (int) ($byStatus['completed'] ?? 0),
                    'cancelled' => (int) ($byStatus['cancelled'] ?? 0),
                    'earnings' => (float) $jobs()->where('status', 'completed')->sum('price'),
                    'avg_arrival_minutes' => $arrivalMinutes === null ? null : (int) round($arrivalMinutes),
                ],
                'car_maintenance' => [
                    'records' => $records()->count(),
                    'value' => (float) $records()->sum('cost'),
                    'by_type' => $records()->selectRaw('service_type, COUNT(*) as total')->groupBy('service_type')->pluck('total', 'service_type'),
                ],
            ],
        ]);
    }

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
