<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\DWorldStatusService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class BookingAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['client', 'address'])->withCount('bookingSubServices')->latest();

        if ($request->filled('status')) {
            $request->status === 'pending'
                ? $query->where(fn ($q) => $q->where('status', 'pending')->orWhereNull('status'))
                : $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where(fn ($q) => $q->whereDate('created_at', '>=', $request->date_from)
                ->orWhereDate('date', '>=', $request->date_from));
        }
        if ($request->filled('date_to')) {
            $query->where(fn ($q) => $q->whereDate('created_at', '<=', $request->date_to)
                ->orWhereDate('date', '<=', $request->date_to));
        }
        if ($request->filled('search')) {
            $search = '%'.$request->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', $search)
                  ->orWhere('vehicle', 'like', $search)
                  ->orWhereHas('client', fn ($c) => $c->where('username', 'like', $search)->orWhere('phone', 'like', $search));
            });
        }

        $bookings = $query->paginate(min(max((int) $request->input('per_page', 20), 1), 100));
        $bookings->getCollection()->transform(fn ($booking) => $this->summary($booking));

        return response()->json(['success' => true] + $bookings->toArray());
    }

    public function show($id)
    {
        $booking = Booking::with(['client', 'address', 'bookingSubServices.subService'])
            ->withCount('bookingSubServices')->findOrFail($id);

        return response()->json(['success' => true, 'data' => $this->detail($booking)]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'grand_total' => 'nullable|numeric|min:0',
            'cancellation_reason' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::findOrFail($id);

        if (in_array($booking->status, DWorldStatusService::FINAL_STATUSES)) {
            return response()->json(['success' => false, 'message' => 'This booking can no longer be updated'], 422);
        }

        try {
            DWorldStatusService::updateBookingStatus($booking, $request->status, $request->grand_total, $request->cancellation_reason);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return $this->respondWithDetail($booking, 'Booking status updated successfully');
    }

    public function updatePrice(Request $request, $id)
    {
        $request->validate(['grand_total' => 'required|numeric|min:0']);

        $booking = Booking::findOrFail($id);
        $booking->grand_total = $request->grand_total;
        $booking->final_total = $request->grand_total;
        $booking->save();

        return $this->respondWithDetail($booking, 'Booking price updated successfully');
    }

    private function respondWithDetail(Booking $booking, string $message)
    {
        $booking->load(['client', 'address', 'bookingSubServices.subService'])->loadCount('bookingSubServices');

        return response()->json(['success' => true, 'message' => $message, 'data' => $this->detail($booking)]);
    }

    private function summary(Booking $booking): array
    {
        $date = $booking->date;

        return [
            'id' => $booking->id,
            'reference_number' => $booking->reference_number,
            'customer_name' => optional($booking->client)->username ?? 'N/A',
            'customer_phone' => optional($booking->client)->phone,
            'grand_total' => (float) $booking->grand_total,
            'status' => $booking->status ?? 'pending',
            'date' => $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : $date,
            'time' => trim($booking->from.' - '.$booking->to, ' -'),
            'vehicle' => $booking->vehicle,
            'address' => optional($booking->address)->location,
            'services_count' => $booking->booking_sub_services_count,
            'created_at' => optional($booking->created_at)->format('Y-m-d H:i'),
        ];
    }

    private function detail(Booking $booking): array
    {
        return $this->summary($booking) + [
            'customer_email' => optional($booking->client)->email,
            'additional_info' => optional($booking->address)->additional_info,
            'cancellation_reason' => $booking->cancellation_reason,
            'unsatisfied_reason' => $booking->unsatisfied_reason,
            'services' => $booking->bookingSubServices->map(fn ($s) => [
                'title' => optional($s->subService)->title ?? 'N/A',
                'description' => optional($s->subService)->description,
            ])->values(),
        ];
    }
}
