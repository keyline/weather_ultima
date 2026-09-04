<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendSmtpTestEmailRequest;
use App\Http\Requests\Admin\UpdateSmtpSettingsRequest;
use App\Models\SmtpSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class SmtpSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.smtp', ['settings' => SmtpSetting::current()]);
    }

    public function update(UpdateSmtpSettingsRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['password', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');

        if (filled($request->input('password'))) {
            $data['password'] = $request->string('password')->toString();
        }

        SmtpSetting::current()->update($data);

        return back()->with('status', 'SMTP configuration saved.');
    }

    public function test(SendSmtpTestEmailRequest $request): RedirectResponse
    {
        $settings = SmtpSetting::current();
        $settings->applyToMailConfig(force: true);
        Mail::forgetMailers();

        $recipient = $request->string('test_email')->toString();

        try {
            Mail::raw(
                'This is a test email from Weather Ultima. Your SMTP configuration is working correctly.',
                function ($message) use ($recipient, $settings): void {
                    $message->to($recipient)
                        ->subject('Weather Ultima SMTP test')
                        ->from($settings->from_address, $settings->from_name);
                }
            );
        } catch (Throwable $exception) {
            return back()->with('smtp_test_error', 'Test email failed: '.$exception->getMessage());
        }

        return back()->with('status', "Test email sent to {$recipient}.");
    }
}
