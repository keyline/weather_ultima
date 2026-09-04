<x-mail::message>
# New contact enquiry

A new message was submitted through the website contact form.

**Name:** {{ $enquiry->name }}
**Email:** {{ $enquiry->email }}
**Phone:** {{ $enquiry->phone ?: 'Not provided' }}
**Subject:** {{ $enquiry->subject }}
**Received:** {{ $enquiry->created_at->format('d M Y, h:i A') }}

<x-mail::panel>
{{ $enquiry->message }}
</x-mail::panel>

Thanks,<br>
{{ $settings->sender_name ?: config('app.name') }}
</x-mail::message>
