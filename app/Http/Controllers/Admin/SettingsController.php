<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrevoSetting;
use App\Models\RecaptchaSetting;
use App\Models\SmtpSetting;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'smtp' => SmtpSetting::current(),
            'brevo' => BrevoSetting::current(),
            'recaptcha' => RecaptchaSetting::current(),
        ]);
    }
}
