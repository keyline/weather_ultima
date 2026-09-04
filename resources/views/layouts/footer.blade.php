<footer class="wx-footer-new">
    <div class="wx-footer-top">
        <div class="container">
            <div class="wx-footer-grid">
                <div class="wx-footer-col wx-footer-col--brand">
                    <img
                        src="{{ $siteSettings->footer_logo_url }}"
                        alt="Weather Ultima"
                        class="wx-footer-logo"
                    />

                    @if (count($siteSettings->social_links))
                        <div class="wx-footer-social">
                            @foreach ($siteSettings->social_links as $social)
                                <a
                                    href="{{ $social['url'] }}"
                                    target="_blank"
                                    rel="noopener"
                                    aria-label="{{ $social['label'] }}"
                                    ><i class="{{ $social['icon'] }}"></i
                                ></a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="wx-footer-col wx-footer-col--nav">
                    <a href="{{ route('home') }}" class="wx-footer-nav-link"
                        >Home</a
                    >
                    <a href="{{ route('products') }}" class="wx-footer-nav-link"
                        >Products</a
                    >
                    <a href="{{ route('services') }}" class="wx-footer-nav-link"
                        >Services</a
                    >
                    <a href="{{ route('contact') }}" class="wx-footer-nav-link"
                        >Contact</a
                    >
                </div>

                <div class="wx-footer-col wx-footer-col--services">
                    <h4>Services</h4>
                    <div class="wx-footer-services-grid">
                        <ul>
                            <li>SkyWatch Live</li>
                            <li>StationCraft</li>
                            <li>SolarSphere</li>
                        </ul>
                        <ul>
                            <li>WeatherWise Academy</li>
                            <li>WaterSphere</li>
                        </ul>
                    </div>
                </div>

                <div class="wx-footer-col wx-footer-col--contact">
                    <div class="wx-footer-contact-item">
                        <i class="fa-solid fa-envelope"></i>
                        <div>
                            <p class="wx-footer-contact-label">Support Email</p>
                            <p class="wx-footer-contact-value">
                                <a href="mailto:{{ $siteSettings->contact_email }}">{{ $siteSettings->contact_email }}</a>
                            </p>
                        </div>
                    </div>

                    <div class="wx-footer-contact-item">
                        <i class="fa-solid fa-phone"></i>
                        <div>
                            <p class="wx-footer-contact-label">Global Support Line</p>
                            <p class="wx-footer-contact-value">
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $siteSettings->contact_phone) }}">{{ $siteSettings->contact_phone }}</a>
                            </p>
                        </div>
                    </div>

                    @if ($siteSettings->contact_address)
                        <div class="wx-footer-contact-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <div>
                                <p class="wx-footer-contact-label">Head Office</p>
                                <p class="wx-footer-contact-value">{{ $siteSettings->contact_address }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="wx-footer-bottom">
        <div class="container wx-footer-bottom-inner">
            <p>© {{ now()->year }} Weather Ultima. All rights reserved.</p>
            <p>Designed &amp; Developed by <a href="https://keylines.net/" target="_blank" class="wx-footer-credit">KEYLINE</a></p>
        </div>
    </div>
</footer>
