<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSubService;
use App\Models\User;
use App\Models\SubService;
use Illuminate\Http\Request;

class BookingViewController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['client', 'address', 'bookingSubServices.subService'])->latest();
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        
        // Filter by time period
        if ($request->filled('period')) {
            $period = $request->period;
            $now = now();
            
            switch ($period) {
                case 'this_week':
                    $query->whereBetween('date', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('date', [$now->subWeek()->startOfWeek(), $now->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('date', $now->month)
                          ->whereYear('date', $now->year);
                    break;
                case 'last_month':
                    $query->whereMonth('date', $now->subMonth()->month)
                          ->whereYear('date', $now->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('date', $now->year);
                    break;
                case 'last_year':
                    $query->whereYear('date', $now->subYear()->year);
                    break;
            }
        }
        
        // Get paginated results
        $bookings = $query->paginate(50);
        
        // Transform data for view
        $bookings->getCollection()->transform(function ($booking) {
            return [
                'id' => $booking->id,
                'reference_number' => $booking->reference_number,
                'customer_name' => $booking->client ? $booking->client->username : 'N/A',
                'grand_total' => number_format($booking->grand_total, 2),
                'status' => $booking->status ?? 'pending',
                'status_badge' => $this->getStatusBadge($booking->status ?? 'pending'),
                'date' => is_string($booking->date) ? $booking->date : $booking->date->format('Y-m-d'),
                'time' => $booking->from . ' - ' . $booking->to,
                'vehicle' => $booking->vehicle,
                'address' => $booking->address ? $booking->address->location : 'N/A',
                'services_count' => $booking->bookingSubServices->count()
            ];
        });
        
        return view('booking.index', compact('bookings'));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $newStatus = $request->input('status');
            
            $validStatuses = ['pending', 'accepted', 'completed', 'cancelled'];
            if (!in_array($newStatus, $validStatuses)) {
                return redirect()->back()->with('error', 'Invalid status provided');
            }
            
            $booking->status = $newStatus;
            $booking->save();
            
            return redirect()->back()->with('success', 'Booking status updated successfully');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating booking status: ' . $e->getMessage());
        }
    }

    public static function getStatusBadgeStatic($status)
    {
        $badges = [
            'pending' => '<span class="badge badge-danger">Pending</span>',
            'accepted' => '<span class="badge badge-warning">Accepted</span>',
            'completed' => '<span class="badge badge-success">Completed</span>',
            'cancelled' => '<span class="badge badge-secondary">Cancelled</span>',
            'confirmed' => '<span class="badge badge-warning">Accepted</span>', // For backward compatibility
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
    }

    private function getStatusBadge($status)
    {
        return self::getStatusBadgeStatic($status);
    }
}
