<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\DWorldStatusService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['client', 'address'])->withCount('eshops')->latest();

        if ($request->filled('status')) {
            $request->status === 'pending'
                ? $query->where(fn ($q) => $q->where('status', 'pending')->orWhereNull('status'))
                : $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = '%'.$request->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', $search)
                  ->orWhereHas('client', fn ($c) => $c->where('username', 'like', $search)->orWhere('phone', 'like', $search));
            });
        }

        $orders = $query->paginate(min(max((int) $request->input('per_page', 20), 1), 100));
        $orders->getCollection()->transform(fn ($order) => $this->summary($order));

        return response()->json(['success' => true] + $orders->toArray());
    }

    public function show($id)
    {
        $order = Order::with(['client', 'address', 'eshops'])->withCount('eshops')->findOrFail($id);

        return response()->json(['success' => true, 'data' => $this->detail($order)]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'cancellation_reason' => 'nullable|string|max:1000',
        ]);

        $order = Order::findOrFail($id);

        if (in_array($order->status, DWorldStatusService::FINAL_STATUSES)) {
            return response()->json(['success' => false, 'message' => 'This order can no longer be updated'], 422);
        }

        try {
            DWorldStatusService::updateOrderStatus($order, $request->status, $request->cancellation_reason);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $order->load(['client', 'address', 'eshops'])->loadCount('eshops');

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $this->detail($order),
        ]);
    }

    private function summary(Order $order): array
    {
        return [
            'id' => $order->id,
            'reference_no' => $order->reference_no,
            'customer_name' => optional($order->client)->username ?? 'N/A',
            'customer_phone' => optional($order->client)->phone,
            'amount' => (float) $order->amount,
            'status' => $order->status ?? 'pending',
            'products_count' => $order->eshops_count,
            'address' => optional($order->address)->location,
            'created_at' => optional($order->created_at)->format('Y-m-d H:i'),
        ];
    }

    private function detail(Order $order): array
    {
        return $this->summary($order) + [
            'customer_email' => optional($order->client)->email,
            'additional_info' => optional($order->address)->additional_info,
            'cancellation_reason' => $order->cancellation_reason,
            'unsatisfied_reason' => $order->unsatisfied_reason,
            'products' => $order->eshops->map(fn ($p) => [
                'name' => $p->p_name,
                'image' => $p->p_image,
                'sku' => $p->p_sku,
                'price' => (float) $p->p_price,
                'quantity' => (int) $p->p_quantity,
                'total' => (float) $p->p_price * (int) $p->p_quantity,
            ])->values(),
        ];
    }
}
