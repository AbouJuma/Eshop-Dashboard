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
     * Display the public landing page with iOS & Android links.
     */
    public function index()
    {
        $iosUrl = $this->iosUrl;
        $androidUrl = $this->androidUrl;
        $downloadPageUrl = route('app.download');

        // Generate base64 QR code image targeting this landing page URL
        $dns2d = new DNS2D();
        $qrBase64 = $dns2d->getBarcodePNG($downloadPageUrl, 'QRCODE', 8, 8);

        return view('app_download.index', compact('iosUrl', 'androidUrl', 'downloadPageUrl', 'qrBase64'));
    }

    /**
     * Display the QR Code manager page with preview and download options.
     */
    public function qr()
    {
        $iosUrl = $this->iosUrl;
        $androidUrl = $this->androidUrl;
        $downloadPageUrl = route('app.download');

        $dns2d = new DNS2D();
        // Generate high resolution QR code (12x12 grid factor)
        $qrBase64 = $dns2d->getBarcodePNG($downloadPageUrl, 'QRCODE', 12, 12);

        return view('app_download.qr', compact('iosUrl', 'androidUrl', 'downloadPageUrl', 'qrBase64'));
    }

    /**
     * Download the QR code as a PNG file.
     */
    public function downloadQr()
    {
        $downloadPageUrl = route('app.download');

        $dns2d = new DNS2D();
        // High resolution QR code (15x15 grid factor for clean printing & scanning)
        $qrBase64 = $dns2d->getBarcodePNG($downloadPageUrl, 'QRCODE', 15, 15);

        $imageData = base64_decode($qrBase64);

        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="d-world-app-qr.png"');
    }
}
