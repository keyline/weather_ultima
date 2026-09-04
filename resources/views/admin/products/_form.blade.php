{{-- Shared product form body. Expects $product (nullable) and $action, $method. --}}
<div class="mx-auto max-w-2xl space-y-6">
    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0b376d]">
        <i class="fa-solid fa-arrow-left"></i> All products
    </a>

    @if ($errors->any())
        <div class="admin-alert admin-alert--error">
            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
            <span>Please fix the highlighted {{ Str::plural('field', $errors->count()) }} below.</span>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="admin-section space-y-6">
        @csrf
        @isset ($method)
            @method ($method)
        @endisset

        <div>
            <h2 class="admin-section-title">Product information</h2>
            <p class="admin-hint">Details shown on the public products page.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.input
                name="name"
                label="Product name"
                :value="$product?->name"
                placeholder="e.g. Automatic Weather Station"
                required
            />
            <x-admin.input
                name="short_description"
                label="Short description"
                :value="$product?->short_description"
                placeholder="One line shown under the product name"
                maxlength="500"
                required
            />
        </div>

        <div>
            <span class="admin-label admin-required">Product image</span>
            <p class="admin-hint">JPG, PNG or WEBP · up to 2&nbsp;MB · square images look best.{{ $product ? ' Leave empty to keep the current image.' : '' }}</p>
            <div class="mt-2 flex items-center gap-4">
                <img id="image-preview" src="{{ $product?->image_url ?? asset(\App\Models\Product::PLACEHOLDER_IMAGE) }}" alt="Preview" class="h-20 w-20 rounded border border-slate-200 object-cover" />
                <input id="image" type="file" name="image" accept="image/png,image/jpeg,image/webp" @if (! $product) required @endif
                    class="admin-file @error('image') border-rose-400 @enderror" />
            </div>
            @error ('image')
                <p class="admin-error"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i> {{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-3 text-sm text-slate-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product?->is_active ?? true)) class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
            <span><strong class="font-semibold">Active</strong> — show this product on the website</span>
        </label>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--ghost">Cancel</a>
            <button type="submit" class="admin-btn admin-btn--primary" data-submit>
                {{ $product ? 'Save changes' : 'Create product' }}
            </button>
        </div>
    </form>
</div>

@push ('scripts')
    <script>
        (() => {
            const input = document.getElementById("image");
            const preview = document.getElementById("image-preview");
            input?.addEventListener("change", () => {
                const file = input.files?.[0];
                if (file) preview.src = URL.createObjectURL(file);
            });
        })();
    </script>
@endpush
