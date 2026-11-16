@extends('layouts.app')
@section('title', 'App Roles & Permissions')

@section('content')
<section class="content-header">
    <h1>App Roles &amp; Permissions
        <small>What each mechanic or app admin can see and do in the mobile app</small>
    </h1>
</section>

<section class="content">
    <div class="callout callout-info">
        Give every app account a role under <a href="{{ route('mobile-admins.index') }}"><strong>App Admins &amp; Mechanics</strong></a>.
        The app shows only the screens the role allows; everyone also sees <strong>My stats</strong> (their own jobs and records).
        Portal admins always have every permission. Changes apply the next time the app is opened.
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => 'Roles'])
        @can('roles.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{ route('app-roles.create') }}"><i class="fa fa-plus"></i> New role</a>
                </div>
            @endslot
        @endcan

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width:22%">Role</th>
                        <th>Permissions</th>
                        <th style="width:90px">Accounts</th>
                        <th style="width:150px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>
                            <strong>{{ $role->name }}</strong>
                            @if($role->is_system)<span class="label label-default">built-in</span>@endif
                            @if($role->description)<br><small class="text-muted">{{ $role->description }}</small>@endif
                        </td>
                        <td>
                            @forelse(\App\Models\AppRole::normalize($role->permissions ?? []) as $key)
                                <span class="label label-info" style="display:inline-block; margin:2px 2px; font-weight:500;" title="{{ $catalogue[$key][2] }}">{{ $catalogue[$key][0] }}: {{ $catalogue[$key][1] }}</span>
                            @empty
                                <span class="text-muted">Only "My stats"</span>
                            @endforelse
                        </td>
                        <td class="text-center">{{ $role->users_count }}</td>
                        <td style="white-space:nowrap">
                            @can('roles.update')
                                <a href="{{ route('app-roles.edit', $role->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edit</a>
                            @endcan
                            @can('roles.delete')
                                @unless($role->is_system)
                                    <form action="{{ route('app-roles.destroy', $role->id) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete role {{ $role->name }}?')"><i class="fa fa-trash"></i> Delete</button>
                                    </form>
                                @endunless
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endcomponent
</section>
@stop
