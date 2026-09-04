<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactEnquiryRequest;
use App\Mail\ContactEnquiryNotification;
use App\Models\ContactEnquiry;
use App\Models\EmailSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function store(StoreContactEnquiryRequest $request): JsonResponse|RedirectResponse
    {
        $enquiry = ContactEnquiry::query()->create($request->safe()->except('g-recaptcha-response'));

        $this->sendNotification($enquiry);

        $message = 'Thank you. Your enquiry has been submitted successfully and our team will get back to you shortly.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 201);
        }

        return redirect()->route('contact')->with('status', $message);
    }

    private function sendNotification(ContactEnquiry $enquiry): void
    {
        $settings = EmailSetting::current();

        if (! $settings->contact_notifications_enabled || blank($settings->contact_notification_email)) {
            return;
        }

        try {
            Mail::to($settings->contact_notification_email)->send(
                (new ContactEnquiryNotification($enquiry, $settings))
                    ->from((string) config('mail.from.address'), $settings->sender_name ?: config('app.name'))
            );
        } catch (Throwable $exception) {
            Log::error('Contact enquiry notification failed.', [
                'contact_enquiry_id' => $enquiry->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
