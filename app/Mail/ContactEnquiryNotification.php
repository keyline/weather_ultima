<?php

namespace App\Mail;

use App\Models\ContactEnquiry;
use App\Models\EmailSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactEnquiryNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactEnquiry $enquiry, public EmailSetting $settings) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->settings->contact_subject ?: 'New website contact enquiry');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact-enquiry-notification', with: ['enquiry' => $this->enquiry]);
    }
}
