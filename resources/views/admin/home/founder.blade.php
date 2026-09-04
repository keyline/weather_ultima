@extends ('admin.layouts.app')
@section ('title', 'About Founder')
@section ('page-title', 'Home · About the founder')

@section ('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <p class="text-sm text-slate-500">The "About the Founder" section of the homepage. The existing design and layout are preserved.</p>

        @if (session('status'))
            <div class="admin-alert admin-alert--success">
                <i class="fa-solid fa-circle-check mt-0.5"></i> <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>Please fix the highlighted fields below.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.home.founder.update') }}" enctype="multipart/form-data" class="admin-section space-y-5">
            @csrf
            @method ('PUT')

            <h2 class="admin-section-title">Founder details</h2>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.input
                    name="founder_name"
                    label="Founder name"
                    :value="$home->founder_name"
                    placeholder="Enter founder's name"
                    required
                />
                <x-admin.input
                    name="founder_designation"
                    label="Founder designation"
                    :value="$home->founder_designation"
                    placeholder="Enter founder's designation"
                />
            </div>

            <x-admin.textarea
                name="founder_intro"
                label="Short introduction"
                :value="$home->founder_intro"
                placeholder="Enter a short introduction (first paragraph shown on the homepage)"
                :rows="3"
                required
            />

            <x-admin.textarea
                name="founder_description"
                label="Full description"
                :value="$home->founder_description"
                placeholder="Enter the full description. Separate paragraphs with a blank line."
                :rows="6"
                hint="Leave a blank line between paragraphs — each becomes its own paragraph on the homepage."
            />

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <span class="admin-label">Founder photo</span>
                    <p class="admin-hint">JPG, PNG or WEBP · up to 2&nbsp;MB.</p>
                    <div class="mt-2 flex items-center gap-4">
                        <img id="founder-preview" src="{{ $home->founder_image_url ?? asset('material/images/owner_img.png') }}" alt="Preview" class="h-20 w-20 rounded-full border border-slate-200 object-cover" />
                        <input id="founder_image" type="file" name="founder_image" accept="image/png,image/jpeg,image/webp" class="admin-file @error('founder_image') border-rose-400 @enderror" />
                    </div>
                    @error ('founder_image')
                        <p class="admin-error"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i> {{ $message }}</p>
                    @enderror
                    @if ($home->founder_image_path)
                        <label class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                            <input type="checkbox" name="remove_founder_image" value="1" class="h-3.5 w-3.5 rounded border-slate-300 text-rose-600" /> Remove &amp; use default
                        </label>
                    @endif
                </div>
                <div>
                    <span class="admin-label">Signature image (optional)</span>
                    <p class="admin-hint">PNG or SVG · up to 1&nbsp;MB. Shown above the button.</p>
                    <div class="mt-2 flex items-center gap-4">
                        @if ($home->founder_signature_url)
                            <img id="signature-preview" src="{{ $home->founder_signature_url }}" alt="Signature" class="h-12 w-auto rounded border border-slate-200 bg-white object-contain p-1" />
                        @else
                            <img id="signature-preview" src="" alt="" class="hidden h-12 w-auto rounded border border-slate-200 bg-white object-contain p-1" />
                        @endif
                        <input id="founder_signature" type="file" name="founder_signature" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="admin-file @error('founder_signature') border-rose-400 @enderror" />
                    </div>
                    @error ('founder_signature')
                        <p class="admin-error"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i> {{ $message }}</p>
                    @enderror
                    @if ($home->founder_signature_path)
                        <label class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                            <input type="checkbox" name="remove_founder_signature" value="1" class="h-3.5 w-3.5 rounded border-slate-300 text-rose-600" /> Remove signature
                        </label>
                    @endif
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-100 pt-5">
                <button type="submit" class="admin-btn admin-btn--primary">Save founder section</button>
            </div>
        </form>
    </div>
@endsection

@push ('scripts')
    <script>
        (() => {
            const bind = (inputId, previewId) => {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                input?.addEventListener("change", () => {
                    const file = input.files?.[0];
                    if (!file) return;
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove("hidden");
                });
            };
            bind("founder_image", "founder-preview");
            bind("founder_signature", "signature-preview");
        })();
    </script>
@endpush
