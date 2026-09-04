<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use App\Models\Product;
use App\Models\ProductEnquiry;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $recent = ContactEnquiry::query()->latest()->take(5)->get()
            ->map(fn (ContactEnquiry $enquiry): array => [
                'name' => $enquiry->name,
                'label' => 'Contact enquiry',
                'when' => $enquiry->created_at,
                'url' => route('admin.contact-enquiries.index'),
                'unread' => ! $enquiry->is_read,
            ])
            ->merge(ProductEnquiry::query()->latest()->take(5)->get()->map(fn (ProductEnquiry $enquiry): array => [
                'name' => $enquiry->name,
                'label' => 'Product enquiry: '.$enquiry->product_name,
                'when' => $enquiry->created_at,
                'url' => route('admin.product-enquiries.index'),
                'unread' => ! $enquiry->is_read,
            ]))
            ->sortByDesc('when')
            ->take(6)
            ->values();

        return view('admin.dashboard', [
            'statistics' => [
                ['label' => 'Contact enquiries', 'value' => ContactEnquiry::count(), 'icon' => 'fa-envelope', 'tone' => 'sky', 'url' => route('admin.contact-enquiries.index')],
                ['label' => 'Product enquiries', 'value' => ProductEnquiry::count(), 'icon' => 'fa-box', 'tone' => 'violet', 'url' => route('admin.product-enquiries.index')],
                ['label' => 'Products', 'value' => Product::count(), 'icon' => 'fa-cloud-sun', 'tone' => 'amber', 'url' => route('admin.products.index')],
                ['label' => 'System status', 'value' => 'Online', 'icon' => 'fa-circle-check', 'tone' => 'emerald', 'url' => null],
            ],
            'recentEnquiries' => $recent,
        ]);
    }
}
