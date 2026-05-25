@component('mail::message')
# New Feedback Received

You have received a new feedback from a user.

## User Information
- **Name:** {{ $user->name ?? 'N/A' }}
- **Email:** {{ $user->email ?? 'N/A' }}
- **Phone:** {{ $user->phone ?? 'N/A' }}

## Feedback Message
{{ $message }}

---

@component('mail::button', ['url' => ''])
View in Admin Panel
@endcomponent

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
