<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Account Deletion - D WORLD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }
        .request-container {
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
        .info-box {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .info-box h5 {
            color: #1565c0;
            margin-bottom: 10px;
        }
        .form-control:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.25);
        }
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(118, 75, 162, 0.3);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .process-steps {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .process-steps h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .process-steps ol {
            margin-bottom: 0;
            padding-left: 20px;
        }
        .process-steps li {
            margin-bottom: 8px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="request-container">
        <div class="logo-section">
            <h1>D WORLD</h1>
            <p>Request Account Deletion</p>
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

        <form action="{{ route('account.deletion.request.process') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="identifier" class="form-label">Email Address or Phone Number</label>
                <input type="text" class="form-control" id="identifier" name="identifier" required 
                       placeholder="Enter your email or phone number">
                <div class="form-text">Enter the email address or phone number associated with your account.</div>
            </div>

            <div class="mb-4">
                <label for="reason" class="form-label">Reason for Deletion</label>
                <textarea class="form-control" id="reason" name="reason" rows="4" required 
                          placeholder="Please tell us why you want to delete your account..."></textarea>
                <div class="form-text">This helps us improve our services. (Maximum 500 characters)</div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    📤 Submit Deletion Request
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
                By submitting this request, you confirm that you want to delete your account and understand that this action is irreversible once approved.
                <br>
                If you change your mind, you can contact our support team before the request is approved.
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
        // Character counter for reason field
        const reasonField = document.getElementById('reason');
        const maxLength = 500;
        
        reasonField.addEventListener('input', function() {
            const remaining = maxLength - this.value.length;
            if (remaining < 0) {
                this.value = this.value.substring(0, maxLength);
            }
        });
    </script>
</body>
</html>
