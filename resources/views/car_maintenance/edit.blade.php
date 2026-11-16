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

                @include('car_maintenance._service_fields', ['maintenance' => $maintenance])

                <div class="row" style="margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('car-maintenance.index') }}" class="btn-back"><i class="fa fas fa-arrow-left"></i> Back to List</a>
                        <button type="submit" class="btn-submit"><i class="fa fas fa-check-circle"></i> Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('car_maintenance._attachments')
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
@include('car_maintenance._service_fields_js')
});
</script>
@endsection
