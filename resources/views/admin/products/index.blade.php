@extends ('admin.layouts.app')
@section ('title', 'Products')
@section ('page-title', 'Products')

@section ('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">Manage the products shown on the public website.</p>
            <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn--primary">
                <i class="fa-solid fa-plus"></i> Add product
            </a>
        </div>

        @if (session('status'))
            <div class="admin-alert admin-alert--success">
                <i class="fa-solid fa-circle-check mt-0.5"></i> <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="GET" action="{{ route('admin.products.index') }}" class="admin-card flex flex-wrap items-end gap-3 p-4">
            <div class="min-w-56 flex-1">
                <label for="search" class="admin-label">Search products</label>
                <input id="search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by product name" class="admin-input" />
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
            @if ($products->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <i class="fa-solid fa-box-open text-3xl text-sky-300"></i>
                    <p class="mt-4 font-semibold text-slate-700">No products yet</p>
                    <p class="mt-1 text-sm">Add your first product to show it on the website.</p>
                    <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn--primary mt-4">
                        <i class="fa-solid fa-plus"></i> Add product
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[48rem] text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Image</th>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Short description</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($products as $product)
                                <tr class="transition hover:bg-slate-50/80">
                                    <td class="px-4 py-3">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-14 w-14 rounded border border-slate-200 object-cover" />
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $product->name }}</td>
                                    <td class="px-4 py-3 text-slate-500"><p class="max-w-md">{{ Str::limit($product->short_description, 90) }}</p></td>
                                    <td class="px-4 py-3">
                                        @if ($product->is_active)
                                            <span class="admin-badge bg-emerald-100 text-emerald-800"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Active</span>
                                        @else
                                            <span class="admin-badge bg-slate-100 text-slate-600"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Disabled</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.products.toggle', $product) }}">
                                                @csrf
                                                @method ('PATCH')
                                                <button class="admin-btn admin-btn--ghost admin-btn--sm">{{ $product->is_active ? 'Disable' : 'Enable' }}</button>
                                            </form>
                                            <a href="{{ route('admin.products.edit', $product) }}" class="admin-btn admin-btn--ghost admin-btn--sm !text-[#0b376d]">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product permanently?');">
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

                <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">
                        Showing <span class="font-semibold text-slate-700">{{ $products->firstItem() }}</span>–<span class="font-semibold text-slate-700">{{ $products->lastItem() }}</span>
                        of <span class="font-semibold text-slate-700">{{ $products->total() }}</span> products
                    </p>
                    <div>{{ $products->onEachSide(1)->links() }}</div>
                </div>
            @endif
        </div>
    </div>
@endsection
