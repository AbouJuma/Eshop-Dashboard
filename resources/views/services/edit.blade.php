@extends('layouts.app')

@section('title', 'Edit Service')

@section('css')
<style>
    /* Card Layouts & Containers */
    .edit-service-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    
    .edit-service-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
    }

    .edit-service-card .card-title {
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
    .btn-save-service {
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

    .btn-save-service:hover {
        background-color: #2563eb;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }

    .btn-cancel-service {
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

    .btn-cancel-service:hover {
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
                <h1 class="m-0 font-weight-bold" style="color: #0f172a; letter-spacing: -0.5px;">Edit Service</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #64748b; font-weight: 500;">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('services.index') }}" style="color: #64748b; font-weight: 500;">Services</a></li>
                    <li class="breadcrumb-item active" style="color: #0f172a; font-weight: 600;">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row" style="text-align: center; display: block;">
            <div class="col-md-8" style="display: inline-block; float: none; text-align: left;">
                <div class="card edit-service-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-edit text-muted mr-2"></i>Service Information</h3>
                    </div>
                    <form action="{{ route('services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body p-4">
                            <div class="form-group mb-4">
                                <label for="title">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $service->title) }}" required placeholder="e.g. Engine Diagnostics">
                                @error('title')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" placeholder="Describe the details of this service..."></textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status', $service->status == 1 ? 'active' : 'inactive') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $service->status == 1 ? 'active' : 'inactive') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

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
                                
                                @if($service->image)
                                    <div class="mt-3 p-3 bg-light rounded-lg border d-inline-block">
                                        <small class="text-muted d-block mb-2 font-weight-bold">Current Cover Image:</small>
                                        <img src="{{ asset('uploads/' . $service->image) }}" alt="Current service image" 
                                             style="max-height: 120px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                    </div>
                                @endif
                                @error('image')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-light border-top p-4 d-flex justify-content-start">
                            <button type="submit" class="btn-save-service mr-2">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="{{ route('services.index') }}" class="btn-cancel-service">
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
    document.querySelector('input[type=file]').addEventListener('change', function(e) {
        var fileName = e.target.files[0]?.name || 'Choose image file...';
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
    
    // Auto-fill existing description if present
    document.addEventListener('DOMContentLoaded', function() {
        const descriptionTextarea = document.getElementById('description');
        if (descriptionTextarea) {
            descriptionTextarea.value = {!! json_encode(old('description', $service->description)) !!};
        }
    });
</script>
@endpush
