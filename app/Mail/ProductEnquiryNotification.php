<?php

namespace App\Mail;

use App\Models\EmailSetting;
use App\Models\ProductEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductEnquiryNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ProductEnquiry $enquiry, public EmailSetting $settings) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->settings->product_subject ?: 'New website product enquiry');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.product-enquiry-notification', with: ['enquiry' => $this->enquiry]);
    }
}
