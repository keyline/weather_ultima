@props(['action' => 'submit'])

@php $recaptcha = \App\Models\RecaptchaSetting::current(); @endphp

@if ($recaptcha->isEnforced())
    @error('g-recaptcha-response')
        <p class="wx-recaptcha-error text-danger" style="margin-bottom:.5rem;color:#dc2626;font-size:.875rem">{{ $message }}</p>
    @enderror

    @if ($recaptcha->isV3())
        <input type="hidden" name="g-recaptcha-response" value="" data-recaptcha-token />

        @once
            @push('scripts')
                <script src="{{ $recaptcha->scriptUrl() }}"></script>
                <script>
                    (() => {
                        const siteKey = @json($recaptcha->site_key);
                        const action = @json($action);
                        const fill = (token) => document
                            .querySelectorAll('input[data-recaptcha-token]')
                            .forEach((input) => { input.value = token; });
                        const refresh = () => {
                            if (!window.grecaptcha || !window.grecaptcha.execute) return;
                            window.grecaptcha.execute(siteKey, { action }).then(fill).catch(() => {});
                        };
                        const start = () => window.grecaptcha
                            ? window.grecaptcha.ready(refresh)
                            : window.addEventListener('load', start, { once: true });
                        start();
                        window.setInterval(refresh, 100000);
                        window.wxRefreshRecaptcha = refresh;
                    })();
                </script>
            @endpush
        @endonce
    @else
        <div class="g-recaptcha" data-sitekey="{{ $recaptcha->site_key }}" data-recaptcha-widget style="margin-bottom:1rem"></div>

        @once
            @push('scripts')
                <script src="{{ $recaptcha->scriptUrl() }}" async defer></script>
            @endpush
        @endonce
    @endif
@endif
