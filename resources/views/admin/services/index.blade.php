@extends ('admin.layouts.app')
@section ('title', 'Services')
@section ('page-title', 'Services')

@section ('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <p class="text-sm text-slate-500">Manage the Services page — the intro block and the tabbed service list. The existing page design and animations are preserved.</p>

        @if (session('status'))
            <div class="admin-alert admin-alert--success">
                <i class="fa-solid fa-circle-check mt-0.5"></i> <span>{{ session('status') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Intro / page content --}}
        <form method="POST" action="{{ route('admin.services.page.update') }}" class="admin-section max-w-2xl space-y-5">
            @csrf
            @method ('PUT')
            <h2 class="admin-section-title">Page content</h2>

            <x-admin.input
                name="banner_title"
                label="Page title"
                :value="$page->banner_title"
                placeholder="Enter the page banner title"
            />
            <x-admin.textarea
                name="intro_heading"
                label="Intro heading"
                :value="$page->intro_heading"
                placeholder="Enter the intro heading (line breaks are kept)"
                :rows="2"
            />
            <x-admin.textarea
                name="intro_body"
                label="Intro paragraphs"
                :value="$page->intro_body"
                placeholder="One paragraph per blank line"
                :rows="6"
                hint="Leave a blank line between paragraphs — each becomes its own paragraph."
            />
            <x-admin.input
                name="intro_statement"
                label="Intro statement line"
                :value="$page->intro_statement"
                placeholder="e.g. We measure. We understand. We help you act."
            />

            <div class="flex justify-end border-t border-slate-100 pt-5">
                <button type="submit" class="admin-btn admin-btn--primary">Save page content</button>
            </div>
        </form>

        {{-- Services list --}}
        <div class="space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="admin-section-title">Services</h2>
                <a href="{{ route('admin.services.create') }}" class="admin-btn admin-btn--primary admin-btn--sm">
                    <i class="fa-solid fa-plus"></i> Add service
                </a>
            </div>

            <form method="GET" action="{{ route('admin.services.index') }}" class="admin-card flex flex-wrap items-end gap-3 p-4">
                <div class="min-w-56 flex-1">
                    <label for="search" class="admin-label">Search</label>
                    <input id="search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name, category or tags" class="admin-input" />
                </div>
                <div>
                    <label for="per_page" class="admin-label">Per page</label>
                    <select id="per_page" name="per_page" class="admin-select w-28">
                        @foreach ($perPageOptions as $option)
                            <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="admin-btn admin-btn--primary">Apply</button>
            </form>

            <div class="admin-card overflow-hidden">
                @if ($services->isEmpty())
                    <div class="p-12 text-center text-slate-500">
                        <i class="fa-solid fa-briefcase text-3xl text-sky-300"></i>
                        <p class="mt-4 font-semibold text-slate-700">No services yet</p>
                        <p class="mt-1 text-sm">Add your first service to show it on the Services page.</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.services.bulk-destroy') }}" id="bulk-form">
                        @csrf
                        @method ('DELETE')
                        <div id="bulk-toolbar" class="hidden items-center justify-between gap-3 border-b border-slate-100 bg-sky-50/70 px-5 py-3">
                            <p class="text-sm font-semibold text-[#0b376d]"><span id="selected-count">0</span> selected</p>
                            <button type="submit" data-bulk-delete class="admin-btn admin-btn--danger admin-btn--sm">
                                <i class="fa-solid fa-trash-can"></i> Delete selected
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[48rem] text-left text-sm">
                                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="w-10 px-4 py-3"><input type="checkbox" id="select-all" aria-label="Select all" class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" /></th>
                                        <th class="px-4 py-3">Name</th>
                                        <th class="px-4 py-3">Category</th>
                                        <th class="px-4 py-3">Images</th>
                                        <th class="px-4 py-3">Order</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($services as $service)
                                        <tr class="transition hover:bg-slate-50/80">
                                            <td class="px-4 py-3 align-top"><input type="checkbox" name="selected[]" value="{{ $service->id }}" aria-label="Select service {{ $service->id }}" class="row-checkbox h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" /></td>
                                            <td class="px-4 py-3 align-top font-semibold text-slate-800">{{ $service->name }}</td>
                                            <td class="px-4 py-3 align-top text-slate-500">{{ $service->category ?: '—' }}</td>
                                            <td class="px-4 py-3 align-top text-slate-600">{{ $service->images_count }}</td>
                                            <td class="px-4 py-3 align-top text-slate-600">{{ $service->display_order }}</td>
                                            <td class="px-4 py-3 align-top">
                                                @if ($service->is_enabled)
                                                    <span class="admin-badge bg-emerald-100 text-emerald-800"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Enabled</span>
                                                @else
                                                    <span class="admin-badge bg-slate-100 text-slate-600"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Disabled</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 align-top">
                                                <div class="flex items-center justify-end gap-2">
                                                    <form method="POST" action="{{ route('admin.services.toggle', $service) }}">
                                                        @csrf
                                                        @method ('PATCH')
                                                        <button class="admin-btn admin-btn--ghost admin-btn--sm">{{ $service->is_enabled ? 'Disable' : 'Enable' }}</button>
                                                    </form>
                                                    <a href="{{ route('admin.services.edit', $service) }}" class="admin-btn admin-btn--ghost admin-btn--sm !text-[#0b376d]"><i class="fa-solid fa-pen"></i> Edit</a>
                                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Delete this service permanently?');">
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

                    <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-slate-500">
                            Showing <span class="font-semibold text-slate-700">{{ $services->firstItem() }}</span>–<span class="font-semibold text-slate-700">{{ $services->lastItem() }}</span>
                            of <span class="font-semibold text-slate-700">{{ $services->total() }}</span> services
                        </p>
                        <div>{{ $services->onEachSide(1)->links() }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push ('scripts')
    @include ('admin.home.partials.bulk-scripts', ['noun' => 'service'])
@endpush
