@extends ('admin.layouts.app')
@section ('title', 'SMTP Settings')
@section ('page-title', 'SMTP settings')

@section ('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <p class="text-sm text-slate-500">
            Configure the outgoing mail server used for every email the website sends. When
            <span class="font-semibold text-slate-700">&ldquo;Use these settings&rdquo;</span> is on, these values replace the
            server&rsquo;s environment configuration. The password is stored encrypted and is never shown again.
        </p>

        @if (session('status'))
            <div class="admin-alert admin-alert--success">
                <i class="fa-solid fa-circle-check mt-0.5"></i> <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('smtp_test_error'))
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>{{ session('smtp_test_error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>Please fix the highlighted fields below.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.smtp.update') }}" class="space-y-6">
            @csrf
            @method ('PUT')

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Mail server</h2>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <x-admin.input
                            name="host"
                            label="SMTP host"
                            :value="$settings->host"
                            placeholder="e.g. smtp.gmail.com"
                            required
                        />
                    </div>
                    <x-admin.input
                        type="number"
                        name="port"
                        label="Port"
                        :value="$settings->port"
                        placeholder="587"
                        required
                    />
                </div>

                <x-admin.select
                    name="encryption"
                    label="Encryption / security"
                    :options="['tls' => 'TLS (recommended, usually port 587)', 'ssl' => 'SSL (usually port 465)']"
                    :selected="$settings->encryption"
                    placeholder="None"
                    hint="Choose the security your mail provider requires."
                />

                <x-admin.input
                    name="username"
                    label="Username / email"
                    :value="$settings->username"
                    placeholder="The login for your SMTP account"
                    autocomplete="off"
                />

                <x-admin.input
                    type="password"
                    name="password"
                    label="Password"
                    placeholder="{{ $settings->hasPassword() ? 'Leave blank to keep the saved password' : 'Enter the SMTP account password' }}"
                    :hint="$settings->hasPassword() ? 'A password is currently saved. Enter a new one only to change it.' : 'No password is saved yet.'"
                    autocomplete="new-password"
                />
            </section>

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Sender</h2>

                <x-admin.input
                    name="from_name"
                    label="From name"
                    :value="$settings->from_name"
                    placeholder="Name shown as the sender, e.g. Weather Ultima"
                    required
                />
                <x-admin.input
                    type="email"
                    name="from_address"
                    label="From email"
                    :value="$settings->from_address"
                    placeholder="Address emails are sent from, e.g. no-reply@weatherultima.com"
                    required
                />
            </section>

            <section class="admin-section space-y-3">
                <h2 class="admin-section-title">Activation</h2>
                <label class="flex items-start gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $settings->is_active)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
                    <span>
                        Use these settings for all outgoing mail.
                        <span class="mt-0.5 block text-xs text-slate-400">Leave off to keep using the server&rsquo;s environment configuration.</span>
                    </span>
                </label>
            </section>

            <div class="flex justify-end">
                <button type="submit" class="admin-btn admin-btn--primary" data-submit>Save configuration</button>
            </div>
        </form>

        <section class="admin-section space-y-4">
            <h2 class="admin-section-title">Send a test email</h2>
            <p class="admin-hint">
                Sends a message using the <span class="font-semibold">saved</span> configuration above, so you can confirm it
                works before turning it on. Save your changes first.
            </p>
            <form method="POST" action="{{ route('admin.settings.smtp.test') }}" class="flex flex-col gap-3 sm:flex-row sm:items-start">
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
