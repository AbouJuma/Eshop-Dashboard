<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderEshop;
use App\Models\User;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['client', 'address', 'eshops'])->latest();
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by time period
        if ($request->filled('period')) {
            $period = $request->period;
            $now = now();
            
            switch ($period) {
                case 'this_week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [$now->subWeek()->startOfWeek(), $now->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', $now->subMonth()->month)
                          ->whereYear('created_at', $now->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', $now->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', $now->subYear()->year);
                    break;
            }
        }
        
        // Get paginated results
        $orders = $query->paginate(50);
        
        // Transform data for view
        $orders->getCollection()->transform(function ($order) {
            return [
                'id' => $order->id,
                'reference_no' => $order->reference_no,
                'customer_name' => $order->client ? $order->client->username : 'N/A',
                'amount' => number_format($order->amount, 2),
                'status' => $order->status ?? 'pending',
                'status_badge' => $this->getStatusBadge($order->status ?? 'pending'),
                'created_at' => $order->created_at->format('Y-m-d H:i'),
                'address' => $order->address ? $order->address->location : 'N/A',
                'products_count' => $order->eshops->count()
            ];
        });

        return view('orders.index', compact('orders'));
    }

    public function getOrderDetails($id)
    {
        $order = Order::with(['client', 'address', 'eshops'])->findOrFail($id);
        
        return response()->json([
            'order' => [
                'id' => $order->id,
                'reference_no' => $order->reference_no,
                'customer_name' => $order->client ? $order->client->username : 'N/A',
                'customer_email' => $order->client ? $order->client->email : 'N/A',
                'customer_phone' => $order->client ? $order->client->phone : 'N/A',
                'amount' => number_format($order->amount, 2),
                'status' => $order->status ?? 'pending',
                'status_badge' => $this->getStatusBadge($order->status ?? 'pending'),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'address' => $order->address ? $order->address->location : 'N/A',
                'additional_info' => $order->address ? $order->address->additional_info : 'N/A'
            ],
            'products' => $order->eshops->map(function ($product) {
                return [
                    'name' => $product->p_name,
                    'image' => $product->p_image,
                    'price' => number_format($product->p_price, 2),
                    'quantity' => $product->p_quantity,
                    'sku' => $product->p_sku,
                    'total' => number_format($product->p_price * $product->p_quantity, 2)
                ];
            })
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            $request->validate([
                'status' => 'required|string',
                'cancellation_reason' => 'nullable|string'
            ]);

            $newStatus = strtolower(trim((string) $request->input('status')));
            
            // Validate status
            $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'denied', 'satisfied'];
            if (!in_array($newStatus, $validStatuses)) {
                return redirect()->back()->with('status', [
                    'success' => 0,
                    'msg' => 'Invalid status provided'
                ]);
            }
            
            // Update status
            $order->status = $newStatus;
            
            $message = 'Order status updated successfully';
            
            // Save cancellation reason if status is cancelled
            if ($newStatus === 'cancelled') {
                if ($request->filled('cancellation_reason')) {
                    $order->cancellation_reason = $request->input('cancellation_reason');
                }
                $message = 'Your order has been cancelled';
            }
            
            $order->save();
            
            return redirect()->back()->with('status', [
                'success' => 1,
                'msg' => $message
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('status', [
                'success' => 0,
                'msg' => 'Error updating order status: ' . $e->getMessage()
            ]);
        }
    }

    public static function getStatusBadgeStatic($status)
    {
        $badges = [
            'pending' => '<span class="badge badge-danger">Pending</span>',
            'processing' => '<span class="badge badge-info">Processing</span>',
            'denied' => '<span class="badge badge-dark">Denied</span>',
            'completed' => '<span class="badge badge-success">Completed</span>',
            'cancelled' => '<span class="badge badge-secondary">Cancelled</span>',
            'delivered' => '<span class="badge badge-primary">Delivered</span>',
            'confirmed' => '<span class="badge badge-warning">Confirmed</span>',
            'shipped' => '<span class="badge badge-info">Shipped</span>',
            'refunded' => '<span class="badge badge-secondary">Refunded</span>',
            'satisfied' => '<span class="badge badge-success">Customer Satisfied</span>'
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
    }

    private function getStatusBadge($status)
    {
        return self::getStatusBadgeStatic($status);
    }
}
