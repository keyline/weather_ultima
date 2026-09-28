<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include ('layouts.head')

<body class="wx-preloading">
    <div id="wxPreloader" class="wx-preloader" aria-hidden="true">
        <div class="wx-preloader-inner">
            <img
                src="{{ $siteSettings->header_logo_url }}"
                alt="{{ $siteSettings->display_name }}"
                class="wx-preloader-logo"
            />
            <span class="wx-preloader-ring"></span>
        </div>
    </div>
    <script>
        // Runs immediately, independent of jQuery/main.js/WOW — so the
        // preloader still clears itself even if one of those fails to load.
        // Waits for the real "load" event (every asset, not just the DOM) and
        // enforces a small minimum-visible time so it never looks like a
        // flicker on a fast connection.
        (function () {
            var preloader = document.getElementById('wxPreloader');
            if (!preloader) {
                return;
            }
            var minVisibleUntil = Date.now() + 400;
            function hidePreloader() {
                var wait = Math.max(0, minVisibleUntil - Date.now());
                setTimeout(function () {
                    preloader.classList.add('is-hidden');
                    document.body.classList.remove('wx-preloading');
                    setTimeout(function () {
                        preloader.remove();
                    }, 600);
                }, wait);
            }
            if (document.readyState === 'complete') {
                hidePreloader();
            } else {
                window.addEventListener('load', hidePreloader);
            }
        })();
    </script>

    @include ('layouts.header')

    <main>
        @yield ('content')
    </main>

    @include ('layouts.footer')
    @include ('partials.whatsapp-button')

    <script src="{{ asset('material/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('material/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('material/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('material/js/main.js') }}?v={{ filemtime(public_path('material/js/main.js')) }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script>
        // WOW.js caches each element's CSS animation-name once, the moment it first
        // hides that element (during its own start()), then reuses that cached name
        // forever afterwards. If it runs before the Animate.css stylesheet has
        // finished loading, or before image-heavy sections (e.g. the product grid)
        // have settled their layout, the box gets cached with an empty animation
        // name and will snap straight to visible with no animation, permanently —
        // it's a WOW.js quirk, not something a rebuild step fixes. Waiting for the
        // window "load" event (fires only once the stylesheet AND every image has
        // finished loading) avoids that race.
        window.addEventListener('load', function () {
            if (typeof WOW !== 'undefined') {
                new WOW({
                    offset: 50,
                    mobile: true,
                    live: true
                }).init();
            }
        });
    </script>

    @stack ('scripts')
</body>
</html>
