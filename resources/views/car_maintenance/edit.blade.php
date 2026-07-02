@extends('layouts.app')

@section('title', 'Edit Car Service Record')

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
</style>
@endsection

@section('content')
<section class="content-header">
    <h1>Car Maintenance
        <small>Edit service record</small>
    </h1>
</section>

<section class="content">
    <div class="maintenance-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fas fa-edit"></i> Edit Service Details Form</h3>
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

            <form action="{{ route('car-maintenance.update', $maintenance->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Vehicle Info (Read-only banner) -->
                <div class="preselected-banner">
                    <h4 style="margin-top: 0; font-weight: 700; color: #1e40af;"><i class="fa fas fa-car"></i> Vehicle Details</h4>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-3">
                            <span class="text-muted" style="font-size: 11px; text-transform: uppercase;">Plate Number</span>
                            <div style="font-size: 18px; font-weight: 800; color: #1e293b;">{{ strtoupper($maintenance->car->car_plate_number) }}</div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted" style="font-size: 11px; text-transform: uppercase;">Brand / Model</span>
                            <div style="font-size: 15px; font-weight: 600; color: #334155;">{{ $maintenance->car->car_brand ?? 'N/A' }} {{ $maintenance->car->car_model ?? '' }} ({{ $maintenance->car->car_year ?? 'N/A' }})</div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted" style="font-size: 11px; text-transform: uppercase;">Owner Name</span>
                            <div style="font-size: 15px; font-weight: 600; color: #334155;">{{ $maintenance->car->owner_name }}</div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted" style="font-size: 11px; text-transform: uppercase;">Phone / Email</span>
                            <div style="font-size: 13px; color: #475569;">
                                {{ $maintenance->car->owner_phone ?? 'No Phone' }}
                                @if($maintenance->car->owner_email) <br><small class="text-muted">{{ $maintenance->car->owner_email }}</small> @endif
                            </div>
                        </div>
                    </div>
                </div>

                <h4 style="margin-top: 30px; margin-bottom: 20px; font-weight: 700; color: #ea580c; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                    <i class="fa fas fa-tools"></i> Service Details
                </h4>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="service_date">Service Date <span class="text-danger">*</span></label>
                            <input type="date" name="service_date" id="service_date" class="form-control" value="{{ old('service_date', $maintenance->service_date ? $maintenance->service_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="service_type">Service Type <span class="text-danger">*</span></label>
                            <select name="service_type" id="service_type" class="form-control" required>
                                <option value="car_services" {{ old('service_type', $maintenance->service_type) == 'car_services' ? 'selected' : '' }}>CAR SERVICES</option>
                                <option value="periodic_maintanance" {{ old('service_type', $maintenance->service_type) == 'periodic_maintanance' ? 'selected' : '' }}>PERIODIC MAINTANANCE</option>
                                <option value="repair" {{ old('service_type', $maintenance->service_type) == 'repair' ? 'selected' : '' }}>REPAIR</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cost">Service Cost</label>
                            <input type="number" step="0.01" name="cost" id="cost" class="form-control" value="{{ old('cost', $maintenance->cost) }}" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <!-- Mileage Section (Hidden if Spares Only is selected) -->
                <div id="mileage-section" style="margin-top: 15px;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="serviced_kilometer">Current Mileage (Kilometers) <span class="text-danger">*</span></label>
                                <input type="number" name="serviced_kilometer" id="serviced_kilometer" class="form-control" value="{{ old('serviced_kilometer', $maintenance->serviced_kilometer) }}" placeholder="e.g. 85000">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="next_service_interval">Next Service Interval</label>
                                <select id="next_service_interval" class="form-control">
                                    <option value="3000">After 3,000 km</option>
                                    <option value="5000">After 5,000 km</option>
                                    <option value="10000">After 10,000 km</option>
                                    <option value="custom" selected>Custom Value</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="next_service_kilometer">Next Service At (Kilometers) <span class="text-danger">*</span></label>
                                <input type="number" name="next_service_kilometer" id="next_service_kilometer" class="form-control" value="{{ old('next_service_kilometer', $maintenance->next_service_kilometer) }}" placeholder="Calculated automatically">
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
                            <textarea name="details" id="details" class="form-control" rows="5" placeholder="List the details of work done, spares changed, lubricants, or filters replaced...">{{ old('details', $maintenance->details) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('car-maintenance.index') }}" class="btn-back"><i class="fa fas fa-arrow-left"></i> Back to List</a>
                        <button type="submit" class="btn-submit"><i class="fa fas fa-check-circle"></i> Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
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
            $('#serviced_kilometer').removeAttr('required');
            $('#next_service_kilometer').removeAttr('required');
        }
    }

    function calculateNextService() {
        var currentKm = parseInt($('#serviced_kilometer').val());
        var intervalVal = $('#next_service_interval').val();
        
        if (isNaN(currentKm) || currentKm < 0) {
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

    // Run initial setup on page load
    toggleMileageSection();
});
</script>
@endsection
