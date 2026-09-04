<header class="wx-header-fixed fixed-top">
    <div class="wx-top-bar" aria-hidden="true"></div>

    <nav class="navbar navbar-expand-lg navbar-light wx-navbar">
        <div class="container">
            <a class="navbar-brand wx-brand" href="{{ route('home') }}">
                <img
                    src="{{ $siteSettings->header_logo_url }}"
                    alt="Weather Ultima"
                    class="wx-brand-logo"
                />
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-label="Open navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div
                class="offcanvas offcanvas-end wx-mobile-menu"
                tabindex="-1"
                id="navbarNav"
            >
                <div class="offcanvas-header">
                    <img
                        src="{{ $siteSettings->header_logo_url }}"
                        alt="Weather Ultima"
                        class="wx-offcanvas-logo"
                    />
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="offcanvas"
                        aria-label="Close navigation"
                    ></button>
                </div>

                <div class="offcanvas-body">
                    <ul class="navbar-nav ms-lg-auto">
                        @foreach (['home' => 'Home', 'products' => 'Products', 'services' => 'Services', 'contact' => 'Contact'] as $route => $label)
                            <li
                                class="nav-item {{ request()->routeIs($route) ? 'active' : '' }}"
                            >
                                <a class="nav-link" href="{{ route($route) }}">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="wx-live-wrap">
                        <button
                            type="button"
                            class="wx-live-weather"
                            id="wxLiveWeather"
                            aria-expanded="false"
                            aria-controls="wxLivePopover"
                        >
                            <span class="wx-live-dot"></span>
                            <span class="wx-live-text">Detecting your local weather…</span>
                        </button>

                        <div class="wx-live-popover" id="wxLivePopover" hidden>
                            <div class="wx-live-popover-head">
                                <div>
                                    <p class="wx-live-popover-title">Weather near you</p>
                                    <p class="wx-live-popover-place" id="wxPopoverPlace">—</p>
                                </div>
                                <p class="wx-live-popover-range" id="wxPopoverRange">—</p>
                            </div>

                            <div class="wx-live-popover-stats">
                                <div>
                                    <span id="wxPopoverFeels">—</span>
                                    <small>Feels like</small>
                                </div>
                                <div>
                                    <span id="wxPopoverHumidity">—</span>
                                    <small>Humidity</small>
                                </div>
                                <div>
                                    <span id="wxPopoverWind">—</span>
                                    <small>Wind</small>
                                </div>
                            </div>

                            <p class="wx-live-popover-tomorrow" id="wxPopoverTomorrow">—</p>
                            <p class="wx-live-popover-disclaimer">General information only; exact place and time may differ. For safety decisions, check official weather authorities.</p>
                            <p class="wx-live-popover-credit">Powered by <a href="https://www.weatherapi.com/" target="_blank" rel="noopener">WeatherAPI.com</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
