@extends ('admin.layouts.app')
@section ('title', 'Weather Station')
@section ('page-title', 'Weather Station')

@section ('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <p class="text-sm text-slate-500">
            Feeds the homepage &ldquo;Our Weather Station&rdquo; section with live readings from the
            <a href="https://ambientweather.net/" target="_blank" rel="noopener" class="font-semibold text-[#0b376d] underline">Ambient Weather Network</a>.
            On ambientweather.net go to <span class="font-semibold">Account &rarr; API Keys</span> &mdash; that page lists two values:
            the row marked <span class="font-semibold">(application key)</span> goes in <span class="font-semibold">Application key</span> below,
            and the other row (labelled just <span class="font-semibold">API Key</span>) goes in <span class="font-semibold">API key</span> below.
            Keys are stored encrypted and never shown again. Readings are cached for a few minutes to stay within the API rate limit.
        </p>

        @if (session('status'))
            <div class="admin-alert admin-alert--success">
                <i class="fa-solid fa-circle-check mt-0.5"></i> <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('weather_test_error'))
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>{{ session('weather_test_error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>Please fix the highlighted fields below.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.weather.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method ('PUT')

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">API credentials</h2>
                <x-admin.input
                    name="application_key"
                    label="Application key"
                    type="password"
                    placeholder="{{ $settings->hasApplicationKey() ? 'Leave blank to keep the saved application key' : 'The AWN row marked “(application key)”' }}"
                    :hint="$settings->hasApplicationKey() ? 'Saved key: ' . $settings->maskedApplicationKey() . ' — enter a new one only to change it.' : 'From Account → API Keys: the value on the row annotated “(application key)”.'"
                    autocomplete="off"
                />
                <x-admin.input
                    name="api_key"
                    label="API key"
                    type="password"
                    placeholder="{{ $settings->hasApiKey() ? 'Leave blank to keep the saved API key' : 'The AWN row labelled just “API Key”' }}"
                    :hint="$settings->hasApiKey() ? 'Saved key: ' . $settings->maskedApiKey() . ' — enter a new one only to change it.' : 'From Account → API Keys: the value on the row with no “(application key)” note. Create one with “Create API Key” if the list is empty.'"
                    autocomplete="off"
                />
            </section>

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Station devices</h2>
                <p class="admin-hint">
                    Paste each station&rsquo;s device MAC address (for example <code class="rounded bg-slate-100 px-1">A4:CF:12:34:56:78</code>).
                    Use <span class="font-semibold">Test connection</span> below to list the devices on your account and their MAC
                    addresses. A station tab without a MAC address keeps its &ldquo;coming soon&rdquo; note on the site.
                </p>
                @foreach (\App\Models\WeatherSetting::STATIONS as $stationLabel => $column)
                    <x-admin.input
                        :name="$column"
                        :label="$stationLabel . ' station — device MAC address'"
                        :value="$settings->{$column}"
                        placeholder="e.g. A4:CF:12:34:56:78"
                    />
                @endforeach
            </section>

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Station images</h2>
                <p class="admin-hint">
                    The photo shown beside each station&rsquo;s readings on the homepage. JPG, PNG or WEBP up to 4&nbsp;MB.
                    Leave a field empty to keep the current image; tick &ldquo;Remove&rdquo; to go back to the default.
                </p>
                <div class="grid gap-6 sm:grid-cols-2">
                    @foreach (\App\Models\WeatherSetting::STATION_IMAGES as $stationLabel => $column)
                        @php $field = \Illuminate\Support\Str::beforeLast($column, '_path'); @endphp
                        <div>
                            <span class="admin-label">{{ $stationLabel }} station image</span>
                            <div class="mt-1.5 overflow-hidden rounded border border-slate-200 bg-slate-50">
                                <img src="{{ $settings->imageUrlFor($stationLabel) }}" alt="{{ $stationLabel }} station image preview" class="h-32 w-full object-cover" />
                            </div>
                            <input type="file" name="{{ $field }}" accept="image/png,image/jpeg,image/webp" class="admin-file mt-2 text-xs @error($field) border-rose-400 @enderror" />
                            @error ($field)
                                <p class="admin-error text-xs"><i class="fa-solid fa-circle-exclamation mt-0.5"></i> {{ $message }}</p>
                            @enderror
                            @if ($settings->{$column})
                                <label class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                    <input type="checkbox" name="remove_{{ $field }}" value="1" class="h-3.5 w-3.5 rounded border-slate-300 text-rose-600" />
                                    Remove &amp; use default
                                </label>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="admin-section space-y-3">
                <h2 class="admin-section-title">Activation</h2>
                <label class="flex items-start gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $settings->is_active)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
                    <span>
                        Show live weather data on the homepage.
                        <span class="mt-0.5 block text-xs text-slate-400">Requires a saved application key and API key. When off, every station tab shows its &ldquo;coming soon&rdquo; note.</span>
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
                Calls the Ambient Weather API with the saved keys and lists the devices it can see, so you can copy each
                station&rsquo;s MAC address. Save the keys first.
            </p>
            <form method="POST" action="{{ route('admin.settings.weather.test') }}">
                @csrf
                <button type="submit" class="admin-btn admin-btn--ghost" data-submit>
                    <i class="fa-solid fa-plug-circle-check"></i> Test connection
                </button>
            </form>
        </section>
    </div>
@endsection
