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
        if (typeof WOW !== 'undefined') {
            new WOW({
                offset: 50,
                mobile: true,
                live: true
            }).init();
        }
    </script>

    @stack ('scripts')
</body>
</html>
