@props(['label', 'name', 'errorName', 'image', 'removeKey', 'removePrefix', 'contentName' => null])

<div class="space-y-2">
    <label class="admin-label">{{ $label }}</label>
    @if ($contentName)
        <input type="hidden" name="{{ $contentName }}" value="{{ $image ?? '' }}" />
    @endif
    @if ($image && str_starts_with($image, 'about/'))
        <img src="{{ asset('storage/'.$image) }}" alt="Current {{ strtolower($label) }}" class="h-28 w-full max-w-xs rounded-md border border-slate-200 bg-slate-50 object-cover" data-image-preview />
    @elseif ($image)
        <img src="{{ asset($image) }}" alt="Current {{ strtolower($label) }}" class="h-28 w-full max-w-xs rounded-md border border-slate-200 bg-slate-50 object-cover" data-image-preview />
    @else
        <img src="" alt="Selected {{ strtolower($label) }} preview" class="hidden h-28 w-full max-w-xs rounded-md border border-slate-200 bg-slate-50 object-cover" data-image-preview />
    @endif
    <input type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp" class="admin-file" data-image-input />
    @error ($errorName)
        <p class="admin-error">{{ $message }}</p>
    @enderror
    @if (str_starts_with((string) $image, 'about/'))
        <label class="admin-hint flex items-center gap-2">
            <input type="checkbox" name="remove_images[{{ $removeKey }}]" value="1" class="h-4 w-4 rounded border-slate-300" data-remove-input data-remove-prefix="{{ $removePrefix }}" @checked(old('remove_images.'.$removeKey)) />
            Remove uploaded image and use the demo image
        </label>
    @endif
</div>
