<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include ('layouts.head')

<body>
    @include ('layouts.header')

    <main>
        @yield ('content')
    </main>

    @include ('layouts.footer')
    @include ('partials.whatsapp-button')

    <script src="{{ asset('material/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('material/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('material/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('material/js/main.js') }}"></script>
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
