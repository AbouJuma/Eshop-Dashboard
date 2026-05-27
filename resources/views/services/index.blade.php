@extends('layouts.app')

@section('title', 'Services')

@section('css')
<style>
    /* Modern Header Styling */
    .services-header-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    
    .services-header-card .card-header {
        background: #ffffff;
        border-bottom: none;
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .btn-create-service {
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
        box-shadow: none !important;
    }
    
    .btn-create-service:hover {
        background-color: #2563eb;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2) !important;
        text-decoration: none;
    }
    
    /* Modern Service Cards Grid */
    .service-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .service-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.03);
        border-color: #e2e8f0;
    }
    
    .service-image-wrapper {
        position: relative;
        height: 200px;
        overflow: hidden;
        background: #f8fafc;
    }
    
    .service-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .service-card:hover .service-image-wrapper img {
        transform: scale(1.06);
    }
    
    .service-placeholder-bg {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
    }
    
    .service-badge-floating {
        position: absolute;
        top: 16px;
        right: 16px;
        z-index: 10;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .service-card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    .service-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
        line-height: 1.4;
    }
    
    .service-desc {
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 20px;
        flex-grow: 1;
    }
    
    .service-card-footer {
        background-color: #f8fafc;
        border-top: 1px solid #f1f5f9;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    /* Action Buttons */
    .btn-service-edit {
        font-size: 13px;
        font-weight: 600;
        color: #2563eb;
        background-color: #eff6ff;
        border: none;
        border-radius: 8px;
        padding: 6px 14px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    
    .btn-service-edit:hover {
        background-color: #2563eb;
        color: #ffffff;
        text-decoration: none;
    }
    
    .btn-service-delete {
        font-size: 13px;
        font-weight: 600;
        color: #dc2626;
        background-color: #fef2f2;
        border: none;
        border-radius: 8px;
        padding: 6px 14px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-service-delete:hover {
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
    
    .badge-success {
        background-color: #d1fae5 !important;
        color: #065f46 !important;
    }
    
    .badge-secondary {
        background-color: #e2e8f0 !important;
        color: #475569 !important;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold" style="color: #0f172a; letter-spacing: -0.5px;">Services</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #64748b; font-weight: 500;">Home</a></li>
                    <li class="breadcrumb-item active" style="color: #0f172a; font-weight: 600;">Services</li>
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
                <div class="card services-header-card">
                    <div class="card-header">
                        <h3 class="font-weight-bold m-0" style="font-size: 16px; color: #1e293b;"><i class="fas fa-wrench text-primary mr-2"></i>Services Catalog</h3>
                        <a href="{{ route('services.create') }}" class="btn-create-service">
                            <i class="fas fa-plus"></i> Add Service
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @forelse($services as $service)
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4 d-flex">
                    <div class="service-card w-100 d-flex flex-column">
                        <!-- Floating active/inactive status -->
                        <span class="badge badge-{{ $service->status == 1 ? 'success' : 'secondary' }} service-badge-floating">
                            {{ $service->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                        
                        <div class="service-image-wrapper">
                            @if(!empty($service->image))
                                <img src="{{ asset('uploads/' . $service->image) }}" alt="{{ $service->title }}">
                            @else
                                <div class="service-placeholder-bg">
                                    <i class="fas fa-wrench fa-3x"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="service-card-body">
                            <h5 class="service-title">{{ $service->title }}</h5>
                            <p class="service-desc">
                                {{ Str::limit($service->description ?? 'No description available', 120) }}
                            </p>
                        </div>
                        
                        <div class="service-card-footer">
                            <a href="{{ route('services.edit', $service->id) }}" class="btn-service-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('services.destroy', $service->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-service-delete" onclick="return confirm('Are you sure you want to delete this service?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded-lg shadow-sm border" style="border-radius: 16px;">
                        <i class="fas fa-wrench fa-3x mb-3 text-muted" style="color: #cbd5e1 !important;"></i>
                        <h5 class="font-weight-bold text-dark">No Services Found</h5>
                        <p class="text-muted mb-4">You haven't added any services yet. Start by creating a new service catalog.</p>
                        <a href="{{ route('services.create') }}" class="btn-create-service">
                            <i class="fas fa-plus"></i> Create Your First Service
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
