@extends ('admin.layouts.app')
@section ('title', 'Testimonials')
@section ('page-title', 'Testimonials')

@section ('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">Manage the client testimonials shown on the website homepage.</p>
            <a href="{{ route('admin.testimonials.create') }}" class="admin-btn admin-btn--primary">
                <i class="fa-solid fa-plus"></i> Add testimonial
            </a>
        </div>

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

        <form method="GET" action="{{ route('admin.testimonials.index') }}" class="admin-card flex flex-wrap items-end gap-3 p-4">
            <div class="min-w-56 flex-1">
                <label for="search" class="admin-label">Search testimonials</label>
                <input id="search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name, role or review text" class="admin-input" />
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
            @if ($testimonials->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <i class="fa-solid fa-quote-right text-3xl text-sky-300"></i>
                    <p class="mt-4 font-semibold text-slate-700">No testimonials yet</p>
                    <p class="mt-1 text-sm">Add your first testimonial to show it on the website.</p>
                </div>
            @else
                <form method="POST" action="{{ route('admin.testimonials.bulk-destroy') }}" id="bulk-form">
                    @csrf
                    @method ('DELETE')
                    <div id="bulk-toolbar" class="hidden items-center justify-between gap-3 border-b border-slate-100 bg-sky-50/70 px-5 py-3">
                        <p class="text-sm font-semibold text-[#0b376d]"><span id="selected-count">0</span> selected</p>
                        <button type="submit" data-bulk-delete class="admin-btn admin-btn--danger admin-btn--sm">
                            <i class="fa-solid fa-trash-can"></i> Delete selected
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[56rem] text-left text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="w-10 px-4 py-3"><input type="checkbox" id="select-all" aria-label="Select all" class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" /></th>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Designation / Company</th>
                                    <th class="px-4 py-3">Review</th>
                                    <th class="px-4 py-3">Rating</th>
                                    <th class="px-4 py-3">Order</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($testimonials as $testimonial)
                                    <tr class="transition hover:bg-slate-50/80">
                                        <td class="px-4 py-3 align-top"><input type="checkbox" name="selected[]" value="{{ $testimonial->id }}" aria-label="Select testimonial {{ $testimonial->id }}" class="row-checkbox h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" /></td>
                                        <td class="px-4 py-3 align-top font-semibold text-slate-800">{{ $testimonial->name }}</td>
                                        <td class="px-4 py-3 align-top text-slate-500">{{ $testimonial->role_line ?: '—' }}</td>
                                        <td class="px-4 py-3 align-top text-slate-500"><p class="max-w-sm">{{ Str::limit($testimonial->review, 80) }}</p></td>
                                        <td class="px-4 py-3 align-top whitespace-nowrap text-amber-500">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa-{{ $i <= $testimonial->rating ? 'solid' : 'regular' }} fa-star text-xs"></i>
                                            @endfor
                                        </td>
                                        <td class="px-4 py-3 align-top text-slate-600">{{ $testimonial->display_order }}</td>
                                        <td class="px-4 py-3 align-top">
                                            @if ($testimonial->is_enabled)
                                                <span class="admin-badge bg-emerald-100 text-emerald-800"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Enabled</span>
                                            @else
                                                <span class="admin-badge bg-slate-100 text-slate-600"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Disabled</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <div class="flex items-center justify-end gap-2">
                                                <form method="POST" action="{{ route('admin.testimonials.toggle', $testimonial) }}">
                                                    @csrf
                                                    @method ('PATCH')
                                                    <button class="admin-btn admin-btn--ghost admin-btn--sm">{{ $testimonial->is_enabled ? 'Disable' : 'Enable' }}</button>
                                                </form>
                                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="admin-btn admin-btn--ghost admin-btn--sm !text-[#0b376d]">
                                                    <i class="fa-solid fa-pen"></i> Edit
                                                </a>
                                                <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" onsubmit="return confirm('Delete this testimonial permanently?');">
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
                        Showing <span class="font-semibold text-slate-700">{{ $testimonials->firstItem() }}</span>–<span class="font-semibold text-slate-700">{{ $testimonials->lastItem() }}</span>
                        of <span class="font-semibold text-slate-700">{{ $testimonials->total() }}</span> testimonials
                    </p>
                    <div>{{ $testimonials->onEachSide(1)->links() }}</div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push ('scripts')
    <script>
        (() => {
            const bulkForm = document.getElementById("bulk-form");
            if (!bulkForm) return;
            const selectAll = document.getElementById("select-all");
            const toolbar = document.getElementById("bulk-toolbar");
            const countLabel = document.getElementById("selected-count");
            const rows = () => Array.from(bulkForm.querySelectorAll(".row-checkbox"));
            const sync = () => {
                const checked = rows().filter((r) => r.checked);
                countLabel.textContent = checked.length;
                toolbar.classList.toggle("hidden", checked.length === 0);
                toolbar.classList.toggle("flex", checked.length > 0);
                selectAll.checked = checked.length > 0 && checked.length === rows().length;
                selectAll.indeterminate = checked.length > 0 && checked.length < rows().length;
            };
            selectAll.addEventListener("change", () => { rows().forEach((r) => (r.checked = selectAll.checked)); sync(); });
            bulkForm.addEventListener("change", (e) => { if (e.target.classList.contains("row-checkbox")) sync(); });
            bulkForm.querySelector("[data-bulk-delete]")?.addEventListener("click", (e) => {
                const count = rows().filter((r) => r.checked).length;
                if (!window.confirm(`Delete ${count} selected testimonial(s) permanently?`)) e.preventDefault();
            });
            sync();
        })();
    </script>
@endpush
