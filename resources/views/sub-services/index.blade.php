@extends('layouts.app')

@section('title', 'Sub Services')

@section('css')
<style>
    /* Card Layouts & Containers */
    .subservices-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    
    .subservices-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .subservices-card .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .btn-create-subservice {
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
    
    .btn-create-subservice:hover {
        background-color: #2563eb;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        text-decoration: none;
    }

    /* Filter Inputs */
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

    .btn-search-subservices {
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
        height: 100%;
    }

    .btn-search-subservices:hover {
        background-color: #1e293b;
        color: #ffffff;
        text-decoration: none;
    }

    .btn-clear-subservices {
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-clear-subservices:hover {
        background-color: #f1f5f9;
        color: #0f172a;
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

    /* Table Actions Buttons */
    .btn-action-edit {
        background-color: #eff6ff;
        color: #2563eb;
        border: none;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-action-edit:hover {
        background-color: #2563eb;
        color: #ffffff;
    }

    .btn-action-delete {
        background-color: #fef2f2;
        color: #dc2626;
        border: none;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-action-delete:hover {
        background-color: #dc2626;
        color: #ffffff;
    }

    /* Thumbnail Styling */
    .subservice-thumbnail {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .subservice-placeholder-thumbnail {
        width: 48px;
        height: 48px;
        background-color: #f1f5f9;
        color: #64748b;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
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

    .badge-info {
        background-color: #e0f2fe !important;
        color: #075985 !important;
    }

    .badge-secondary {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
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
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold" style="color: #0f172a; letter-spacing: -0.5px;">Sub Services</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #64748b; font-weight: 500;">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('services.index') }}" style="color: #64748b; font-weight: 500;">Services</a></li>
                    <li class="breadcrumb-item active" style="color: #0f172a; font-weight: 600;">Sub Services</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="background-color: #d1fae5; color: #065f46; border-radius: 12px; padding: 16px 20px;">
                <button type="button" class="close" data-dismiss="alert" style="color: #065f46; opacity: 0.8; outline: none;">&times;</button>
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card subservices-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tasks text-muted mr-2"></i>Sub Services Management</h3>
                        <a href="{{ route('sub-services.create') }}" class="btn-create-subservice">
                            <i class="fas fa-plus"></i> Create New
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter Form -->
                        <form method="GET" action="{{ route('sub-services.index') }}" class="mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-2">
                                    <input type="text" name="search" class="form-control" placeholder="Search by title or description..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <select name="service_id" class="form-control">
                                        <option value="">All Primary Services</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2 d-flex">
                                    <button type="submit" class="btn-search-subservices mr-2">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    <a href="{{ route('sub-services.index') }}" class="btn-clear-subservices">
                                        <i class="fas fa-times-circle"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Sub Services Table -->
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Service</th>
                                        <th>Description</th>
                                        <th>Price Range</th>
                                        <th>Discount</th>
                                        <th>Status</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subServices as $subService)
                                        <tr>
                                            <td class="font-weight-bold">#{{ $subService->id }}</td>
                                            <td>
                                                @if($subService->image)
                                                    <img src="{{ asset('storage/' . $subService->image) }}" 
                                                         alt="{{ $subService->title }}" 
                                                         class="subservice-thumbnail">
                                                @else
                                                    <div class="subservice-placeholder-thumbnail">
                                                        <i class="fas fa-wrench"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="font-weight-bold" style="color: #0f172a;">{{ $subService->title }}</td>
                                            <td>
                                                @if($subService->service)
                                                    <span class="badge badge-info">{{ $subService->service->title }}</span>
                                                @endif
                                            </td>
                                            <td class="text-muted" style="max-width: 250px;">
                                                @if($subService->description)
                                                    {{ Str::limit($subService->description, 60) }}
                                                @else
                                                    <span class="text-muted" style="font-size: 13px;">No description</span>
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-success">
                                                @if($subService->serviceVehiclePrices && $subService->serviceVehiclePrices->isNotEmpty())
                                                    TZS {{ number_format($subService->serviceVehiclePrices->min('price'), 0) }} - 
                                                    {{ number_format($subService->serviceVehiclePrices->max('price'), 0) }}
                                                @else
                                                    <span class="text-muted" style="font-size: 13px; font-weight: normal;">No pricing</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subService->discount)
                                                    <span class="badge badge-danger">{{ $subService->discount }}% OFF</span>
                                                @else
                                                    <span class="text-muted" style="font-size: 13px;">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subService->status)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('sub-services.edit', $subService->id) }}" 
                                                   class="btn-action-edit mr-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('sub-services.destroy', $subService->id) }}" 
                                                      method="POST" 
                                                      style="display: inline-block;"
                                                      onsubmit="return confirm('Are you sure you want to delete this sub-service?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action-delete" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <div class="py-4 text-muted">
                                                    <i class="fas fa-wrench fa-3x mb-3 text-muted" style="color: #cbd5e1 !important;"></i>
                                                    <h5 class="font-weight-bold text-dark">No Sub Services Found</h5>
                                                    <p class="text-muted mb-4">Get started by creating your first sub service catalog item.</p>
                                                    <a href="{{ route('sub-services.create') }}" class="btn-create-subservice">
                                                        <i class="fas fa-plus"></i> Create Sub Service
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($subServices->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top" style="border-top-color: #f1f5f9 !important;">
                                <div class="text-muted" style="font-size: 13.5px; font-weight: 500;">
                                    Showing {{ $subServices->firstItem() }} to {{ $subServices->lastItem() }} of {{ $subServices->total() }} entries
                                </div>
                                <div>
                                    {{ $subServices->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
