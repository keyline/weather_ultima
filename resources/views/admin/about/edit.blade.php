@extends ('admin.layouts.app')
@section ('title', 'About page content')
@section ('page-title', 'About · '.$sectionTitle)

@section ('content')
    @php
        $paragraphText = fn (array $paragraphs): string => implode("\n\n", $paragraphs);
    @endphp

    <div class="space-y-6">
        <p class="text-sm text-slate-600">Edit the {{ $sectionTitle }} section. Save changes here without affecting the other About sections. Leave image fields empty to keep the current image.</p>

        @if (session('status'))
            <div class="admin-alert admin-alert--success"><i class="fa-solid fa-circle-check"></i> {{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="admin-alert admin-alert--error"><div><i class="fa-solid fa-triangle-exclamation"></i> Please fix the fields below.<ul class="mt-2 list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
        @endif

        <form method="POST" action="{{ route('admin.about.update', ['section' => $selectedSection]) }}" enctype="multipart/form-data" class="space-y-5" data-about-editor>
            @csrf
            @method ('PUT')
            <input type="hidden" name="section" value="{{ $selectedSection }}" />

            @if ($selectedSection === 'banner-intro')
                <details id="about-banner-intro" class="admin-section space-y-5" open>
                <summary class="admin-section-title cursor-pointer">1. Page banner and introduction</summary>
                <div class="grid gap-5 lg:grid-cols-2">
                    <x-admin.input name="content[banner_title]" label="Banner title" :value="old('content.banner_title', $content['banner_title'])" required />
                    <x-admin.input name="content[banner][image_alt]" label="Banner image description" :value="old('content.banner.image_alt', $content['banner']['image_alt'] ?? '')" hint="Describe the banner image for accessibility." />
                </div>
                <div class="grid gap-5 lg:grid-cols-2">
                    <x-admin.input name="content[intro][label]" label="Introduction label" :value="old('content.intro.label', $content['intro']['label'])" required />
                    <x-admin.textarea name="content[intro][title]" label="Introduction heading" :value="old('content.intro.title', $content['intro']['title'])" :rows="2" required />
                    <x-admin.textarea name="content[intro][body]" label="Introduction paragraphs" :value="old('content.intro.body', $paragraphText($content['intro']['paragraphs'] ?? []))" hint="Separate paragraphs with a blank line." :rows="7" />
                </div>
                <div class="grid gap-5 lg:grid-cols-2">
                    @include ('admin.about._image', ['label' => 'Banner image', 'name' => 'uploads[banner]', 'errorName' => 'uploads.banner', 'image' => $content['banner']['image'] ?? null, 'removeKey' => 'banner', 'removePrefix' => 'banner', 'contentName' => 'content[banner][image]'])
                    @include ('admin.about._image', ['label' => 'Introduction image', 'name' => 'uploads[intro]', 'errorName' => 'uploads.intro', 'image' => $content['intro']['image'] ?? null, 'removeKey' => 'intro', 'removePrefix' => 'intro', 'contentName' => 'content[intro][image]'])
                </div>
            </details>
            @endif

            @if (in_array($selectedSection, ['mission', 'vision'], true))
                @php($sectionHeading = $selectedSection === 'mission' ? '2. Mission carousel' : '3. Vision carousel')
                @php($section = $selectedSection)
                <details id="about-{{ $section }}" class="admin-section space-y-5" open>
                    <summary class="admin-section-title cursor-pointer">{{ $sectionHeading }}</summary>
                    @php($cards = old('content.'.$section.'.cards', $content[$section]['cards']))
                    <div class="grid gap-5 lg:grid-cols-2">
                        <x-admin.input name="content[{{ $section }}][label]" label="Section label" :value="old('content.'.$section.'.label', $content[$section]['label'])" required />
                        <x-admin.input name="content[{{ $section }}][title]" label="Section heading" :value="old('content.'.$section.'.title', $content[$section]['title'])" required />
                    </div>
                    <div class="space-y-5" data-repeat-group="{{ $section }}-cards">
                        @foreach ($cards as $index => $card)
                            <article class="admin-card space-y-5 p-5 sm:p-6" data-repeat-item>
                                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3"><h3 class="text-base font-semibold text-slate-900">Slide {{ $index + 1 }}</h3><button type="button" class="admin-btn admin-btn--danger admin-btn--sm shrink-0" data-remove-item>Remove slide</button></div>
                                <x-admin.input name="content[{{ $section }}][cards][{{ $index }}][title]" label="Slide title" :value="old('content.'.$section.'.cards.'.$index.'.title', $card['title'])" required />
                                <x-admin.textarea name="content[{{ $section }}][cards][{{ $index }}][body]" label="Slide text" :value="old('content.'.$section.'.cards.'.$index.'.body', $paragraphText($card['paragraphs'] ?? []))" hint="Separate paragraphs with a blank line." :rows="5" />
                                @include ('admin.about._image', ['label' => 'Slide image', 'name' => "uploads[$section][cards][$index][image]", 'errorName' => "uploads.$section.cards.$index.image", 'image' => old('content.'.$section.'.cards.'.$index.'.image', $content[$section]['cards'][$index]['image'] ?? null), 'removeKey' => "$section-cards-$index", 'removePrefix' => "$section-cards", 'contentName' => "content[$section][cards][$index][image]"])
                            </article>
                        @endforeach
                    </div>
                    <button type="button" class="admin-btn admin-btn--ghost admin-btn--sm" data-add-item>Add slide</button>
                    <template data-repeat-template="{{ $section }}-cards">
                        <article class="admin-card space-y-5 p-5 sm:p-6" data-repeat-item>
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3"><h3 class="text-base font-semibold text-slate-900">New slide</h3><button type="button" class="admin-btn admin-btn--danger admin-btn--sm shrink-0" data-remove-item>Remove slide</button></div>
                            <div><label class="admin-label">Slide title</label><input class="admin-input" name="content[{{ $section }}][cards][__INDEX__][title]" required maxlength="255" /></div>
                            <div><label class="admin-label">Slide text</label><textarea class="admin-textarea" name="content[{{ $section }}][cards][__INDEX__][body]" rows="5"></textarea><p class="admin-hint">Separate paragraphs with a blank line.</p></div>
                            <div><label class="admin-label">Slide image</label><input type="hidden" name="content[{{ $section }}][cards][__INDEX__][image]" value="" /><img src="" alt="Selected slide preview" class="hidden h-28 w-full max-w-xs rounded-md border border-slate-200 bg-slate-50 object-cover" data-image-preview /><input type="file" name="uploads[{{ $section }}][cards][__INDEX__][image]" accept="image/jpeg,image/png,image/webp" class="admin-file" data-image-input /></div>
                        </article>
                    </template>
                </details>
            @endif

            @if ($selectedSection === 'founder')
                <details id="about-founder" class="admin-section space-y-5" open>
                <summary class="admin-section-title cursor-pointer">4. Founder story</summary>
                <div class="grid gap-5 lg:grid-cols-2">
                    <x-admin.input name="content[founder][label]" label="Eyebrow label" :value="old('content.founder.label', $content['founder']['label'])" required />
                    <x-admin.input name="content[founder][name]" label="Founder name" :value="old('content.founder.name', $content['founder']['name'])" required />
                </div>
                <x-admin.input name="content[founder][title]" label="Founder section heading" :value="old('content.founder.title', $content['founder']['title'])" required />
                <x-admin.textarea name="content[founder][body]" label="Founder story" :value="old('content.founder.body', $paragraphText($content['founder']['paragraphs'] ?? []))" hint="Separate paragraphs with a blank line." :rows="10" />
                <x-admin.textarea name="content[founder][signature]" label="Closing line" :value="old('content.founder.signature', $content['founder']['signature'])" :rows="2" />
                @include ('admin.about._image', ['label' => 'Founder image', 'name' => 'uploads[founder]', 'errorName' => 'uploads.founder', 'image' => $content['founder']['image'] ?? null, 'removeKey' => 'founder', 'removePrefix' => 'founder', 'contentName' => 'content[founder][image]'])
                </details>
            @endif

            @if ($selectedSection === 'team')
                <details id="about-team" class="admin-section space-y-5" open>
                <summary class="admin-section-title cursor-pointer">5. Team members</summary>
                <x-admin.input name="content[team_title]" label="Team section title" :value="old('content.team_title', $content['team_title'])" required />
                <div class="space-y-5" data-repeat-group="team">
                    @foreach (old('content.team', $content['team']) as $index => $member)
                        <article class="admin-card space-y-5 p-5 sm:p-6" data-repeat-item>
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3"><h3 class="text-base font-semibold text-slate-900">Team member {{ $index + 1 }}</h3><button type="button" class="admin-btn admin-btn--danger admin-btn--sm shrink-0" data-remove-item>Remove member</button></div>
                            <div class="grid gap-5 lg:grid-cols-2"><x-admin.input name="content[team][{{ $index }}][name]" label="Name" :value="old('content.team.'.$index.'.name', $member['name'])" required /><x-admin.input name="content[team][{{ $index }}][role]" label="Role" :value="old('content.team.'.$index.'.role', $member['role'] ?? '')" /><x-admin.input name="content[team][{{ $index }}][organisation]" label="Organisation (optional)" :value="old('content.team.'.$index.'.organisation', $member['organisation'] ?? '')" /></div>
                            <x-admin.textarea name="content[team][{{ $index }}][bio]" label="Biography" :value="old('content.team.'.$index.'.bio', $paragraphText($member['paragraphs'] ?? []))" :rows="4" />
                            @include ('admin.about._image', ['label' => 'Member image', 'name' => "uploads[team][$index][image]", 'errorName' => "uploads.team.$index.image", 'image' => old('content.team.'.$index.'.image', $content['team'][$index]['image'] ?? null), 'removeKey' => "team-$index", 'removePrefix' => 'team', 'contentName' => "content[team][$index][image]"])
                        </article>
                    @endforeach
                </div>
                <button type="button" class="admin-btn admin-btn--ghost admin-btn--sm" data-add-item>Add team member</button>
                <template data-repeat-template="team">
                    <article class="admin-card space-y-5 p-5 sm:p-6" data-repeat-item>
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3"><h3 class="text-base font-semibold text-slate-900">New team member</h3><button type="button" class="admin-btn admin-btn--danger admin-btn--sm shrink-0" data-remove-item>Remove member</button></div>
                        <div class="grid gap-5 lg:grid-cols-2"><div><label class="admin-label">Name</label><input class="admin-input" name="content[team][__INDEX__][name]" required maxlength="150" /></div><div><label class="admin-label">Role</label><input class="admin-input" name="content[team][__INDEX__][role]" maxlength="150" /></div><div><label class="admin-label">Organisation</label><input class="admin-input" name="content[team][__INDEX__][organisation]" maxlength="150" /></div></div>
                        <div><label class="admin-label">Biography</label><textarea class="admin-textarea" name="content[team][__INDEX__][bio]" rows="4"></textarea></div>
                        <div><label class="admin-label">Member image</label><input type="hidden" name="content[team][__INDEX__][image]" value="" /><img src="" alt="Selected member preview" class="hidden h-28 w-full max-w-xs rounded-md border border-slate-200 bg-slate-50 object-cover" data-image-preview /><input type="file" name="uploads[team][__INDEX__][image]" accept="image/jpeg,image/png,image/webp" class="admin-file" data-image-input /></div>
                    </article>
                </template>
                </details>
            @endif

            @if ($selectedSection === 'northstar')
                <details id="about-northstar" class="admin-section space-y-5" open>
                <summary class="admin-section-title cursor-pointer">6. Northstar people</summary>
                <x-admin.input name="content[northstar_title]" label="Northstar section title" :value="old('content.northstar_title', $content['northstar_title'])" required />
                <p class="admin-hint">This section shows each person’s name and image only on the public page.</p>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-repeat-group="northstar">
                    @foreach (old('content.northstar', $content['northstar']) as $index => $person)
                        <article class="admin-card space-y-4 p-4 sm:p-5" data-repeat-item>
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3"><h3 class="text-base font-semibold text-slate-900">Person {{ $index + 1 }}</h3><button type="button" class="admin-btn admin-btn--danger admin-btn--sm shrink-0" data-remove-item>Remove</button></div>
                            <x-admin.input name="content[northstar][{{ $index }}][name]" label="Name" :value="old('content.northstar.'.$index.'.name', $person['name'])" required />
                            @include ('admin.about._image', ['label' => 'Image', 'name' => "uploads[northstar][$index][image]", 'errorName' => "uploads.northstar.$index.image", 'image' => old('content.northstar.'.$index.'.image', $content['northstar'][$index]['image'] ?? null), 'removeKey' => "northstar-$index", 'removePrefix' => 'northstar', 'contentName' => "content[northstar][$index][image]"])
                        </article>
                    @endforeach
                </div>
                <button type="button" class="admin-btn admin-btn--ghost admin-btn--sm" data-add-item>Add person</button>
                <template data-repeat-template="northstar">
                    <article class="admin-card space-y-4 p-4 sm:p-5" data-repeat-item><div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3"><h3 class="text-base font-semibold text-slate-900">New person</h3><button type="button" class="admin-btn admin-btn--danger admin-btn--sm shrink-0" data-remove-item>Remove</button></div><div><label class="admin-label">Name</label><input class="admin-input" name="content[northstar][__INDEX__][name]" required maxlength="150" /></div><div><label class="admin-label">Image</label><input type="hidden" name="content[northstar][__INDEX__][image]" value="" /><img src="" alt="Selected image preview" class="hidden h-28 w-full rounded-md border border-slate-200 bg-slate-50 object-cover" data-image-preview /><input type="file" name="uploads[northstar][__INDEX__][image]" accept="image/jpeg,image/png,image/webp" class="admin-file" data-image-input /></div></article>
                </template>
                </details>
            @endif

            <div class="flex justify-end border-t border-slate-200 pt-5">
                <button type="submit" class="admin-btn admin-btn--primary"><i class="fa-solid fa-floppy-disk"></i> Save {{ $sectionTitle }}</button>
            </div>
        </form>
    </div>
@endsection

@push ('scripts')
    <script>
        (() => {
            const form = document.querySelector('[data-about-editor]');
            if (!form) return;

            const bindPreview = (input) => {
                input.addEventListener('change', () => {
                    const preview = input.closest('[data-repeat-item], .space-y-2')?.querySelector('[data-image-preview]');
                    const file = input.files?.[0];
                    if (!preview || !file) return;
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('hidden');
                });
            };
            form.querySelectorAll('[data-image-input]').forEach(bindPreview);

            const reindex = (group) => {
                const list = group.dataset.repeatGroup;
                group.querySelectorAll('[data-repeat-item]').forEach((item, index) => {
                    item.querySelectorAll('[name]').forEach((field) => {
                        field.name = field.name.replace(/\[(cards|team|northstar)\]\[\d+\]/g, (_, name) => `[${name}][${index}]`);
                    });
                    const removeImage = item.querySelector('[data-remove-input]');
                    if (removeImage) removeImage.name = `remove_images[${removeImage.dataset.removePrefix}-${index}]`;
                    const heading = item.querySelector('h3');
                    if (heading && !heading.textContent.startsWith('New')) {
                        heading.textContent = `${list.endsWith('-cards') ? 'Slide' : list === 'team' ? 'Team member' : 'Person'} ${index + 1}`;
                    }
                });
            };

            const updateRemoveButtons = (group) => {
                const items = group.querySelectorAll('[data-repeat-item]');
                items.forEach((item) => {
                    const removeButton = item.querySelector('[data-remove-item]');
                    removeButton.disabled = items.length <= 1;
                    removeButton.title = items.length <= 1 ? 'At least one item is required' : '';
                });
            };

            form.querySelectorAll('[data-repeat-group]').forEach((group) => {
                updateRemoveButtons(group);
                group.addEventListener('click', (event) => {
                    const removeButton = event.target.closest('[data-remove-item]');
                    if (removeButton) {
                        if (group.querySelectorAll('[data-repeat-item]').length <= 1) {
                            return;
                        }
                        removeButton.closest('[data-repeat-item]').remove();
                        reindex(group);
                        updateRemoveButtons(group);
                        return;
                    }

                    if (event.target.closest('[data-add-item]')) {
                        const template = form.querySelector(`[data-repeat-template="${group.dataset.repeatGroup}"]`);
                        const index = group.querySelectorAll('[data-repeat-item]').length;
                        const fragment = template.content.cloneNode(true);
                        fragment.querySelectorAll('[name]').forEach((field) => {
                            field.name = field.name.replaceAll('__INDEX__', String(index));
                            if (field.matches('[data-image-input]')) bindPreview(field);
                        });
                        group.appendChild(fragment);
                        updateRemoveButtons(group);
                    }
                });
            });

            form.addEventListener('submit', () => form.querySelectorAll('[data-repeat-group]').forEach(reindex));
        })();
    </script>
@endpush
