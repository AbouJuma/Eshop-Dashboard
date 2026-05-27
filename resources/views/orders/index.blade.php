@extends('layouts.app')

@section('title', 'Orders')

@section('css')
<style>
    /* Card & General Layout */
    .orders-card {
        background: #ffffff;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    
    .orders-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .orders-card .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        letter-spacing: -0.2px;
    }

    .orders-card .card-body {
        padding: 24px;
    }
    
    /* Modern Filters Panel */
    .filter-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        margin-bottom: 8px;
        display: block;
    }
    
    .filter-control {
        height: 42px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        color: #334155;
        padding: 8px 12px;
        transition: all 0.2s ease;
        background-color: #f8fafc;
        box-shadow: none !important;
        width: 100%;
    }
    
    .filter-control:focus {
        border-color: #3b82f6;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
    }
    
    .btn-apply-filters {
        height: 42px;
        background-color: #3b82f6;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        padding: 0 20px;
        transition: all 0.2s ease;
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-apply-filters:hover {
        background-color: #2563eb;
        color: #ffffff;
    }
    
    .btn-clear-filters {
        height: 32px;
        font-size: 13px;
        font-weight: 600;
        color: #ef4444;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: 1px dashed #fee2e2;
        padding: 4px 12px;
        border-radius: 6px;
        background: #fef2f2;
    }
    
    .btn-clear-filters:hover {
        background-color: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
        text-decoration: none;
    }

    /* Premium Custom Table */
    .orders-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .orders-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 16px 20px;
        border-bottom: 2px solid #e2e8f0;
        border-top: none;
    }
    
    .orders-table td {
        padding: 16px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 14px;
    }
    
    .orders-table tbody tr {
        transition: all 0.2s ease;
    }
    
    .orders-table tbody tr:hover {
        background-color: #f8fafc;
    }
    
    /* Modernized Badge Colors (Soft pastel background with high contrast text) */
    .badge {
        padding: 6px 12px;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: none !important;
        border: none !important;
    }
    
    .badge-success { background-color: #d1fae5 !important; color: #065f46 !important; }
    .badge-danger { background-color: #fee2e2 !important; color: #991b1b !important; }
    .badge-info { background-color: #e0f2fe !important; color: #075985 !important; }
    .badge-warning { background-color: #fef3c7 !important; color: #92400e !important; }
    .badge-secondary { background-color: #f1f5f9 !important; color: #475569 !important; }
    .badge-dark { background-color: #e2e8f0 !important; color: #1e293b !important; }
    .badge-primary { background-color: #dbeafe !important; color: #1e40af !important; }
    
    /* Action Buttons styling */
    .btn-action-view {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #eff6ff;
        color: #3b82f6;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        padding: 0;
    }
    
    .btn-action-view:hover {
        background-color: #3b82f6;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
    }
    
    /* Clean Modern Pagination */
    .pagination {
        margin: 0;
        gap: 4px;
    }
    
    .pagination .page-item .page-link {
        border-radius: 8px !important;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-weight: 600;
        font-size: 13px;
        padding: 8px 14px;
        transition: all 0.2s ease;
        box-shadow: none !important;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    
    .pagination .page-item .page-link:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    
    /* Modals Upgrade */
    .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }
    
    .modal-header {
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
        background-color: #ffffff;
    }
    
    .modal-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }
    
    .modal-body {
        padding: 24px;
        background-color: #f8fafc;
    }
    
    .modal-footer {
        border-top: 1px solid #f1f5f9;
        padding: 16px 24px;
        background-color: #ffffff;
    }
    
    /* Modal Cards */
    .modal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: none;
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .modal-card-header {
        padding: 14px 20px;
        font-weight: 700;
        font-size: 12px;
        border-bottom: 1px solid #e2e8f0;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    
    .modal-card-header.bg-primary {
        background-color: #eff6ff !important;
        color: #2563eb !important;
        border-bottom-color: #dbeafe;
    }
    
    .modal-card-header.bg-success {
        background-color: #ecfdf5 !important;
        color: #059669 !important;
        border-bottom-color: #d1fae5;
    }
    
    .modal-card-body {
        padding: 20px;
    }
    
    .info-label {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    
    .info-value {
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 12px;
    }
    
    /* Order Products List in Modal */
    .items-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .items-table th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .items-table td {
        padding: 14px;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    
    .items-table tfoot td {
        padding: 14px;
        font-size: 14px;
        font-weight: 700;
        background-color: #f8fafc;
        border-top: 1px solid #cbd5e1;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold" style="color: #0f172a; letter-spacing: -0.5px;">Orders Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #64748b; font-weight: 500;">Home</a></li>
                    <li class="breadcrumb-item active" style="color: #0f172a; font-weight: 600;">Orders</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filters Card -->
        <div class="card orders-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter text-primary mr-2"></i>Filter Orders</h3>
                @if(request()->has('status') || request()->has('date_from') || request()->has('date_to') || request()->has('period'))
                    <a href="{{ route('orders.index') }}" class="btn-clear-filters">
                        <i class="fas fa-trash-alt"></i> Clear Filters
                    </a>
                @endif
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('orders.index') }}">
                    <div class="row align-items-end">
                        <!-- Status Filter -->
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="form-group mb-0">
                                <label for="status" class="filter-label">Status</label>
                                <select name="status" id="status" class="form-control filter-control">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="denied" {{ request('status') == 'denied' ? 'selected' : '' }}>Denied</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="satisfied" {{ request('status') == 'satisfied' ? 'selected' : '' }}>Customer Satisfied</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Date From -->
                        <div class="col-md-2 mb-3 mb-md-0">
                            <div class="form-group mb-0">
                                <label for="date_from" class="filter-label">Date From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control filter-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        
                        <!-- Date To -->
                        <div class="col-md-2 mb-3 mb-md-0">
                            <div class="form-group mb-0">
                                <label for="date_to" class="filter-label">Date To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control filter-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        
                        <!-- Period Filter -->
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="form-group mb-0">
                                <label for="period" class="filter-label">Period</label>
                                <select name="period" id="period" class="form-control filter-control">
                                    <option value="">All Time</option>
                                    <option value="this_week" {{ request('period') == 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="last_week" {{ request('period') == 'last_week' ? 'selected' : '' }}>Last Week</option>
                                    <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                    <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>This Year</option>
                                    <option value="last_year" {{ request('period') == 'last_year' ? 'selected' : '' }}>Last Year</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="col-md-2">
                            <button type="submit" class="btn-apply-filters">
                                <i class="fas fa-search"></i> Apply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Orders List Card -->
        <div class="card orders-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list text-primary mr-2"></i>Orders ({{ $orders->total() }} total)
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-center">Products Count</th>
                                <th class="text-right">Total Amount</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td class="font-weight-bold" style="color: #0f172a;">#{{ $order['id'] }}</td>
                                    <td style="font-weight: 500;">{{ $order['customer_name'] }}</td>
                                    <td>{!! $order['status_badge'] !!}</td>
                                    <td style="color: #64748b;">{{ $order['created_at'] }}</td>
                                    <td class="text-center" style="font-weight: 600;">{{ $order['products_count'] }}</td>
                                    <td class="text-right font-weight-bold text-success">{{ $order['amount'] }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn-action-view" data-toggle="modal" data-target="#orderModal{{ $order['id'] }}" title="View Order Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 text-gray-200" style="display: block;"></i>
                                        No orders found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Custom pagination footer -->
                @if($orders->total() > 0)
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center px-4 py-3" style="background-color: #ffffff; border-top: 1px solid #f1f5f9;">
                    <div class="text-muted mb-2 mb-sm-0" style="font-size: 13px; font-weight: 500;">
                        Showing <span class="text-dark font-weight-bold">{{ $orders->firstItem() }}</span> to <span class="text-dark font-weight-bold">{{ $orders->lastItem() }}</span> of <span class="text-dark font-weight-bold">{{ $orders->total() }}</span> entries
                    </div>
                    <div>
                        {{ $orders->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Individual Modals for Each Order -->
@forelse($orders as $order)
    <!-- Get order details for this specific order -->
    @php
    $orderDetails = \App\Models\Order::with(['client', 'address', 'eshops'])->find($order['id']);
    $products = $orderDetails ? $orderDetails->eshops : collect([]);
    $grandTotal = $products->sum(function($product) {
        return $product->p_price * $product->p_quantity;
    });
    $currentStatus = $order['status'] ?? 'pending';
    @endphp
    
    <!-- Order Details Modal -->
    <div class="modal fade" id="orderModal{{ $order['id'] }}" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel{{ $order['id'] }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel{{ $order['id'] }}">
                        <i class="fas fa-shopping-bag text-primary mr-2"></i>Order #{{ $order['id'] }} Details
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Order Info Card -->
                    <div class="modal-card">
                        <div class="modal-card-header bg-primary">
                            Order Information
                        </div>
                        <div class="modal-card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-label">Order ID</div>
                                    <div class="info-value">#{{ $order['id'] }}</div>
                                    
                                    <div class="info-label">Reference No</div>
                                    <div class="info-value">{{ $order['reference_no'] }}</div>
                                    
                                    <div class="info-label">Status</div>
                                    <div class="info-value">{!! $order['status_badge'] !!}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Date Created</div>
                                    <div class="info-value">{{ $order['created_at'] }}</div>
                                    
                                    <div class="info-label">Order Amount</div>
                                    <div class="info-value text-success font-weight-bold" style="font-size: 1.1em;">{{ $order['amount'] }}</div>
                                    
                                    <div class="info-label">Total Items</div>
                                    <div class="info-value">{{ $products->count() }} item(s)</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Customer</div>
                                    <div class="info-value">{{ $orderDetails->client->username ?? 'N/A' }}</div>
                                    
                                    <div class="info-label">Email Address</div>
                                    <div class="info-value" style="font-size:13px; font-weight: 500;">{{ $orderDetails->client->email ?? 'N/A' }}</div>
                                    
                                    <div class="info-label">Phone Number</div>
                                    <div class="info-value">{{ $orderDetails->client->phone ?? 'N/A' }}</div>
                                </div>
                            </div>
                            
                            @if($currentStatus === 'cancelled' && !empty($orderDetails->cancellation_reason))
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="p-3 bg-red-light rounded" style="background-color: #fff1f2; border: 1px solid #ffe4e6; border-radius: 8px;">
                                        <div class="info-label text-danger" style="color: #e11d48;"><i class="fas fa-exclamation-circle mr-1"></i> Cancellation Reason</div>
                                        <div class="text-dark font-weight-bold" style="color: #9f1239; font-size: 13px;">{!! nl2br(e($orderDetails->cancellation_reason)) !!}</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="info-label">Delivery Address</div>
                                    <div class="info-value mb-1" style="font-weight: 600;">{{ $orderDetails->address->location ?? 'N/A' }}</div>
                                    @if($orderDetails->address && $orderDetails->address->additional_info)
                                        <div style="font-size: 12px; color: #64748b; font-weight: 500; background: #f1f5f9; padding: 6px 12px; border-radius: 6px; display: inline-block;">
                                            <strong>Additional Info:</strong> {{ $orderDetails->address->additional_info }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Products Card -->
                    <div class="modal-card">
                        <div class="modal-card-header bg-success">
                            Ordered Products
                        </div>
                        <div class="modal-card-body p-0">
                            <div class="table-responsive">
                                <table class="items-table">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>SKU</th>
                                            <th class="text-right">Price</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($products->count() > 0)
                                            @foreach($products as $product)
                                                <tr>
                                                    <td class="font-weight-bold">{{ $product->p_name }}</td>
                                                    <td><span class="badge badge-light" style="background-color: #f1f5f9; color: #475569; font-family: monospace; font-size: 11px;">{{ $product->p_sku }}</span></td>
                                                    <td class="text-right font-weight-bold">{{ number_format($product->p_price, 2) }}</td>
                                                    <td class="text-center font-weight-bold" style="color: #3b82f6;">{{ $product->p_quantity }}</td>
                                                    <td class="text-right font-weight-bold" style="color: #0f172a;">{{ number_format($product->p_price * $product->p_quantity, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">No products found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right border-0 font-weight-bold" style="color: #64748b;">Grand Total:</td>
                                            <td class="text-right text-success font-weight-bold border-0" style="font-size: 1.1em; color: #10b981;" colspan="2">
                                                {{ number_format($grandTotal, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                @if ($currentStatus === 'pending')
                                    <div class="btn-group mr-2" role="group">
                                        <form action="{{ route('orders.update-status', $order['id']) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-success" style="border-radius: 8px 0 0 8px; font-weight: 600; padding: 8px 16px;">
                                                <i class="fas fa-check mr-1"></i> Confirm
                                            </button>
                                        </form>
                                        <form action="{{ route('orders.update-status', $order['id']) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="status" value="denied">
                                            <button type="submit" class="btn btn-warning" style="border-radius: 0 8px 8px 0; font-weight: 600; padding: 8px 16px;">
                                                <i class="fas fa-times mr-1"></i> Deny
                                            </button>
                                        </form>
                                    </div>
                                @elseif ($currentStatus === 'confirmed')
                                    <form action="{{ route('orders.update-status', $order['id']) }}" method="POST" style="display: inline;" class="mr-2">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 600; padding: 8px 16px;">
                                            <i class="fas fa-check-circle mr-1"></i> Complete Order
                                        </button>
                                    </form>
                                @endif
                                
                                @if (!in_array($currentStatus, ['completed', 'cancelled', 'satisfied']))
                                    <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#cancelModal{{ $order['id'] }}" data-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 8px 16px;">
                                        <i class="fas fa-times-circle mr-1"></i> Cancel Order
                                    </button>
                                @endif
                            </div>
                            <div class="col-md-4 text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 8px 16px; background-color: #64748b; border: none;">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cancel Order Modal -->
    @if (!in_array($currentStatus, ['completed', 'cancelled', 'satisfied']))
    <div class="modal fade" id="cancelModal{{ $order['id'] }}" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel{{ $order['id'] }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('orders.update-status', $order['id']) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="cancelled">
                    <div class="modal-header bg-danger text-white" style="border-radius: 0;">
                        <h5 class="modal-title text-white" id="cancelModalLabel{{ $order['id'] }}"><i class="fas fa-exclamation-triangle mr-2"></i>Cancel Order #{{ $order['id'] }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="outline: none;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-white">
                        <div class="p-3 mb-3 rounded" style="background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b; font-size: 14px; font-weight: 500;">
                            Are you sure you want to cancel this order? This action cannot be undone.
                        </div>
                        <div class="form-group">
                            <label for="cancellation_reason_{{ $order['id'] }}" class="filter-label text-dark" style="font-size: 12px;">Reason for Cancellation <span class="text-danger">*</span></label>
                            <textarea name="cancellation_reason" id="cancellation_reason_{{ $order['id'] }}" class="form-control filter-control" rows="3" required placeholder="Please write a reason for cancelling this order..." style="height: auto; background-color: #ffffff;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-toggle="modal" data-target="#orderModal{{ $order['id'] }}" style="border-radius: 8px; font-weight: 600;">Back to Order</button>
                        <button type="submit" class="btn btn-danger" style="border-radius: 8px; font-weight: 600;">Confirm Cancellation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

@endsection
