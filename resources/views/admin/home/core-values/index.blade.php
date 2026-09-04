@extends ('admin.layouts.app')
@section ('title', 'Core Values')
@section ('page-title', 'Home · Core values')

@section ('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">The "Core Values" (RAINBOW) list on the homepage.</p>
            <a href="{{ route('admin.home.core-values.create') }}" class="admin-btn admin-btn--primary">
                <i class="fa-solid fa-plus"></i> Add core value
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

        <form method="GET" action="{{ route('admin.home.core-values.index') }}" class="admin-card flex flex-wrap items-end gap-3 p-4">
            <div class="min-w-56 flex-1">
                <label for="search" class="admin-label">Search</label>
                <input id="search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by title or description" class="admin-input" />
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
            @if ($values->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <i class="fa-solid fa-list-check text-3xl text-sky-300"></i>
                    <p class="mt-4 font-semibold text-slate-700">No core values yet</p>
                    <p class="mt-1 text-sm">Add the values shown in the homepage RAINBOW section.</p>
                </div>
            @else
                <form method="POST" action="{{ route('admin.home.core-values.bulk-destroy') }}" id="bulk-form">
                    @csrf
                    @method ('DELETE')
                    <div id="bulk-toolbar" class="hidden items-center justify-between gap-3 border-b border-slate-100 bg-sky-50/70 px-5 py-3">
                        <p class="text-sm font-semibold text-[#0b376d]"><span id="selected-count">0</span> selected</p>
                        <button type="submit" data-bulk-delete class="admin-btn admin-btn--danger admin-btn--sm">
                            <i class="fa-solid fa-trash-can"></i> Delete selected
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[52rem] text-left text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="w-10 px-4 py-3"><input type="checkbox" id="select-all" aria-label="Select all" class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" /></th>
                                    <th class="px-4 py-3">Icon</th>
                                    <th class="px-4 py-3">Title</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3">Order</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($values as $value)
                                    <tr class="transition hover:bg-slate-50/80">
                                        <td class="px-4 py-3 align-top"><input type="checkbox" name="selected[]" value="{{ $value->id }}" aria-label="Select value {{ $value->id }}" class="row-checkbox h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" /></td>
                                        <td class="px-4 py-3 align-top">
                                            <span class="flex h-9 w-9 items-center justify-center rounded bg-[#0b376d] text-sm font-bold text-white">{{ $value->icon ?: '·' }}</span>
                                        </td>
                                        <td class="px-4 py-3 align-top font-semibold text-slate-800">{{ $value->title }}</td>
                                        <td class="px-4 py-3 align-top text-slate-500"><p class="max-w-md">{{ Str::limit($value->description, 100) }}</p></td>
                                        <td class="px-4 py-3 align-top text-slate-600">{{ $value->display_order }}</td>
                                        <td class="px-4 py-3 align-top">
                                            @if ($value->is_enabled)
                                                <span class="admin-badge bg-emerald-100 text-emerald-800"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Enabled</span>
                                            @else
                                                <span class="admin-badge bg-slate-100 text-slate-600"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Disabled</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <div class="flex items-center justify-end gap-2">
                                                <form method="POST" action="{{ route('admin.home.core-values.toggle', $value) }}">
                                                    @csrf
                                                    @method ('PATCH')
                                                    <button class="admin-btn admin-btn--ghost admin-btn--sm">{{ $value->is_enabled ? 'Disable' : 'Enable' }}</button>
                                                </form>
                                                <a href="{{ route('admin.home.core-values.edit', $value) }}" class="admin-btn admin-btn--ghost admin-btn--sm !text-[#0b376d]"><i class="fa-solid fa-pen"></i> Edit</a>
                                                <form method="POST" action="{{ route('admin.home.core-values.destroy', $value) }}" onsubmit="return confirm('Delete this core value permanently?');">
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
                        Showing <span class="font-semibold text-slate-700">{{ $values->firstItem() }}</span>–<span class="font-semibold text-slate-700">{{ $values->lastItem() }}</span>
                        of <span class="font-semibold text-slate-700">{{ $values->total() }}</span> core values
                    </p>
                    <div>{{ $values->onEachSide(1)->links() }}</div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push ('scripts')
    @include ('admin.home.partials.bulk-scripts', ['noun' => 'core value'])
@endpush
