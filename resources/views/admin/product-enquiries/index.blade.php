@extends ('admin.layouts.app')
@section ('title', 'Product Enquiries')
@section ('page-title', 'Product enquiries')

@section ('content')
    @php
        $exportQuery = array_filter($filters, fn ($value) => filled($value));
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">Enquiries submitted from the products page.</p>
            <a href="{{ route('admin.product-enquiries.export', $exportQuery) }}" class="admin-btn admin-btn--ghost">
                <i class="fa-solid fa-file-excel text-emerald-600"></i> Export all
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
            @if ($enquiries->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <i class="fa-solid fa-inbox text-3xl text-sky-300"></i>
                    <p class="mt-4 font-semibold text-slate-700">No product enquiries yet</p>
                    <p class="mt-1 text-sm">Enquiries sent from a product card will appear here.</p>
                </div>
            @else
                <form method="POST" action="{{ route('admin.product-enquiries.bulk-destroy') }}" id="bulk-form">
                    @csrf
                    @method ('DELETE')
                    <div id="bulk-toolbar" class="hidden items-center justify-between gap-3 border-b border-slate-100 bg-sky-50/70 px-5 py-3">
                        <p class="text-sm font-semibold text-[#0b376d]"><span id="selected-count">0</span> selected</p>
                        <div class="flex items-center gap-2">
                            <button type="submit" data-bulk-delete class="admin-btn admin-btn--danger admin-btn--sm">
                                <i class="fa-solid fa-trash-can"></i> Delete selected
                            </button>
                            <button type="submit" formaction="{{ route('admin.product-enquiries.export') }}" formmethod="GET" class="admin-btn admin-btn--success admin-btn--sm">
                                <i class="fa-solid fa-file-excel"></i> Export selected
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[64rem] text-left text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="w-10 px-4 py-3"><input type="checkbox" id="select-all" aria-label="Select all" class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" /></th>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">Phone</th>
                                    <th class="px-4 py-3">Message</th>
                                    <th class="px-4 py-3">Submitted at</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($enquiries as $enquiry)
                                    @php
                                        $payload = [
                                            'id' => $enquiry->id,
                                            'product' => $enquiry->product_name,
                                            'name' => $enquiry->name,
                                            'email' => $enquiry->email,
                                            'phone' => $enquiry->phone ?: 'Not provided',
                                            'message' => $enquiry->message ?: 'No message provided.',
                                            'submittedAt' => $enquiry->created_at->format('d M Y, h:i A'),
                                            'deleteUrl' => route('admin.product-enquiries.destroy', $enquiry),
                                            'readUrl' => route('admin.product-enquiries.read', $enquiry),
                                        ];
                                    @endphp
                                    <tr class="transition hover:bg-slate-50/80" data-enquiry='@json($payload)' data-read="{{ $enquiry->is_read ? '1' : '0' }}">
                                        <td class="px-4 py-4 align-top"><input type="checkbox" name="selected[]" value="{{ $enquiry->id }}" aria-label="Select enquiry {{ $enquiry->id }}" class="row-checkbox h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" /></td>
                                        <td class="px-4 py-4 align-top font-mono text-xs text-slate-400">
                                            @unless ($enquiry->is_read)
                                                <span data-unread-dot class="mr-1 inline-block h-1.5 w-1.5 rounded-full bg-[#0b376d] align-middle" title="New"></span>
                                            @endunless
                                            #{{ $enquiry->id }}
                                        </td>
                                        <td class="px-4 py-4 align-top font-semibold text-slate-800">{{ $enquiry->product_name }}</td>
                                        <td class="px-4 py-4 align-top text-slate-700">{{ $enquiry->name }}</td>
                                        <td class="px-4 py-4 align-top text-slate-500"><a href="mailto:{{ $enquiry->email }}" class="hover:text-[#0b376d]">{{ $enquiry->email }}</a></td>
                                        <td class="px-4 py-4 align-top text-slate-500">{{ $enquiry->phone ?: '—' }}</td>
                                        <td class="px-4 py-4 align-top">
                                            <p class="max-w-xs text-slate-500">{{ Str::limit($enquiry->message, 70) ?: '—' }}</p>
                                            <button type="button" data-view-enquiry class="text-xs font-semibold text-[#0b376d]">View details</button>
                                        </td>
                                        <td class="px-4 py-4 align-top whitespace-nowrap text-slate-500">
                                            {{ $enquiry->created_at->format('d M Y') }}
                                            <span class="block text-xs text-slate-400">{{ $enquiry->created_at->format('h:i A') }}</span>
                                        </td>
                                        <td class="px-4 py-4 align-top text-right">
                                            <button type="button" data-view-enquiry class="admin-btn admin-btn--ghost admin-btn--sm !text-[#0b376d]">
                                                <i class="fa-solid fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

                <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">
                        Showing <span class="font-semibold text-slate-700">{{ $enquiries->firstItem() }}</span>–<span class="font-semibold text-slate-700">{{ $enquiries->lastItem() }}</span>
                        of <span class="font-semibold text-slate-700">{{ $enquiries->total() }}</span> enquiries
                    </p>
                    <div>{{ $enquiries->onEachSide(1)->links() }}</div>
                </div>
            @endif
        </div>
    </div>

    @include ('admin.partials.enquiry-modal', [
        'fields' => [
            'product' => 'Product',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'submittedAt' => 'Submitted at',
        ],
    ])
@endsection

@push ('scripts')
    @include ('admin.partials.enquiry-scripts')
@endpush
