{{-- Shared dimension-card form. Expects $card (nullable) + $action, $method. --}}
<div class="mx-auto max-w-xl space-y-6">
    <a href="{{ route('admin.home.banner.edit') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0b376d]">
        <i class="fa-solid fa-arrow-left"></i> Back to Top banner
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

        <h2 class="admin-section-title">Dimension card</h2>

        <x-admin.input
            name="title"
            label="Card title"
            :value="$card?->title"
            placeholder="e.g. SkyWatch Live"
            required
        />

        <x-admin.textarea
            name="description"
            label="Card description"
            :value="$card?->description"
            placeholder="e.g. Sports • Agriculture • Mountaineering • Outdoor Operations"
            :rows="3"
            maxlength="500"
            required
        />

        <x-admin.input
            name="link_url"
            label="Link URL (optional)"
            :value="$card?->link_url"
            placeholder="e.g. /services or https://…"
            hint="Where the card's arrow links to. Leave empty for no link."
        />

        <div>
            <span class="admin-label">Card image (optional)</span>
            <p class="admin-hint">JPG, PNG or WEBP · up to 2&nbsp;MB. Used as the card background. Falls back to the banner's fallback image.{{ $card ? ' Leave empty to keep the current image.' : '' }}</p>
            <div class="mt-2 flex items-center gap-4">
                <img id="card-preview" src="{{ $card?->image_url ?? asset('material/images/service1.png') }}" alt="Preview" class="h-16 w-28 rounded border border-slate-200 object-cover" />
                <input id="image" type="file" name="image" accept="image/png,image/jpeg,image/webp" class="admin-file @error('image') border-rose-400 @enderror" />
            </div>
            @error ('image')
                <p class="admin-error"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i> {{ $message }}</p>
            @enderror
            @if ($card?->image)
                <label class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                    <input type="checkbox" name="remove_image" value="1" class="h-3.5 w-3.5 rounded border-slate-300 text-rose-600" /> Remove the current image
                </label>
            @endif
        </div>

        <x-admin.input
            type="number"
            name="display_order"
            label="Display order"
            :value="$card?->display_order ?? 0"
            min="0"
            placeholder="0"
            hint="Lower numbers appear first. The first three cards fill the top row."
        />

        <label class="flex items-center gap-3 text-sm text-slate-700">
            <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $card?->is_enabled ?? true)) class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
            <span><strong class="font-semibold">Enabled</strong> — show this card on the website</span>
        </label>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('admin.home.banner.edit') }}" class="admin-btn admin-btn--ghost">Cancel</a>
            <button type="submit" class="admin-btn admin-btn--primary">{{ $card ? 'Save changes' : 'Add card' }}</button>
        </div>
    </form>
</div>

@push ('scripts')
    <script>
        (() => {
            const input = document.getElementById("image");
            const preview = document.getElementById("card-preview");
            input?.addEventListener("change", () => {
                const file = input.files?.[0];
                if (file) preview.src = URL.createObjectURL(file);
            });
        })();
    </script>
@endpush
