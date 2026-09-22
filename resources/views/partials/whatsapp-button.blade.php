{{-- Included directly from layouts.app (not a child view), so styles are inlined here
     rather than @push('styles') — the <head> @stack has already rendered by this point. --}}
@if ($siteSettings->whatsapp_link)
    <a
        href="{{ $siteSettings->whatsapp_link }}"
        target="_blank"
        rel="noopener"
        class="wx-whatsapp-btn"
        aria-label="Chat with us on WhatsApp"
    >
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <style>
        .wx-whatsapp-btn {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #25d366;
            color: #fff;
            font-size: 30px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
            transition: transform 0.2s ease;
        }

        .wx-whatsapp-btn:hover,
        .wx-whatsapp-btn:focus {
            color: #fff;
            transform: scale(1.08);
        }

        @media (max-width: 575px) {
            .wx-whatsapp-btn {
                right: 14px;
                bottom: 14px;
                width: 50px;
                height: 50px;
                font-size: 26px;
            }
        }
    </style>
@endif
