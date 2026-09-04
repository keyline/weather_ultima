{{-- Shared brand-logo form. Expects $brandLogo (nullable) + $action, $method. --}}
<div class="mx-auto max-w-xl space-y-6">
    <a href="{{ route('admin.home.logo.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0b376d]">
        <i class="fa-solid fa-arrow-left"></i> All logos
    </a>

    @if ($errors->any())
        <div class="admin-alert admin-alert--error">
            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>Please fix the highlighted fields below.</span>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="admin-section space-y-5">
        @csrf
        @isset ($method)
            @method ($method)
        @endisset

        <h2 class="admin-section-title">Logo</h2>

        <div>
            <span @class(['admin-label', 'admin-required' => ! $brandLogo])>Logo image</span>
            <p class="admin-hint">PNG, JPG, WEBP or SVG · up to 2&nbsp;MB. Transparent PNG/SVG looks best.{{ $brandLogo ? ' Leave empty to keep the current image.' : '' }}</p>
            <div class="mt-2 flex items-center gap-4">
                <span class="flex h-16 w-28 items-center justify-center rounded border border-slate-200 bg-white p-2">
                    <img id="logo-preview" src="{{ $brandLogo?->image_url }}" alt="Preview" class="max-h-full max-w-full object-contain {{ $brandLogo ? '' : 'hidden' }}" />
                </span>
                <input id="image" type="file" name="image" accept="image/png,image/jpeg,image/webp,image/svg+xml" @if (! $brandLogo) required @endif class="admin-file @error('image') border-rose-400 @enderror" />
            </div>
            @error ('image')
                <p class="admin-error"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i> {{ $message }}</p>
            @enderror
        </div>

        <x-admin.input
            name="alt_text"
            label="Alt text (optional)"
            :value="$brandLogo?->alt_text"
            placeholder="e.g. Hindustan Times"
        />

        <x-admin.input
            type="number"
            name="display_order"
            label="Display order"
            :value="$brandLogo?->display_order ?? 0"
            min="0"
            placeholder="0"
            hint="Lower numbers appear first."
        />

        <label class="flex items-center gap-3 text-sm text-slate-700">
            <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $brandLogo?->is_enabled ?? true)) class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
            <span><strong class="font-semibold">Enabled</strong> — show this logo on the website</span>
        </label>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('admin.home.logo.index') }}" class="admin-btn admin-btn--ghost">Cancel</a>
            <button type="submit" class="admin-btn admin-btn--primary">{{ $brandLogo ? 'Save changes' : 'Add logo' }}</button>
        </div>
    </form>
</div>

@push ('scripts')
    <script>
        (() => {
            const input = document.getElementById("image");
            const preview = document.getElementById("logo-preview");
            input?.addEventListener("change", () => {
                const file = input.files?.[0];
                if (!file) return;
                preview.src = URL.createObjectURL(file);
                preview.classList.remove("hidden");
            });
        })();
    </script>
@endpush
