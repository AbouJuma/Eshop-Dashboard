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
        
        // Filter by date range - allow both created_at and date columns
        if ($request->filled('date_from')) {
            $query->where(function($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date_from)
                  ->orWhereDate('date', '>=', $request->date_from);
            });
        }
        if ($request->filled('date_to')) {
            $query->where(function($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->date_to)
                  ->orWhereDate('date', '<=', $request->date_to);
            });
        }
        
        // Filter by time period - use created_at for consistency
        if ($request->filled('period')) {
            $period = $request->period;
            $now = now();
            
            switch ($period) {
                case 'this_week':
                    $query->where(function($q) use ($now) {
                        $q->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()])
                          ->orWhereBetween('date', [$now->startOfWeek(), $now->endOfWeek()]);
                    });
                    break;
                case 'last_week':
                    $query->where(function($q) use ($now) {
                        $q->whereBetween('created_at', [$now->subWeek()->startOfWeek(), $now->subWeek()->endOfWeek()])
                          ->orWhereBetween('date', [$now->subWeek()->startOfWeek(), $now->subWeek()->endOfWeek()]);
                    });
                    break;
                case 'this_month':
                    $query->where(function($q) use ($now) {
                        $q->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)
                          ->orWhereMonth('date', $now->month)->whereYear('date', $now->year);
                    });
                    break;
                case 'last_month':
                    $query->where(function($q) use ($now) {
                        $q->whereMonth('created_at', $now->subMonth()->month)->whereYear('created_at', $now->subMonth()->year)
                          ->orWhereMonth('date', $now->subMonth()->month)->whereYear('date', $now->subMonth()->year);
                    });
                    break;
                case 'this_year':
                    $query->where(function($q) use ($now) {
                        $q->whereYear('created_at', $now->year)
                          ->orWhereYear('date', $now->year);
                    });
                    break;
                case 'last_year':
                    $query->where(function($q) use ($now) {
                        $q->whereYear('created_at', $now->subYear()->year)
                          ->orWhereYear('date', $now->subYear()->year);
                    });
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
                'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                'time' => $booking->from . ' - ' . $booking->to,
                'vehicle' => $booking->vehicle,
                'address' => $booking->address ? $booking->address->location : 'N/A',
                'services_count' => $booking->bookingSubServices->count(),
                'cancellation_reason' => $booking->cancellation_reason
            ];
        });

        return view('booking.index', compact('bookings'));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $request->validate([
                'status' => 'required|string',
            ]);

            $newStatus = strtolower(trim((string) $request->input('status')));

            if ($newStatus === '') {
                \Log::warning('Booking updateStatus called with empty status', [
                    'booking_id' => $id,
                    'payload' => $request->except(['_token']),
                ]);

                return redirect()->back()->with('error', 'Status is required');
            }
            
            $validStatuses = ['pending', 'accepted', 'completed', 'cancelled', 'denied'];
            if (!in_array($newStatus, $validStatuses)) {
                return redirect()->back()->with('error', 'Invalid status provided');
            }

            if ($newStatus === 'accepted' && $request->filled('grand_total')) {
                $request->validate([
                    'grand_total' => 'numeric|min:0'
                ]);
                $booking->grand_total = $request->grand_total;
            }

            if ($newStatus === 'cancelled') {
                $request->validate([
                    'cancellation_reason' => 'required|string|max:1000'
                ]);
                $booking->cancellation_reason = $request->cancellation_reason;
            }
            
            $booking->status = $newStatus;
            $booking->save();
            
            return redirect()->back()->with('success', 'Booking status updated successfully');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating booking status: ' . $e->getMessage());
        }
    }

    public function updatePrice(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            // Validate input
            $request->validate([
                'confirmed_price' => 'required|numeric|min:0',
                'price_note' => 'nullable|string|max:500'
            ]);
            
            // Update booking price and note
            $booking->grand_total = $request->confirmed_price;
            $booking->save();
            
            // If there's a price note, you could store it in a separate table or log it
            if ($request->filled('price_note')) {
                // Log the price note for reference
                \Log::info("Price note for booking {$id}: " . $request->price_note);
            }
            
            return redirect()->back()->with('success', 'Booking price updated successfully');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating booking price: ' . $e->getMessage());
        }
    }

    public static function getStatusBadgeStatic($status)
    {
        $badges = [
            'pending' => '<span class="badge badge-danger">Pending</span>',
            'accepted' => '<span class="badge badge-warning">Accepted</span>',
            'denied' => '<span class="badge badge-dark">Denied</span>',
            'completed' => '<span class="badge badge-success">Completed</span>',
            'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
            'confirmed' => '<span class="badge badge-warning">Accepted</span>', // For backward compatibility
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
    }

    private function getStatusBadge($status)
    {
        return self::getStatusBadgeStatic($status);
    }
}
