@extends ('admin.layouts.app')
@section ('title', 'Edit Service')
@section ('page-title', 'Edit service')

@section ('content')
    <div class="mx-auto max-w-3xl space-y-6">
        @include ('admin.services._form', [
            'action' => route('admin.services.update', $service),
            'method' => 'PUT',
        ])

        {{-- Image gallery --}}
        <div class="admin-section space-y-5">
            <div>
                <h2 class="admin-section-title">Images</h2>
                <p class="admin-hint">The row of images shown in this service's panel. Reorder with the number field, then save.</p>
            </div>

            @if ($service->images->isEmpty())
                <p class="rounded border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                    No images yet — add one below.
                </p>
            @else
                <form method="POST" action="{{ route('admin.services.images.update', $service) }}" class="space-y-3">
                    @csrf
                    @method ('PUT')
                    @foreach ($service->images as $i => $image)
                        <div class="flex flex-wrap items-center gap-3 rounded border border-slate-200 p-3">
                            <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}" class="h-14 w-20 rounded border border-slate-200 object-cover" />
                            <input type="hidden" name="images[{{ $i }}][id]" value="{{ $image->id }}" />
                            <div class="w-20">
                                <label class="admin-label text-xs">Order</label>
                                <input type="number" min="0" name="images[{{ $i }}][display_order]" value="{{ $image->display_order }}" class="admin-input" />
                            </div>
                            <div class="min-w-48 flex-1">
                                <label class="admin-label text-xs">Alt text</label>
                                <input type="text" name="images[{{ $i }}][alt_text]" value="{{ $image->alt_text }}" placeholder="Describe the image" class="admin-input" />
                            </div>
                        </div>
                    @endforeach
                    <div class="flex items-center justify-between gap-3">
                        <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm">Save order &amp; captions</button>
                    </div>
                </form>

                <div class="flex flex-wrap gap-2">
                    @foreach ($service->images as $image)
                        <form method="POST" action="{{ route('admin.services.images.destroy', [$service, $image]) }}" onsubmit="return confirm('Remove this image?');">
                            @csrf
                            @method ('DELETE')
                            <button class="admin-btn admin-btn--danger admin-btn--sm"><i class="fa-solid fa-trash-can"></i> Remove #{{ $image->display_order }}</button>
                        </form>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.services.images.store', $service) }}" enctype="multipart/form-data" class="space-y-3 border-t border-slate-100 pt-5">
                @csrf
                <h3 class="text-sm font-semibold text-slate-800">Add image</h3>
                <div class="flex items-center gap-4">
                    <img id="image-preview" src="" alt="" class="hidden h-14 w-20 rounded border border-slate-200 object-cover" />
                    <input id="image" type="file" name="image" accept="image/png,image/jpeg,image/webp" required class="admin-file @error('image') border-rose-400 @enderror" />
                </div>
                @error ('image')
                    <p class="admin-error"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i> {{ $message }}</p>
                @enderror
                <input type="text" name="alt_text" placeholder="Alt text (optional)" class="admin-input" />
                <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm">Upload image</button>
            </form>
        </div>
    </div>
@endsection

@push ('scripts')
    <script>
        (() => {
            const input = document.getElementById("image");
            const preview = document.getElementById("image-preview");
            input?.addEventListener("change", () => {
                const file = input.files?.[0];
                if (!file) return;
                preview.src = URL.createObjectURL(file);
                preview.classList.remove("hidden");
            });
        })();
    </script>
@endpush
