@extends ('admin.layouts.app')

@section ('title', 'Dashboard')
@section ('page-title', 'Dashboard overview')

@section ('content')
    <div class="mx-auto max-w-7xl space-y-7">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-semibold text-[#0b376d]">Welcome back, {{ auth()->user()->name }}.</p>
                <p class="mt-1 text-sm text-slate-500">Here is a quick view of your {{ $siteSettings->display_name }} workspace.</p>
            </div>
            <p class="inline-flex items-center gap-2 self-start rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span> All systems operational
            </p>
        </div>

        {{-- New enquiries notification --}}
        @if ($enquiryNotifications['total'] > 0)
            <div class="rounded-lg border border-sky-200 bg-sky-50 p-5">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#0b376d] text-white">
                        <i class="fa-solid fa-bell"></i>
                    </span>
                    <div>
                        <p class="font-bold text-slate-900">New enquiries received</p>
                        <div class="mt-1.5 flex flex-col gap-1 text-sm">
                            @if ($enquiryNotifications['contact'] > 0)
                                <a href="{{ route('admin.contact-enquiries.index') }}" class="font-medium text-[#0b376d] hover:underline">
                                    {{ $enquiryNotifications['contact'] }} new contact {{ Str::plural('enquiry', $enquiryNotifications['contact']) }} received
                                </a>
                            @endif
                            @if ($enquiryNotifications['product'] > 0)
                                <a href="{{ route('admin.product-enquiries.index') }}" class="font-medium text-[#0b376d] hover:underline">
                                    {{ $enquiryNotifications['product'] }} new product {{ Str::plural('enquiry', $enquiryNotifications['product']) }} received
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($statistics as $statistic)
                <a
                    @if ($statistic['url']) href="{{ $statistic['url'] }}" @else href="#" onclick="return false;" @endif
                    class="block rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md @unless ($statistic['url']) cursor-default @endunless"
                >
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-lg @class(['bg-sky-50 text-sky-700' => $statistic['tone'] === 'sky', 'bg-violet-50 text-violet-700' => $statistic['tone'] === 'violet', 'bg-amber-50 text-amber-700' => $statistic['tone'] === 'amber', 'bg-emerald-50 text-emerald-700' => $statistic['tone'] === 'emerald'])">
                        <i class="fa-solid {{ $statistic['icon'] }}"></i>
                    </span>
                    <p class="mt-5 text-2xl font-bold tracking-tight text-slate-900">{{ $statistic['value'] }}</p>
                    <p class="mt-1 text-sm font-medium text-slate-500">{{ $statistic['label'] }}</p>
                </a>
            @endforeach
        </div>

        <div class="grid gap-7 xl:grid-cols-[1.45fr_1fr]">
            <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="font-bold text-slate-900">Recent enquiries</h2>
                        <p class="mt-1 text-sm text-slate-500">Latest messages from the website</p>
                    </div>
                    <a href="{{ route('admin.contact-enquiries.index') }}" class="rounded border border-slate-200 px-3 py-1.5 text-xs font-semibold text-[#0b376d] transition hover:border-[#0b376d] hover:bg-sky-50">
                        View all
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($recentEnquiries as $item)
                        <a href="{{ $item['url'] }}" class="flex items-center gap-4 px-5 py-4 transition hover:bg-slate-50">
                            <span @class([
                                'flex h-10 w-10 shrink-0 items-center justify-center rounded-lg',
                                'bg-[#0b376d] text-white' => $item['unread'],
                                'bg-slate-100 text-[#0b376d]' => ! $item['unread'],
                            ])>
                                <i class="fa-solid {{ Str::startsWith($item['label'], 'Product') ? 'fa-box' : 'fa-envelope' }}"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-slate-800">
                                    {{ $item['name'] }}
                                    @if ($item['unread'])
                                        <span class="ml-1 align-middle text-[10px] font-bold uppercase text-[#0b376d]">New</span>
                                    @endif
                                </p>
                                <p class="truncate text-sm text-slate-500">{{ $item['label'] }} — {{ $item['when']->diffForHumans() }}</p>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-300"></i>
                        </a>
                    @empty
                        <p class="px-5 py-8 text-center text-sm text-slate-500">No enquiries yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-bold text-slate-900">Quick actions</h2>
                <p class="mt-1 text-sm text-slate-500">Common workspace actions</p>
                <div class="mt-5 grid gap-3">
                    @php
                        $quickActions = [
                            ['label' => 'Add product', 'hint' => 'Publish a new product to the website', 'url' => route('admin.products.create'), 'icon' => 'fa-plus', 'tone' => 'bg-amber-100 text-amber-700'],
                            ['label' => 'Contact enquiries', 'hint' => 'Review messages from the contact form', 'url' => route('admin.contact-enquiries.index'), 'icon' => 'fa-envelope', 'tone' => 'bg-sky-100 text-[#0b376d]'],
                            ['label' => 'Product enquiries', 'hint' => 'Review enquiries from the products page', 'url' => route('admin.product-enquiries.index'), 'icon' => 'fa-box', 'tone' => 'bg-sky-100 text-[#0b376d]'],
                        ];
                    @endphp
                    @foreach ($quickActions as $action)
                        <a href="{{ $action['url'] }}" class="group flex items-center justify-between rounded-lg border border-slate-200 p-4 transition hover:border-[#0b376d] hover:bg-sky-50">
                            <span class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded {{ $action['tone'] }}">
                                    <i class="fa-solid {{ $action['icon'] }}"></i>
                                </span>
                                <span>
                                    <span class="block text-sm font-semibold text-slate-800">{{ $action['label'] }}</span>
                                    <span class="block text-xs text-slate-500">{{ $action['hint'] }}</span>
                                </span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-[#0b376d]"></i>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection
