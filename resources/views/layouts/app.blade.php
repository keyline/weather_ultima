<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include ('layouts.head')

<body>
    @include ('layouts.header')

    <main>
        @yield ('content')
    </main>

    @include ('layouts.footer')

    <script src="{{ asset('material/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('material/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('material/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('material/js/main.js') }}"></script>

    @stack ('scripts')
</body>
</html>
