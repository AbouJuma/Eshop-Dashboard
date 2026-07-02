@extends('layouts.app')

@section('title', 'Add Car Service Record')

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
    }

    .maintenance-card .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
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

    .btn-submit {
        background-color: #3b82f6;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        padding: 10px 24px;
        transition: all 0.2s ease;
    }

    .btn-submit:hover {
        background-color: #2563eb;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-back {
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        border-radius: 8px;
        padding: 10px 24px;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-back:hover {
        background-color: #e2e8f0;
        color: #334155;
        text-decoration: none;
    }

    .calculation-panel {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        padding: 16px;
        margin-top: 10px;
    }

    .preselected-banner {
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
    }

    .toggle-btn-group {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .toggle-btn {
        flex: 1;
        padding: 12px;
        text-align: center;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-weight: 600;
        color: #64748b;
        background-color: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .toggle-btn.active {
        border-color: #3b82f6;
        color: #3b82f6;
        background-color: #eff6ff;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1) inset;
    }

    /* Custom select2 styling override for styling harmony */
    .select2-container--default .select2-selection--single {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        height: 42px !important;
        padding: 6px 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold" style="color: #0f172a; letter-spacing: -0.5px;">Add Service Record</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #64748b; font-weight: 500;">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('car-maintenance.index') }}" style="color: #64748b; font-weight: 500;">Car Maintenance</a></li>
                    <li class="breadcrumb-item active" style="color: #0f172a; font-weight: 600;">Add Record</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="maintenance-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fas fa-plus-circle"></i> Service Details Form</h3>
        </div>
        
        <div class="modal-body">
            @if ($errors->any())
                <div class="alert alert-danger" style="border-radius: 8px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('car-maintenance.store') }}" method="POST">
                @csrf
                
                <!-- 1. VEHICLE INFO SECTION -->
                @if($selected_car)
                    <!-- Case A: Specific Car preselected -->
                    <input type="hidden" name="car_id" value="{{ $selected_car->id }}">
                    <input type="hidden" name="vehicle_selection_type" value="existing">
                    
                    <div class="preselected-banner">
                        <h4 style="margin-top: 0; font-weight: 700; color: #1e40af;"><i class="fa fas fa-car"></i> Selected Vehicle Detail</h4>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-2">
                                <span class="text-muted" style="font-size: 11px; text-transform: uppercase;">Plate Number</span>
                                <div style="font-size: 18px; font-weight: 800; color: #1e293b;">{{ strtoupper($selected_car->car_plate_number) }}</div>
                            </div>
                            <div class="col-md-2">
                                <span class="text-muted" style="font-size: 11px; text-transform: uppercase;">Chassis Number</span>
                                <div style="font-size: 14px; font-weight: 700; color: #475569;">{{ strtoupper($selected_car->car_chassis_number ?? 'N/A') }}</div>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted" style="font-size: 11px; text-transform: uppercase;">Brand / Model</span>
                                <div style="font-size: 15px; font-weight: 600; color: #334155;">{{ $selected_car->car_brand ?? 'N/A' }} {{ $selected_car->car_model ?? '' }} ({{ $selected_car->car_year ?? 'N/A' }})</div>
                            </div>
                            <div class="col-md-2">
                                <span class="text-muted" style="font-size: 11px; text-transform: uppercase;">Owner Name</span>
                                <div style="font-size: 15px; font-weight: 600; color: #334155;">{{ $selected_car->owner_name }}</div>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted" style="font-size: 11px; text-transform: uppercase;">Phone / Email</span>
                                <div style="font-size: 13px; color: #475569;">
                                    {{ $selected_car->owner_phone ?? 'No Phone' }}
                                    @if($selected_car->owner_email) <br><small class="text-muted">{{ $selected_car->owner_email }}</small> @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Case B: Selection / Registration needed -->
                    <h4 style="margin-top: 0; margin-bottom: 15px; font-weight: 700; color: #3b82f6; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                        <i class="fa fas fa-car"></i> 1. Vehicle Selection
                    </h4>

                    <div class="toggle-btn-group">
                        <div class="toggle-btn active" id="btn-select-existing" onclick="toggleVehicleSource('existing')">
                            <i class="fa fas fa-list-ul"></i> Select Existing Registered Car
                        </div>
                        <div class="toggle-btn" id="btn-register-new" onclick="toggleVehicleSource('new')">
                            <i class="fa fas fa-plus-circle"></i> Register New Car
                        </div>
                    </div>
                    
                    <input type="hidden" name="vehicle_selection_type" id="vehicle_selection_type" value="existing">

                    <!-- Existing Vehicle Dropdown Container -->
                    <div id="existing-vehicle-container">
                        <div class="form-group">
                            <label for="car_id">Choose Vehicle <span class="text-danger">*</span></label>
                            <select name="car_id" id="car_id" class="form-control select2" style="width: 100%;">
                                <option value="">-- Search & Select Registered Car --</option>
                                @foreach($cars as $car)
                                    <option value="{{ $car->id }}" {{ old('car_id') == $car->id ? 'selected' : '' }}>
                                        {{ strtoupper($car->car_plate_number) }} @if($car->car_chassis_number) (Chassis: {{ strtoupper($car->car_chassis_number) }}) @endif - {{ $car->owner_name }} ({{ $car->car_brand }} {{ $car->car_model }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Register New Vehicle Form Container -->
                    <div id="new-vehicle-container" style="display: none;">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="car_plate_number">Plate Number <span class="text-danger">*</span></label>
                                    <input type="text" name="car_plate_number" id="car_plate_number" class="form-control" value="{{ old('car_plate_number') }}" placeholder="e.g. TX-123-AB">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="car_chassis_number">Chassis Number</label>
                                    <input type="text" name="car_chassis_number" id="car_chassis_number" class="form-control" value="{{ old('car_chassis_number') }}" placeholder="e.g. 1HGCR2F8XHA...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="car_brand">Car Brand</label>
                                    <input type="text" name="car_brand" id="car_brand" class="form-control" value="{{ old('car_brand') }}" placeholder="e.g. Toyota">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="car_model">Car Model</label>
                                    <input type="text" name="car_model" id="car_model" class="form-control" value="{{ old('car_model') }}" placeholder="e.g. Camry">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="car_year">Year</label>
                                    <input type="text" name="car_year" id="car_year" class="form-control" value="{{ old('car_year') }}" placeholder="e.g. 2018">
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="owner_name">Owner Name <span class="text-danger">*</span></label>
                                    <input type="text" name="owner_name" id="owner_name" class="form-control" value="{{ old('owner_name') }}" placeholder="e.g. John Doe">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="owner_phone">Owner Phone</label>
                                    <input type="text" name="owner_phone" id="owner_phone" class="form-control" value="{{ old('owner_phone') }}" placeholder="e.g. +1234567890">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="owner_email">Owner Email</label>
                                    <input type="email" name="owner_email" id="owner_email" class="form-control" value="{{ old('owner_email') }}" placeholder="e.g. john@example.com">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 2. SERVICE INFO SECTION -->
                <h4 style="margin-top: 30px; margin-bottom: 20px; font-weight: 700; color: #ea580c; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                    <i class="fa fas fa-tools"></i> 2. Service Log details
                </h4>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="service_date">Service Date <span class="text-danger">*</span></label>
                            <input type="date" name="service_date" id="service_date" class="form-control" value="{{ old('service_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="service_type">Service Type <span class="text-danger">*</span></label>
                            <select name="service_type" id="service_type" class="form-control" required>
                                <option value="car_services" {{ old('service_type', 'car_services') == 'car_services' ? 'selected' : '' }}>CAR SERVICES</option>
                                <option value="periodic_maintanance" {{ old('service_type') == 'periodic_maintanance' ? 'selected' : '' }}>PERIODIC MAINTANANCE</option>
                                <option value="repair" {{ old('service_type') == 'repair' ? 'selected' : '' }}>REPAIR</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cost">Service Cost</label>
                            <input type="number" step="0.01" name="cost" id="cost" class="form-control" value="{{ old('cost') }}" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <!-- 3. MILEAGES INPUT SECTION (Hidden if Spares Only is selected) -->
                <div id="mileage-section" style="margin-top: 15px;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="serviced_kilometer">Current Mileage (Kilometers) <span class="text-danger">*</span></label>
                                <input type="number" name="serviced_kilometer" id="serviced_kilometer" class="form-control" value="{{ old('serviced_kilometer') }}" placeholder="e.g. 85000">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="next_service_interval">Next Service Interval</label>
                                <select id="next_service_interval" class="form-control">
                                    <option value="3000">After 3,000 km</option>
                                    <option value="5000" selected>After 5,000 km</option>
                                    <option value="10000">After 10,000 km</option>
                                    <option value="custom">Custom Value</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="next_service_kilometer">Next Service At (Kilometers) <span class="text-danger">*</span></label>
                                <input type="number" name="next_service_kilometer" id="next_service_kilometer" class="form-control" value="{{ old('next_service_kilometer') }}" placeholder="Calculated automatically">
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 5px; margin-bottom: 15px;">
                        <div class="col-md-12">
                            <div class="calculation-panel">
                                <strong><i class="fa fas fa-info-circle"></i> Automatic Calculator:</strong>
                                <span id="calc-summary">Please input current mileage to calculate next service.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="details">Service description / Spares changed list</label>
                            <textarea name="details" id="details" class="form-control" rows="5" placeholder="List the details of work done, spares changed, lubricants, or filters replaced..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('car-maintenance.index') }}" class="btn-back"><i class="fa fas fa-arrow-left"></i> Back to List</a>
                        <button type="submit" class="btn-submit"><i class="fa fas fa-check-circle"></i> Save Record</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
// Toggle vehicle source (existing vs new)
function toggleVehicleSource(source) {
    if (source === 'existing') {
        $('#btn-select-existing').addClass('active');
        $('#btn-register-new').removeClass('active');
        $('#existing-vehicle-container').show();
        $('#new-vehicle-container').hide();
        $('#vehicle_selection_type').val('existing');
        
        // Remove requirements on new vehicle forms
        $('#car_plate_number').removeAttr('required');
        $('#owner_name').removeAttr('required');
        $('#car_id').attr('required', true);
    } else {
        $('#btn-register-new').addClass('active');
        $('#btn-select-existing').removeClass('active');
        $('#new-vehicle-container').show();
        $('#existing-vehicle-container').hide();
        $('#vehicle_selection_type').val('new');
        
        // Add requirements to new vehicle forms
        $('#car_plate_number').attr('required', true);
        $('#owner_name').attr('required', true);
        $('#car_id').removeAttr('required');
    }
}

$(document).ready(function() {
    // Initialize Select2 search dropdown
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            placeholder: "Search and select registered vehicle...",
            allowClear: true,
            minimumResultsForSearch: 0
        });
    }

    // Toggle mileage forms based on service type
    // Only show mileage section when 'CAR SERVICES' is selected
    function toggleMileageSection() {
        var serviceType = $('#service_type').val();
        if (serviceType === 'car_services') {
            $('#mileage-section').slideDown(300);
            $('#serviced_kilometer').attr('required', true);
            $('#next_service_kilometer').attr('required', true);
            calculateNextService();
        } else {
            // Hide for 'repair' and 'periodic_maintanance'
            $('#mileage-section').slideUp(300);
            $('#serviced_kilometer').removeAttr('required').val('');
            $('#next_service_kilometer').removeAttr('required').val('');
        }
    }

    function calculateNextService() {
        var currentKm = parseInt($('#serviced_kilometer').val());
        var intervalVal = $('#next_service_interval').val();
        
        if (isNaN(currentKm) || currentKm < 0) {
            $('#next_service_kilometer').val('');
            $('#calc-summary').text('Please input current mileage to calculate next service.');
            return;
        }

        if (intervalVal === 'custom') {
            $('#next_service_kilometer').removeAttr('readonly');
            var nextKm = parseInt($('#next_service_kilometer').val());
            if (isNaN(nextKm)) {
                $('#calc-summary').text('Input custom mileage for next service.');
            } else {
                var diff = nextKm - currentKm;
                $('#calc-summary').html('Next service scheduled after custom addition of <strong>' + diff.toLocaleString() + ' km</strong> (At <strong>' + nextKm.toLocaleString() + ' km</strong>).');
            }
        } else {
            $('#next_service_kilometer').attr('readonly', true);
            var addKm = parseInt(intervalVal);
            var resultKm = currentKm + addKm;
            $('#next_service_kilometer').val(resultKm);
            $('#calc-summary').html('Auto-calculated next service: <strong>' + currentKm.toLocaleString() + ' km</strong> + <strong>' + addKm.toLocaleString() + ' km</strong> = <strong>' + resultKm.toLocaleString() + ' km</strong>.');
        }
    }

    // Initial setup for vehicle inputs
    @if(!$selected_car)
        toggleVehicleSource('existing');
    @endif
    
    // Listen to changes
    $('#service_type').change(function() {
        toggleMileageSection();
    });

    $('#serviced_kilometer, #next_service_interval').on('input change', function() {
        calculateNextService();
    });

    $('#next_service_kilometer').on('input', function() {
        if ($('#next_service_interval').val() === 'custom') {
            calculateNextService();
        }
    });

    // Run initial status check
    toggleMileageSection();
});
</script>
@endsection
