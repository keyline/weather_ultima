@extends ('admin.layouts.app')
@section ('title', 'Brand Logos')
@section ('page-title', 'Home · Brand logos')

@section ('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">Partner / media logos shown in the "When the Weather Makes News" strip on the homepage.</p>
            <a href="{{ route('admin.home.logo.create') }}" class="admin-btn admin-btn--primary">
                <i class="fa-solid fa-plus"></i> Add logo
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

        <div class="admin-card overflow-hidden">
            @if ($logos->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <i class="fa-solid fa-images text-3xl text-sky-300"></i>
                    <p class="mt-4 font-semibold text-slate-700">No logos yet</p>
                    <p class="mt-1 text-sm">Add the media / partner logos that should appear on the homepage.</p>
                </div>
            @else
                <form method="POST" action="{{ route('admin.home.logo.bulk-destroy') }}" id="bulk-form">
                    @csrf
                    @method ('DELETE')
                    <div id="bulk-toolbar" class="hidden items-center justify-between gap-3 border-b border-slate-100 bg-sky-50/70 px-5 py-3">
                        <p class="text-sm font-semibold text-[#0b376d]"><span id="selected-count">0</span> selected</p>
                        <button type="submit" data-bulk-delete class="admin-btn admin-btn--danger admin-btn--sm">
                            <i class="fa-solid fa-trash-can"></i> Delete selected
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[44rem] text-left text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="w-10 px-4 py-3"><input type="checkbox" id="select-all" aria-label="Select all" class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" /></th>
                                    <th class="px-4 py-3">Logo</th>
                                    <th class="px-4 py-3">Alt text</th>
                                    <th class="px-4 py-3">Order</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($logos as $logo)
                                    <tr class="transition hover:bg-slate-50/80">
                                        <td class="px-4 py-3 align-middle"><input type="checkbox" name="selected[]" value="{{ $logo->id }}" aria-label="Select logo {{ $logo->id }}" class="row-checkbox h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" /></td>
                                        <td class="px-4 py-3">
                                            <span class="flex h-12 w-24 items-center justify-center rounded border border-slate-200 bg-white p-1">
                                                <img src="{{ $logo->image_url }}" alt="{{ $logo->alt_text }}" class="max-h-full max-w-full object-contain" />
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-700">{{ $logo->alt_text ?: '—' }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $logo->display_order }}</td>
                                        <td class="px-4 py-3">
                                            @if ($logo->is_enabled)
                                                <span class="admin-badge bg-emerald-100 text-emerald-800"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Enabled</span>
                                            @else
                                                <span class="admin-badge bg-slate-100 text-slate-600"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Disabled</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-2">
                                                <form method="POST" action="{{ route('admin.home.logo.toggle', $logo) }}">
                                                    @csrf
                                                    @method ('PATCH')
                                                    <button class="admin-btn admin-btn--ghost admin-btn--sm">{{ $logo->is_enabled ? 'Disable' : 'Enable' }}</button>
                                                </form>
                                                <a href="{{ route('admin.home.logo.edit', $logo) }}" class="admin-btn admin-btn--ghost admin-btn--sm !text-[#0b376d]"><i class="fa-solid fa-pen"></i> Edit</a>
                                                <form method="POST" action="{{ route('admin.home.logo.destroy', $logo) }}" onsubmit="return confirm('Delete this logo permanently?');">
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
                        Showing <span class="font-semibold text-slate-700">{{ $logos->firstItem() }}</span>–<span class="font-semibold text-slate-700">{{ $logos->lastItem() }}</span>
                        of <span class="font-semibold text-slate-700">{{ $logos->total() }}</span> logos
                    </p>
                    <div>{{ $logos->onEachSide(1)->links() }}</div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push ('scripts')
    @include ('admin.home.partials.bulk-scripts', ['noun' => 'logo'])
@endpush
