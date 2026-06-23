<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public static function create($userId, $title, $content, $actionTime = null)
    {
        \Log::info('Creating notification', ['user_id' => $userId, 'title' => $title, 'content' => $content]);
        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'action_time' => $actionTime ?? now(),
        ]);
        \Log::info('Notification created', ['id' => $notification->id, 'user_id' => $userId]);

        // Send FCM push notification using HTTP v1 API
        self::sendFCMNotification($userId, $title, $content);

        return $notification;
    }

    /**
     * Send FCM push notification to user using HTTP v1 API
     */
    private static function sendFCMNotification($userId, $title, $content)
    {
        \Log::info('Attempting to send FCM notification', ['user_id' => $userId, 'title' => $title]);

        $user = User::find($userId);
        if (!$user) {
            \Log::warning('Cannot send FCM notification: user not found', ['user_id' => $userId]);
            return;
        }

        \Log::info('User found', ['user_id' => $userId, 'fcm_token_value' => $user->fcm_token, 'fcm_token_is_null' => is_null($user->fcm_token), 'fcm_token_is_empty' => empty($user->fcm_token)]);

        if (!$user->fcm_token) {
            \Log::warning('Cannot send FCM notification: FCM token not found for user', ['user_id' => $userId]);
            return;
        }

        \Log::info('User and FCM token found', ['user_id' => $userId, 'fcm_token' => substr($user->fcm_token, 0, 20) . '...']);

        $credentialsPath = base_path(env('FIREBASE_CREDENTIALS', 'firebase-credentials.json'));
        if (!file_exists($credentialsPath)) {
            \Log::error('Firebase credentials file not found', ['path' => $credentialsPath]);
            return;
        }

        \Log::info('Firebase credentials file found', ['path' => $credentialsPath]);

        try {
            $credentials = json_decode(file_get_contents($credentialsPath), true);
            if (!$credentials) {
                \Log::error('Failed to decode Firebase credentials');
                return;
            }

            \Log::info('Firebase credentials decoded successfully', ['project_id' => $credentials['project_id']]);

            // Get OAuth 2.0 access token
            $accessToken = self::getAccessToken($credentials);
            if (!$accessToken) {
                \Log::error('Failed to get OAuth access token');
                return;
            }

            \Log::info('OAuth access token obtained successfully', ['token_length' => strlen($accessToken)]);

            $projectId = $credentials['project_id'];
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $message = [
                'message' => [
                    'token' => $user->fcm_token,
                    'notification' => [
                        'title' => $title,
                        'body' => $content,
                    ],
                    'data' => [
                        'title' => $title,
                        'body' => $content,
                        'type' => 'notification',
                    ],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ];

            \Log::info('Sending FCM request', ['url' => $url, 'token' => substr($user->fcm_token, 0, 20) . '...']);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $message);

            \Log::info('FCM notification sent via HTTP v1 API', [
                'user_id' => $userId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send FCM notification', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Get OAuth 2.0 access token from service account credentials
     */
    private static function getAccessToken($credentials)
    {
        $now = time();
        $iat = $now;
        $exp = $now + 3600; // 1 hour

        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $payload = [
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $iat,
            'exp' => $exp,
        ];

        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        $signature = self::sign($headerEncoded . '.' . $payloadEncoded, $credentials['private_key']);
        $jwt = $headerEncoded . '.' . $payloadEncoded . '.' . $signature;

        // Exchange JWT for access token
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'] ?? null;
        }

        return null;
    }

    /**
     * Base64 URL encode
     */
    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Sign data with private key
     */
    private static function sign($data, $privateKey)
    {
        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return self::base64UrlEncode($signature);
    }

    /**
     * Create notification for multiple users
     */
    public static function createBulk($userIds, $title, $content, $actionTime = null)
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => $title,
                'content' => $content,
                'action_time' => $actionTime ?? now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return Notification::insert($notifications);
    }

    /**
     * Create notification for user by phone
     */
    public static function createByPhone($phone, $title, $content, $actionTime = null)
    {
        $user = User::where('phone', $phone)->first();
        if ($user) {
            return self::create($user->id, $title, $content, $actionTime);
        }
        return null;
    }

    /**
     * Common notification templates
     */
    public static function orderCreated($userId, $orderNumber)
    {
        return self::create($userId, 'Order Created', "Your order #{$orderNumber} has been created successfully.");
    }

    public static function orderShipped($userId, $orderNumber)
    {
        return self::create($userId, 'Order Shipped', "Your order #{$orderNumber} has been shipped and is on its way.");
    }

    public static function orderDelivered($userId, $orderNumber)
    {
        return self::create($userId, 'Order Delivered', "Your order #{$orderNumber} has been delivered successfully.");
    }

    public static function paymentReceived($userId, $amount)
    {
        return self::create($userId, 'Payment Received', "We have received your payment of {$amount}.");
    }

    public static function bookingConfirmed($userId, $bookingId, $referenceNumber = null)
    {
        $refNumber = $referenceNumber ?? $bookingId;
        return self::create($userId, 'Booking Confirmed', "Your booking #{$refNumber} has been confirmed.");
    }

    public static function bookingCancelled($userId, $bookingId, $referenceNumber = null)
    {
        $refNumber = $referenceNumber ?? $bookingId;
        return self::create($userId, 'Booking Cancelled', "Your booking #{$refNumber} has been cancelled.");
    }

    public static function bookingCompleted($userId, $bookingId, $referenceNumber = null)
    {
        $refNumber = $referenceNumber ?? $bookingId;
        return self::create($userId, 'Booking Completed', "Your booking #{$refNumber} has been completed successfully.");
    }

    public static function bookingStatusChanged($userId, $bookingId, $referenceNumber, $status, $message)
    {
        $refNumber = $referenceNumber ?? $bookingId;
        $title = 'Booking ' . ucfirst($status);
        return self::create($userId, $title, $message);
    }

    public static function orderStatusChanged($userId, $orderNumber, $status, $message)
    {
        $title = 'Order ' . ucfirst($status);
        return self::create($userId, $title, $message);
    }
}
