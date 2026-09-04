{{-- Shared service form body. Expects $service (nullable) + $action, $method. --}}
<div class="space-y-6">
    <a href="{{ route('admin.services.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0b376d]">
        <i class="fa-solid fa-arrow-left"></i> All services
    </a>

    @if ($errors->any())
        <div class="admin-alert admin-alert--error">
            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>Please fix the highlighted fields below.</span>
        </div>
    @endif

    @if (session('status'))
        <div class="admin-alert admin-alert--success">
            <i class="fa-solid fa-circle-check mt-0.5"></i> <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" class="admin-section space-y-5">
        @csrf
        @isset ($method)
            @method ($method)
        @endisset

        <h2 class="admin-section-title">Service details</h2>

        <x-admin.input
            name="name"
            label="Service name"
            :value="$service?->name"
            placeholder="Enter the service name (also the tab label)"
            required
        />

        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.input
                name="category"
                label="Category line"
                :value="$service?->category"
                placeholder="e.g. Weather Forecasting & Intelligence"
            />
            <x-admin.input
                name="tags"
                label="Tags line"
                :value="$service?->tags"
                placeholder="e.g. Sports • Agriculture • Mountaineering"
            />
        </div>

        <x-admin.textarea
            name="statement"
            label="Statement (italic lead line)"
            :value="$service?->statement"
            placeholder="Enter the italic statement line. Line breaks are kept."
            :rows="2"
        />

        <x-admin.textarea
            name="body"
            label="Body"
            :value="$service?->body"
            placeholder="One paragraph per blank line"
            :rows="10"
            required
            hint="Leave a blank line between paragraphs — each becomes its own paragraph."
        />

        <x-admin.input
            name="result"
            label="Result line (italic closing line)"
            :value="$service?->result"
            placeholder="e.g. The result: …"
        />

        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.input
                type="number"
                name="display_order"
                label="Display order"
                :value="$service?->display_order ?? 0"
                min="0"
                placeholder="0"
                hint="Lower numbers appear first."
            />
        </div>

        <label class="flex items-center gap-3 text-sm text-slate-700">
            <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $service?->is_enabled ?? true)) class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
            <span><strong class="font-semibold">Enabled</strong> — show this service on the website</span>
        </label>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('admin.services.index') }}" class="admin-btn admin-btn--ghost">Cancel</a>
            <button type="submit" class="admin-btn admin-btn--primary">{{ $service ? 'Save changes' : 'Create service' }}</button>
        </div>
    </form>
</div>
