@extends('layouts.app')

@section('title', 'Edit Billboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Billboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('billboard.index') }}">Billboard</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Display errors if any -->
@if($errors->any())
    <div class="container-fluid">
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Billboard Information</h3>
                    </div>
                    <form action="{{ route('billboard.update', $schedule->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $schedule->title) }}" required>
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

                            <div class="form-group">
                                <label for="image">Image</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*">
                                        <label class="custom-file-label" for="image">Choose image...</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Allowed formats: JPEG, PNG, JPG, GIF (Max: 2MB)</small>
                                @if($schedule->image)
                                    <div class="mt-2">
                                        <small class="text-muted">Current image:</small><br>
                                        <img src="{{ asset('uploads/' . $schedule->image) }}" alt="Current image" 
                                             style="max-height: 100px; border-radius: 5px;">
                                    </div>
                                @endif
                                @error('image')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Billboard
                            </button>
                            <a href="{{ route('billboard.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Show file name in custom file input
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.querySelector('input[type=file]');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                var fileName = e.target.files[0]?.name || 'Choose image...';
                var label = document.querySelector('label[for="image"]');
                if (label) {
                    label.innerText = fileName;
                }
            });
        }
        
        // Debug form submission
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('Form submitting...');
                const formData = new FormData(form);
                console.log('Form data:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ':', value);
                }
            });
        }
    });
</script>
@endpush
@endsection
