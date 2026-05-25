@extends('layouts.app')

@section('title', 'Billboard Gallery')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Billboard Gallery</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Billboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content mb-5">
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
                        <h3 class="card-title">Billboard Gallery</h3>
                        <a href="{{ route('billboard.create') }}" class="btn btn-primary mb-3">
                            <i class="fas fa-plus"></i> Create New
                        </a>
                        
                    </div><br>
                    
                    <div class="card-body pb-4">
                        <div class="row mb-4">
                            @forelse($schedules as $schedule)
                                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                                    <div class="card h-100">
                                        @if(!empty($schedule->image))
                                            <img src="{{ asset('uploads/' . $schedule->image) }}" class="card-img-top" alt="{{ $schedule->title }}" style="height: 250px; object-fit: cover; width: 100%;">
                                        @else
                                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 250px;">
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title flex-grow-1">{{ $schedule->title }}</h5>
                                        </div>
                                        
                                        <div class="card-footer bg-transparent d-flex justify-content-between mt-auto">
                                            <a href="{{ route('billboard.edit', $schedule->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('billboard.destroy', $schedule->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                                        <h5>No billboards found</h5>
                                        <p>Click "Create New" to add your first billboard.</p>
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
