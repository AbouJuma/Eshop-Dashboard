@extends('layouts.app')

@section('title', 'Billboard Gallery')

@section('css')
<style>
    /* Card Container Grid styling */
    .billboard-grid-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .billboard-grid-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
    }

    .billboard-img-container {
        position: relative;
        height: 220px;
        overflow: hidden;
        background: #f8fafc;
    }

    .billboard-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .billboard-grid-card:hover .billboard-img {
        transform: scale(1.05);
    }

    /* Fallback image style */
    .billboard-fallback-img {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: #4f46e5;
    }

    .billboard-body {
        padding: 20px 24px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .billboard-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 8px 0;
        line-height: 1.4;
    }

    /* Card Footer Actions styling */
    .billboard-footer {
        padding: 16px 24px;
        background-color: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Buttons & Controls */
    .btn-create-billboard {
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
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        text-decoration: none;
    }

    .btn-create-billboard:hover {
        background-color: #2563eb;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.25);
        text-decoration: none;
    }

    .btn-action-edit {
        background-color: #eff6ff;
        color: #1d4ed8;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 13.5px;
    }

    .btn-action-edit:hover {
        background-color: #1d4ed8;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-action-delete {
        background-color: #fef2f2;
        color: #b91c1c;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 13.5px;
    }

    .btn-action-delete:hover {
        background-color: #b91c1c;
        color: #ffffff;
        transform: translateY(-1px);
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold" style="color: #0f172a; letter-spacing: -0.5px;">Billboard Gallery</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #64748b; font-weight: 500;">Home</a></li>
                    <li class="breadcrumb-item active" style="color: #0f172a; font-weight: 600;">Billboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content mb-5">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" style="border-radius: 12px; border: none; background-color: #d1fae5; color: #065f46; font-weight: 500;">
                <button type="button" class="close" data-dismiss="alert" style="color: #065f46; opacity: 0.8;">&times;</button>
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-end">
                <a href="{{ route('billboard.create') }}" class="btn-create-billboard">
                    <i class="fas fa-plus"></i> Create New Billboard
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($schedules as $schedule)
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="billboard-grid-card">
                        <div class="billboard-img-container">
                            @if(!empty($schedule->image))
                                <img src="{{ asset('uploads/' . $schedule->image) }}" class="billboard-img" alt="{{ $schedule->title }}">
                            @else
                                <div class="billboard-fallback-img">
                                    <i class="fas fa-image fa-3x"></i>
                                </div>
                            @endif
                        </div>
                        <div class="billboard-body">
                            <h5 class="billboard-title">{{ $schedule->title }}</h5>
                            <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i> Added {{ \Carbon\Carbon::parse($schedule->created_at)->diffForHumans() }}</small>
                        </div>
                        
                        <div class="billboard-footer">
                            <a href="{{ route('billboard.edit', $schedule->id) }}" class="btn-action-edit" title="Edit Billboard">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('billboard.destroy', $schedule->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this billboard?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action-delete" title="Delete Billboard">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card p-5 text-center" style="border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h5 class="font-weight-bold mb-2" style="color: #0f172a;">No Billboards Found</h5>
                        <p class="text-muted mb-4">Click "Create New Billboard" to publish your first billboard advertisement.</p>
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('billboard.create') }}" class="btn-create-billboard">
                                <i class="fas fa-plus"></i> Create New Billboard
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
