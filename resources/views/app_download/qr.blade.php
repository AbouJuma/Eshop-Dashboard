<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D WORLD - App QR Code Generator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            background: #f4f6f9;
            color: #2c3e50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .container {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            max-width: 460px;
            width: 100%;
            padding: 40px 32px;
            text-align: center;
        }

        .logo-title {
            font-size: 24px;
            font-weight: 700;
            color: #4f7ec4;
            margin-bottom: 4px;
            letter-spacing: 1px;
        }

        .logo-sub {
            font-size: 13px;
            font-weight: 600;
            color: #888;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .qr-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 20px;
            padding: 24px;
            display: inline-block;
            margin-bottom: 24px;
        }

        .qr-box img {
            width: 240px;
            height: 240px;
            display: block;
            margin: 0 auto;
        }

        .info-text {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 28px;
            line-height: 1.5;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 24px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: #4f7ec4;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #3e68a8;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(79, 126, 196, 0.3);
        }

        .btn-outline {
            background: #ffffff;
            color: #4f7ec4;
            border: 1px solid #cbd5e1;
        }

        .btn-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #1e293b;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 500;
            opacity: 0;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="logo-title">D WORLD</h1>
        <p class="logo-sub">DADIS APP QR CODE</p>

        <div class="qr-box">
            <img src="data:image/png;base64,{{ $qrBase64 }}" alt="D WORLD App QR Code">
        </div>

        <p class="info-text">
            Scanning this QR code directs users to the <strong>D WORLD</strong> landing page where they can choose to download the app for <strong>iOS (App Store)</strong> or <strong>Android (Google Play)</strong>.
        </p>

        <div class="actions">
            <a href="{{ route('app.download.qr.download') }}" class="btn btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Download QR Code (PNG)
            </a>

            <button class="btn btn-outline" onclick="copyLink()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
                Copy Download Link
            </button>

            <a href="{{ route('app.download') }}" class="btn btn-outline" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
                Preview Landing Page
            </a>
        </div>
    </div>

    <div class="toast" id="toast">Link copied to clipboard!</div>

    <script>
        function copyLink() {
            navigator.clipboard.writeText("{{ $downloadPageUrl }}").then(function() {
                const toast = document.getElementById('toast');
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 2500);
            });
        }
    </script>
</body>
</html>
