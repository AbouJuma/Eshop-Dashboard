<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\OTPTrait;

class PassportAuthController extends Controller
{
    use OTPTrait;

    /**
     * Authenticate user with OTP and return access token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "fname" => "nullable",
            "mname" => "nullable",
            "sname" => "nullable",
            "username" => "required",
            "phone" => "required",
            "otp" => "required",
            "email" => "nullable|email",
            "password" => "nullable|min:8",
            "fcm_token" => "nullable"
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 'error',
                'message' => $validator->errors(),
                'status_code' => 200,
            ];

            return response()->json($response, 200);
        }

        // Verify OTP only (don't update during auth)
        if (!$this->verify($request->phone, $request->otp)) {
            $response = [
                'status' => 'error',
                'message' => "OTP error",
                'status_code' => 200,
            ];

            return response()->json($response, 200);
        }

        $password = $request->phone;
        $user = User::where('phone', $request->phone)->where('username', $request->username)->first();

        if ($user) {
            if(auth()->check()) {
                return response()->json(["error" => "Already logged in"]);
            }

            // Update FCM token if provided
            if ($request->has('fcm_token') && $request->fcm_token) {
                $user->fcm_token = $request->fcm_token;
                $user->save();
                \Log::info('FCM token updated for existing user', ['user_id' => $user->id, 'fcm_token' => substr($request->fcm_token, 0, 20) . '...']);
            } else {
                \Log::warning('FCM token not provided in login request', ['user_id' => $user->id]);
            }

            auth()->login($user);
            $token = auth()->user()->createToken('ClientAuthToken')->accessToken;
            return response()->json(
                [
                    "token" => $token,
                    "client_id" => auth()->user()->id
                ], 200
            );
        } else {
            // create random username just in case and create a new client
            $randomUserName = substr($request->fname, 0, 1).substr($request->sname, 0, 1).'_'.generateRandomString(6);

            $user = User::updateOrCreate(
                [
                    "phone" => $request->phone,
                    // "password" => $request->password ? bcrypt($request->password) : bcrypt($request->phone)
                ],
                [
                    "username" => $request->username ? $request->username : $randomUserName,
                    "fcm_token" => $request->fcm_token ?? null,
                ]
            );

            if ($request->fcm_token) {
                \Log::info('FCM token saved for new user', ['user_id' => $user->id, 'fcm_token' => substr($request->fcm_token, 0, 20) . '...']);
            } else {
                \Log::warning('FCM token not provided for new user', ['user_id' => $user->id]);
            }

            $token = $user->createToken('LaravelAuthApp')->accessToken;

            return response()->json([
                'token'=> $token,
                "client_id" => $user->id
            ], 200);
        }
    }

    /**
     * Login user (alias for authenticate)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        return $this->authenticate($request);
    }

    /**
     * Logout user (revoke token)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        
        return response()->json([
            'message' => 'Successfully logged out',
            'status_code' => 200
        ], 200);
    }

    /**
     * Get authenticated user info
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'status_code' => 200
        ], 200);
    }
}
