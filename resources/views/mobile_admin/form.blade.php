@extends('layouts.app')
@php($isEdit = $user->exists)
@section('title', $isEdit ? 'Edit Mobile Admin' : 'Add Mobile Admin')

@section('content')
<section class="content-header">
    <h1>{{ $isEdit ? 'Edit mobile admin' : 'Add mobile admin' }}</h1>
</section>

<section class="content">
    <form method="POST" action="{{ $isEdit ? route('mobile-admins.update', $user->id) : route('mobile-admins.store') }}" autocomplete="off">
        @csrf
        @if($isEdit) @method('PUT') @endif

        @component('components.widget', ['class' => 'box-primary'])
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 form-group">
                    <label>First name *</label>
                    <input type="text" name="first_name" class="form-control" required value="{{ old('first_name', $user->first_name) }}">
                </div>
                <div class="col-md-6 form-group">
                    <label>Last name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}">
                </div>
                <div class="col-md-6 form-group">
                    <label>Phone <small class="text-muted">(shown to customers on roadside jobs)</small></label>
                    <input type="text" name="contact_no" class="form-control" maxlength="15" placeholder="2557XXXXXXXX" value="{{ old('contact_no', $user->contact_no) }}">
                </div>
                <div class="col-md-6 form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                </div>
                <div class="col-md-6 form-group">
                    <label>Username * <small class="text-muted">(used to sign in to the app)</small></label>
                    <input type="text" name="username" class="form-control" required value="{{ old('username', $user->username) }}">
                </div>
                <div class="col-md-6 form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ old('status', $user->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive (cannot sign in)</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>Password {{ $isEdit ? '' : '*' }} <small class="text-muted">{{ $isEdit ? '(leave empty to keep the current password)' : '(at least 8 characters)' }}</small></label>
                    <input type="password" name="password" class="form-control" autocomplete="new-password" {{ $isEdit ? '' : 'required' }}>
                </div>
                <div class="col-md-6 form-group">
                    <label>Confirm password</label>
                    <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                </div>
            </div>
        @endcomponent

        <div class="text-right">
            <a href="{{ route('mobile-admins.index') }}" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
        </div>
    </form>
</section>
@stop
