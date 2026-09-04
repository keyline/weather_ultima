<x-mail::message>
# New product enquiry

A visitor has enquired about a product on the website.

**Product:** {{ $enquiry->product_name }}
**Name:** {{ $enquiry->name }}
**Email:** {{ $enquiry->email }}
**Phone:** {{ $enquiry->phone ?: 'Not provided' }}
**Received:** {{ $enquiry->created_at->format('d M Y, h:i A') }}

<x-mail::panel>
{{ $enquiry->message ?: 'No message provided.' }}
</x-mail::panel>

Thanks,<br>
{{ $settings->sender_name ?: config('app.name') }}
</x-mail::message>
