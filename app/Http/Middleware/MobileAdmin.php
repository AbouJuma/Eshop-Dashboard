<?php

namespace App\Http\Middleware;

use App\Models\AppRole;
use App\User;
use Closure;
use Illuminate\Http\Request;

/**
 * Gate for the mobile admin API. Must run after auth:api.
 *
 * Who may use the app panel, and what they may do:
 *  - portal admins (Admin#<bid>): every permission
 *  - users with an app role (users.app_role_id): that role's permissions (see AppRole::PERMISSIONS)
 *  - legacy Spatie roles Mobile Admin#<bid> / Mechanic#<bid> (before app roles existed)
 *
 * `mobile.admin` = any app account; `mobile.admin:orders.view,orders.manage` = needs at least one of them.
 */
class MobileAdmin
{
    const TOKEN_NAME = 'MobileAdminToken';

    /** Per-request cache: user id => permissions|null */
    private static $cache = [];

    public function handle(Request $request, Closure $next, ...$required)
    {
        $user = $request->user();

        // Only tokens issued by the admin password login are accepted. Customer tokens (OTP login)
        // live in the same users table and must never grant admin access, even for an admin's phone.
        $token = $user ? $user->token() : null;
        $permissions = $user ? self::permissions($user) : null;

        if (! $user || ! $token || $token->name !== self::TOKEN_NAME || $permissions === null) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to access the admin panel.',
            ], 403);
        }

        if ($required && ! array_intersect($required, $permissions)) {
            // 404, not 403: the app treats 403 as "signed out"
            return response()->json(['success' => false, 'message' => 'Your role does not allow this.'], 404);
        }

        return $next($request);
    }

    /** Legacy role names (kept so accounts created before app roles keep working). */
    public static function mobileRoleName($businessId): string
    {
        return 'Mobile Admin#'.$businessId;
    }

    public static function mechanicRoleName($businessId): string
    {
        return 'Mechanic#'.$businessId;
    }

    public static function isPortalAdmin(User $user): bool
    {
        return $user->hasRole('Admin#'.$user->business_id);
    }

    /** Permission keys the user has in the app panel, or null when they may not use it at all. */
    public static function permissions(User $user): ?array
    {
        if (array_key_exists($user->id, self::$cache)) {
            return self::$cache[$user->id];
        }

        return self::$cache[$user->id] = self::resolve($user);
    }

    private static function resolve(User $user): ?array
    {
        if (empty($user->business_id) || $user->status !== 'active' || ! $user->allow_login) {
            return null;
        }
        if (self::isPortalAdmin($user)) {
            return AppRole::allPermissions();
        }
        if ($user->app_role_id) {
            $role = AppRole::where('business_id', $user->business_id)->find($user->app_role_id);

            return $role ? AppRole::normalize($role->permissions ?? []) : null;
        }
        if ($user->hasRole(self::mobileRoleName($user->business_id))) {
            return AppRole::DEFAULTS['App Admin']['permissions'];
        }
        if ($user->hasRole(self::mechanicRoleName($user->business_id))) {
            return AppRole::DEFAULTS['Mechanic']['permissions'];
        }

        return null;
    }

    /** Forget cached permissions (after a role change in the same request). */
    public static function flush(?User $user = null): void
    {
        if ($user) {
            unset(self::$cache[$user->id]);
        } else {
            self::$cache = [];
        }
    }

    public static function can(User $user, string ...$anyOf): bool
    {
        return (bool) array_intersect($anyOf, self::permissions($user) ?? []);
    }

    /** May use the app panel at all. */
    public static function isAdmin(User $user): bool
    {
        return self::permissions($user) !== null;
    }

    /** Name shown in the app and the portal. */
    public static function roleName(User $user): ?string
    {
        if (self::permissions($user) === null) {
            return null;
        }
        if (self::isPortalAdmin($user)) {
            return 'Portal Admin';
        }
        if ($user->app_role_id && ($role = AppRole::find($user->app_role_id))) {
            return $role->name;
        }

        return $user->hasRole(self::mechanicRoleName($user->business_id)) ? 'Mechanic' : 'App Admin';
    }
}
