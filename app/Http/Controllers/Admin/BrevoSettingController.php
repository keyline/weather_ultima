<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendBrevoTestEmailRequest;
use App\Http\Requests\Admin\UpdateBrevoSettingsRequest;
use App\Models\BrevoSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class BrevoSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.brevo', ['settings' => BrevoSetting::current()]);
    }

    public function update(UpdateBrevoSettingsRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['api_key', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');

        if (filled($request->input('api_key'))) {
            $data['api_key'] = $request->string('api_key')->toString();
        }

        BrevoSetting::current()->update($data);

        return back()->with('status', 'Brevo configuration saved.');
    }

    public function test(SendBrevoTestEmailRequest $request): RedirectResponse
    {
        $settings = BrevoSetting::current();

        if (! $settings->hasApiKey()) {
            return back()->with('brevo_test_error', 'Add and save a Brevo API key before sending a test.');
        }

        $settings->applyToMailConfig(force: true);
        Mail::forgetMailers();

        $recipient = $request->string('test_email')->toString();

        try {
            Mail::raw(
                'This is a test email from Weather Ultima, delivered through the Brevo API. Your configuration is working.',
                function ($message) use ($recipient, $settings): void {
                    $message->to($recipient)
                        ->subject('Weather Ultima Brevo test')
                        ->from($settings->sender_email, $settings->sender_name);

                    if (filled($settings->reply_to_email)) {
                        $message->replyTo($settings->reply_to_email);
                    }
                }
            );
        } catch (Throwable $exception) {
            return back()->with('brevo_test_error', 'Test email failed: '.$exception->getMessage());
        }

        return back()->with('status', "Test email sent to {$recipient} via Brevo.");
    }
}
