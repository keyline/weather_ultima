@extends ('admin.layouts.app')
@section ('title', 'Instagram Feed')
@section ('page-title', 'Instagram Feed')

@section ('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <p class="text-sm text-slate-500">
            Feeds the homepage &ldquo;Follow Us on Instagram&rdquo; section. Three sources, in priority order:
            <span class="font-semibold">1)</span> the embed code below, <span class="font-semibold">2)</span> the Graph API
            connection further down, <span class="font-semibold">3)</span> the
            <a href="{{ route('admin.home.instagram.index') }}" class="font-semibold text-[#0b376d] underline">manually uploaded fallback photos</a>.
            The first one that's set up is what shows.
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
                <h2 class="admin-section-title">Embed code <span class="text-xs font-normal text-slate-400">(simplest option)</span></h2>
                <p class="admin-hint">
                    Sign up with a widget service &mdash;
                    <a href="https://behold.so/" target="_blank" rel="noopener" class="font-semibold text-[#0b376d] underline">Behold.so</a>,
                    <a href="https://snapwidget.com/" target="_blank" rel="noopener" class="font-semibold text-[#0b376d] underline">SnapWidget</a>,
                    or <a href="https://elfsight.com/instagram-feed-widget/" target="_blank" rel="noopener" class="font-semibold text-[#0b376d] underline">Elfsight</a> &mdash;
                    connect your Instagram account there with a normal login (no Meta developer app needed), and paste the
                    embed code they give you below. It'll show up on the homepage exactly as it looks on their site. Leave
                    empty to use the Graph API or fallback photos instead.
                </p>
                <div>
                    <label for="embed_code" class="admin-label">Embed code</label>
                    <textarea
                        id="embed_code"
                        name="embed_code"
                        rows="5"
                        placeholder="Paste the <script>/<div> snippet from your widget provider here"
                        class="admin-input font-mono text-xs @error('embed_code') border-rose-400 @enderror"
                    >{{ old('embed_code', $settings->embed_code) }}</textarea>
                    @error ('embed_code')
                        <p class="admin-error"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i> {{ $message }}</p>
                    @enderror
                </div>
            </section>

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Advanced: Instagram Graph API <span class="text-xs font-normal text-slate-400">(optional, only used when there's no embed code above)</span></h2>
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
                        <span class="mt-0.5 block text-xs text-slate-400">Requires a saved access token and Business Account ID, and no embed code above (embed code always wins). When off, or if the API can't be reached, the fallback photos are shown instead.</span>
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
