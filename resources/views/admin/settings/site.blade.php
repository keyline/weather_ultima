@extends ('admin.layouts.app')
@section ('title', 'Site Settings')
@section ('page-title', 'Site settings')

@section ('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <p class="text-sm text-slate-500">Control the public website name, logos, favicon, contact details and social links. Changes apply everywhere immediately.</p>

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

        <form method="POST" action="{{ route('admin.settings.site.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method ('PUT')

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">General</h2>
                <x-admin.input
                    name="site_name"
                    label="Site name"
                    :value="$settings->site_name"
                    placeholder="Enter your website name"
                    required
                />
            </section>

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Branding</h2>
                <p class="admin-hint">Logos: PNG, JPG, WEBP or SVG up to 2&nbsp;MB. Favicon up to 1&nbsp;MB. Leave a field empty to keep the current file.</p>

                <div class="grid gap-6 sm:grid-cols-3">
                    @foreach ([['field' => 'header_logo', 'label' => 'Header logo', 'url' => $settings->header_logo_url, 'custom' => $settings->header_logo_path], ['field' => 'footer_logo', 'label' => 'Footer logo', 'url' => $settings->footer_logo_url, 'custom' => $settings->footer_logo_path], ['field' => 'favicon', 'label' => 'Favicon', 'url' => $settings->favicon_url, 'custom' => $settings->favicon_path]] as $asset)
                        <div>
                            <span class="admin-label">{{ $asset['label'] }}</span>
                            <div class="mt-1.5 flex items-center justify-center rounded border border-dashed border-slate-300 bg-slate-50 p-4">
                                <img src="{{ $asset['url'] }}" alt="{{ $asset['label'] }} preview" class="h-12 w-auto" />
                            </div>
                            <input type="file" name="{{ $asset['field'] }}" class="admin-file mt-2 text-xs @error($asset['field']) border-rose-400 @enderror" />
                            @error ($asset['field'])
                                <p class="admin-error text-xs"><i class="fa-solid fa-circle-exclamation mt-0.5"></i> {{ $message }}</p>
                            @enderror
                            @if ($asset['custom'])
                                <label class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                    <input type="checkbox" name="remove_{{ $asset['field'] }}" value="1" class="h-3.5 w-3.5 rounded border-slate-300 text-rose-600" />
                                    Remove &amp; use default
                                </label>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Contact details</h2>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        type="email"
                        name="contact_email"
                        label="Contact email"
                        :value="$settings->contact_email"
                        placeholder="Enter the public contact email address"
                        required
                    />
                    <x-admin.input
                        name="contact_phone"
                        label="Phone number"
                        :value="$settings->contact_phone"
                        placeholder="Enter the contact phone number"
                        required
                    />
                </div>
                <x-admin.input
                    name="contact_address"
                    label="Address (optional)"
                    :value="$settings->contact_address"
                    placeholder="Enter the office address shown on the site"
                />
            </section>

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Social links</h2>
                <p class="admin-hint">Full URLs starting with https://. Empty fields are hidden from the footer.</p>
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach (['social_facebook' => 'Facebook page URL', 'social_instagram' => 'Instagram profile URL', 'social_linkedin' => 'LinkedIn page URL', 'social_twitter' => 'X (Twitter) profile URL', 'social_youtube' => 'YouTube channel URL'] as $field => $placeholder)
                        <x-admin.input
                            type="url"
                            :name="$field"
                            :label="Str::of($field)->after('social_')->title()->replace('Twitter', 'X (Twitter)')"
                            :value="$settings->{$field}"
                            :placeholder="$placeholder"
                        />
                    @endforeach
                </div>
            </section>

            <div class="flex justify-end">
                <button type="submit" class="admin-btn admin-btn--primary" data-submit>Save settings</button>
            </div>
        </form>
    </div>
@endsection
