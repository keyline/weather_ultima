<?php

use App\Http\Controllers\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BrandLogoController;
use App\Http\Controllers\Admin\BrevoSettingController;
use App\Http\Controllers\Admin\ContactEnquiryController;
use App\Http\Controllers\Admin\CoreValueController;
use App\Http\Controllers\Admin\DimensionCardController;
use App\Http\Controllers\Admin\EmailSettingController;
use App\Http\Controllers\Admin\EnquiryNotificationController;
use App\Http\Controllers\Admin\HomeBannerController;
use App\Http\Controllers\Admin\HomeFounderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductEnquiryController as AdminProductEnquiryController;
use App\Http\Controllers\Admin\RecaptchaSettingController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ServicePageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SmtpSettingController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductEnquiryController;
use App\Http\Controllers\ServicesPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('products', [ProductController::class, 'index'])->name('products');
Route::post('products/{product}/enquiry', [ProductEnquiryController::class, 'store'])->middleware('throttle:enquiry')->name('products.enquiry');
Route::get('services', [ServicesPageController::class, 'show'])->name('services');
Route::view('contact', 'contact')->name('contact');
Route::post('contact', [ContactController::class, 'store'])->middleware('throttle:enquiry')->name('contact.store');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])->middleware('throttle:admin-login')->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('enquiry-notifications', EnquiryNotificationController::class)->name('enquiry-notifications');

        Route::prefix('home')->name('home.')->group(function (): void {
            Route::get('banner', [HomeBannerController::class, 'edit'])->name('banner.edit');
            Route::put('banner', [HomeBannerController::class, 'update'])->name('banner.update');
            Route::delete('banner/cards/bulk', [DimensionCardController::class, 'bulkDestroy'])->name('cards.bulk-destroy');
            Route::patch('banner/cards/{dimension_card}/toggle', [DimensionCardController::class, 'toggle'])->name('cards.toggle');
            Route::resource('banner/cards', DimensionCardController::class)->parameters(['cards' => 'dimension_card'])->names('cards')->only(['create', 'store', 'edit', 'update', 'destroy']);
            Route::get('founder', [HomeFounderController::class, 'edit'])->name('founder.edit');
            Route::put('founder', [HomeFounderController::class, 'update'])->name('founder.update');

            Route::delete('logo/bulk', [BrandLogoController::class, 'bulkDestroy'])->name('logo.bulk-destroy');
            Route::patch('logo/{brand_logo}/toggle', [BrandLogoController::class, 'toggle'])->name('logo.toggle');
            Route::resource('logo', BrandLogoController::class)->parameters(['logo' => 'brand_logo'])->names('logo')->except('show');

            Route::delete('core-values/bulk', [CoreValueController::class, 'bulkDestroy'])->name('core-values.bulk-destroy');
            Route::patch('core-values/{core_value}/toggle', [CoreValueController::class, 'toggle'])->name('core-values.toggle');
            Route::resource('core-values', CoreValueController::class)->parameters(['core-values' => 'core_value'])->names('core-values')->except('show');
        });

        Route::resource('products', AdminProductController::class)->except(['show']);
        Route::patch('products/{product}/toggle', [AdminProductController::class, 'toggle'])->name('products.toggle');

        Route::get('product-enquiries/export', [AdminProductEnquiryController::class, 'export'])->name('product-enquiries.export');
        Route::delete('product-enquiries/bulk', [AdminProductEnquiryController::class, 'bulkDestroy'])->name('product-enquiries.bulk-destroy');
        Route::patch('product-enquiries/{product_enquiry}/read', [AdminProductEnquiryController::class, 'markRead'])->name('product-enquiries.read');
        Route::resource('product-enquiries', AdminProductEnquiryController::class)->only(['index', 'destroy']);

        Route::delete('testimonials/bulk', [TestimonialController::class, 'bulkDestroy'])->name('testimonials.bulk-destroy');
        Route::patch('testimonials/{testimonial}/toggle', [TestimonialController::class, 'toggle'])->name('testimonials.toggle');
        Route::resource('testimonials', TestimonialController::class)->except(['show']);

        Route::put('services/page', [ServicePageController::class, 'update'])->name('services.page.update');
        Route::delete('services/bulk', [AdminServiceController::class, 'bulkDestroy'])->name('services.bulk-destroy');
        Route::patch('services/{service}/toggle', [AdminServiceController::class, 'toggle'])->name('services.toggle');
        Route::post('services/{service}/images', [AdminServiceController::class, 'storeImage'])->name('services.images.store');
        Route::put('services/{service}/images', [AdminServiceController::class, 'updateImages'])->name('services.images.update');
        Route::delete('services/{service}/images/{serviceImage}', [AdminServiceController::class, 'destroyImage'])->name('services.images.destroy');
        Route::resource('services', AdminServiceController::class)->except(['show']);

        Route::get('contact-enquiries/export', [ContactEnquiryController::class, 'export'])->name('contact-enquiries.export');
        Route::delete('contact-enquiries/bulk', [ContactEnquiryController::class, 'bulkDestroy'])->name('contact-enquiries.bulk-destroy');
        Route::patch('contact-enquiries/{contact_enquiry}/read', [ContactEnquiryController::class, 'markRead'])->name('contact-enquiries.read');
        Route::resource('contact-enquiries', ContactEnquiryController::class)->only(['index', 'destroy']);

        Route::prefix('settings')->name('settings.')->group(function (): void {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::get('email', [EmailSettingController::class, 'edit'])->name('email.edit');
            Route::put('email', [EmailSettingController::class, 'update'])->name('email.update');
            Route::get('smtp', [SmtpSettingController::class, 'edit'])->name('smtp.edit');
            Route::put('smtp', [SmtpSettingController::class, 'update'])->name('smtp.update');
            Route::post('smtp/test', [SmtpSettingController::class, 'test'])->name('smtp.test');
            Route::get('brevo', [BrevoSettingController::class, 'edit'])->name('brevo.edit');
            Route::put('brevo', [BrevoSettingController::class, 'update'])->name('brevo.update');
            Route::post('brevo/test', [BrevoSettingController::class, 'test'])->name('brevo.test');
            Route::get('recaptcha', [RecaptchaSettingController::class, 'edit'])->name('recaptcha.edit');
            Route::put('recaptcha', [RecaptchaSettingController::class, 'update'])->name('recaptcha.update');
            Route::get('site', [SiteSettingController::class, 'edit'])->name('site.edit');
            Route::put('site', [SiteSettingController::class, 'update'])->name('site.update');
        });

        Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});
