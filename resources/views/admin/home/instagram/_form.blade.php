{{-- Shared Instagram-post form. Expects $post (nullable) + $action, $method. --}}
<div class="mx-auto max-w-xl space-y-6">
    <a href="{{ route('admin.home.instagram.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0b376d]">
        <i class="fa-solid fa-arrow-left"></i> All photos
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

        <h2 class="admin-section-title">Photo</h2>

        <div>
            <span @class(['admin-label', 'admin-required' => ! $post])>Photo</span>
            <p class="admin-hint">JPG, PNG or WEBP · up to 4&nbsp;MB. Square images look best.{{ $post ? ' Leave empty to keep the current image.' : '' }}</p>
            <div class="mt-2 flex items-center gap-4">
                <span class="flex h-20 w-20 items-center justify-center overflow-hidden rounded border border-slate-200 bg-white">
                    <img id="photo-preview" src="{{ $post?->image_url }}" alt="Preview" class="h-full w-full object-cover {{ $post ? '' : 'hidden' }}" />
                </span>
                <input id="image" type="file" name="image" accept="image/png,image/jpeg,image/webp" @if (! $post) required @endif class="admin-file @error('image') border-rose-400 @enderror" />
            </div>
            @error ('image')
                <p class="admin-error"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i> {{ $message }}</p>
            @enderror
        </div>

        <x-admin.input
            type="url"
            name="link_url"
            label="Link (optional)"
            :value="$post?->link_url"
            placeholder="https://www.instagram.com/p/..."
            hint="Leave empty to link to your Instagram profile instead."
        />

        <x-admin.input
            type="number"
            name="display_order"
            label="Display order"
            :value="$post?->display_order ?? 0"
            min="0"
            placeholder="0"
            hint="Lower numbers appear first."
        />

        <label class="flex items-center gap-3 text-sm text-slate-700">
            <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $post?->is_enabled ?? true)) class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
            <span><strong class="font-semibold">Enabled</strong> — show this photo on the website</span>
        </label>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('admin.home.instagram.index') }}" class="admin-btn admin-btn--ghost">Cancel</a>
            <button type="submit" class="admin-btn admin-btn--primary">{{ $post ? 'Save changes' : 'Add photo' }}</button>
        </div>
    </form>
</div>

@push ('scripts')
    <script>
        (() => {
            const input = document.getElementById("image");
            const preview = document.getElementById("photo-preview");
            input?.addEventListener("change", () => {
                const file = input.files?.[0];
                if (!file) return;
                preview.src = URL.createObjectURL(file);
                preview.classList.remove("hidden");
            });
        })();
    </script>
@endpush
