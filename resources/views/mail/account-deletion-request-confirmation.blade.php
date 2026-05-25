@component('mail::message')
# Account Deletion Request Received

We have received your request to delete your account.

## Request Details
- **Name:** {{ $user->name ?? $user->username }}
- **Email:** {{ $user->email }}
- **Request ID:** #{{ $deletionRequest->id }}
- **Submitted:** {{ $deletionRequest->created_at->format('Y-m-d H:i:s') }}
- **Reason:** {{ $deletionRequest->reason }}

## What Happens Next?

1. **Review Process:** Our admin team will review your request within 24-48 hours
2. **Decision:** You will receive an email with the decision (approved/rejected)
3. **If Approved:** Your account will be permanently deleted and cannot be recovered
4. **If Rejected:** Your account will remain active and you can continue using our services

## Need Help?

If you have any questions or need to withdraw your request, please contact our support team immediately.

---

@component('mail::button', ['url' => 'mailto:support@dadisonestop.com'])
Contact Support
@endcomponent

Thank you for your patience while we process your request.

@component('mail::subcopy')
This is an automated confirmation. If you did not request this action, please contact our support team immediately.
@endcomponent
@endcomponent
