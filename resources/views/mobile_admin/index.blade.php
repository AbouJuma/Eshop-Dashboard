@extends('layouts.app')
@section('title', 'Mobile Admins')

@section('content')
<section class="content-header">
    <h1>Mobile Admins &amp; Mechanics
        <small>Accounts that can sign in to the Admin Panel in the mobile app</small>
    </h1>
</section>

<section class="content">
    <div class="callout callout-info">
        Mobile admins sign in from the app (<strong>Drawer → Admin Panel</strong>) with the username and password below.
        They can manage D-World orders, bookings, car maintenance and roadside requests and receive the new-request alarm.
        What each account can see and do comes from its <a href="{{ route('app-roles.index') }}"><strong>role</strong></a> (for example <strong>Mechanic</strong>: roadside jobs only, or <strong>App Admin</strong>: everything). You can create your own roles with any mix of permissions. Accounts created here never get access to the web portal. Portal admins can also sign in to the app.
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => 'All mobile admins'])
        @can('user.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{ route('mobile-admins.create') }}">
                        <i class="fa fa-plus"></i> Add mechanic / admin</a>
                </div>
            @endslot
        @endcan

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Phone / Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Signed in to app</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php($isPortalAdmin = $user->roles->contains('name', 'Admin#' . $businessId))
                        <tr>
                            <td><strong>{{ $user->username }}</strong></td>
                            <td>{{ trim($user->first_name . ' ' . $user->last_name) }}</td>
                            <td>{{ $user->contact_no ?: '—' }}<br><small class="text-muted">{{ $user->email }}</small></td>
                            <td>
                                @if($isPortalAdmin)
                                    <span class="label label-primary">Portal admin (everything)</span>
                                @elseif($user->app_role_id && isset($appRoles[$user->app_role_id]))
                                    <span class="label label-info">{{ $appRoles[$user->app_role_id] }}</span>
                                @else
                                    <span class="label label-warning">{{ \App\Http\Middleware\MobileAdmin::roleName($user) ?? 'No access' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="label label-success">Active</span>
                                @else
                                    <span class="label label-default">{{ ucfirst($user->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($user->app_sessions > 0)
                                    <span class="label label-success">{{ $user->app_sessions }} device(s)</span>
                                @else
                                    <span class="text-muted">No</span>
                                @endif
                            </td>
                            <td style="white-space: nowrap;">
                                @can('user.update')
                                    <a href="{{ route('mobile-admins.edit', $user->id) }}" class="btn btn-xs btn-primary">
                                        <i class="fa fa-edit"></i> Edit</a>
                                    @if($user->app_sessions > 0)
                                        <form action="{{ route('mobile-admins.revoke', $user->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-warning"
                                                onclick="return confirm('Sign {{ $user->username }} out of the app on all devices?')">
                                                <i class="fa fa-sign-out-alt"></i> Sign out of app</button>
                                        </form>
                                    @endif
                                @endcan
                                @can('user.delete')
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('mobile-admins.destroy', $user->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger"
                                                onclick="return confirm('{{ $isPortalAdmin ? 'Remove app access for ' . $user->username . '? Their portal account stays.' : 'Delete mobile admin ' . $user->username . '?' }}')">
                                                <i class="fa fa-trash"></i> {{ $isPortalAdmin ? 'Remove app access' : 'Delete' }}</button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No mobile admins yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endcomponent
</section>
@stop
