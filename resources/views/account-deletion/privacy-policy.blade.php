<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Dadis Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .privacy-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 40px auto;
            max-width: 900px;
            overflow: hidden;
        }
        .privacy-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .privacy-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .privacy-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        .privacy-content {
            padding: 40px 30px;
        }
        .section-title {
            color: #333;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
        }
        .section-content {
            color: #666;
            line-height: 1.8;
            margin-bottom: 30px;
        }
        .highlight-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .contact-info {
            background: #e3f2fd;
            padding: 25px;
            border-radius: 10px;
            margin-top: 30px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        .nav-links {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
        }
        .nav-links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .privacy-container {
                margin: 20px;
                border-radius: 10px;
            }
            .privacy-header {
                padding: 30px 20px;
            }
            .privacy-header h1 {
                font-size: 2rem;
            }
            .privacy-content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="nav-links">
        <a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a>
        <a href="{{ route('account.deletion') }}"><i class="fas fa-user-times"></i> Account Deletion</a>
        <a href="{{ route('account.deletion.request') }}"><i class="fas fa-envelope"></i> Request Deletion</a>
    </div>

    <div class="privacy-container">
        <div class="privacy-header">
            <h1><i class="fas fa-shield-alt"></i> Privacy Policy</h1>
            <p>Your privacy is important to us. This policy explains how we handle your data.</p>
        </div>

        <div class="privacy-content">
            <div class="section-title">Information We Collect</div>
            <div class="section-content">
                <p>We collect and process the following information when you use our services:</p>
                <ul>
                    <li><strong>Personal Information:</strong> Name, email address, phone number</li>
                    <li><strong>Account Information:</strong> Username, account status, registration date</li>
                    <li><strong>Usage Data:</strong> Login history, service usage patterns, preferences</li>
                    <li><strong>Communication Data:</strong> Messages, support requests, feedback</li>
                </ul>
            </div>

            <div class="section-title">How We Use Your Information</div>
            <div class="section-content">
                <p>We use your information to:</p>
                <ul>
                    <li>Provide and maintain our services</li>
                    <li>Process your bookings and orders</li>
                    <li>Communicate with you about your account</li>
                    <li>Improve our services and user experience</li>
                    <li>Ensure security and prevent fraud</li>
                    <li>Comply with legal obligations</li>
                </ul>
            </div>

            <div class="section-title">Data Retention</div>
            <div class="section-content">
                <p>We retain your personal information for as long as necessary to:</p>
                <ul>
                    <li>Fulfill the purposes outlined in this privacy policy</li>
                    <li>Comply with legal requirements</li>
                    <li>Resolve disputes and enforce our agreements</li>
                    <li>Detect and prevent fraud</li>
                </ul>
                
                <div class="highlight-box">
                    <strong>Account Deletion:</strong> When you request account deletion, we will permanently delete your personal information within 30 days, unless required to retain it for legal or security purposes.
                </div>
            </div>

            <div class="section-title">Account Deletion Rights</div>
            <div class="section-content">
                <p>You have the right to delete your account at any time. We provide two methods:</p>
                
                <div class="highlight-box">
                    <h6><i class="fas fa-bolt"></i> Immediate Deletion</h6>
                    <p>Visit <a href="{{ route('account.deletion') }}" style="color: #667eea;">Account Deletion Page</a> to immediately deactivate your account. This action is reversible within 7 days.</p>
                </div>
                
                <div class="highlight-box">
                    <h6><i class="fas fa-user-shield"></i> Admin-Reviewed Deletion</h6>
                    <p>Submit a deletion request through <a href="{{ route('account.deletion.request') }}" style="color: #667eea;">Request Form</a>. Our admin team will review and process your request within 24-48 hours.</p>
                </div>
            </div>

            <div class="section-title">Data Security</div>
            <div class="section-content">
                <p>We implement appropriate security measures to protect your information:</p>
                <ul>
                    <li>SSL encryption for all data transmissions</li>
                    <li>Secure password storage with hashing</li>
                    <li>Regular security audits and updates</li>
                    <li>Access controls and authentication systems</li>
                    <li>Employee training on data protection</li>
                </ul>
            </div>

            <div class="section-title">Third-Party Sharing</div>
            <div class="section-content">
                <p>We do not sell your personal information. We may share your data only with:</p>
                <ul>
                    <li>Service providers who help operate our business</li>
                    <li>Payment processors for transaction processing</li>
                    <li>Legal authorities when required by law</li>
                    <li>Business partners with your explicit consent</li>
                </ul>
            </div>

            <div class="section-title">Your Rights</div>
            <div class="section-content">
                <p>You have the right to:</p>
                <ul>
                    <li>Access your personal information</li>
                    <li>Correct inaccurate information</li>
                    <li>Delete your account and data</li>
                    <li>Restrict processing of your information</li>
                    <li>Data portability</li>
                    <li>Object to processing</li>
                    <li>Withdraw consent</li>
                </ul>
            </div>

            <div class="section-title">Cookies and Tracking</div>
            <div class="section-content">
                <p>We use cookies and similar technologies to:</p>
                <ul>
                    <li>Remember your preferences</li>
                    <li>Analyze website usage</li>
                    <li>Improve user experience</li>
                    <li>Provide personalized content</li>
                </ul>
                <p>You can control cookies through your browser settings.</p>
            </div>

            <div class="section-title">Children's Privacy</div>
            <div class="section-content">
                <p>Our services are not intended for children under 13. We do not knowingly collect personal information from children under 13. If we become aware of such collection, we will take steps to delete it immediately.</p>
            </div>

            <div class="section-title">International Data Transfers</div>
            <div class="section-content">
                <p>Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place to protect your data in accordance with applicable data protection laws.</p>
            </div>

            <div class="section-title">Changes to This Policy</div>
            <div class="section-content">
                <p>We may update this privacy policy from time to time. We will notify you of any changes by:</p>
                <ul>
                    <li>Posting the new policy on our website</li>
                    <li>Sending you an email notification</li>
                    <li>Displaying a prominent notice on our app</li>
                </ul>
                <p>Your continued use of our services after such changes constitutes acceptance of the updated policy.</p>
            </div>

            <div class="contact-info">
                <div class="section-title">Contact Us</div>
                <p>If you have any questions about this Privacy Policy or your data rights, please contact us:</p>
                <ul>
                    <li><strong>Email:</strong> info@dadisonestop.com</li>
                    <!-- <li><strong>Website:</strong> www.dadisonestop.com</li>
                    <li><strong>Phone:</strong> [Your Phone Number]</li>
                    <li><strong>Address:</strong> [Your Business Address]</li> -->
                </ul>
                <p>We will respond to your inquiries within 30 days.</p>
            </div>
        </div>

        <div class="footer">
            <p><strong>Last Updated:</strong> {{ date('F j, Y') }}</p>
            <p>&copy; {{ date('Y') }} Dadis Dashboard. All rights reserved.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
