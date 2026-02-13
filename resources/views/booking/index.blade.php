@extends('layouts.app')

@section('title', 'Bookings')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Bookings Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Bookings</li>
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
                <form method="GET" action="{{ route('booking.index') }}">
                    <div class="row">
                        <!-- Status Filter -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
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
                            <a href="{{ route('booking.index') }}" class="btn btn-secondary btn-sm">Clear Filters</a>
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>
        
        <!-- Bookings Table -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Bookings List ({{ $bookings->total() }} total bookings)</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Reference No</th>
                                <th>Customer Name</th>
                                <th>Status</th>
                                <th>Booking Date</th>
                                <th>Created At</th>
                                <th>Time</th>
                                <th>Vehicle</th>
                                <th>Services</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td>{{ $booking['id'] }}</td>
                                    <td>{{ $booking['reference_number'] }}</td>
                                    <td>{{ $booking['customer_name'] }}</td>
                                    <td>{!! $booking['status_badge'] !!}</td>
                                    <td>{{ $booking['date'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking['created_at'])->format('Y-m-d H:i') }}</td>
                                    <td>{{ $booking['time'] }}</td>
                                    <td>{{ $booking['vehicle'] }}</td>
                                    <td>{{ $booking['services_count'] }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info rounded-circle" data-toggle="modal" data-target="#bookingModal{{ $booking['id'] }}" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No bookings found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} entries
                    </div>
                    <div>
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Individual Modals for Each Booking -->
@foreach($bookings as $booking)
    <!-- Get booking details for this specific booking -->
    <?php
    $bookingDetails = \App\Models\Booking::with(['client', 'address', 'bookingSubServices.subService'])->find($booking['id']);
    $services = $bookingDetails ? $bookingDetails->bookingSubServices : collect([]);
    $grandTotal = $bookingDetails ? $bookingDetails->grand_total : 0;
    ?>
    
    <div class="modal fade" id="bookingModal{{ $booking['id'] }}" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel{{ $booking['id'] }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel{{ $booking['id'] }}">Booking #{{ $booking['id'] }} Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Booking Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Booking ID:</strong> #{{ $booking['id'] }}</p>
                                            <p><strong>Reference No:</strong> {{ $booking['reference_number'] }}</p>
                                            <p><strong>Status:</strong> {!! $booking['status_badge'] !!}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Date:</strong> {{ $booking['date'] }}</p>
                                            <p><strong>Created At:</strong> {{ \Carbon\Carbon::parse($booking['created_at'])->format('Y-m-d H:i') }}</p>
                                            <p><strong>Time:</strong> {{ $booking['time'] }}</p>
                                            <p><strong>Vehicle:</strong> {{ $booking['vehicle'] }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Customer:</strong> <?php echo $bookingDetails->client->username ?? 'N/A'; ?></p>
                                            <p><strong>Email:</strong> <?php echo $bookingDetails->client->email ?? 'N/A'; ?></p>
                                            <p><strong>Phone:</strong> <?php echo $bookingDetails->client->phone ?? 'N/A'; ?></p>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <p><strong>Service Address:</strong> <?php echo $bookingDetails->address->location ?? 'N/A'; ?></p>
                                            <?php if($bookingDetails->address && $bookingDetails->address->additional_info): ?>
                                                <p><strong>Additional Info:</strong> <?php echo $bookingDetails->address->additional_info; ?></p>
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
                                    <h5 class="mb-0">Booking Services</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Service Name</th>
                                                    <th>Description</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if($services->count() > 0): ?>
                                                    <?php foreach($services as $service): ?>
                                                        <tr>
                                                            <td><strong><?php echo $service->subService->title ?? 'N/A'; ?></strong></td>
                                                            <td><?php echo $service->subService->description ?? 'N/A'; ?></td>
                                                            <td class="text-right font-weight-bold">-</td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center">No services found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light">
                                                    <th colspan="2" class="text-right">Grand Total:</th>
                                                    <th class="text-right text-success font-weight-bold" style="font-size: 1.2em;"><?php echo number_format($grandTotal, 2); ?></th>
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
                        <div class="row">
                            <div class="col-md-6">
                                <?php 
                                $currentStatus = $booking['status'] ?? 'pending';
                                if ($currentStatus === 'pending'): ?>
                                    <div class="w-100">
                                        <div class="row">
                                            <div class="col-md-12 d-flex justify-content-center">
                                                <div class="col-md-6 px-0">
                                                    <label class="small font-weight-bold mb-1 text-center d-block">Confirm Price / Grand Total</label>
                                                    <form id="acceptForm{{ $booking['id'] }}" action="{{ route('booking.update-status', $booking['id']) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="accepted">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">TZS</span>
                                                            </div>
                                                            <input type="number" name="grand_total" class="form-control" value="{{ $grandTotal }}" step="0.01" min="0" required>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12 d-flex justify-content-center">
                                                <div class="d-flex flex-wrap justify-content-center">
                                                    <button type="submit" form="acceptForm{{ $booking['id'] }}" class="btn btn-success mr-2 mb-2" style="width: 140px;">
                                                        <i class="fas fa-check"></i> Accept
                                                    </button>

                                                    <form action="{{ route('booking.update-status', $booking['id']) }}" method="POST" class="mr-2 mb-2" style="display: inline;">
                                                        @csrf
                                                        <input type="hidden" name="status" value="denied">
                                                        <button type="submit" class="btn btn-warning" style="width: 140px;">
                                                            <i class="fas fa-times"></i> Deny
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('booking.update-status', $booking['id']) }}" method="POST" class="mb-2" style="display: inline;">
                                                        @csrf
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="btn btn-danger" style="width: 140px;">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif ($currentStatus === 'accepted'): ?>
                                    <form action="{{ route('booking.update-status', $booking['id']) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check-circle"></i> Complete
                                        </button>
                                    </form>
                                <?php elseif ($currentStatus === 'denied'): ?>
                                    <span class="text-muted">Booking has been denied</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection
