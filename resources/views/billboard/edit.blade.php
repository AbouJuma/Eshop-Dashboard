@extends('layouts.app')

@section('title', 'Edit Schedule')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Schedule</h1>
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

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Schedule Information</h3>
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

                            <div class="form-group">
                                <label for="location">Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location', $schedule->location) }}" required>
                                @error('location')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                               id="date" name="date" value="{{ old('date', $schedule->date) }}" required>
                                        @error('date')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="from">From <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control @error('from') is-invalid @enderror" 
                                               id="from" name="from" value="{{ old('from', $schedule->from) }}" required>
                                        @error('from')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="to">To <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control @error('to') is-invalid @enderror" 
                                               id="to" name="to" value="{{ old('to', $schedule->to) }}" required>
                                        @error('to')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="visibility">Visibility <span class="text-danger">*</span></label>
                                <select class="form-control @error('visibility') is-invalid @enderror" 
                                        id="visibility" name="visibility" required>
                                    <option value="">Select Visibility</option>
                                    <option value="public" {{ old('visibility', $schedule->visibility) == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="private" {{ old('visibility', $schedule->visibility) == 'private' ? 'selected' : '' }}>Private</option>
                                </select>
                                @error('visibility')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

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
                                <i class="fas fa-save"></i> Update Schedule
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
    document.querySelector('input[type=file]').addEventListener('change', function(e) {
        var fileName = e.target.files[0]?.name || 'Choose image...';
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>
@endpush
@endsection
