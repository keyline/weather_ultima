<?php

namespace App\Providers;

use App\Http\Controllers\Admin\EnquiryNotificationController;
use App\Mail\MailChannelConfigurator;
use App\Mail\Transport\BrevoApiTransport;
use App\Models\SiteSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        RateLimiter::for('admin-login', fn (Request $request): Limit => Limit::perMinute(5)->by(mb_strtolower($request->string('email')->toString()).'|'.$request->ip()));
        RateLimiter::for('enquiry', fn (Request $request): Limit => Limit::perMinute(3)->by(mb_strtolower($request->string('email')->toString()).'|'.$request->ip()));

        Mail::extend('brevo', fn (array $config): BrevoApiTransport => new BrevoApiTransport((string) ($config['key'] ?? '')));

        MailChannelConfigurator::configure();

        View::composer(
            ['layouts.*', 'admin.*', 'errors.*', 'errors::*', 'home', 'products', 'services', 'contact'],
            fn ($view) => $view->with('siteSettings', SiteSetting::current()),
        );

        View::composer('admin.*', fn ($view) => $view->with('enquiryNotifications', EnquiryNotificationController::summary()));
    }
}
