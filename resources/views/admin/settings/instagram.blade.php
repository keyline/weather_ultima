@extends ('admin.layouts.app')
@section ('title', 'Instagram Feed')
@section ('page-title', 'Instagram Feed')

@section ('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <p class="text-sm text-slate-500">
            Feeds the homepage &ldquo;Follow Us on Instagram&rdquo; grid with your latest real posts, via the
            <a href="https://developers.facebook.com/docs/instagram-platform/instagram-graph-api" target="_blank" rel="noopener" class="font-semibold text-[#0b376d] underline">Instagram Graph API</a>.
            This needs an Instagram <span class="font-semibold">Business or Creator</span> account linked to a Facebook Page. In
            <a href="https://developers.facebook.com/" target="_blank" rel="noopener" class="font-semibold text-[#0b376d] underline">Meta for Developers</a>,
            create an app, use the <span class="font-semibold">Graph API Explorer</span> to generate a long-lived access token
            (with the <code class="rounded bg-slate-100 px-1">instagram_basic</code> permission), then look up your account's
            <span class="font-semibold">Instagram Business Account ID</span> from the linked Facebook Page. The token is stored
            encrypted and never shown again. When the live feed isn't configured or can't be reached, the
            <a href="{{ route('admin.home.instagram.index') }}" class="font-semibold text-[#0b376d] underline">fallback photos</a> are shown instead.
        </p>

        @if (session('status'))
            <div class="admin-alert admin-alert--success">
                <i class="fa-solid fa-circle-check mt-0.5"></i> <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('instagram_test_error'))
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>{{ session('instagram_test_error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>Please fix the highlighted fields below.</span>
            </div>
        @endif

        @if ($settings->tokenExpiresSoon())
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <span>The saved access token expires on {{ $settings->token_expires_at->toFormattedDateString() }}. Generate a new long-lived token in Meta for Developers and paste it below before it expires, or the live feed will stop updating.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.instagram.update') }}" class="space-y-6">
            @csrf
            @method ('PUT')

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">API credentials</h2>
                <x-admin.input
                    name="access_token"
                    label="Access token"
                    type="password"
                    placeholder="{{ $settings->hasAccessToken() ? 'Leave blank to keep the saved token' : 'Paste the long-lived access token from Graph API Explorer' }}"
                    :hint="$settings->hasAccessToken() ? 'Saved token: ' . $settings->maskedAccessToken() . ' — enter a new one only to change it.' : 'Generate this in Meta for Developers → Graph API Explorer, with the instagram_basic permission.'"
                    autocomplete="off"
                />
                <x-admin.input
                    name="instagram_user_id"
                    label="Instagram Business Account ID"
                    :value="$settings->instagram_user_id"
                    placeholder="e.g. 17841400000000000"
                    hint="Numeric ID of your Instagram Business/Creator account, found via its linked Facebook Page."
                />
                <x-admin.input
                    type="date"
                    name="token_expires_at"
                    label="Token expiry date (optional)"
                    :value="optional($settings->token_expires_at)->toDateString()"
                    hint="Graph API Explorer shows how long the token is valid for. Setting this shows a renewal warning here before it expires."
                />
                @if ($settings->username)
                    <p class="admin-hint">Last confirmed account: <span class="font-semibold text-slate-700">@{{ $settings->username }}</span></p>
                @endif
            </section>

            <section class="admin-section space-y-3">
                <h2 class="admin-section-title">Activation</h2>
                <label class="flex items-start gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $settings->is_active)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
                    <span>
                        Show your live Instagram posts on the homepage.
                        <span class="mt-0.5 block text-xs text-slate-400">Requires a saved access token and Business Account ID. When off, or if the API can't be reached, the fallback photos are shown instead.</span>
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

        <section class="admin-section space-y-3">
            <h2 class="admin-section-title">Test connection</h2>
            <p class="admin-hint">
                Calls the Instagram Graph API with the saved credentials and confirms the account it can see. Save the
                credentials first.
            </p>
            <form method="POST" action="{{ route('admin.settings.instagram.test') }}">
                @csrf
                <button type="submit" class="admin-btn admin-btn--ghost" data-submit>
                    <i class="fa-solid fa-plug-circle-check"></i> Test connection
                </button>
            </form>
        </section>
    </div>
@endsection
