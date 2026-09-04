<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Admin') · {{ $siteSettings->display_name }}</title>
    <link rel="icon" href="{{ $siteSettings->favicon_url }}" />
    <link rel="stylesheet" href="{{ asset('material/css/all.min.css') }}" />
    @vite (['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <div id="admin-sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-slate-950/50 lg:hidden"></div>

    <aside
        id="admin-sidebar"
        class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-[#0b376d] px-5 py-6 text-white shadow-2xl transition-transform duration-300 lg:translate-x-0"
    >
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-2">
            <img src="{{ $siteSettings->header_logo_url }}" alt="{{ $siteSettings->display_name }}" class="h-11 w-auto rounded-lg bg-white p-1.5" />
            <span class="text-lg font-semibold tracking-tight">{{ $siteSettings->display_name }}</span>
        </a>
        <p class="mt-2 px-2 text-xs font-medium uppercase tracking-[0.2em] text-sky-200">Admin workspace</p>

        @php
            $notifications = $enquiryNotifications ?? ['contact' => 0, 'product' => 0, 'total' => 0];

            $navigation = [
                [
                    'label' => 'Dashboard',
                    'icon' => 'fa-gauge-high',
                    'url' => route('admin.dashboard'),
                    'active' => request()->routeIs('admin.dashboard'),
                ],
                [
                    'label' => 'Home',
                    'icon' => 'fa-house',
                    'children' => [
                        ['label' => 'Brand Logo', 'url' => route('admin.home.logo.index'), 'active' => request()->routeIs('admin.home.logo.*')],
                        ['label' => 'Top Banner', 'url' => route('admin.home.banner.edit'), 'active' => request()->routeIs('admin.home.banner.*') || request()->routeIs('admin.home.cards.*')],
                        ['label' => 'About Founder', 'url' => route('admin.home.founder.edit'), 'active' => request()->routeIs('admin.home.founder.*')],
                        ['label' => 'Core Values', 'url' => route('admin.home.core-values.index'), 'active' => request()->routeIs('admin.home.core-values.*')],
                    ],
                ],
                [
                    'label' => 'Products',
                    'icon' => 'fa-box',
                    'badgeKey' => 'products-group',
                    'badge' => $notifications['product'],
                    'children' => [
                        ['label' => 'Products', 'url' => route('admin.products.index'), 'active' => request()->routeIs('admin.products.*')],
                        ['label' => 'Product Enquiries', 'url' => route('admin.product-enquiries.index'), 'active' => request()->routeIs('admin.product-enquiries.*'), 'badgeKey' => 'product', 'badge' => $notifications['product']],
                    ],
                ],
                [
                    'label' => 'Testimonials',
                    'icon' => 'fa-quote-right',
                    'url' => route('admin.testimonials.index'),
                    'active' => request()->routeIs('admin.testimonials.*'),
                ],
                [
                    'label' => 'Services',
                    'icon' => 'fa-briefcase',
                    'url' => route('admin.services.index'),
                    'active' => request()->routeIs('admin.services.*'),
                ],
                [
                    'label' => 'Contact Enquiries',
                    'icon' => 'fa-envelope',
                    'url' => route('admin.contact-enquiries.index'),
                    'active' => request()->routeIs('admin.contact-enquiries.*'),
                    'badgeKey' => 'contact',
                    'badge' => $notifications['contact'],
                ],
                [
                    'label' => 'Settings',
                    'icon' => 'fa-sliders',
                    'children' => [
                        ['label' => 'Overview', 'url' => route('admin.settings.index'), 'active' => request()->routeIs('admin.settings.index')],
                        ['label' => 'Email Settings', 'url' => route('admin.settings.email.edit'), 'active' => request()->routeIs('admin.settings.email.*')],
                        ['label' => 'SMTP Settings', 'url' => route('admin.settings.smtp.edit'), 'active' => request()->routeIs('admin.settings.smtp.*')],
                        ['label' => 'Brevo', 'url' => route('admin.settings.brevo.edit'), 'active' => request()->routeIs('admin.settings.brevo.*')],
                        ['label' => 'Google reCAPTCHA', 'url' => route('admin.settings.recaptcha.edit'), 'active' => request()->routeIs('admin.settings.recaptcha.*')],
                        ['label' => 'Site Settings', 'url' => route('admin.settings.site.edit'), 'active' => request()->routeIs('admin.settings.site.*')],
                    ],
                ],
            ];
        @endphp

        @php
            $badge = function (?string $key, int $count): string {
                $hidden = $count > 0 ? '' : 'hidden';

                return "<span data-badge=\"{$key}\" class=\"ml-auto inline-flex min-w-5 items-center justify-center rounded-full bg-amber-300 px-1.5 py-0.5 text-[10px] font-bold text-[#0b376d] {$hidden}\">{$count}</span>";
            };
        @endphp

        <nav class="admin-nav-scroll -mr-2 mt-9 flex-1 space-y-2 overflow-y-auto pr-2" aria-label="Admin navigation">
            @foreach ($navigation as $item)
                @if (! empty($item['children']))
                    @php $groupActive = collect($item['children'])->contains('active', true); @endphp
                    <details class="group" data-nav-group="{{ $item['label'] }}" @if ($groupActive) open @endif>
                        <summary class="flex cursor-pointer list-none items-center gap-3 rounded-md px-4 py-3 text-sm font-medium text-sky-100/85 transition hover:bg-white/10">
                            <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>
                            {{ $item['label'] }}
                            @if (! empty($item['badgeKey']))
                                {!! $badge($item['badgeKey'], (int) ($item['badge'] ?? 0)) !!}
                            @endif
                            <i class="fa-solid fa-chevron-down @if (empty($item['badgeKey'])) ml-auto @else ml-2 @endif text-xs transition group-open:rotate-180"></i>
                        </summary>
                        <div class="mt-1 space-y-1 pl-4">
                            @foreach ($item['children'] as $child)
                                <a
                                    href="{{ $child['url'] }}"
                                    data-nav="{{ $child['label'] }}"
                                    @class([
                                        'flex items-center gap-3 rounded-md px-4 py-2.5 text-sm transition',
                                        'nav-link--active bg-white/15 font-semibold text-white shadow-sm' => $child['active'],
                                        'font-medium text-sky-100/80 hover:bg-white/10' => ! $child['active'],
                                    ])
                                    @if ($child['active']) aria-current="page" @endif
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-60"></span>
                                    {{ $child['label'] }}
                                    @if (! empty($child['badgeKey']))
                                        {!! $badge($child['badgeKey'], (int) ($child['badge'] ?? 0)) !!}
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </details>
                @else
                    <a
                        href="{{ $item['url'] }}"
                        data-nav="{{ $item['label'] }}"
                        @class([
                            'flex items-center gap-3 rounded-md px-4 py-3 text-sm transition',
                            'nav-link--active bg-white/15 font-semibold text-white shadow-sm' => $item['active'],
                            'font-medium text-sky-100/85 hover:bg-white/10' => ! $item['active'],
                        ])
                        @if ($item['active']) aria-current="page" @endif
                    >
                        <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>
                        {{ $item['label'] }}
                        @if (! empty($item['badgeKey']))
                            {!! $badge($item['badgeKey'], (int) ($item['badge'] ?? 0)) !!}
                        @endif
                    </a>
                @endif
            @endforeach
        </nav>

        <p class="mt-4 px-2 text-[11px] text-sky-200/70">Signed in as {{ auth()->user()->name }}</p>
    </aside>

    <div class="min-h-screen lg:pl-72">
        <header class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-3">
                <button
                    id="admin-sidebar-toggle"
                    type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded border border-slate-200 text-slate-600 transition hover:border-[#0b376d] hover:text-[#0b376d] lg:hidden"
                    aria-label="Open navigation" aria-controls="admin-sidebar" aria-expanded="false"
                >
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="min-w-0">
                    <p class="hidden text-xs font-semibold uppercase tracking-[0.16em] text-slate-400 sm:block">Administration</p>
                    <h1 class="truncate text-lg font-bold text-slate-900">@yield('page-title', 'Dashboard')</h1>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <div class="relative" id="notification-bell">
                    <button
                        type="button"
                        data-bell-toggle
                        class="relative inline-flex h-10 w-10 items-center justify-center rounded border border-slate-200 text-slate-600 transition hover:border-[#0b376d] hover:text-[#0b376d]"
                        aria-label="New enquiries" aria-haspopup="true" aria-expanded="false"
                    >
                        <i class="fa-regular fa-bell"></i>
                        <span data-badge="bell" class="absolute -right-1.5 -top-1.5 inline-flex min-w-5 items-center justify-center rounded-full bg-[#0b376d] px-1 py-0.5 text-[10px] font-bold text-white {{ $notifications['total'] > 0 ? '' : 'hidden' }}">
                            {{ $notifications['total'] }}
                        </span>
                    </button>
                    <div data-bell-menu class="absolute right-0 mt-2 hidden w-64 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <p class="text-sm font-bold text-slate-900">New enquiries</p>
                            <p data-bell-empty class="mt-0.5 text-xs text-slate-500 {{ $notifications['total'] > 0 ? 'hidden' : '' }}">You're all caught up.</p>
                        </div>
                        <a href="{{ route('admin.contact-enquiries.index') }}" data-bell-row="contact" class="flex items-center justify-between px-4 py-3 text-sm transition hover:bg-slate-50 {{ $notifications['contact'] > 0 ? '' : 'hidden' }}">
                            <span class="font-medium text-slate-700"><i class="fa-solid fa-envelope mr-2 text-slate-400"></i> Contact Enquiries</span>
                            <span data-badge="bell-contact" class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-800">{{ $notifications['contact'] }}</span>
                        </a>
                        <a href="{{ route('admin.product-enquiries.index') }}" data-bell-row="product" class="flex items-center justify-between px-4 py-3 text-sm transition hover:bg-slate-50 {{ $notifications['product'] > 0 ? '' : 'hidden' }}">
                            <span class="font-medium text-slate-700"><i class="fa-solid fa-box mr-2 text-slate-400"></i> Product Enquiries</span>
                            <span data-badge="bell-product" class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-800">{{ $notifications['product'] }}</span>
                        </a>
                    </div>
                </div>

                <a
                    href="{{ route('home') }}" target="_blank"
                    class="hidden items-center gap-2 rounded border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 transition hover:border-[#0b376d] hover:text-[#0b376d] sm:flex"
                ><i class="fa-solid fa-arrow-up-right-from-square"></i> View website</a>

                <div class="relative" id="user-menu">
                    <button
                        type="button"
                        data-user-toggle
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0b376d] text-xs font-bold text-white transition hover:ring-2 hover:ring-[#0b376d]/20 focus:outline-none focus:ring-2 focus:ring-[#0b376d]/30"
                        aria-label="Account menu" aria-haspopup="true" aria-expanded="false"
                    >
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </button>
                    <div data-user-dropdown class="absolute right-0 mt-2 hidden w-60 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl">
                        <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3.5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#0b376d] text-sm font-bold text-white">
                                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-slate-500">Administrator</p>
                            </div>
                        </div>
                        <p class="truncate px-4 pt-3 text-xs text-slate-400">{{ auth()->user()->email }}</p>
                        <form method="POST" action="{{ route('admin.logout') }}" class="p-3">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm w-full !text-rose-600 hover:!border-rose-300 hover:!bg-rose-50">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="px-4 py-6 sm:px-6 lg:px-8">@yield('content')</main>
    </div>

    <script>
        (() => {
            const sidebar = document.getElementById("admin-sidebar");
            const backdrop = document.getElementById("admin-sidebar-backdrop");
            const toggle = document.getElementById("admin-sidebar-toggle");
            const toggleSidebar = () => {
                const open = sidebar.classList.toggle("-translate-x-full") === false;
                backdrop.classList.toggle("hidden", !open);
                toggle.setAttribute("aria-expanded", String(open));
            };
            toggle?.addEventListener("click", toggleSidebar);
            backdrop?.addEventListener("click", toggleSidebar);

            // Header dropdowns (notification bell + account menu) — only one open at a time.
            const bell = document.getElementById("notification-bell");
            const dropdowns = [
                { root: bell, btn: "[data-bell-toggle]", menu: "[data-bell-menu]" },
                { root: document.getElementById("user-menu"), btn: "[data-user-toggle]", menu: "[data-user-dropdown]" },
            ].filter((d) => d.root).map((d) => ({
                root: d.root,
                btn: d.root.querySelector(d.btn),
                menu: d.root.querySelector(d.menu),
            }));

            const closeAll = (except) => dropdowns.forEach((d) => {
                if (d === except) return;
                d.menu.classList.add("hidden");
                d.btn.setAttribute("aria-expanded", "false");
            });

            dropdowns.forEach((d) => {
                d.btn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    const open = d.menu.classList.toggle("hidden") === false;
                    d.btn.setAttribute("aria-expanded", String(open));
                    if (open) closeAll(d);
                });
            });

            document.addEventListener("click", (e) => {
                dropdowns.forEach((d) => {
                    if (!d.root.contains(e.target)) {
                        d.menu.classList.add("hidden");
                        d.btn.setAttribute("aria-expanded", "false");
                    }
                });
            });
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") closeAll();
            });

            // Keep every enquiry badge in sync from a {contact, product, total} payload.
            const applyCounts = (data) => {
                if (!data || typeof data.total !== "number") return;
                const map = {
                    contact: data.contact,
                    product: data.product,
                    "products-group": data.product,
                    bell: data.total,
                    "bell-contact": data.contact,
                    "bell-product": data.product,
                };
                document.querySelectorAll("[data-badge]").forEach((el) => {
                    const value = map[el.dataset.badge];
                    if (value === undefined) return;
                    el.textContent = value;
                    el.classList.toggle("hidden", value <= 0);
                });
                bell?.querySelector('[data-bell-row="contact"]')?.classList.toggle("hidden", data.contact <= 0);
                bell?.querySelector('[data-bell-row="product"]')?.classList.toggle("hidden", data.product <= 0);
                bell?.querySelector("[data-bell-empty]")?.classList.toggle("hidden", data.total > 0);
            };
            window.applyEnquiryCounts = applyCounts;

            const poll = () => fetch("{{ route('admin.enquiry-notifications') }}", { headers: { Accept: "application/json" } })
                .then((r) => (r.ok ? r.json() : null))
                .then(applyCounts)
                .catch(() => {});

            setInterval(poll, 45000);
            document.addEventListener("visibilitychange", () => {
                if (document.visibilityState === "visible") poll();
            });
        })();
    </script>
    @stack ('scripts')
</body>
</html>
