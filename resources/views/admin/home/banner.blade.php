@extends ('admin.layouts.app')
@section ('title', 'Top Banner')
@section ('page-title', 'Home · Top banner')

@section ('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <p class="text-sm text-slate-500">
            The heading, subtitle and the dimension cards of the "One Vision. Five Dimensions." section at the top of the homepage.
        </p>

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

        <form method="POST" action="{{ route('admin.home.banner.update') }}" class="admin-section max-w-2xl space-y-5">
            @csrf
            @method ('PUT')

            <div>
                <h2 class="admin-section-title">Banner heading</h2>
                <p class="admin-hint">Existing homepage layout and animation are kept exactly as they are.</p>
            </div>

            <x-admin.input
                name="banner_title"
                label="Banner title"
                :value="$home->banner_title"
                placeholder="Enter the main banner title"
                required
            />

            <x-admin.textarea
                name="banner_subtitle"
                label="Banner subtitle"
                :value="$home->banner_subtitle"
                placeholder="Enter a short supporting description"
                :rows="3"
            />

            <div class="flex justify-end border-t border-slate-100 pt-5">
                <button type="submit" class="admin-btn admin-btn--primary">Save heading</button>
            </div>
        </form>

        {{-- Dimension cards --}}
        <div class="admin-section space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="admin-section-title">Dimension cards</h2>
                    <p class="admin-hint">The numbered cards below the heading. Each has its own image, text and link.</p>
                </div>
                <a href="{{ route('admin.home.cards.create') }}" class="admin-btn admin-btn--primary admin-btn--sm">
                    <i class="fa-solid fa-plus"></i> Add card
                </a>
            </div>

            @if ($cards->isEmpty())
                <p class="rounded border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                    No cards yet — the homepage will show the fallback image with no cards.
                </p>
            @else
                <form method="POST" action="{{ route('admin.home.cards.bulk-destroy') }}" id="bulk-form">
                    @csrf
                    @method ('DELETE')
                    <div id="bulk-toolbar" class="mb-3 hidden items-center justify-between gap-3 rounded bg-sky-50 px-4 py-2.5">
                        <p class="text-sm font-semibold text-[#0b376d]"><span id="selected-count">0</span> selected</p>
                        <button type="submit" data-bulk-delete class="admin-btn admin-btn--danger admin-btn--sm">
                            <i class="fa-solid fa-trash-can"></i> Delete selected
                        </button>
                    </div>

                    <div class="overflow-x-auto rounded border border-slate-200">
                        <table class="w-full min-w-[44rem] text-left text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="w-10 px-4 py-3"><input type="checkbox" id="select-all" aria-label="Select all" class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" /></th>
                                    <th class="px-4 py-3">Image</th>
                                    <th class="px-4 py-3">Title</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3">Order</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($cards as $card)
                                    <tr class="transition hover:bg-slate-50/80">
                                        <td class="px-4 py-3 align-top"><input type="checkbox" name="selected[]" value="{{ $card->id }}" aria-label="Select card {{ $card->id }}" class="row-checkbox h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" /></td>
                                        <td class="px-4 py-3">
                                            <img src="{{ $card->image_url ?? asset('material/images/service1.png') }}" alt="{{ $card->title }}" class="h-12 w-20 rounded border border-slate-200 object-cover" />
                                        </td>
                                        <td class="px-4 py-3 align-top font-semibold text-slate-800">{{ $card->title }}</td>
                                        <td class="px-4 py-3 align-top text-slate-500"><p class="max-w-xs">{{ Str::limit($card->description, 70) }}</p></td>
                                        <td class="px-4 py-3 align-top text-slate-600">{{ $card->display_order }}</td>
                                        <td class="px-4 py-3 align-top">
                                            @if ($card->is_enabled)
                                                <span class="admin-badge bg-emerald-100 text-emerald-800"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Enabled</span>
                                            @else
                                                <span class="admin-badge bg-slate-100 text-slate-600"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Disabled</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <div class="flex items-center justify-end gap-2">
                                                <form method="POST" action="{{ route('admin.home.cards.toggle', $card) }}">
                                                    @csrf
                                                    @method ('PATCH')
                                                    <button class="admin-btn admin-btn--ghost admin-btn--sm">{{ $card->is_enabled ? 'Disable' : 'Enable' }}</button>
                                                </form>
                                                <a href="{{ route('admin.home.cards.edit', $card) }}" class="admin-btn admin-btn--ghost admin-btn--sm !text-[#0b376d]"><i class="fa-solid fa-pen"></i> Edit</a>
                                                <form method="POST" action="{{ route('admin.home.cards.destroy', $card) }}" onsubmit="return confirm('Delete this card permanently?');">
                                                    @csrf
                                                    @method ('DELETE')
                                                    <button class="admin-btn admin-btn--danger admin-btn--sm"><i class="fa-solid fa-trash-can"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection

@push ('scripts')
    @include ('admin.home.partials.bulk-scripts', ['noun' => 'card'])
@endpush
