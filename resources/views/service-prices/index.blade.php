@extends('layouts.app')

@section('title', 'Service Prices')

@section('css')
<style>
    /* Card Layouts & Containers */
    .prices-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    
    .prices-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .prices-card .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .btn-create-price {
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
    
    .btn-create-price:hover {
        background-color: #2563eb;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        text-decoration: none;
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
    }

    .btn-apply-filters:hover {
        background-color: #1e293b;
        color: #ffffff;
        transform: translateY(-1px);
        text-decoration: none;
    }

    .btn-clear-filters {
        font-size: 14px;
        font-weight: 600;
        color: #ef4444;
        border: 1px dashed #fee2e2;
        background-color: #fef2f2;
        border-radius: 8px;
        padding: 9px 18px;
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

    .badge-info {
        background-color: #e0f2fe !important;
        color: #075985 !important;
    }

    .badge-success {
        background-color: #d1fae5 !important;
        color: #065f46 !important;
    }

    .badge-danger {
        background-color: #fee2e2 !important;
        color: #991b1b !important;
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
        background-color: #ffffff;
    }

    .modal-footer {
        border-top: 1px solid #f1f5f9;
        padding: 20px 24px;
        background-color: #ffffff;
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
                <h1 class="m-0 font-weight-bold" style="color: #0f172a; letter-spacing: -0.5px;">Service Prices</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #64748b; font-weight: 500;">Home</a></li>
                    <li class="breadcrumb-item active" style="color: #0f172a; font-weight: 600;">Service Prices</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filters Card -->
        <div class="card prices-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter text-muted mr-2"></i>Filters</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('service-prices.index') }}">
                    <div class="row align-items-end">
                        <!-- Service Filter -->
                        <div class="col-md-3 mb-2">
                            <div class="form-group mb-0">
                                <label for="sub_service_id">Service</label>
                                <select name="sub_service_id" id="sub_service_id" class="form-control">
                                    <option value="">All Services</option>
                                    @foreach($subServices as $subService)
                                        <option value="{{ $subService->id }}" {{ request('sub_service_id') == $subService->id ? 'selected' : '' }}>
                                            {{ $subService->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Make Filter -->
                        <div class="col-md-3 mb-2">
                            <div class="form-group mb-0">
                                <label for="make_id">Make</label>
                                <select name="make_id" id="make_id" class="form-control">
                                    <option value="">All Makes</option>
                                    @foreach($makes as $make)
                                        <option value="{{ $make->id }}" {{ request('make_id') == $make->id ? 'selected' : '' }}>
                                            {{ $make->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Model Filter -->
                        <div class="col-md-3 mb-2">
                            <div class="form-group mb-0">
                                <label for="model_id">Model</label>
                                <select name="model_id" id="model_id" class="form-control">
                                    <option value="">All Models</option>
                                    @foreach($models as $model)
                                        <option value="{{ $model->id }}" {{ request('model_id') == $model->id ? 'selected' : '' }} data-make-id="{{ $model->make_id }}">
                                            {{ $model->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="col-md-3 mb-2 d-flex">
                            <button type="submit" class="btn-apply-filters mr-2 flex-grow-1 justify-content-center">
                                <i class="fas fa-search"></i> Apply
                            </button>
                            <a href="{{ route('service-prices.index') }}" class="btn-clear-filters">
                                <i class="fas fa-times-circle"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Service Prices List Card -->
        <div class="card prices-card mt-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tags text-muted mr-2"></i>Service Prices List</h3>
                <button type="button" class="btn-create-price" data-toggle="modal" data-target="#addPriceModal">
                    <i class="fas fa-plus"></i> Add Price
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Year Range</th>
                                <th>Base Price</th>
                                <th>Discount</th>
                                <th>Final Price</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($servicePrices as $price)
                                <tr>
                                    <td class="font-weight-bold">#{{ $price->id }}</td>
                                    <td class="font-weight-bold" style="color: #0f172a;">{{ $price->subService ? $price->subService->title : 'N/A' }}</td>
                                    <td><span class="badge badge-info">{{ $price->make ? $price->make->name : 'N/A' }}</span></td>
                                    <td class="font-weight-bold text-muted">{{ $price->model ? $price->model->name : 'N/A' }}</td>
                                    <td class="text-muted">{{ $price->year_from }} - {{ $price->year_to }}</td>
                                    <td class="font-weight-bold text-muted">TZS {{ number_format($price->price, 2) }}</td>
                                    <td>
                                        @if($price->discount)
                                            <span class="badge badge-danger">{{ $price->discount }}% OFF</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold text-success" style="font-size: 15px;">
                                        TZS {{ number_format($price->price - ($price->price * ($price->discount ?? 0) / 100), 2) }}
                                    </td>
                                    <td class="text-right">
                                        <button type="button" class="btn-action-edit mr-1" data-toggle="modal" data-target="#editPriceModal{{ $price->id }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('service-prices.destroy', $price->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-delete" onclick="return confirm('Are you sure you want to delete this price?')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-tags fa-2x mb-2 text-muted"></i>
                                            <p class="mb-0">No service prices found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Card Footer Pagination -->
            @if($servicePrices->hasPages())
                <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3 px-4">
                    <div class="text-muted" style="font-size: 13.5px; font-weight: 500;">
                        Showing {{ $servicePrices->firstItem() }} to {{ $servicePrices->lastItem() }} of {{ $servicePrices->total() }} entries
                    </div>
                    <div>
                        {{ $servicePrices->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Add Price Modal -->
<div class="modal fade" id="addPriceModal" tabindex="-1" role="dialog" aria-labelledby="addPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPriceModalLabel">Add Service Price</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="font-size: 24px; color: #64748b;">&times;</span>
                </button>
            </div>
            <form action="{{ route('service-prices.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sub_service_id_add">Service</label>
                        <select name="sub_service_id" id="sub_service_id_add" class="form-control" required>
                            <option value="">Select Service</option>
                            @foreach($subServices as $subService)
                                <option value="{{ $subService->id }}">{{ $subService->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="make_id_add">Make</label>
                        <select name="make_id" id="make_id_add" class="form-control" required>
                            <option value="">Select Make</option>
                            @foreach($makes as $make)
                                <option value="{{ $make->id }}">{{ $make->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="model_id_add">Model</label>
                        <select name="model_id" id="model_id_add" class="form-control" required>
                            <option value="">Select Model</option>
                            @foreach($models as $model)
                                <option value="{{ $model->id }}" data-make-id="{{ $model->make_id }}">
                                    {{ $model->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="year_from">Year From</label>
                                <input type="number" name="year_from" id="year_from" class="form-control" min="1900" max="{{ date('Y') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="year_to">Year To</label>
                                <input type="number" name="year_to" id="year_to" class="form-control" min="1900" max="{{ date('Y') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Price (TZS)</label>
                                <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="discount">Discount (%)</label>
                                <input type="number" name="discount" id="discount" class="form-control" step="0.01" min="0" max="100" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" style="border-radius: 8px; background-color: #3b82f6; border: none;">Add Price</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modals for Each Price -->
@foreach($servicePrices as $price)
<div class="modal fade" id="editPriceModal{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="editPriceModalLabel{{ $price->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPriceModalLabel{{ $price->id }}">Edit Service Price</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="font-size: 24px; color: #64748b;">&times;</span>
                </button>
            </div>
            <form action="{{ route('service-prices.update', $price->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sub_service_id_edit_{{ $price->id }}">Service</label>
                        <select name="sub_service_id" id="sub_service_id_edit_{{ $price->id }}" class="form-control" required>
                            <option value="">Select Service</option>
                            @foreach($subServices as $subService)
                                <option value="{{ $subService->id }}" {{ $price->sub_service_id == $subService->id ? 'selected' : '' }}>
                                    {{ $subService->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="make_id_edit_{{ $price->id }}">Make</label>
                        <select name="make_id" id="make_id_edit_{{ $price->id }}" class="form-control" required>
                            <option value="">Select Make</option>
                            @foreach($makes as $make)
                                <option value="{{ $make->id }}" {{ $price->make_id == $make->id ? 'selected' : '' }}>
                                    {{ $make->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="model_id_edit_{{ $price->id }}">Model</label>
                        <select name="model_id" id="model_id_edit_{{ $price->id }}" class="form-control" required>
                            <option value="">Select Model</option>
                            @foreach($models as $model)
                                <option value="{{ $model->id }}" {{ $price->model_id == $model->id ? 'selected' : '' }} data-make-id="{{ $model->make_id }}">
                                    {{ $model->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="year_from_edit_{{ $price->id }}">Year From</label>
                                <input type="number" name="year_from" id="year_from_edit_{{ $price->id }}" class="form-control" min="1900" max="{{ date('Y') }}" value="{{ $price->year_from }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="year_to_edit_{{ $price->id }}">Year To</label>
                                <input type="number" name="year_to" id="year_to_edit_{{ $price->id }}" class="form-control" min="1900" max="{{ date('Y') }}" value="{{ $price->year_to }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price_edit_{{ $price->id }}">Price (TZS)</label>
                                <input type="number" name="price" id="price_edit_{{ $price->id }}" class="form-control" step="0.01" min="0" value="{{ $price->price }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="discount_edit_{{ $price->id }}">Discount (%)</label>
                                <input type="number" name="discount" id="discount_edit_{{ $price->id }}" class="form-control" step="0.01" min="0" max="100" value="{{ $price->discount ?? 0 }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" style="border-radius: 8px; background-color: #3b82f6; border: none;">Update Price</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('javascript')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter form functionality
    const filterMakeSelect = document.getElementById('make_id');
    const filterModelSelect = document.getElementById('model_id');
    
    // Store original model options
    const originalModelOptions = Array.from(filterModelSelect.options);
    
    // Function to filter models by make
    function filterModelsByMake(makeId, modelSelect) {
        // Clear current options
        modelSelect.innerHTML = '';
        
        // Add "All Models" option
        const allOption = document.createElement('option');
        allOption.value = '';
        allOption.textContent = modelSelect.id === 'model_id' ? 'All Models' : 'Select Model';
        modelSelect.appendChild(allOption);
        
        if (makeId) {
            // Filter and add models for selected make
            originalModelOptions.forEach(option => {
                if (option.value === '' || option.dataset.makeId == makeId) {
                    modelSelect.appendChild(option.cloneNode(true));
                }
            });
        } else {
            // Add all models
            originalModelOptions.forEach(option => {
                modelSelect.appendChild(option.cloneNode(true));
            });
        }
    }
    
    // Event listener for filter form make change
    filterMakeSelect.addEventListener('change', function() {
        filterModelsByMake(this.value, filterModelSelect);
    });
    
    // Initialize filter form on page load
    if (filterMakeSelect.value) {
        filterModelsByMake(filterMakeSelect.value, filterModelSelect);
    }
    
    // Modal functionality for add/edit forms
    function setupModalFiltering(modalElement) {
        const modalMakeSelect = modalElement.querySelector('[id^="make_id"]:not(#make_id)');
        const modalModelSelect = modalElement.querySelector('[id^="model_id"]:not(#model_id)');
        
        if (modalMakeSelect && modalModelSelect) {
            // Store original model options for this modal
            const modalOriginalOptions = Array.from(modalModelSelect.options);
            
            modalMakeSelect.addEventListener('change', function() {
                // For modals, we filter using the original options
                modalModelSelect.innerHTML = '';
                const selectOption = document.createElement('option');
                selectOption.value = '';
                selectOption.textContent = 'Select Model';
                modalModelSelect.appendChild(selectOption);
                
                modalOriginalOptions.forEach(option => {
                    if (option.value !== '' && option.dataset.makeId == this.value) {
                        modalModelSelect.appendChild(option.cloneNode(true));
                    }
                });
            });
        }
    }
    
    // Setup filtering for add modal
    const addModal = document.getElementById('addPriceModal');
    if (addModal) {
        setupModalFiltering(addModal);
    }
    
    // Setup filtering for all edit modals
    document.querySelectorAll('[id^="editPriceModal"]').forEach(modal => {
        setupModalFiltering(modal);
    });
});
</script>
@endsection
