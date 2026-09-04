{{-- Shared core-value form. Expects $coreValue (nullable) + $action, $method. --}}
<div class="mx-auto max-w-xl space-y-6">
    <a href="{{ route('admin.home.core-values.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0b376d]">
        <i class="fa-solid fa-arrow-left"></i> All core values
    </a>

    @if ($errors->any())
        <div class="admin-alert admin-alert--error">
            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>Please fix the highlighted fields below.</span>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" class="admin-section space-y-5">
        @csrf
        @isset ($method)
            @method ($method)
        @endisset

        <h2 class="admin-section-title">Core value</h2>

        <div class="grid gap-5 sm:grid-cols-[7rem_1fr]">
            <x-admin.input
                name="icon"
                label="Icon"
                :value="$coreValue?->icon"
                placeholder="R"
                maxlength="32"
                hint="A short letter or glyph."
            />
            <x-admin.input
                name="title"
                label="Title"
                :value="$coreValue?->title"
                placeholder="Enter core value title"
                required
            />
        </div>

        <x-admin.textarea
            name="description"
            label="Description"
            :value="$coreValue?->description"
            placeholder="Enter a short description of this core value"
            :rows="4"
            required
        />

        <x-admin.input
            type="number"
            name="display_order"
            label="Display order"
            :value="$coreValue?->display_order ?? 0"
            min="0"
            placeholder="0"
            hint="Lower numbers appear first."
        />

        <label class="flex items-center gap-3 text-sm text-slate-700">
            <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $coreValue?->is_enabled ?? true)) class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
            <span><strong class="font-semibold">Enabled</strong> — show this value on the website</span>
        </label>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('admin.home.core-values.index') }}" class="admin-btn admin-btn--ghost">Cancel</a>
            <button type="submit" class="admin-btn admin-btn--primary">{{ $coreValue ? 'Save changes' : 'Create core value' }}</button>
        </div>
    </form>
</div>
