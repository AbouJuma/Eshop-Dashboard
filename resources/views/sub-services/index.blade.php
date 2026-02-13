@extends('layouts.app')

@section('title', 'Sub Services')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Sub Services</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Services</a></li>
                    <li class="breadcrumb-item active">Sub Services</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Sub Services Management</h3>
                        <a href="{{ route('sub-services.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter Form -->
                        <form method="GET" action="{{ route('sub-services.index') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control" placeholder="Search by title or description..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="service_id" class="form-control">
                                        <option value="">All Services</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    <a href="{{ route('sub-services.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Sub Services Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subServices as $subService)
                                        <tr>
                                            <td>{{ $subService->id }}</td>
                                            <td>
                                                @if($subService->image)
                                                    <img src="{{ asset('storage/' . $subService->image) }}" 
                                                         alt="{{ $subService->title }}" 
                                                         class="img-thumbnail" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-wrench text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $subService->title }}</td>
                                            <td>
                                                @if($subService->service)
                                                    <span class="badge badge-info">{{ $subService->service->title }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subService->description)
                                                    {{ Str::limit($subService->description, 50) }}
                                                @else
                                                    <span class="text-muted">No description</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subService->serviceVehiclePrices && $subService->serviceVehiclePrices->isNotEmpty())
                                                    {{ number_format($subService->serviceVehiclePrices->min('price'), 0) }} - 
                                                    {{ number_format($subService->serviceVehiclePrices->max('price'), 0) }}
                                                @else
                                                    <span class="text-muted">No pricing</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subService->discount)
                                                    {{ $subService->discount }}%
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subService->status)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('sub-services.edit', $subService->id) }}" 
                                                       class="btn btn-sm btn-info" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('sub-services.destroy', $subService->id) }}" 
                                                          method="POST" 
                                                          style="display: inline-block;"
                                                          onsubmit="return confirm('Are you sure you want to delete this sub-service?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <div class="py-4">
                                                    <i class="fas fa-wrench fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No sub services found</h5>
                                                    <p class="text-muted">Get started by creating your first sub service.</p>
                                                    <a href="{{ route('sub-services.create') }}" class="btn btn-primary">
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
                            <div class="d-flex justify-content-center">
                                {{ $subServices->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.img-thumbnail {
    border-radius: 8px;
}
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
.badge {
    font-size: 0.75em;
}
</style>
@endpush
