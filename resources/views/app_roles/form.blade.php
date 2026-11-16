@extends('layouts.app')
@php($isEdit = $role->exists)
@php($current = old('permissions', $role->permissions ?? []))
@section('title', $isEdit ? 'Edit App Role' : 'New App Role')

@section('content')
<section class="content-header">
    <h1>{{ $isEdit ? 'Edit role: '.$role->name : 'New app role' }}</h1>
</section>

<section class="content">
    <form method="POST" action="{{ $isEdit ? route('app-roles.update', $role->id) : route('app-roles.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif

        @component('components.widget', ['class' => 'box-primary'])
            @if($errors->any())
                <div class="alert alert-danger"><ul style="margin:0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <div class="row">
                <div class="col-md-5 form-group">
                    <label>Role name *</label>
                    <input type="text" name="name" class="form-control" required maxlength="100" placeholder="e.g. Senior Mechanic"
                           value="{{ old('name', $role->name) }}" {{ $role->is_system ? 'readonly' : '' }}>
                    @if($role->is_system)<small class="text-muted">Built-in role: the name cannot change, the permissions can.</small>@endif
                </div>
                <div class="col-md-7 form-group">
                    <label>Description</label>
                    <input type="text" name="description" class="form-control" maxlength="255" placeholder="Who gets this role"
                           value="{{ old('description', $role->description) }}">
                </div>
            </div>

            <label style="font-size:15px; margin-top:6px">Permissions</label>
            <p class="text-muted" style="margin-top:-2px">Everyone can see <strong>My stats</strong> and their own roadside jobs. "Update" permissions include "See".
                <a href="#" id="perm-all">Select all</a> · <a href="#" id="perm-none">Clear</a></p>

            <div class="row">
                @foreach($groups as $group => $permissions)
                    <div class="col-md-6 col-lg-4">
                        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px 14px; margin-bottom:14px; min-height:150px;">
                            <h4 style="margin:0 0 8px; font-size:15px; font-weight:700;">{{ $group }}</h4>
                            @foreach($permissions as $key => [$label, $help])
                                <div class="checkbox" style="margin:6px 0">
                                    <label>
                                        <input type="checkbox" name="permissions[]" value="{{ $key }}" class="perm" data-key="{{ $key }}" {{ in_array($key, $current) ? 'checked' : '' }}>
                                        <strong>{{ $label }}</strong><br>
                                        <small class="text-muted">{{ $help }}</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endcomponent

        <div class="text-right">
            <a href="{{ route('app-roles.index') }}" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save role</button>
        </div>
    </form>
</section>
@stop

@section('javascript')
<script>
$(function () {
    // "Update X" needs "See X"
    $('.perm').on('change', function () {
        var key = $(this).data('key');
        if (this.checked && /\.manage$/.test(key)) $('.perm[data-key="' + key.replace('.manage', '.view') + '"]').prop('checked', true);
        if (!this.checked && /\.view$/.test(key)) $('.perm[data-key="' + key.replace('.view', '.manage') + '"]').prop('checked', false);
    });
    $('#perm-all').on('click', function (e) { e.preventDefault(); $('.perm').prop('checked', true); });
    $('#perm-none').on('click', function (e) { e.preventDefault(); $('.perm').prop('checked', false); });
});
</script>
@endsection
