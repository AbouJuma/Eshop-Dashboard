<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D WORLD - DADIS App</title>
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
            background: linear-gradient(180deg, #4f7ec4 0%, #6fa0ba 45%, #84c09e 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 60px 20px 40px;
            color: #ffffff;
        }

        .header {
            text-align: center;
            margin-bottom: 35px;
        }

        .header h1 {
            font-size: 26px;
            font-weight: 600;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 2px;
            opacity: 0.9;
            text-transform: uppercase;
        }

        .card {
            background: #ffffff;
            width: 100%;
            max-width: 380px;
            border-radius: 20px;
            padding: 30px 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .store-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            text-decoration: none;
            padding: 14px 20px;
            border-radius: 30px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            width: 100%;
            cursor: pointer;
        }

        .store-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .store-btn:active {
            transform: translateY(0);
        }

        .btn-appstore {
            background-color: #3b3b3d;
            color: #ffffff;
        }

        .btn-googleplay {
            background-color: #000000;
            color: #ffffff;
        }

        .store-icon {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .store-icon svg {
            width: 100%;
            height: 100%;
            fill: currentColor;
        }

        .store-text {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
        }

        .store-text .subtitle {
            font-size: 11px;
            font-weight: 400;
            line-height: 1.2;
            opacity: 0.95;
        }

        .store-text .title {
            font-size: 18px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.2px;
        }

        .footer-tools {
            margin-top: 40px;
            text-align: center;
        }

        .qr-badge-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 10px 18px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .qr-badge-btn:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: translateY(-2px);
        }

        .qr-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
            padding: 20px;
        }

        .qr-modal-overlay.active {
            display: flex;
        }

        .qr-modal-card {
            background: #ffffff;
            color: #1a1a1a;
            border-radius: 20px;
            padding: 30px;
            max-width: 340px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .qr-modal-card h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #222;
        }

        .qr-modal-card p {
            font-size: 13px;
            color: #666;
            margin-bottom: 20px;
        }

        .qr-modal-card img {
            width: 200px;
            height: 200px;
            border-radius: 12px;
            border: 1px solid #eeeeee;
            padding: 8px;
            margin-bottom: 20px;
        }

        .qr-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-download-qr {
            background: #4f7ec4;
            color: #ffffff;
            padding: 12px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.2s ease;
        }

        .btn-download-qr:hover {
            background: #3e68a8;
        }

        .btn-close-modal {
            background: #f1f3f5;
            color: #555;
            padding: 10px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
        }

        .btn-close-modal:hover {
            background: #e4e7eb;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>D WORLD</h1>
        <p>DADIS</p>
    </div>

    <div class="card">
        <!-- App Store Button -->
        <a href="{{ $iosUrl }}" target="_blank" rel="noopener noreferrer" class="store-btn btn-appstore">
            <div class="store-icon">
                <svg viewBox="0 0 170 170" xmlns="http://www.w3.org/2000/svg">
                    <path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.34.13-9.13-1.9-14.35-6.1-3.68-3.05-7.68-7.85-12.01-14.4-7.23-10.9-12.83-22.9-16.79-35.98-3.96-13.08-5.94-25.29-5.94-36.63 0-14.18 3.57-26.06 10.7-35.64 7.14-9.58 16.12-14.49 26.96-14.74 4.58 0 9.87 1.25 15.87 3.75 6 2.5 10.15 3.75 12.44 3.75 2.01 0 6.13-1.25 12.35-3.75 6.22-2.5 11.29-3.7 15.22-3.62 9.49.5 17.5 4.12 24.03 10.86 6.53 6.74 10.55 14.88 12.06 24.41-10.74 6.46-16.03 15.42-15.87 26.89.17 9.07 3.6 16.65 10.29 22.75 6.7 6.1 14.8 9.53 24.31 10.29-2.45 7.18-5.74 14.44-9.87 21.78zM119.22 31.86c0-6.79 2.45-13.25 7.35-19.38 4.9-6.13 11.05-10.17 18.45-12.12.84 8.04-1.26 15.28-6.3 21.72-5.04 6.44-11.24 10.4-18.6 11.88-.34-.73-.55-1.43-.6-2.1-.2-1.07-.3-2.17-.3-3.32z"/>
                </svg>
            </div>
            <div class="store-text">
                <span class="subtitle">Download on the</span>
                <span class="title">App Store</span>
            </div>
        </a>

        <!-- Google Play Button -->
        <a href="{{ $androidUrl }}" target="_blank" rel="noopener noreferrer" class="store-btn btn-googleplay">
            <div class="store-icon">
                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                    <path d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1zM47 0C34 6.8 25.3 19.2 25.3 35.3v441.3c0 16.1 8.7 28.5 21.7 35.3l256.6-256L47 0zm425.2 225.6l-58.9-34.1-65.7 64.5 65.7 64.5 60.1-34.1c18-14.3 18-46.5-1.2-60.8zM104.6 499l220.7-221.3 60.1 60.1L104.6 499z"/>
                </svg>
            </div>
            <div class="store-text">
                <span class="subtitle">Get it on</span>
                <span class="title">Google Play</span>
            </div>
        </a>
    </div>

    <!-- QR Code Option -->
    <div class="footer-tools">
        <button class="qr-badge-btn" onclick="openQrModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            View / Download QR Code
        </button>
    </div>

    <!-- QR Modal -->
    <div class="qr-modal-overlay" id="qrModal">
        <div class="qr-modal-card">
            <h3>D WORLD App QR Code</h3>
            <p>Scan to open this download page on any phone</p>
            <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code">
            <div class="qr-actions">
                <a href="{{ route('app.download.qr.download') }}" class="btn-download-qr">
                    📥 Download QR Code (PNG)
                </a>
                <button class="btn-close-modal" onclick="closeQrModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        function openQrModal() {
            document.getElementById('qrModal').classList.add('active');
        }
        function closeQrModal() {
            document.getElementById('qrModal').classList.remove('active');
        }
        document.getElementById('qrModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQrModal();
            }
        });
    </script>
</body>
</html>
