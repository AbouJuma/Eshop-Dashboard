@component('mail::message')
# Account Deletion Request {{ ucfirst($action) }}

Your account deletion request has been {{ $action }} by our admin team.

## Request Details
- **Request ID:** #{{ $deletionRequest->id }}
- **Submitted:** {{ $deletionRequest->created_at->format('Y-m-d H:i:s') }}
- **Processed:** {{ $deletionRequest->processed_at->format('Y-m-d H:i:s') }}

@if($action === 'approved')
## ⚠️ Account Deleted

Your account has been **permanently deleted** as requested. This action is irreversible.

**What this means:**
- You can no longer log in to your account
- All your data, bookings, and orders have been removed
- You will not receive any further communications from us
- Your email address can be used to create a new account if needed

@else
## ✅ Request Rejected

Your account deletion request has been **rejected**. Your account remains **active** and you can continue using our services.

**Why your request might have been rejected:**
- Account verification issues
- Pending transactions or bookings
- Security concerns
- Or other administrative reasons

@if($deletionRequest->admin_notes)
**Admin Notes:** {{ $deletionRequest->admin_notes }}
@endif

**Your account is still fully accessible** with your existing credentials.
@endif

## Need Help?

If you have any questions or need assistance, please contact our support team.

---

@component('mail::button', ['url' => 'mailto:support@dadisonestop.com'])
Contact Support
@endcomponent

@if($action === 'approved')
Thank you for using D WORLD services. We're sorry to see you go!
@else
Thank you for your understanding. We're happy to continue serving you!
@endif

@component('mail::subcopy')
This is an automated notification. If you believe this was sent in error, please contact our support team immediately.
@endcomponent
@endcomponent
