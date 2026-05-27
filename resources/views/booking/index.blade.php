@extends('layouts.app')

@section('title', 'Bookings')

@section('css')
<style>
    /* Card Layouts & Containers */
    .booking-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    
    .booking-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
    }

    .booking-card .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    /* Filter Inputs */
    .form-group label {
        font-weight: 600;
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .form-control {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        color: #334155;
        height: auto;
        transition: all 0.2s ease;
        box-shadow: none !important;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }

    .btn-apply-filters {
        background-color: #0f172a;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        padding: 10px 20px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 1px;
    }

    .btn-apply-filters:hover {
        background-color: #1e293b;
        color: #ffffff;
        transform: translateY(-1px);
        text-decoration: none;
    }

    .btn-clear-filters {
        font-size: 13px;
        font-weight: 600;
        color: #ef4444;
        border: 1px dashed #fee2e2;
        background-color: #fef2f2;
        border-radius: 8px;
        padding: 8px 16px;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-clear-filters:hover {
        background-color: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
        text-decoration: none;
    }

    /* Premium Table Styling */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border: none;
    }

    .table th {
        font-weight: 700;
        font-size: 12px;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: #f8fafc;
        border-bottom: 2px solid #f1f5f9;
        border-top: none;
        padding: 16px 20px;
    }

    .table td {
        padding: 16px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        border-top: none;
        color: #334155;
        font-size: 14px;
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Table Actions Button */
    .btn-action-view {
        background-color: #eff6ff;
        color: #2563eb;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-action-view:hover {
        background-color: #2563eb;
        color: #ffffff;
        transform: scale(1.05);
    }

    /* Pastel Badges */
    .badge {
        padding: 6px 12px;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    .badge-success {
        background-color: #d1fae5 !important;
        color: #065f46 !important;
    }

    .badge-danger {
        background-color: #fee2e2 !important;
        color: #991b1b !important;
    }

    .badge-warning {
        background-color: #fef3c7 !important;
        color: #92400e !important;
    }

    .badge-info {
        background-color: #e0f2fe !important;
        color: #075985 !important;
    }

    .badge-dark {
        background-color: #f1f5f9 !important;
        color: #1e293b !important;
    }

    .badge-secondary {
        background-color: #fee2e2 !important; /* Cancelled badge red */
        color: #991b1b !important;
    }

    /* Pagination Styles */
    .pagination {
        margin: 0;
        gap: 4px;
    }

    .page-item .page-link {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #475569;
        font-weight: 600;
        font-size: 14px;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }

    .page-item.active .page-link {
        background-color: #0f172a;
        border-color: #0f172a;
        color: #ffffff;
    }

    .page-item .page-link:hover {
        background-color: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    /* Modal Enhancements */
    .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .modal-header {
        border-bottom: 1px solid #f1f5f9;
        padding: 24px;
        background-color: #ffffff;
    }

    .modal-title {
        font-weight: 800;
        color: #0f172a;
        font-size: 18px;
    }

    .modal-body {
        padding: 24px;
        background-color: #f8fafc;
    }

    .modal-footer {
        border-top: 1px solid #f1f5f9;
        padding: 20px 24px;
        background-color: #ffffff;
    }

    .modal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .modal-card-header {
        padding: 14px 20px;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modal-card-header.bg-primary {
        background-color: #eff6ff !important;
        color: #1e40af !important;
        border-bottom: 1px solid #dbeafe;
    }

    .modal-card-header.bg-success {
        background-color: #f0fdf4 !important;
        color: #166534 !important;
        border-bottom: 1px solid #dcfce7;
    }

    .modal-card-body {
        padding: 20px;
    }

    .modal-card-body p {
        margin-bottom: 10px;
        color: #334155;
        font-size: 13.5px;
    }

    .modal-card-body p:last-child {
        margin-bottom: 0;
    }

    .modal-card-body p strong {
        color: #64748b;
        font-weight: 600;
        margin-right: 6px;
        display: inline-block;
        width: 110px;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold" style="color: #0f172a; letter-spacing: -0.5px;">Bookings Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #64748b; font-weight: 500;">Home</a></li>
                    <li class="breadcrumb-item active" style="color: #0f172a; font-weight: 600;">Bookings</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filters Card -->
        <div class="card booking-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter text-muted mr-2"></i>Filters</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('booking.index') }}">
                    <div class="row align-items-end">
                        <!-- Status Filter -->
                        <div class="col-md-3 mb-3">
                            <div class="form-group mb-0">
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
                        <div class="col-md-2 mb-3">
                            <div class="form-group mb-0">
                                <label for="date_from">Date From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        
                        <!-- Date To -->
                        <div class="col-md-2 mb-3">
                            <div class="form-group mb-0">
                                <label for="date_to">Date To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        
                        <!-- Period Filter -->
                        <div class="col-md-3 mb-3">
                            <div class="form-group mb-0">
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
                        <div class="col-md-2 mb-3">
                            <button type="submit" class="btn-apply-filters w-100 justify-content-center">
                                <i class="fas fa-search"></i> Apply
                            </button>
                        </div>
                    </div>
                    
                    <!-- Clear Filters -->
                    @if(request()->has('status') || request()->has('date_from') || request()->has('date_to') || request()->has('period'))
                    <div class="row mt-2">
                        <div class="col-12">
                            <a href="{{ route('booking.index') }}" class="btn-clear-filters">
                                <i class="fas fa-times-circle"></i> Clear Filters
                            </a>
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>
        
        <!-- Bookings List Card -->
        <div class="card booking-card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-list text-muted mr-2"></i>Bookings List</h3>
                <span class="text-muted font-weight-bold" style="font-size: 13px;">Total Bookings: {{ $bookings->total() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
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
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td class="font-weight-bold" style="color: #0f172a;">#{{ $booking['id'] }}</td>
                                    <td class="font-weight-bold text-muted">{{ $booking['reference_number'] }}</td>
                                    <td>{{ $booking['customer_name'] }}</td>
                                    <td>{!! $booking['status_badge'] !!}</td>
                                    <td class="font-weight-bold">{{ $booking['date'] }}</td>
                                    <td class="text-muted">{{ \Carbon\Carbon::parse($booking['created_at'])->format('Y-m-d H:i') }}</td>
                                    <td><span class="badge badge-info">{{ $booking['time'] }}</span></td>
                                    <td class="font-weight-bold text-muted" style="font-size: 13px;">{{ $booking['vehicle'] }}</td>
                                    <td><span class="badge badge-dark">{{ $booking['services_count'] }}</span></td>
                                    <td class="text-right">
                                        <button type="button" class="btn-action-view" data-toggle="modal" data-target="#bookingModal{{ $booking['id'] }}" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-calendar-times fa-2x mb-2 text-muted"></i>
                                            <p class="mb-0">No bookings found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Card Footer Pagination -->
            @if($bookings->hasPages())
                <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3 px-4">
                    <div class="text-muted" style="font-size: 13.5px; font-weight: 500;">
                        Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} entries
                    </div>
                    <div>
                        {{ $bookings->links() }}
                    </div>
                </div>
            @endif
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
                    <h5 class="modal-title" id="bookingModalLabel{{ $booking['id'] }}">Booking Details #{{ $booking['id'] }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 24px; color: #64748b;">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="modal-card">
                                <div class="modal-card-header bg-primary">
                                    <i class="fas fa-info-circle mr-2"></i>Booking Information
                                </div>
                                <div class="modal-card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Booking ID:</strong> #{{ $booking['id'] }}</p>
                                            <p><strong>Ref Number:</strong> {{ $booking['reference_number'] }}</p>
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
                                    @if($booking['status'] === 'cancelled' && !empty($bookingDetails->cancellation_reason))
                                    <div class="row mt-3 pt-3 border-top" style="border-top-color: #fee2e2 !important; background-color: #fef2f2; border-radius: 8px; padding: 12px 16px; margin: 8px 0 0 0;">
                                        <div class="col-12">
                                            <p class="mb-0" style="color: #991b1b;"><strong style="width: auto; color: #991b1b;"><i class="fas fa-exclamation-triangle mr-1"></i> Cancellation Reason:</strong> {{ $bookingDetails->cancellation_reason }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="row mt-3 pt-3 border-top" style="border-top-color: #f1f5f9 !important;">
                                        <div class="col-12">
                                            <p><strong>Service Address:</strong> <span class="font-weight-bold"><?php echo $bookingDetails->address->location ?? 'N/A'; ?></span></p>
                                            <?php if($bookingDetails->address && $bookingDetails->address->additional_info): ?>
                                                <p><strong>Additional Info:</strong> <?php echo $bookingDetails->address->additional_info; ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="modal-card">
                                <div class="modal-card-header bg-success">
                                    <i class="fas fa-wrench mr-2"></i>Booking Services
                                </div>
                                <div class="modal-card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                                <tr style="background-color: #f8fafc;">
                                                    <th>Service Name</th>
                                                    <th>Description</th>
                                                    <th class="text-right">Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if($services->count() > 0): ?>
                                                    <?php foreach($services as $service): ?>
                                                        <tr>
                                                            <td><strong style="color: #0f172a;"><?php echo $service->subService->title ?? 'N/A'; ?></strong></td>
                                                            <td style="font-size: 13px; color: #64748b;"><?php echo $service->subService->description ?? 'N/A'; ?></td>
                                                            <td class="text-right font-weight-bold text-muted">-</td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center py-3 text-muted">No services found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr style="background-color: #f8fafc;">
                                                    <th colspan="2" class="text-right border-top-0">Grand Total:</th>
                                                    <th class="text-right text-success font-weight-bold border-top-0" style="font-size: 1.25em;">TZS <?php echo number_format($grandTotal, 2); ?></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-block">
                    <div class="w-100">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <?php 
                                $currentStatus = $booking['status'] ?? 'pending';
                                if ($currentStatus === 'pending'): ?>
                                    <div class="w-100">
                                        <form id="acceptForm{{ $booking['id'] }}" action="{{ route('booking.update-status', $booking['id']) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="accepted">
                                            <div class="row align-items-end">
                                                <div class="col-md-6 mb-2">
                                                    <label class="small font-weight-bold mb-1 text-muted">Confirm Price / Grand Total</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" style="font-size: 12px; font-weight: 700; background-color: #f1f5f9; border-color: #cbd5e1;">TZS</span>
                                                        </div>
                                                        <input type="number" name="grand_total" class="form-control" value="{{ $grandTotal }}" step="0.01" min="0" required style="padding: 6px 12px;">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2 d-flex">
                                                    <button type="submit" form="acceptForm{{ $booking['id'] }}" class="btn btn-success mr-2 font-weight-bold" style="border-radius: 8px; font-size: 13.5px; padding: 8px 16px;">
                                                        <i class="fas fa-check mr-1"></i> Accept
                                                    </button>
                                        </form>
                                                    <form action="{{ route('booking.update-status', $booking['id']) }}" method="POST" class="mr-2" style="display: inline;">
                                                        @csrf
                                                        <input type="hidden" name="status" value="denied">
                                                        <button type="submit" class="btn btn-warning font-weight-bold text-white" style="border-radius: 8px; font-size: 13.5px; padding: 8px 16px; background-color: #f59e0b; border-color: #f59e0b;">
                                                            <i class="fas fa-times mr-1"></i> Deny
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-danger font-weight-bold" data-toggle="modal" data-target="#cancelBookingModal{{ $booking['id'] }}" data-dismiss="modal" style="border-radius: 8px; font-size: 13.5px; padding: 8px 16px;">
                                                        <i class="fas fa-ban mr-1"></i> Cancel
                                                    </button>
                                                </div>
                                            </div>
                                    </div>
                                <?php elseif ($currentStatus === 'accepted'): ?>
                                    <form action="{{ route('booking.update-status', $booking['id']) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-primary font-weight-bold" style="border-radius: 8px; padding: 8px 20px;">
                                            <i class="fas fa-check-circle mr-1"></i> Complete Booking
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger font-weight-bold ml-2" data-toggle="modal" data-target="#cancelBookingModal{{ $booking['id'] }}" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px;">
                                        <i class="fas fa-ban mr-1"></i> Cancel Booking
                                    </button>
                                <?php elseif ($currentStatus === 'denied'): ?>
                                    <span class="text-muted font-weight-bold"><i class="fas fa-info-circle mr-1"></i> Booking has been denied</span>
                                <?php elseif ($currentStatus === 'cancelled'): ?>
                                    <span class="text-danger font-weight-bold"><i class="fas fa-ban mr-1"></i> Booking has been cancelled</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-right">
                                <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px;">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (in_array($currentStatus, ['pending', 'accepted']))
    <div class="modal fade" id="cancelBookingModal{{ $booking['id'] }}" tabindex="-1" role="dialog" aria-labelledby="cancelBookingModalLabel{{ $booking['id'] }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <form action="{{ route('booking.update-status', $booking['id']) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="cancelled">
                    <div class="modal-header bg-danger" style="background-color: #fee2e2 !important; border-bottom: 1px solid #fca5a5 !important; padding: 20px 24px;">
                        <h5 class="modal-title font-weight-bold" id="cancelBookingModalLabel{{ $booking['id'] }}" style="color: #991b1b; font-size: 16px;"><i class="fas fa-exclamation-triangle mr-2"></i>Cancel Booking #{{ $booking['id'] }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #991b1b; outline: none; opacity: 0.8; padding: 20px 24px; margin: -20px -24px -20px auto;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-white" style="padding: 24px;">
                        <p class="text-muted mb-3" style="font-size: 14px; line-height: 1.5;">Are you sure you want to cancel this booking? This action cannot be undone.</p>
                        <div class="form-group mb-0">
                            <label for="cancellation_reason_{{ $booking['id'] }}" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: block;">Reason for Cancellation <span class="text-danger">*</span></label>
                            <textarea name="cancellation_reason" id="cancellation_reason_{{ $booking['id'] }}" class="form-control" rows="3" required placeholder="Please write a reason for cancelling this booking..." style="background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; padding: 10px 14px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light" style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 8px;">
                        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal" style="border-radius: 8px; font-size: 13.5px; padding: 8px 16px; background-color: #f1f5f9; border: 1px solid #cbd5e1; color: #475569;">No, Keep Booking</button>
                        <button type="submit" class="btn btn-danger font-weight-bold" style="border-radius: 8px; font-size: 13.5px; padding: 8px 16px; background-color: #ef4444; border: none; color: #ffffff;">Confirm Cancellation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

@endsection
