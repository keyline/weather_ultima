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
                    @foreach ($footerMenuItems as $item)
                        <a href="{{ $item['url'] }}" class="wx-footer-nav-link">{{ $item['label'] }}</a>
                    @endforeach
                </div>

                <div class="wx-footer-col wx-footer-col--services">
                    <h4>Services</h4>
                    <div class="wx-footer-services-grid">
                        <ul>
                            <li><a href="https://weather-dev.keylines.in/services" target="_blank">Ultimate Weather</a></li>
                            <li><a href="https://weather-dev.keylines.in/services" target="_blank">Station Craft</a></li>
                            <li><a href="https://weather-dev.keylines.in/services" target="_blank">WeatherWise Academy</a></li>
                            <li><a href="https://weather-dev.keylines.in/services" target="_blank">SolarSphere</a></li>
                        </ul>
                        <ul>
                            <li><a href="https://weather-dev.keylines.in/services" target="_blank">WaterSphere</a></li>
                            <li><a href="https://weather-dev.keylines.in/services" target="_blank">MetEdge Consulting</a></li>
                            <li><a href="https://weather-dev.keylines.in/services" target="_blank">GreenHorizon</a></li>
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
                                <a href="tel:+919903371108">9903371108</a> / <a href="tel:+918910296427">8910296427</a><br /><span>Landline: <a href="tel:+913346377803">033-46377803</a></span>
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
