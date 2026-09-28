<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <base href="{{ asset('material') }}/" />

    <title>
        @yield ('title', $siteSettings->display_name)
    </title>

    <link rel="icon" href="{{ $siteSettings->favicon_url }}" />
    <link
        rel="stylesheet"
        href="{{ asset('material/css/bootstrap.min.css') }}"
    />
    <link rel="stylesheet" href="{{ asset('material/css/all.min.css') }}" />
    <link
        rel="stylesheet"
        href="{{ asset('material/css/owl.carousel.min.css') }}"
    />
    <link
        rel="stylesheet"
        href="{{ asset('material/css/owl.theme.default.min.css') }}"
    />
    <link rel="stylesheet" href="{{ asset('material/css/menu.css') }}?v={{ filemtime(public_path('material/css/menu.css')) }}" />
    {{-- Versioned with filemtime() (not asset()'s usual bare path) so a browser that already
         cached this file from an earlier visit re-fetches it the moment it changes on disk,
         instead of silently reusing a stale copy indefinitely. --}}
    <link rel="stylesheet" href="{{ asset('material/css/style.css') }}?v={{ filemtime(public_path('material/css/style.css')) }}" />
    <link rel="stylesheet" href="{{ asset('material/css/responsive.css') }}?v={{ filemtime(public_path('material/css/responsive.css')) }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css" />

    @stack ('styles')
</head>
