<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;

/**
 * Gate for the mobile admin API. Must run after auth:api.
 *
 * Access levels:
 *  - admin:    portal admins (Admin#<bid>) and app admins (Mobile Admin#<bid>) — everything
 *  - mechanic: Mechanic#<bid> — roadside requests only, and only the jobs they accepted
 *
 * Use `mobile.admin` for routes both may use and `mobile.admin:full` for admin-only routes.
 */
class MobileAdmin
{
    const TOKEN_NAME = 'MobileAdminToken';

    const LEVEL_ADMIN = 'admin';
    const LEVEL_MECHANIC = 'mechanic';

    public function handle(Request $request, Closure $next, ?string $required = null)
    {
        $user = $request->user();

        // Only tokens issued by the admin password login are accepted. Customer tokens (OTP login)
        // live in the same users table and must never grant admin access, even for an admin's phone.
        $token = $user ? $user->token() : null;
        $level = $user ? self::accessLevel($user) : null;

        if (! $user || ! $token || $token->name !== self::TOKEN_NAME || ! $level) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to access the admin panel.',
            ], 403);
        }

        if ($required === 'full' && $level !== self::LEVEL_ADMIN) {
            // 404, not 403: the app treats 403 as "signed out"
            return response()->json(['success' => false, 'message' => 'Not available for mechanics.'], 404);
        }

        return $next($request);
    }

    /** Role for users who may use the whole mobile admin panel without being full portal admins. */
    public static function mobileRoleName($businessId): string
    {
        return 'Mobile Admin#'.$businessId;
    }

    /** Role for mechanics who only attend roadside requests. */
    public static function mechanicRoleName($businessId): string
    {
        return 'Mechanic#'.$businessId;
    }

    /** 'admin', 'mechanic', or null when the user may not use the mobile admin panel. */
    public static function accessLevel(User $user): ?string
    {
        if (empty($user->business_id) || $user->status !== 'active' || ! $user->allow_login) {
            return null;
        }
        if ($user->hasRole('Admin#'.$user->business_id) || $user->hasRole(self::mobileRoleName($user->business_id))) {
            return self::LEVEL_ADMIN;
        }
        if ($user->hasRole(self::mechanicRoleName($user->business_id))) {
            return self::LEVEL_MECHANIC;
        }

        return null;
    }

    public static function isAdmin(User $user): bool
    {
        return self::accessLevel($user) !== null;
    }

    public static function isMechanic(User $user): bool
    {
        return self::accessLevel($user) === self::LEVEL_MECHANIC;
    }
}
