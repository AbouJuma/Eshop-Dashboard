@extends('layouts.app')

@section('title', 'Service Prices')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Service Prices</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Service Prices</li>
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
                <form method="GET" action="{{ route('service-prices.index') }}">
                    <div class="row">
                        <!-- Service Filter -->
                        <div class="col-md-3">
                            <div class="form-group">
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
                        <div class="col-md-3">
                            <div class="form-group">
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
                        <div class="col-md-3">
                            <div class="form-group">
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
                        
                        <!-- Submit Button -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <a href="{{ route('service-prices.index') }}" class="btn btn-secondary btn-sm ml-2">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
        // Filter form make-model functionality
        document.addEventListener('DOMContentLoaded', function() {
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
        });
        </script>
        
        <!-- Service Prices Table -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Service Prices List ({{ $servicePrices->total() }} total prices)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addPriceModal">
                        <i class="fas fa-plus"></i> Add Price
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Year Range</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Final Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($servicePrices as $price)
                                <tr>
                                    <td>{{ $price->id }}</td>
                                    <td>{{ $price->subService ? $price->subService->title : 'N/A' }}</td>
                                    <td>{{ $price->make ? $price->make->name : 'N/A' }}</td>
                                    <td>{{ $price->model ? $price->model->name : 'N/A' }}</td>
                                    <td>{{ $price->year_from }} - {{ $price->year_to }}</td>
                                    <td>{{ number_format($price->price, 2) }}</td>
                                    <td>{{ $price->discount ? $price->discount . '%' : '-' }}</td>
                                    <td class="font-weight-bold">
                                        {{ number_format($price->price - ($price->price * ($price->discount ?? 0) / 100), 2) }}
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editPriceModal{{ $price->id }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('service-prices.destroy', $price->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No service prices found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $servicePrices->firstItem() }} to {{ $servicePrices->lastItem() }} of {{ $servicePrices->total() }} entries
                    </div>
                    <div>
                        {{ $servicePrices->links() }}
                    </div>
                </div>
            </div>
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
                    <span aria-hidden="true">&times;</span>
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
                                <label for="price">Price</label>
                                <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="discount">Discount (%)</label>
                                <input type="number" name="discount" id="discount" class="form-control" step="0.01" min="0" max="100">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Price</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modals for Each Price -->
@forelse($servicePrices as $price)
<div class="modal fade" id="editPriceModal{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="editPriceModalLabel{{ $price->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPriceModalLabel{{ $price->id }}">Edit Service Price</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
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
                                <label for="price_edit_{{ $price->id }}">Price</label>
                                <input type="number" name="price" id="price_edit_{{ $price->id }}" class="form-control" step="0.01" min="0" value="{{ $price->price }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="discount_edit_{{ $price->id }}">Discount (%)</label>
                                <input type="number" name="discount" id="discount_edit_{{ $price->id }}" class="form-control" step="0.01" min="0" max="100" value="{{ $price->discount }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Price</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

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
                filterModelsByMake(this.value, modalModelSelect);
            });
            
            // Initialize modal on show
            modalElement.addEventListener('show.bs.modal', function() {
                if (modalMakeSelect.value) {
                    filterModelsByMake(modalMakeSelect.value, modalModelSelect);
                }
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
