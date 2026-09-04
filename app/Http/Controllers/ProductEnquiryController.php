<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductEnquiryRequest;
use App\Mail\ProductEnquiryNotification;
use App\Models\EmailSetting;
use App\Models\Product;
use App\Models\ProductEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProductEnquiryController extends Controller
{
    public function store(StoreProductEnquiryRequest $request, Product $product): JsonResponse|RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $enquiry = $product->productEnquiries()->create($request->safe()->except('g-recaptcha-response') + [
            'product_name' => $product->name,
        ]);

        $this->sendNotification($enquiry);

        $message = 'Thank you. Your product enquiry has been submitted successfully and our team will contact you shortly.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 201);
        }

        return redirect()->route('products')->with('status', $message);
    }

    private function sendNotification(ProductEnquiry $enquiry): void
    {
        $settings = EmailSetting::current();

        if (! $settings->product_notifications_enabled || blank($settings->product_notification_email)) {
            return;
        }

        try {
            Mail::to($settings->product_notification_email)->send(
                (new ProductEnquiryNotification($enquiry, $settings))
                    ->from((string) config('mail.from.address'), $settings->sender_name ?: config('app.name'))
            );
        } catch (Throwable $exception) {
            Log::error('Product enquiry notification failed.', [
                'product_enquiry_id' => $enquiry->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
