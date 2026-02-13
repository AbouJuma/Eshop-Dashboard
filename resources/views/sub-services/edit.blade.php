@extends('layouts.app')

@section('title', 'Edit Sub Service')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Sub Service</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Services</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sub-services.index') }}">Sub Services</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Sub Service: {{ $subService->title }}</h3>
                        <a href="{{ route('sub-services.index') }}" class="btn btn-secondary float-right">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('sub-services.update', $subService->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Basic Information -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ $subService->title }}" required>
                                        @error('title')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service_id">Service <span class="text-danger">*</span></label>
                                        <select class="form-control @error('service_id') is-invalid @enderror" 
                                                id="service_id" name="service_id" required>
                                            <option value="">Select Service</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}" {{ $subService->service_id == $service->id ? 'selected' : '' }}>
                                                    {{ $service->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('service_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="3">{{ $subService->description }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="image">Image</label>
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*">
                                        @error('image')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        @if($subService->image)
                                            <br>
                                            <img src="{{ asset('storage/' . $subService->image) }}" 
                                                 alt="{{ $subService->title }}" 
                                                 class="img-thumbnail" 
                                                 style="width: 100px; height: 100px; object-fit: cover;">
                                            <br>
                                            <small class="form-text text-muted">Current image</small>
                                        @endif
                                        <small class="form-text text-muted">Allowed formats: JPEG, PNG, JPG, GIF (Max: 2MB)</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="discount">Discount (%)</label>
                                        <input type="number" class="form-control @error('discount') is-invalid @enderror" 
                                               id="discount" name="discount" value="{{ $subService->discount }}" min="0">
                                        @error('discount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            <option value="active" {{ $subService->status ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ !$subService->status ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h4>Vehicle Pricing</h4>
                            <p class="text-muted">Update pricing information for different vehicle makes and models.</p>
                            <p class="text-info"><strong>Note:</strong> This sub-service has {{ $subService->serviceVehiclePrices->count() }} pricing entries. You can update them below.</p>
                            
                            <div id="vehicle-prices" style="max-height: 400px; overflow-y: auto;">
                                @foreach($subService->serviceVehiclePrices as $index => $price)
                                    <div class="vehicle-price-row border rounded p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Make <span class="text-danger">*</span></label>
                                                    <select class="form-control make-select" name="vehicle_prices[{{ $index }}][make_id]" required>
                                                        <option value="">Select Make</option>
                                                        @foreach($makes as $make)
                                                            <option value="{{ $make->id }}" {{ $price->make_id == $make->id ? 'selected' : '' }}>
                                                                {{ $make->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Model <span class="text-danger">*</span></label>
                                                    <select class="form-control model-select" name="vehicle_prices[{{ $index }}][model_id]" required>
                                                        <option value="">Select Model</option>
                                                        @foreach($models->where('make_id', $price->make_id) as $model)
                                                            <option value="{{ $model->id }}" {{ $price->model_id == $model->id ? 'selected' : '' }}>
                                                                {{ $model->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>Year From <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="vehicle_prices[{{ $index }}][year_from]" 
                                                           value="{{ $price->year_from }}" min="1900" max="{{ date('Y') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>Year To <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="vehicle_prices[{{ $index }}][year_to]" 
                                                           value="{{ $price->year_to }}" min="1900" max="{{ date('Y') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Price <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="vehicle_prices[{{ $index }}][price]" 
                                                           value="{{ $price->price }}" step="0.01" min="0" required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Discount %</label>
                                                    <input type="number" class="form-control" name="vehicle_prices[{{ $index }}][discount]" 
                                                           value="{{ $price->discount ?? '' }}" step="0.01" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    @if($loop->last)
                                                        <button type="button" class="btn btn-sm btn-danger remove-price-row" style="width: 100%;">
                                                            <i class="fas fa-trash"></i> Remove
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-danger remove-price-row" style="width: 100%;">
                                                            <i class="fas fa-trash"></i> Remove
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" id="add-price-row" class="btn btn-info mb-3">
                                <i class="fas fa-plus"></i> Add More Pricing
                            </button>

                            <hr>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Sub Service
                                </button>
                                <a href="{{ route('sub-services.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
let priceRowCount = {{ $subService->serviceVehiclePrices->count() }};

$('#add-price-row').click(function() {
    const newRow = `
        <div class="vehicle-price-row border rounded p-3 mb-3">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Make <span class="text-danger">*</span></label>
                        <select class="form-control make-select" name="vehicle_prices[${priceRowCount}][make_id]" required>
                            <option value="">Select Make</option>
                            @foreach($makes as $make)
                                <option value="{{ $make->id }}">{{ $make->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Model <span class="text-danger">*</span></label>
                        <select class="form-control model-select" name="vehicle_prices[${priceRowCount}][model_id]" required>
                            <option value="">Select Model</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label>Year From <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="vehicle_prices[${priceRowCount}][year_from]" 
                               value="{{ date('Y') - 10 }}" min="1900" max="{{ date('Y') }}" required>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label>Year To <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="vehicle_prices[${priceRowCount}][year_to]" 
                               value="{{ date('Y') }}" min="1900" max="{{ date('Y') }}" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Price <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="vehicle_prices[${priceRowCount}][price]" 
                               step="0.01" min="0" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Discount %</label>
                        <input type="number" class="form-control" name="vehicle_prices[${priceRowCount}][discount]" 
                               step="0.01" min="0">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-danger remove-price-row" style="width: 100%;">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#vehicle-prices').append(newRow);
    priceRowCount++;
});

$(document).on('click', '.remove-price-row', function() {
    $(this).closest('.vehicle-price-row').remove();
});

$(document).on('change', '.make-select', function() {
    const makeId = $(this).val();
    const modelSelect = $(this).closest('.vehicle-price-row').find('.model-select');
    
    modelSelect.html('<option value="">Select Model</option>');
    
    if (makeId) {
        $.get(`/sub-services/get-models/${makeId}`, function(data) {
            data.forEach(function(model) {
                modelSelect.append(`<option value="${model.id}">${model.name}</option>`);
            });
        });
    }
});
</script>
@endpush
