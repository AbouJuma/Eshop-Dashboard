<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Milon\Barcode\DNS2D;

class AppDownloadController extends Controller
{
    /**
     * iOS App Store URL
     */
    protected string $iosUrl = 'https://apps.apple.com/tz/app/d-world-tanzania/id6499099643';

    /**
     * Android Google Play Store URL
     */
    protected string $androidUrl = 'https://play.google.com/store/apps/details?id=com.dads.garage';

    /**
     * Helper to get the public landing page URL.
     * Uses current request domain (Host header) to prevent IP/localhost issues.
     */
    protected function getAppDownloadUrl(Request $request): string
    {
        // 1. If explicit URL passed in query parameter (e.g. ?target_url=...)
        if ($request->has('target_url') && filter_var($request->get('target_url'), FILTER_VALIDATE_URL)) {
            return $request->get('target_url');
        }

        // 2. Check APP_URL from .env / config
        $configUrl = config('app.url');
        if (!empty($configUrl) 
            && !str_contains($configUrl, 'localhost') 
            && !str_contains($configUrl, '127.0.0.1')
            && !preg_match('/^https?:\/\/(192\.168|10\.|172\.(1[6-9]|2[0-9]|3[0-1]))/', $configUrl)) {
            return rtrim($configUrl, '/') . '/app-download';
        }

        // 3. Dynamic HTTP Host from request (e.g., https://yourdomain.com/app-download)
        $scheme = $request->isSecure() || $request->header('X-Forwarded-Proto') === 'https' ? 'https' : $request->getScheme();
        $host = $request->header('X-Forwarded-Host') ?? $request->getHttpHost();

        return $scheme . '://' . $host . '/app-download';
    }

    /**
     * Display the public landing page with iOS & Android links.
     */
    public function index(Request $request)
    {
        $iosUrl = $this->iosUrl;
        $androidUrl = $this->androidUrl;
        $downloadPageUrl = $this->getAppDownloadUrl($request);

        // Generate base64 QR code image targeting this landing page URL
        $dns2d = new DNS2D();
        $qrBase64 = $dns2d->getBarcodePNG($downloadPageUrl, 'QRCODE', 8, 8);

        return view('app_download.index', compact('iosUrl', 'androidUrl', 'downloadPageUrl', 'qrBase64'));
    }

    /**
     * Display the QR Code manager page with preview and download options.
     */
    public function qr(Request $request)
    {
        $iosUrl = $this->iosUrl;
        $androidUrl = $this->androidUrl;
        $downloadPageUrl = $this->getAppDownloadUrl($request);

        $dns2d = new DNS2D();
        // Generate high resolution QR code (12x12 grid factor)
        $qrBase64 = $dns2d->getBarcodePNG($downloadPageUrl, 'QRCODE', 12, 12);

        return view('app_download.qr', compact('iosUrl', 'androidUrl', 'downloadPageUrl', 'qrBase64'));
    }

    /**
     * Download the QR code as a PNG file.
     */
    public function downloadQr(Request $request)
    {
        $downloadPageUrl = $this->getAppDownloadUrl($request);

        $dns2d = new DNS2D();
        // High resolution QR code (15x15 grid factor for clean printing & scanning)
        $qrBase64 = $dns2d->getBarcodePNG($downloadPageUrl, 'QRCODE', 15, 15);

        $imageData = base64_decode($qrBase64);

        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="d-world-app-qr.png"');
    }
}
