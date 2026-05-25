@component('mail::message')
# Account Suspension Confirmation

Your account has been suspended as per your request.

## Account Details
- **Name:** {{ $user->name ?? $user->username }}
- **Email:** {{ $user->email }}
- **Phone:** {{ $user->phone }}
- **Suspension Date:** {{ now()->format('Y-m-d H:i:s') }}

## What This Means
- Your account has been suspended and is no longer accessible
- All your bookings, orders, and personal data have been preserved
- You will not receive any further communications from us
- Your data will be permanently deleted after 30 days

## Need Help?
If this was done by mistake or you need assistance, please contact our support team immediately.

---

@component('mail::button', ['url' => 'mailto:support@dadisonestop.com'])
Contact Support
@endcomponent

Thank you for using D WORLD services.

@component('mail::subcopy')
This is an automated notification. If you did not request this action, please contact our support team immediately.
@endcomponent
@endcomponent
