<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;

/**
 * Allows only portal admins (role Admin#<business_id>) through the mobile admin API.
 * Must run after auth:api.
 */
class MobileAdmin
{
    const TOKEN_NAME = 'MobileAdminToken';

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Only tokens issued by the admin password login are accepted. Customer tokens (OTP login)
        // live in the same users table and must never grant admin access, even for an admin's phone.
        $token = $user ? $user->token() : null;

        if (! $user || ! $token || $token->name !== self::TOKEN_NAME || ! self::isAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to access the admin panel.',
            ], 403);
        }

        return $next($request);
    }

    /** Role for users who may use the mobile admin panel without being full portal admins. */
    public static function mobileRoleName($businessId): string
    {
        return 'Mobile Admin#'.$businessId;
    }

    public static function isAdmin(User $user): bool
    {
        return ! empty($user->business_id)
            && $user->status === 'active'
            && $user->allow_login
            && ($user->hasRole('Admin#'.$user->business_id) || $user->hasRole(self::mobileRoleName($user->business_id)));
    }
}
