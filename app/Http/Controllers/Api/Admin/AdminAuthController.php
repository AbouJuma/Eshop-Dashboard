<?php

namespace App\Http\Controllers\Api\Admin;

use App\Business;
use App\Http\Controllers\Controller;
use App\Http\Middleware\MobileAdmin;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    /**
     * Log a portal admin into the mobile app with their portal username & password.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->whereNotNull('business_id')->first();

        if (! $user || ! $user->password || ! Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid username or password'], 401);
        }

        if (! MobileAdmin::isAdmin($user)) {
            return response()->json(['success' => false, 'message' => 'This account does not have admin access'], 403);
        }

        if ($request->filled('fcm_token')) {
            $user->fcm_token = $request->fcm_token;
            $user->save();
        }

        $token = $user->createToken(MobileAdmin::TOKEN_NAME)->accessToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $this->userPayload($user),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(['success' => true, 'user' => $this->userPayload($request->user())]);
    }

    /** Device push token, so new roadside requests ring on this admin's phone. */
    public function updateFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string|max:255']);
        $user = $request->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['success' => true, 'message' => 'Logged out']);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'name' => trim($user->first_name.' '.$user->last_name),
            'email' => $user->email,
            'business_id' => $user->business_id,
            'business_name' => optional(Business::find($user->business_id))->name,
        ];
    }
}
