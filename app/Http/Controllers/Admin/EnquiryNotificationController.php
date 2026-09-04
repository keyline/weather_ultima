<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use App\Models\ProductEnquiry;
use Illuminate\Http\JsonResponse;

class EnquiryNotificationController extends Controller
{
    /**
     * Lightweight JSON endpoint polled by the admin layout to keep badges current.
     */
    public function __invoke(): JsonResponse
    {
        return response()->json(self::summary());
    }

    /**
     * Unread enquiry counts used by the sidebar badges, header bell and dashboard.
     *
     * @return array{contact: int, product: int, total: int}
     */
    public static function summary(): array
    {
        $contact = ContactEnquiry::query()->unread()->count();
        $product = ProductEnquiry::query()->unread()->count();

        return [
            'contact' => $contact,
            'product' => $product,
            'total' => $contact + $product,
        ];
    }
}
