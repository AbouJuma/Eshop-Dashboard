<?php

namespace App\Http\Controllers;

use App\Http\Middleware\MobileAdmin;
use App\Models\AppRole;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Portal CRUD for accounts that can sign in to the mobile app's admin panel
 * (orders, bookings, car maintenance, roadside requests).
 */
class MobileAdminController extends Controller
{
    public function index()
    {
        $this->authorizeAccess('user.view');
        $businessId = $this->businessId();

        AppRole::ensureDefaults($businessId);
        $appRoles = AppRole::where('business_id', $businessId)->pluck('name', 'id');
        $users = User::where('business_id', $businessId)
            ->where(fn ($q) => $q->whereNotNull('app_role_id')
                ->orWhereHas('roles', fn ($r) => $r->whereIn('name', [MobileAdmin::mobileRoleName($businessId), MobileAdmin::mechanicRoleName($businessId), 'Admin#'.$businessId])))
            ->with('roles')
            ->withCount(['tokens as app_sessions' => fn ($q) => $q->where('name', MobileAdmin::TOKEN_NAME)->where('revoked', false)])
            ->orderBy('first_name')
            ->get();

        return view('mobile_admin.index', compact('users', 'appRoles', 'businessId'));
    }

    public function create()
    {
        $this->authorizeAccess('user.create');

        return view('mobile_admin.form', ['user' => new User(), 'roles' => $this->roles()]);
    }

    public function store(Request $request)
    {
        $this->authorizeAccess('user.create');
        $businessId = $this->businessId();
        $data = $this->validated($request);

        $user = new User();
        $this->fill($user, $data, $businessId);
        $user->password = bcrypt($data['password']);
        $user->save();
        $this->setRole($user, (int) $data['app_role_id']);

        return redirect()->route('mobile-admins.index')
            ->with('status', ['success' => 1, 'msg' => "{$user->username} created. They can now sign in to the app."]);
    }

    public function edit($id)
    {
        $this->authorizeAccess('user.update');

        return view('mobile_admin.form', ['user' => $this->findUser($id), 'roles' => $this->roles()]);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAccess('user.update');
        $user = $this->findUser($id);
        $data = $this->validated($request, $user);

        $this->fill($user, $data, $user->business_id);
        if (! empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->save();
        $accessChanged = $this->setRole($user, (int) $data['app_role_id']);

        // deactivated, password or role changed: sign them out so the app reloads with the new permissions
        if ($user->status !== 'active' || ! empty($data['password']) || $accessChanged) {
            $this->revokeAppSessions($user);
        }

        return redirect()->route('mobile-admins.index')
            ->with('status', ['success' => 1, 'msg' => "Mobile admin {$user->username} updated."]);
    }

    public function destroy($id)
    {
        $this->authorizeAccess('user.delete');
        $user = $this->findUser($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('status', ['success' => 0, 'msg' => 'You cannot delete your own account.']);
        }

        $this->revokeAppSessions($user);

        // portal admins only lose app access; mobile-only accounts are removed
        if ($user->hasRole('Admin#'.$user->business_id)) {
            $this->clearLegacyRoles($user);
            $user->app_role_id = null;
            $user->save();
            $msg = "{$user->username} is a portal admin; their app sessions were signed out.";
        } else {
            $user->delete();
            $msg = "Mobile admin {$user->username} deleted.";
        }

        return redirect()->route('mobile-admins.index')->with('status', ['success' => 1, 'msg' => $msg]);
    }

    /** Sign the user out of the mobile app on all devices. */
    public function revoke($id)
    {
        $this->authorizeAccess('user.update');
        $user = $this->findUser($id);
        $this->revokeAppSessions($user);

        return redirect()->back()->with('status', ['success' => 1, 'msg' => "{$user->username} was signed out of the app."]);
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'nullable|string|max:191',
            'username' => ['required', 'string', 'max:191', 'regex:/^[A-Za-z0-9_.\-]+$/',
                Rule::unique('users', 'username')->ignore(optional($user)->id)],
            'email' => 'nullable|email|max:191',
            'contact_no' => 'nullable|string|max:15',
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'status' => 'required|in:active,inactive',
            'app_role_id' => ['required', 'integer', Rule::exists('app_roles', 'id')->where('business_id', $this->businessId())],
        ], ['username.regex' => 'Username may only contain letters, numbers, dots, dashes and underscores.']);
    }

    private function fill(User $user, array $data, $businessId): void
    {
        // these attributes are $guarded on App\User, so assign them directly
        $user->user_type = 'user';
        $user->business_id = $businessId;
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'] ?? null;
        $user->username = $data['username'];
        $user->email = $data['email'] ?? null;
        $user->contact_no = $data['contact_no'] ?? null;
        $user->language = $user->language ?: 'en';
        $user->allow_login = 1;
        $user->status = $data['status'];
    }

    private function findUser($id): User
    {
        return User::where('business_id', $this->businessId())->findOrFail($id);
    }

    private function roles()
    {
        AppRole::ensureDefaults($this->businessId());

        return AppRole::where('business_id', $this->businessId())->orderByDesc('is_system')->orderBy('name')->get();
    }

    /** Remove the pre-app-role Spatie roles (Mobile Admin#<bid> / Mechanic#<bid>), if the user still has them. */
    private function clearLegacyRoles(User $user): void
    {
        foreach ([MobileAdmin::mobileRoleName($user->business_id), MobileAdmin::mechanicRoleName($user->business_id)] as $legacy) {
            if ($user->hasRole($legacy)) {
                $user->removeRole($legacy);
            }
        }
        $user->unsetRelation('roles');
    }

    /** Give the user an app role. Returns true when their permissions changed. */
    private function setRole(User $user, int $roleId): bool
    {
        $before = MobileAdmin::permissions($user);
        $user->app_role_id = $roleId;
        $user->save();
        $this->clearLegacyRoles($user);
        MobileAdmin::flush($user);

        return $before !== null && $before != MobileAdmin::permissions($user);
    }
    private function revokeAppSessions(User $user): void
    {
        $user->tokens()->where('name', MobileAdmin::TOKEN_NAME)->update(['revoked' => true]);
    }

    private function businessId()
    {
        return request()->session()->get('user.business_id');
    }

    private function authorizeAccess(string $permission): void
    {
        if (! auth()->user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }
    }
}
