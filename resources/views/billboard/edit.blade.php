@extends('layouts.app')

@section('title', 'Edit Billboard')

@section('css')
<style>
    /* Card Layouts & Containers */
    .edit-billboard-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    
    .edit-billboard-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
    }

    .edit-billboard-card .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    /* Form Fields */
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

    /* File Input Styling */
    .custom-file-input:focus ~ .custom-file-label {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }

    .custom-file-label {
        border-radius: 8px;
        border-color: #cbd5e1;
        padding: 10px 14px;
        height: auto;
        line-height: 1.5;
    }

    .custom-file-label::after {
        border-radius: 0 8px 8px 0;
        padding: 10px 16px;
        height: auto;
        line-height: 1.5;
        background-color: #f1f5f9;
        color: #475569;
        font-weight: 600;
    }

    /* Form Buttons */
    .btn-save-billboard {
        background-color: #3b82f6;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        padding: 10px 24px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-save-billboard:hover {
        background-color: #2563eb;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }

    .btn-cancel-billboard {
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
        border-radius: 8px;
        padding: 10px 24px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-cancel-billboard:hover {
        background-color: #f1f5f9;
        color: #0f172a;
        text-decoration: none;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold" style="color: #0f172a; letter-spacing: -0.5px;">Edit Billboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #64748b; font-weight: 500;">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('billboard.index') }}" style="color: #64748b; font-weight: 500;">Billboard</a></li>
                    <li class="breadcrumb-item active" style="color: #0f172a; font-weight: 600;">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 12px; border: none; background-color: #fee2e2; color: #991b1b;">
                <h5 class="font-weight-bold mb-2">Whoops! There were some problems with your input.</h5>
                <ul class="mb-0" style="padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row" style="text-align: center; display: block;">
            <div class="col-md-8" style="display: inline-block; float: none; text-align: left;">
                <div class="card edit-billboard-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-edit text-muted mr-2"></i>Billboard Information</h3>
                    </div>
                    <form action="{{ route('billboard.update', $schedule->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body p-4">
                            <div class="form-group mb-4">
                                <label for="title">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $schedule->title) }}" required placeholder="e.g. End of Season Sale 30% Off">
                                @error('title')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Hidden fields for API compatibility -->
                            <input type="hidden" name="location" value="{{ $schedule->location ?? 'default' }}">
                            <input type="hidden" name="date" value="{{ $schedule->date ?? date('Y-m-d') }}">
                            <input type="hidden" name="from" value="{{ $schedule->from ?? '00:00' }}">
                            <input type="hidden" name="to" value="{{ $schedule->to ?? '23:59' }}">
                            <input type="hidden" name="visibility" value="public">

                            <div class="form-group mb-0">
                                <label for="image">Cover Image</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*">
                                        <label class="custom-file-label" for="image">Choose image file...</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted mt-2">Allowed formats: JPEG, PNG, JPG, GIF (Max: 2MB)</small>
                                
                                @if($schedule->image)
                                    <div class="mt-3 p-3 bg-light rounded-lg border d-inline-block">
                                        <small class="text-muted d-block mb-2 font-weight-bold">Current Billboard Image:</small>
                                        <img src="{{ asset('uploads/' . $schedule->image) }}" alt="Current billboard image" 
                                             style="max-height: 120px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                    </div>
                                @endif
                                @error('image')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-light border-top p-4 d-flex justify-content-start">
                            <button type="submit" class="btn-save-billboard mr-2">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="{{ route('billboard.index') }}" class="btn-cancel-billboard">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Show file name in custom file input
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.querySelector('input[type=file]');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                var fileName = e.target.files[0]?.name || 'Choose image file...';
                var label = document.querySelector('label[for="image"]');
                if (label) {
                    label.innerText = fileName;
                }
            });
        }
    });
</script>
@endpush
