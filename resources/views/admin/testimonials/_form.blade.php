{{-- Shared testimonial form. Expects $testimonial (nullable) + $action, $method. --}}
<div class="mx-auto max-w-2xl space-y-6">
    <a href="{{ route('admin.testimonials.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0b376d]">
        <i class="fa-solid fa-arrow-left"></i> All testimonials
    </a>

    @if ($errors->any())
        <div class="admin-alert admin-alert--error">
            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
            <span>Please fix the highlighted {{ Str::plural('field', $errors->count()) }} below.</span>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" class="admin-section space-y-6">
        @csrf
        @isset ($method)
            @method ($method)
        @endisset

        <div>
            <h2 class="admin-section-title">Testimonial details</h2>
            <p class="admin-hint">Shown in the “What our clients say” section on the homepage.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.input
                name="name"
                label="Customer name"
                :value="$testimonial?->name"
                placeholder="Enter the customer's full name"
                required
            />
            <x-admin.select
                name="rating"
                label="Rating"
                :selected="$testimonial?->rating ?? 5"
                :options="[5 => '5 stars', 4 => '4 stars', 3 => '3 stars', 2 => '2 stars', 1 => '1 star']"
                required
            />
            <x-admin.input
                name="designation"
                label="Designation (optional)"
                :value="$testimonial?->designation"
                placeholder="e.g. Farm Owner"
            />
            <x-admin.input
                name="company"
                label="Company (optional)"
                :value="$testimonial?->company"
                placeholder="e.g. Green Fields Pvt Ltd"
            />
            <x-admin.input
                type="number"
                name="display_order"
                label="Display order"
                :value="$testimonial?->display_order ?? 0"
                min="0"
                placeholder="0"
                hint="Lower numbers appear first."
            />
        </div>

        <x-admin.textarea
            name="review"
            label="Testimonial / review"
            :value="$testimonial?->review"
            placeholder="Enter the customer testimonial"
            :rows="4"
            maxlength="2000"
            required
        />

        <label class="flex items-center gap-3 text-sm text-slate-700">
            <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $testimonial?->is_enabled ?? true)) class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
            <span><strong class="font-semibold">Enabled</strong> — show this testimonial on the website</span>
        </label>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('admin.testimonials.index') }}" class="admin-btn admin-btn--ghost">Cancel</a>
            <button type="submit" class="admin-btn admin-btn--primary" data-submit>
                {{ $testimonial ? 'Save changes' : 'Create testimonial' }}
            </button>
        </div>
    </form>
</div>
