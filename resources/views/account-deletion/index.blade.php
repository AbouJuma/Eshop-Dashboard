<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deletion - D WORLD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .deletion-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-section h1 {
            color: #764ba2;
            font-weight: bold;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .logo-section p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .warning-box h5 {
            color: #856404;
            margin-bottom: 10px;
        }
        .form-control:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.25);
        }
        .btn-danger {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(238, 90, 36, 0.3);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .confirmation-text {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            color: #dc3545;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="deletion-container">
        <div class="logo-section">
            <h1>D WORLD</h1>
            <p>Account Deletion Request</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="warning-box">
            <h5>⚠️ Important Notice</h5>
            <p class="mb-0"><strong>This action cannot be undone.</strong> Once your account is suspended, you will lose access to all services, bookings, orders, and personal data associated with your account.</p>
        </div>

        <form action="{{ route('account.deletion.process') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="identifier" class="form-label">Email Address or Phone Number</label>
                <input type="text" class="form-control" id="identifier" name="identifier" required 
                       placeholder="Enter your email or phone number">
                <div class="form-text">Enter the email address or phone number associated with your account.</div>
            </div>

            <div class="mb-4">
                <label for="confirmation" class="form-label">Type DELETE to Confirm</label>
                <div class="confirmation-text">DELETE</div>
                <input type="text" class="form-control" id="confirmation" name="confirmation" required 
                       placeholder="Type DELETE exactly as shown above">
                <div class="form-text">This confirms that you understand this action is permanent.</div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-danger">
                    🗑️ Suspend My Account
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <p class="mb-2">
                <a href="{{ route('privacy.policy') }}" class="text-muted text-decoration-none">
                    <i class="fas fa-shield-alt"></i> Privacy Policy
                </a>
            </p>
            <p class="small text-muted">
                Need help? Contact us at 
                <a href="mailto:info@dadisonestop.com" class="text-muted">info@dadisonestop.com</a>
            </p>
            <small class="text-muted">
                By proceeding, you confirm that you want to suspend your account and understand that this action is irreversible.
                <br>
                If you have any questions, please contact our support team.
            </small>
        </div>

        <div class="text-center mt-3">
            <a href="/" class="btn btn-outline-secondary btn-sm">
                ← Back to Home
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Prevent form submission if confirmation doesn't match exactly
        document.querySelector('form').addEventListener('submit', function(e) {
            const confirmation = document.getElementById('confirmation').value;
            if (confirmation !== 'DELETE') {
                e.preventDefault();
                alert('Please type DELETE exactly as shown to confirm account suspension.');
            }
        });
    </script>
</body>
</html>
