<?php

namespace App\Http\Controllers;

use App\Http\Middleware\MobileAdmin;
use App\Models\AppRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Portal CRUD for the roles used in the mobile app's admin panel (what each app account can see and do).
 */
class AppRoleController extends Controller
{
    public function index()
    {
        $this->authorizeAccess('roles.view');
        $businessId = $this->businessId();
        AppRole::ensureDefaults($businessId);

        $roles = AppRole::where('business_id', $businessId)->withCount('users')->orderByDesc('is_system')->orderBy('name')->get();

        return view('app_roles.index', ['roles' => $roles, 'catalogue' => AppRole::PERMISSIONS]);
    }

    public function create()
    {
        $this->authorizeAccess('roles.create');

        return view('app_roles.form', ['role' => new AppRole(['permissions' => []]), 'groups' => AppRole::groupedPermissions()]);
    }

    public function store(Request $request)
    {
        $this->authorizeAccess('roles.create');
        $data = $this->validated($request);

        AppRole::create([
            'business_id' => $this->businessId(),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'permissions' => AppRole::normalize($data['permissions'] ?? []),
        ]);

        return redirect()->route('app-roles.index')->with('status', ['success' => 1, 'msg' => "Role {$data['name']} created."]);
    }

    public function edit($id)
    {
        $this->authorizeAccess('roles.update');

        return view('app_roles.form', ['role' => $this->findRole($id), 'groups' => AppRole::groupedPermissions()]);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAccess('roles.update');
        $role = $this->findRole($id);
        $data = $this->validated($request, $role);

        $role->update([
            'name' => $role->is_system ? $role->name : $data['name'], // built-in roles keep their name
            'description' => $data['description'] ?? null,
            'permissions' => AppRole::normalize($data['permissions'] ?? []),
        ]);

        return redirect()->route('app-roles.index')
            ->with('status', ['success' => 1, 'msg' => "Role {$role->name} updated. The app picks up the change the next time it opens."]);
    }

    public function destroy($id)
    {
        $this->authorizeAccess('roles.delete');
        $role = $this->findRole($id);

        if ($role->is_system) {
            return redirect()->back()->with('status', ['success' => 0, 'msg' => 'Built-in roles cannot be deleted.']);
        }
        if ($role->users()->exists()) {
            return redirect()->back()->with('status', ['success' => 0, 'msg' => "{$role->name} is still given to {$role->users()->count()} account(s). Move them to another role first."]);
        }

        $role->delete();

        return redirect()->route('app-roles.index')->with('status', ['success' => 1, 'msg' => "Role {$role->name} deleted."]);
    }

    private function validated(Request $request, ?AppRole $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100',
                Rule::unique('app_roles', 'name')->where('business_id', $this->businessId())->ignore(optional($role)->id)],
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => ['string', Rule::in(AppRole::allPermissions())],
        ]);
    }

    private function findRole($id): AppRole
    {
        return AppRole::where('business_id', $this->businessId())->findOrFail($id);
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
