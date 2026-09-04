@extends ('admin.layouts.app')
@section ('title', 'Brevo Settings')
@section ('page-title', 'Brevo settings')

@section ('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <p class="text-sm text-slate-500">
            Send the website&rsquo;s email through the <span class="font-semibold text-slate-700">Brevo</span> transactional
            API. When enabled, Brevo takes priority over SMTP and the server&rsquo;s environment configuration. The API key is
            stored encrypted and never shown again.
        </p>

        @if (session('status'))
            <div class="admin-alert admin-alert--success">
                <i class="fa-solid fa-circle-check mt-0.5"></i> <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('brevo_test_error'))
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>{{ session('brevo_test_error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>Please fix the highlighted fields below.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.brevo.update') }}" class="space-y-6">
            @csrf
            @method ('PUT')

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">API access</h2>
                <x-admin.input
                    name="api_key"
                    label="Brevo API key"
                    type="password"
                    placeholder="{{ $settings->hasApiKey() ? 'Leave blank to keep the saved key' : 'Paste your Brevo v3 API key' }}"
                    :hint="$settings->hasApiKey() ? 'Saved key: ' . $settings->maskedApiKey() . ' — enter a new one only to change it.' : 'Create one in Brevo under SMTP & API → API Keys.'"
                    autocomplete="off"
                />
            </section>

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Sender</h2>
                <x-admin.input
                    name="sender_name"
                    label="Sender name"
                    :value="$settings->sender_name"
                    placeholder="Name shown as the sender, e.g. Weather Ultima"
                    required
                />
                <x-admin.input
                    type="email"
                    name="sender_email"
                    label="Sender email"
                    :value="$settings->sender_email"
                    placeholder="A sender verified in your Brevo account"
                    required
                />
                <x-admin.input
                    type="email"
                    name="reply_to_email"
                    label="Reply-to email (optional)"
                    :value="$settings->reply_to_email"
                    placeholder="Where replies should go, if different from the sender"
                />
            </section>

            <section class="admin-section space-y-3">
                <h2 class="admin-section-title">Activation</h2>
                <label class="flex items-start gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $settings->is_active)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
                    <span>
                        Use Brevo for all outgoing mail.
                        <span class="mt-0.5 block text-xs text-slate-400">Requires a saved API key. Overrides the SMTP and environment configuration.</span>
                    </span>
                </label>
            </section>

            <div class="flex justify-end">
                <button type="submit" class="admin-btn admin-btn--primary" data-submit>Save configuration</button>
            </div>
        </form>

        <section class="admin-section space-y-4">
            <h2 class="admin-section-title">Send a test email</h2>
            <p class="admin-hint">Sends a message through the <span class="font-semibold">saved</span> Brevo API key. Save your changes first.</p>
            <form method="POST" action="{{ route('admin.settings.brevo.test') }}" class="flex flex-col gap-3 sm:flex-row sm:items-start">
                @csrf
                <div class="flex-1">
                    <x-admin.input
                        type="email"
                        name="test_email"
                        :value="auth()->user()->email"
                        placeholder="Where should the test email go?"
                        required
                    />
                </div>
                <button type="submit" class="admin-btn admin-btn--ghost" data-submit>Send test</button>
            </form>
        </section>
    </div>
@endsection
