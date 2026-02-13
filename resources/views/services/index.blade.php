@extends('layouts.app')

@section('title', 'Services')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Services</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Services</li>
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
                        <h3 class="card-title">Services Management</h3>
                        <a href="{{ route('services.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($services as $service)
                                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                                    <div class="card border-primary shadow-sm h-100">
                                        @if(!empty($service->image))
                                            <img src="{{ asset('uploads/' . $service->image) }}" class="card-img-top" alt="{{ $service->title }}" style="height: 200px; object-fit: cover;">
                                        @else
                                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light border-bottom" style="height: 200px;">
                                                <i class="fas fa-wrench fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title text-primary font-weight-bold">{{ $service->title }}</h5>
                                            <p class="card-text text-muted flex-grow-1">
                                                {{ Str::limit($service->description ?? 'No description available', 100) }}
                                            </p>
                                            <div class="mb-3">
                                                <span class="badge badge-{{ $service->status == 1 ? 'success' : 'secondary' }} p-2">
                                                    {{ $service->status == 1 ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light border-top d-flex justify-content-between align-items-center">
                                            <a href="{{ route('services.edit', $service->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('services.destroy', $service->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this service?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-wrench fa-2x mb-2"></i>
                                        <h5>No services found</h5>
                                        <p>Click "Create New" to add your first service.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
