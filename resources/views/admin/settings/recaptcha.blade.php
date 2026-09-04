@extends ('admin.layouts.app')
@section ('title', 'Google reCAPTCHA')
@section ('page-title', 'Google reCAPTCHA')

@section ('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <p class="text-sm text-slate-500">
            Protect the public forms with Google reCAPTCHA. When enabled, the challenge is enforced on the contact form, the
            product enquiry form and the admin login — and on any form that includes the shared reCAPTCHA component in future.
            The secret key is stored encrypted and never shown again.
        </p>

        @if (session('status'))
            <div class="admin-alert admin-alert--success">
                <i class="fa-solid fa-circle-check mt-0.5"></i> <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>Please fix the highlighted fields below.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.recaptcha.update') }}" class="space-y-6">
            @csrf
            @method ('PUT')

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Keys</h2>
                <x-admin.select
                    name="version"
                    label="Version / type"
                    :options="\App\Models\RecaptchaSetting::VERSIONS"
                    :selected="$settings->version"
                    hint="Use keys generated for this exact type in the Google reCAPTCHA admin console."
                    required
                />
                <x-admin.input
                    name="site_key"
                    label="Site key"
                    :value="$settings->site_key"
                    placeholder="Public key rendered in the page"
                    hint="This key is public and appears in the page source."
                />
                <x-admin.input
                    name="secret_key"
                    label="Secret key"
                    type="password"
                    placeholder="{{ $settings->hasSecretKey() ? 'Leave blank to keep the saved secret key' : 'Server-side verification key' }}"
                    :hint="$settings->hasSecretKey() ? 'Saved key: ' . $settings->maskedSecretKey() . ' — enter a new one only to change it.' : 'Kept private on the server for verification.'"
                    autocomplete="off"
                />
                <x-admin.input
                    type="number"
                    name="minimum_score"
                    label="Minimum score (v3 only)"
                    :value="$settings->minimum_score"
                    placeholder="0.5"
                    step="0.1"
                    min="0"
                    max="1"
                    hint="v3 returns a 0.0–1.0 score. Submissions below this value are rejected. Ignored for v2."
                />
            </section>

            <section class="admin-section space-y-3">
                <h2 class="admin-section-title">Activation</h2>
                <label class="flex items-start gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $settings->is_active)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
                    <span>
                        Enforce reCAPTCHA on the site&rsquo;s forms.
                        <span class="mt-0.5 block text-xs text-slate-400">Requires a saved site key and secret key.</span>
                    </span>
                </label>
                @error ('is_active')
                    <p class="admin-error"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i> {{ $message }}</p>
                @enderror
            </section>

            <div class="flex justify-end">
                <button type="submit" class="admin-btn admin-btn--primary" data-submit>Save configuration</button>
            </div>
        </form>
    </div>
@endsection
