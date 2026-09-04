@extends ('admin.layouts.app')
@section ('title', 'Settings')
@section ('page-title', 'Settings')

@section ('content')
    @php
        $status = fn (bool $on): string => $on
            ? '<span class="admin-badge bg-emerald-100 text-emerald-700"><i class="fa-solid fa-circle-check text-[10px]"></i> Active</span>'
            : '<span class="admin-badge bg-slate-100 text-slate-500"><i class="fa-solid fa-circle text-[8px]"></i> Off</span>';

        $cards = [
            ['label' => 'General Settings', 'icon' => 'fa-sliders', 'url' => route('admin.settings.site.edit'), 'desc' => 'Site name, logos, favicon, contact details and social links.', 'badge' => null],
            ['label' => 'Email Settings', 'icon' => 'fa-bell', 'url' => route('admin.settings.email.edit'), 'desc' => 'Where contact and product enquiry notifications are sent.', 'badge' => null],
            ['label' => 'SMTP Settings', 'icon' => 'fa-server', 'url' => route('admin.settings.smtp.edit'), 'desc' => 'Outgoing mail server host, port, credentials and encryption.', 'badge' => $smtp->is_active],
            ['label' => 'Brevo', 'icon' => 'fa-paper-plane', 'url' => route('admin.settings.brevo.edit'), 'desc' => 'API-based email delivery through Brevo (takes priority over SMTP).', 'badge' => $brevo->is_active],
            ['label' => 'Google reCAPTCHA', 'icon' => 'fa-shield-halved', 'url' => route('admin.settings.recaptcha.edit'), 'desc' => 'Spam protection for the contact, enquiry and login forms.', 'badge' => $recaptcha->is_active],
        ];
    @endphp

    <div class="space-y-6">
        <p class="text-sm text-slate-500">Manage site configuration and third-party integrations. Credentials are stored encrypted and loaded from the database — no <code class="rounded bg-slate-100 px-1">.env</code> editing required.</p>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($cards as $card)
                <a href="{{ $card['url'] }}" class="admin-card group flex flex-col gap-3 p-5 transition hover:border-[#0b376d] hover:shadow">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded bg-sky-50 text-[#0b376d]">
                            <i class="fa-solid {{ $card['icon'] }}"></i>
                        </span>
                        @if (! is_null($card['badge']))
                            {!! $status($card['badge']) !!}
                        @endif
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 group-hover:text-[#0b376d]">{{ $card['label'] }}</h2>
                        <p class="mt-1 text-xs leading-5 text-slate-500">{{ $card['desc'] }}</p>
                    </div>
                    <span class="mt-auto text-xs font-semibold text-[#0b376d]">Manage <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i></span>
                </a>
            @endforeach

            
        </div>
    </div>
@endsection
