@extends('layouts.app')

@section('title', 'Car Maintenance')

@section('css')
<style>
    .maintenance-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    
    .maintenance-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .maintenance-card .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .nav-tabs-custom {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 24px;
        border: none;
    }

    .nav-tabs-custom > .nav-tabs {
        border-bottom: 1px solid #f1f5f9;
        background-color: #f8fafc;
        margin: 0;
        padding: 0 10px;
    }

    .nav-tabs-custom > .nav-tabs > li {
        margin-bottom: -1px;
        margin-right: 5px;
    }

    .nav-tabs-custom > .nav-tabs > li > a {
        color: #64748b;
        font-weight: 600;
        padding: 15px 20px;
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        background: transparent;
        transition: all 0.2s ease;
    }

    .nav-tabs-custom > .nav-tabs > li > a:hover {
        color: #0f172a;
        background: transparent;
        border-bottom-color: #e2e8f0;
    }

    .nav-tabs-custom > .nav-tabs > li.active > a,
    .nav-tabs-custom > .nav-tabs > li.active:hover > a {
        color: #3b82f6;
        background: transparent;
        border-bottom: 3px solid #3b82f6;
        border-top: none;
        border-left: none;
        border-right: none;
    }

    .nav-tabs-custom > .tab-content {
        padding: 24px;
    }

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

    .btn-action-primary {
        background-color: #3b82f6;
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
    }

    .btn-action-primary:hover {
        background-color: #2563eb;
        color: #ffffff;
        transform: translateY(-1px);
        text-decoration: none;
    }

    .btn-action-pdf {
        background-color: #ef4444;
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
    }

    .btn-action-pdf:hover {
        background-color: #dc2626;
        color: #ffffff;
        transform: translateY(-1px);
        text-decoration: none;
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
        color: #64748b;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        border-radius: 8px;
        padding: 10px 20px;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-clear-filters:hover {
        background-color: #e2e8f0;
        color: #334155;
        text-decoration: none;
    }

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

    .badge-service-type {
        font-size: 11px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
    }

    .badge-normal {
        background-color: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .badge-spares {
        background-color: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }

    .badge-both {
        background-color: #faf5ff;
        color: #7c3aed;
        border: 1px solid #e9d5ff;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 4px;
        transition: all 0.2s ease;
        border: none;
    }

    .action-btn-view {
        background-color: #ecfdf5;
        color: #059669;
    }
    .action-btn-view:hover {
        background-color: #059669;
        color: #ffffff;
    }

    .action-btn-print {
        background-color: #fff7ed;
        color: #ea580c;
    }
    .action-btn-print:hover {
        background-color: #ea580c;
        color: #ffffff;
    }

    .action-btn-edit {
        background-color: #eff6ff;
        color: #2563eb;
    }
    .action-btn-edit:hover {
        background-color: #2563eb;
        color: #ffffff;
    }

    .action-btn-delete {
        background-color: #fef2f2;
        color: #ef4444;
    }
    .action-btn-delete:hover {
        background-color: #ef4444;
        color: #ffffff;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 16px;
        border: none;
        overflow: hidden;
    }

    .modal-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        padding: 18px 24px;
    }

    .modal-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 16px;
    }

    .modal-body {
        padding: 24px;
    }
</style>
@endsection

@section('content')
<div class="content-header no-print">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold" style="color: #0f172a; letter-spacing: -0.5px;">Car Maintenance <small>Track services, spare parts and history</small></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #64748b; font-weight: 500;">Home</a></li>
                    <li class="breadcrumb-item active" style="color: #0f172a; font-weight: 600;">Car Maintenance</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    
    <!-- Custom Navigation Tabs -->
    <div class="nav-tabs-custom no-print">
        <ul class="nav nav-tabs">
            <li class="{{ empty(request('tab')) || request('tab') == 'vehicles' ? 'active' : '' }}">
                <a href="#vehicles-tab" data-toggle="tab"><i class="fa fas fa-car"></i> Registered Vehicles</a>
            </li>
            <li class="{{ request('tab') == 'history' ? 'active' : '' }}">
                <a href="#history-tab" data-toggle="tab"><i class="fa fas fa-history"></i> Service Logs</a>
            </li>
        </ul>
        
        <div class="tab-content">
            <!-- REGISTERED VEHICLES TAB -->
            <div class="tab-pane {{ empty(request('tab')) || request('tab') == 'vehicles' ? 'active' : '' }}" id="vehicles-tab">
                <div class="maintenance-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fas fa-car"></i> Registered Vehicles Database</h3>
                        <div class="text-right no-print">
                            <a href="{{ route('car-maintenance.create') }}" class="btn-action-primary"><i class="fa fas fa-plus-circle"></i> Register New Car</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="vehicles_table">
                            <thead>
                                <tr>
                                    <th>Plate Number</th>
                                    <th>Chassis Number</th>
                                    <th>Brand / Model</th>
                                    <th>Owner Info</th>
                                    <th>Total Services</th>
                                    <th class="no-print">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cars as $car)
                                    <tr>
                                        <td><strong style="font-size: 15px;">{{ strtoupper($car->car_plate_number) }}</strong></td>
                                        <td>
                                            @if($car->car_chassis_number)
                                                <code style="font-size: 12px; font-weight: 700; color: #475569;">{{ strtoupper($car->car_chassis_number) }}</code>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($car->car_brand)
                                                {{ $car->car_brand }} {{ $car->car_model }} {{ $car->car_year ? '('.$car->car_year.')' : '' }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $car->owner_name }}</strong>
                                            @if($car->owner_phone) <br><small class="text-muted"><i class="fa fas fa-phone"></i> {{ $car->owner_phone }}</small> @endif
                                            @if($car->owner_email) | <small class="text-muted"><i class="fa fas fa-envelope"></i> {{ $car->owner_email }}</small> @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary" style="background-color: #3b82f6;">{{ $car->maintenances_count }} services</span>
                                        </td>
                                        <td class="no-print" style="white-space: nowrap;">
                                            <a href="{{ route('car-maintenance.create', ['car_id' => $car->id]) }}" class="btn btn-xs btn-primary" style="border-radius: 6px; font-weight: 600; padding: 6px 12px; background-color: #3b82f6; border: none;">
                                                <i class="fa fas fa-plus"></i> Add Service Log
                                            </a>
                                            <a href="{{ route('car-maintenance.index', ['car_plate_number' => $car->car_plate_number, 'tab' => 'history']) }}" class="btn btn-xs btn-info" style="border-radius: 6px; font-weight: 600; padding: 6px 12px; background-color: #0f172a; border: none; margin-left: 5px;">
                                                <i class="fa fas fa-history"></i> History
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted" style="padding: 30px;">
                                            <i class="fa fas fa-car-crash" style="font-size: 30px; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
                                            No registered vehicles found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SERVICE LOGS TAB -->
            <div class="tab-pane {{ request('tab') == 'history' ? 'active' : '' }}" id="history-tab">
                <!-- Filters Card -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <h4 style="margin-top: 0; margin-bottom: 15px; font-size: 14px; font-weight: 700; color: #334155;"><i class="fa fas fa-filter"></i> Search Logs</h4>
                    <form id="filter-form" method="GET" action="{{ route('car-maintenance.index') }}">
                        <input type="hidden" name="tab" value="history">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="car_plate_number">Plate Number</label>
                                    <input type="text" name="car_plate_number" id="car_plate_number" class="form-control" value="{{ request('car_plate_number') }}" placeholder="e.g. TX-123-AB">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="owner_name">Owner Name</label>
                                    <input type="text" name="owner_name" id="owner_name" class="form-control" value="{{ request('owner_name') }}" placeholder="e.g. John Doe">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="service_type">Service Type</label>
                                    <select name="service_type" id="service_type" class="form-control">
                                        <option value="">All</option>
                                        <option value="normal" {{ request('service_type') == 'normal' ? 'selected' : '' }}>Normal Maintenance</option>
                                        <option value="spares" {{ request('service_type') == 'spares' ? 'selected' : '' }}>Spares Changed</option>
                                        <option value="both" {{ request('service_type') == 'both' ? 'selected' : '' }}>Both</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn-apply-filters"><i class="fa fas fa-search"></i> Search</button>
                                <a href="{{ route('car-maintenance.index', ['tab' => 'history']) }}" class="btn-clear-filters"><i class="fa fas fa-sync"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="maintenance-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fas fa-list"></i> Maintenance History</h3>
                        <div class="text-right no-print">
                            <button type="button" id="btn-export-pdf" class="btn-action-pdf"><i class="fa fas fa-file-pdf"></i> Export PDF Report</button>
                            <a href="{{ route('car-maintenance.create') }}" class="btn-action-primary"><i class="fa fas fa-plus-circle"></i> Add Service Record</a>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="history_table">
                            <thead>
                                <tr>
                                    <th>Plate No</th>
                                    <th>Car Description</th>
                                    <th>Owner</th>
                                    <th>Service Date</th>
                                    <th>Kilometers</th>
                                    <th>Next Service At</th>
                                    <th>Type</th>
                                    <th>Cost</th>
                                    <th class="no-print">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($maintenances as $item)
                                    <tr>
                                        <td><strong>{{ strtoupper($item->car->car_plate_number) }}</strong></td>
                                        <td>
                                            @if($item->car->car_brand)
                                                {{ $item->car->car_brand }} {{ $item->car->car_model }} {{ $item->car->car_year ? '('.$item->car->car_year.')' : '' }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $item->car->owner_name }}</div>
                                            @if($item->car->owner_phone)
                                                <small class="text-muted"><i class="fa fas fa-phone"></i> {{ $item->car->owner_phone }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->service_date ? $item->service_date->format('Y-m-d') : 'N/A' }}</td>
                                        <td>
                                            @if($item->service_type == 'spares' && is_null($item->serviced_kilometer))
                                                <span class="text-muted">N/A (Spares Only)</span>
                                            @else
                                                {{ number_format($item->serviced_kilometer) }} km
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->service_type == 'spares' && is_null($item->next_service_kilometer))
                                                <span class="text-muted">N/A (Spares Only)</span>
                                            @else
                                                <strong class="text-primary">{{ number_format($item->next_service_kilometer) }} km</strong>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->service_type == 'normal')
                                                <span class="badge-service-type badge-normal">Normal</span>
                                            @elseif($item->service_type == 'spares')
                                                <span class="badge-service-type badge-spares">Spares</span>
                                            @else
                                                <span class="badge-service-type badge-both">Both</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ @num_format($item->cost) }}</strong>
                                        </td>
                                        <td class="no-print" style="white-space: nowrap;">
                                            <button type="button" class="action-btn action-btn-view btn-view-details" 
                                                data-id="{{ $item->id }}" 
                                                data-plate="{{ $item->car->car_plate_number }}"
                                                data-chassis="{{ $item->car->car_chassis_number ?? 'N/A' }}"
                                                data-car="{{ $item->car->car_brand }} {{ $item->car->car_model }} {{ $item->car->car_year }}"
                                                data-owner="{{ $item->car->owner_name }}"
                                                data-phone="{{ $item->car->owner_phone }}"
                                                data-email="{{ $item->car->owner_email }}"
                                                data-date="{{ $item->service_date ? $item->service_date->format('Y-m-d') : '' }}"
                                                data-km="{{ $item->serviced_kilometer ?? 'N/A' }}"
                                                data-next-km="{{ $item->next_service_kilometer ?? 'N/A' }}"
                                                data-type="{{ $item->service_type }}"
                                                data-cost="{{ $item->cost }}"
                                                data-details="{{ $item->details }}"
                                                title="View Details">
                                                <i class="fa fas fa-eye"></i>
                                            </button>

                                            <a href="{{ route('car-maintenance.print-label', $item->id) }}" target="_blank" class="action-btn action-btn-print" title="Print Sticker Label">
                                                <i class="fa fas fa-print"></i>
                                            </a>

                                            <a href="{{ route('car-maintenance.edit', $item->id) }}" class="action-btn action-btn-edit" title="Edit">
                                                <i class="fa fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('car-maintenance.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this maintenance record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn action-btn-delete" title="Delete">
                                                    <i class="fa fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted" style="padding: 30px;">
                                            <i class="fa fas fa-car-crash" style="font-size: 30px; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
                                            No car maintenance records found matching filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Detail view Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="detailsModalLabel">Service History Details</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6" style="border-right: 1px solid #f1f5f9;">
                        <h4 style="margin-top:0; margin-bottom: 15px; font-weight:700; color:#3b82f6;"><i class="fa fas fa-car"></i> Vehicle Info</h4>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th style="width: 40%">Plate Number</th>
                                <td id="modal-plate" style="font-weight:700;"></td>
                            </tr>
                            <tr>
                                <th>Chassis Number</th>
                                <td id="modal-chassis" style="font-weight:700; color:#475569;"></td>
                            </tr>
                            <tr>
                                <th>Brand / Model</th>
                                <td id="modal-car"></td>
                            </tr>
                            <tr>
                                <th>Owner Name</th>
                                <td id="modal-owner" style="font-weight: 600;"></td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td id="modal-phone"></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td id="modal-email"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4 style="margin-top:0; margin-bottom: 15px; font-weight:700; color:#ea580c;"><i class="fa fas fa-tools"></i> Service Details</h4>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th style="width: 40%">Service Date</th>
                                <td id="modal-date"></td>
                            </tr>
                            <tr>
                                <th>Kilometers at Service</th>
                                <td id="modal-km"></td>
                            </tr>
                            <tr>
                                <th>Next Service Kilometers</th>
                                <td id="modal-next-km" style="font-weight:700; color:#2563eb;"></td>
                            </tr>
                            <tr>
                                <th>Service Type</th>
                                <td id="modal-type"></td>
                            </tr>
                            <tr>
                                <th>Total Cost</th>
                                <td id="modal-cost" style="font-weight: 700; color: #059669;"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <h4 style="font-weight:700; color:#0f172a;"><i class="fa fas fa-clipboard-list"></i> Maintenance & Spares Description</h4>
                        <div id="modal-details" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:15px; font-size:14px; color:#334155; min-height:80px; white-space: pre-wrap;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #f1f5f9;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Open details modal and populate fields
    $('.btn-view-details').click(function() {
        var btn = $(this);
        $('#modal-plate').text(btn.data('plate').toUpperCase());
        $('#modal-chassis').text(btn.data('chassis') || 'N/A');
        $('#modal-car').text(btn.data('car'));
        $('#modal-owner').text(btn.data('owner'));
        $('#modal-phone').text(btn.data('phone') || 'N/A');
        $('#modal-email').text(btn.data('email') || 'N/A');
        $('#modal-date').text(btn.data('date'));
        
        var km = btn.data('km');
        var nextKm = btn.data('next-km');
        
        $('#modal-km').text(km === 'N/A' || km === '' ? 'N/A (Spares Only)' : parseInt(km).toLocaleString() + ' km');
        $('#modal-next-km').text(nextKm === 'N/A' || nextKm === '' ? 'N/A (Spares Only)' : parseInt(nextKm).toLocaleString() + ' km');
        
        var type = btn.data('type');
        var typeHtml = '';
        if(type === 'normal') {
            typeHtml = '<span class="badge-service-type badge-normal">Normal Maintenance</span>';
        } else if(type === 'spares') {
            typeHtml = '<span class="badge-service-type badge-spares">Spares Changed</span>';
        } else {
            typeHtml = '<span class="badge-service-type badge-both">Both</span>';
        }
        $('#modal-type').html(typeHtml);
        
        // Formatted cost
        var cost = parseFloat(btn.data('cost'));
        $('#modal-cost').text(cost.toFixed(2));
        
        $('#modal-details').text(btn.data('details') || 'No description written.');
        
        $('#detailsModal').modal('show');
    });

    // Handle export pdf with current filters
    $('#btn-export-pdf').click(function(e) {
        e.preventDefault();
        var params = $('#filter-form').serialize();
        window.open("{{ route('car-maintenance.export-pdf') }}?" + params, '_blank');
    });

    // Initialize DataTables
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#vehicles_table').DataTable({
            columnDefs: [ {
                "targets": 5,
                "orderable": false,
                "searchable": false
            } ],
            order: [[0, 'asc']]
        });
        $('#history_table').DataTable({
            columnDefs: [ {
                "targets": 8,
                "orderable": false,
                "searchable": false
            } ],
            order: [[3, 'desc']]
        });
    }
});
</script>
@endsection
