@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Orders Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filters</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('orders.index') }}">
                    <div class="row">
                        <!-- Status Filter -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="denied" {{ request('status') == 'denied' ? 'selected' : '' }}>Denied</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Date From -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_from">Date From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        
                        <!-- Date To -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_to">Date To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        
                        <!-- Period Filter -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="period">Period</label>
                                <select name="period" id="period" class="form-control">
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
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Clear Filters -->
                    @if(request()->has('status') || request()->has('date_from') || request()->has('date_to') || request()->has('period'))
                    <div class="row">
                        <div class="col-12">
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">Clear Filters</a>
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>
        
        <!-- Orders Table -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Orders List ({{ $orders->total() }} total orders)</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Products Count</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order['id'] }}</td>
                                    <td>{{ $order['customer_name'] }}</td>
                                    <td>{!! $order['status_badge'] !!}</td>
                                    <td>{{ $order['created_at'] }}</td>
                                    <td>{{ $order['products_count'] }}</td>
                                    <td class="font-weight-bold">{{ $order['amount'] }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info rounded-circle" data-toggle="modal" data-target="#orderModal{{ $order['id'] }}" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries
                    </div>
                    <div>
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Individual Modals for Each Order -->
@forelse($orders as $order)
    <!-- Get order details for this specific order -->
    <?php
    $orderDetails = \App\Models\Order::with(['client', 'address', 'eshops'])->find($order['id']);
    $products = $orderDetails ? $orderDetails->eshops : collect([]);
    $grandTotal = $products->sum(function($product) {
        return $product->p_price * $product->p_quantity;
    });
    $currentStatus = $order['status'] ?? 'pending';
    ?>
    
    <div class="modal fade" id="orderModal{{ $order['id'] }}" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel{{ $order['id'] }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel{{ $order['id'] }}">Order #{{ $order['id'] }} Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Order Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Order ID:</strong> #{{ $order['id'] }}</p>
                                            <p><strong>Reference No:</strong> {{ $order['reference_no'] }}</p>
                                            <p><strong>Status:</strong> {!! $order['status_badge'] !!}</p>
                                            <?php if($currentStatus === 'cancelled' && !empty($orderDetails->cancellation_reason)): ?>
                                                <p class="text-danger"><strong>Cancel Reason:</strong> <br><?php echo nl2br(e($orderDetails->cancellation_reason)); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Date:</strong> {{ $order['created_at'] }}</p>
                                            <p><strong>Amount:</strong> <span class="text-success font-weight-bold">{{ $order['amount'] }}</span></p>
                                            <p><strong>Products:</strong> <?php echo $products->count(); ?> items</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Customer:</strong> <?php echo $orderDetails->client->username ?? 'N/A'; ?></p>
                                            <p><strong>Email:</strong> <?php echo $orderDetails->client->email ?? 'N/A'; ?></p>
                                            <p><strong>Phone:</strong> <?php echo $orderDetails->client->phone ?? 'N/A'; ?></p>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <p><strong>Delivery Address:</strong> <?php echo $orderDetails->address->location ?? 'N/A'; ?></p>
                                            <?php if($orderDetails->address && $orderDetails->address->additional_info): ?>
                                                <p><strong>Additional Info:</strong> <?php echo $orderDetails->address->additional_info; ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Order Products</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>SKU</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if($products->count() > 0): ?>
                                                    <?php foreach($products as $product): ?>
                                                        <tr>
                                                            <td><strong><?php echo $product->p_name; ?></strong></td>
                                                            <td><code><?php echo $product->p_sku; ?></code></td>
                                                            <td class="text-right"><?php echo number_format($product->p_price, 2); ?></td>
                                                            <td class="text-center"><?php echo $product->p_quantity; ?></td>
                                                            <td class="text-right font-weight-bold"><?php echo number_format($product->p_price * $product->p_quantity, 2); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No products found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light">
                                                    <th colspan="3" class="text-right">Grand Total:</th>
                                                    <th class="text-right text-success font-weight-bold" style="font-size: 1.2em;"><?php echo number_format($grandTotal, 2); ?></th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <?php if ($currentStatus === 'pending'): ?>
                                    <div class="btn-group mr-2" role="group">
                                        <form action="{{ route('orders.update-status', $order['id']) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check"></i> Confirm Order
                                            </button>
                                        </form>
                                        <form action="{{ route('orders.update-status', $order['id']) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="status" value="denied">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-times"></i> Deny
                                            </button>
                                        </form>
                                    </div>
                                <?php elseif ($currentStatus === 'confirmed'): ?>
                                    <form action="{{ route('orders.update-status', $order['id']) }}" method="POST" style="display: inline;" class="mr-2">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check-circle"></i> Complete Order
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if (!in_array($currentStatus, ['completed', 'cancelled'])): ?>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelModal{{ $order['id'] }}" data-dismiss="modal">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cancel Order Modal -->
    <?php if (!in_array($currentStatus, ['completed', 'cancelled'])): ?>
    <div class="modal fade" id="cancelModal{{ $order['id'] }}" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel{{ $order['id'] }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('orders.update-status', $order['id']) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="cancelled">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="cancelModalLabel{{ $order['id'] }}">Cancel Order #{{ $order['id'] }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to cancel this order? This action cannot be undone.</p>
                        <div class="form-group">
                            <label for="cancellation_reason_{{ $order['id'] }}">Cancellation Reason <span class="text-danger">*</span></label>
                            <textarea name="cancellation_reason" id="cancellation_reason_{{ $order['id'] }}" class="form-control" rows="3" required placeholder="Please provide a reason for cancelling this order..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-toggle="modal" data-target="#orderModal{{ $order['id'] }}">Back to Order</button>
                        <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
@endforeach

@endsection
