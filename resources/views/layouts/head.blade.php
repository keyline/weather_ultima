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
    <link rel="stylesheet" href="{{ asset('material/css/menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('material/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('material/css/responsive.css') }}" />

    @stack ('styles')
</head>
