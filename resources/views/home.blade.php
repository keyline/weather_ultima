@extends ('layouts.app')
@section ('title', 'Weather Ultima | Science. Service. Sustainability.')
@section ('content')
    <!-- TOP SCENE: animated weather backdrop behind the dimension cards -->
    <div class="wx-top-scene" id="wxHero">
        <div class="wx-scene" aria-hidden="true">
            <div class="wx-sun"></div>
            <img
                src="images/cloud.png"
                class="wx-cloud-photo cloud-photo-a"
                alt=""
            />
            <img
                src="images/cloud.png"
                class="wx-cloud-photo cloud-photo-b"
                alt=""
            />
            <img
                src="images/cloud.png"
                class="wx-cloud-photo cloud-photo-c"
                alt=""
            />
            <img
                src="images/cloud2.png"
                class="wx-cloud-photo cloud-photo-d"
                alt=""
            />
            <svg class="wx-cloud cloud-a" viewBox="0 0 95 61"><use href="#wx-cloud-shape"></use></svg>
            <svg class="wx-cloud cloud-b" viewBox="0 0 95 61"><use href="#wx-cloud-shape"></use></svg>
            <svg class="wx-cloud cloud-c" viewBox="0 0 95 61"><use href="#wx-cloud-shape"></use></svg>
            <svg class="wx-cloud cloud-d" viewBox="0 0 95 61"><use href="#wx-cloud-shape"></use></svg>
            <div class="wx-rain" id="wxRain"></div>
            <div class="wx-lightning"></div>
            <div class="wx-weather-overlay"></div>
        </div>

        <!-- 5 DIMENSION CARDS -->
        <section class="services-section py-5" id="services">
            <div class="container">
                <div class="wx-dim-head reveal" data-reveal>
                    <h2>{{ $home->banner_title ?: 'One Vision. Five Dimensions.' }}</h2>
                    <p>{{ $home->banner_subtitle ?: 'Explore our specialised solutions across weather intelligence, water, environmental monitoring, education and sustainable energy.' }}</p>
                </div>

                @php
                    $fallbackCardImage = asset('material/images/service1.png');
                    $cardRows = [$dimensionCards->take(3), $dimensionCards->slice(3)];
                    $cardNumber = 0;
                @endphp
                @foreach ($cardRows as $rowIndex => $rowCards)
                    @continue ($rowCards->isEmpty())
                    <div class="row row-cols-1 {{ $rowIndex === 0 ? 'row-cols-sm-2 row-cols-lg-3' : 'row-cols-lg-2 mt-1' }} g-4">
                        @foreach ($rowCards as $card)
                            @php $cardNumber++; @endphp
                            <div class="col">
                                <a
                                    href="{{ $card->link_url ?: '#' }}"
                                    class="dim-card reveal"
                                    data-reveal
                                    @if ($cardNumber > 1) data-reveal-delay="{{ $cardNumber - 1 }}" @endif
                                    style="--dim-bg: url('{{ $card->image_url ?? $fallbackCardImage }}');"
                                >
                                    <span class="dim-card-num">{{ $cardNumber }}</span>
                                    <h3 class="dim-card-title">{{ $card->title }}</h3>
                                    <p class="dim-card-desc">{{ $card->description }}</p>
                                    <span class="dim-card-arrow"
                                        ><i class="fa-solid fa-arrow-right"></i
                                    ></span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </section>
    </div>
    <!-- /wx-top-scene -->

    
    <!-- ABOUT THE FOUNDER -->
    <section class="wx-founder-section" id="founder">
        <div class="container">
            @php
                $founderName = $home->founder_name ?: 'Mr. Rabindra Goenka';
                $founderRole = $home->founder_designation ?: '~ Founder & CEO';
                $founderIntro = $home->founder_intro ?: 'Mr. Rabindra Goenka, Founder & CEO of Weather Ultima, brings together a deep passion for meteorology with a commitment to making weather knowledge more accessible and meaningful.';
                $founderParagraphs = $home->founder_paragraphs ?: [
                    'As a weather forecaster, analyst and interpreter, his journey has been driven by a simple belief — understanding weather is not just about predicting what comes next, but about helping people prepare, adapt and make informed decisions.',
                    'With his experience in weather analysis and teaching Geography, Mr. Goenka continues to champion a more informed approach to understanding our atmosphere, climate and the forces that shape our everyday lives.',
                ];
            @endphp
            <div class="wx-founder-card reveal" data-reveal>
                <div class="wx-founder-photo">
                    <img src="{{ $home->founder_image_url ?? asset('material/images/owner_img.png') }}" alt="{{ $founderName }}" />
                    <p class="wx-founder-name">{{ $founderName }}</p>
                    <p class="wx-founder-role">{{ $founderRole }}</p>
                </div>
                <div class="wx-founder-divider" aria-hidden="true"></div>
                <div class="wx-founder-content">
                    <span class="wx-founder-eyebrow">About the Founder</span>
                    <p>{{ $founderIntro }}</p>
                    @foreach ($founderParagraphs as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                    @if ($home->founder_signature_url)
                        <img src="{{ $home->founder_signature_url }}" alt="{{ $founderName }} signature" class="wx-founder-signature" />
                    @endif
                    <a href="{{ route('contact') }}" class="wx-founder-btn"
                        >Read More <i class="fa-solid fa-arrow-right"></i
                    ></a>
                </div>
            </div>
        </div>
    </section>

    <!-- WEATHER STATION -->
    <section class="wx-station-section" id="station">
        <div class="container">
            <div class="wx-station-card reveal" data-reveal>
                <div class="wx-section-head">
                    <h2>Our Weather Station</h2>
                </div>

                <div class="wx-station-tabs" role="tablist">
                    <button
                        type="button"
                        class="wx-station-tab is-active"
                        role="tab"
                        aria-selected="true"
                        data-station="Kolkata"
                    >
                        Kolkata
                    </button>
                    <button
                        type="button"
                        class="wx-station-tab"
                        role="tab"
                        aria-selected="false"
                        data-station="Deoghar"
                    >
                        Deoghar
                    </button>
                    <button
                        type="button"
                        class="wx-station-tab"
                        role="tab"
                        aria-selected="false"
                        data-station="Sundarban"
                    >
                        Sundarban
                    </button>
                    <button
                        type="button"
                        class="wx-station-tab"
                        role="tab"
                        aria-selected="false"
                        data-station="Bardhaman"
                    >
                        Bardhaman
                    </button>
                </div>

                <div id="wxStationBody">
                    @foreach ($weatherStations as $stationName => $stationReading)
                        <div
                            class="wx-station-body"
                            data-station-panel="{{ $stationName }}"
                            @unless ($loop->first) hidden @endunless
                        >
                            @include ('partials.weather-station-panel', [
                                'station' => $stationName,
                                'reading' => $stationReading,
                                'imageUrl' => $weatherStationImages[$stationName] ?? null,
                            ])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- STATS BANNER -->
    <section class="wx-stats-section" id="stats">
        <div class="container">
            <div class="wx-stats-banner reveal" data-reveal>
                <div class="wx-stat">
                    <p class="wx-stat-num"><span class="count-number" data-count="10">0</span>K+</p>
                    <p class="wx-stat-label">Weather-Smart Clients</p>
                </div>
                <div class="wx-stat">
                    <p class="wx-stat-num"><span class="count-number" data-count="14">0</span>+</p>
                    <p class="wx-stat-label">Milestones &amp; Recognitions</p>
                </div>
                <div class="wx-stat">
                    <p class="wx-stat-num"><span class="count-number" data-count="5">0</span>+</p>
                    <p class="wx-stat-label">Stations Reading the Sky 24/7</p>
                </div>
                <div class="wx-stat">
                    <p class="wx-stat-num"><span class="count-number" data-count="20">0</span>+</p>
                    <p class="wx-stat-label">Years of Meteorological Innovation</p>
                </div>
            </div>
        </div>
    </section>

    <!-- MEDIA MENTIONS -->
    <section class="wx-media-section" id="media">
        <img
            src="images/logo-white.svg"
            class="wx-media-logo-cloud wx-media-logo-cloud--top"
            alt=""
            aria-hidden="true"
        />
        <img
            src="images/logo-white.svg"
            class="wx-media-logo-cloud wx-media-logo-cloud--bottom"
            alt=""
            aria-hidden="true"
        />
        <div class="container">
            <div class="wx-section-head reveal" data-reveal>
                <h2>When the Weather Makes News, They Call the Experts</h2>
                <p>Expert insights, panel discussions and conversations across leading media platforms.</p>
            </div>
            <div class="wx-media-grid reveal" data-reveal data-reveal-delay="1">
                @forelse ($brandLogos as $logo)
                    <div class="wx-media-logo">
                        <img src="{{ $logo->image_url }}" alt="{{ $logo->alt_text }}" />
                    </div>
                @empty
                    <div class="wx-media-logo">
                        <img src="images/brand-logo1.png" alt="All India Radio" />
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- CORE VALUES -->
    @if ($coreValues->isNotEmpty())
        <section class="wx-values-section" id="values">
            <div class="container">
                <div class="wx-section-head reveal" data-reveal>
                    <h2>Core Values</h2>
                    <p>Our RAINBOW Has More Than Colours.<br />It Has Values.</p>
                </div>
                <div class="wx-values-list">
                    @foreach ($coreValues as $value)
                        <div class="wx-value-row">
                            <span class="wx-value-letter">{{ $value->icon }}</span>
                            <div class="wx-value-body">
                                <h3>{{ $value->title }}</h3>
                                <p>{{ $value->description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- INSTAGRAM GRID -->
    @if ($instagramEmbedCode || $instagramPosts->isNotEmpty())
        <section class="wx-insta-section" id="instagram">
            <div class="container">
                <div class="wx-section-head reveal" data-reveal>
                    <h2>Follow Us on Instagram</h2>
                    @if ($siteSettings->social_instagram)
                        <p><a href="{{ $siteSettings->social_instagram }}" target="_blank" rel="noopener">@ Weather Ultima</a></p>
                    @endif
                </div>

                @if ($instagramEmbedCode)
                    {{-- Admin-only field (see .ai/rules/partials.md) — intentionally unescaped, it's a widget <script>/<div> snippet. --}}
                    <div class="wx-insta-embed reveal" data-reveal data-reveal-delay="1">{!! $instagramEmbedCode !!}</div>
                @else
                    <div class="wx-insta-carousel owl-carousel reveal" data-reveal data-reveal-delay="1">
                        @foreach ($instagramPosts as $post)
                            <a
                                href="{{ $post->link_url ?: ($siteSettings->social_instagram ?: '#') }}"
                                target="_blank"
                                rel="noopener"
                                class="wx-insta-item"
                            >
                                <img src="{{ $post->image_url }}" alt="Instagram post" loading="lazy" />
                                <span class="wx-insta-overlay"><i class="fa-brands fa-instagram"></i></span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- TESTIMONIALS -->
    @if ($testimonials->isNotEmpty())
        <section class="wx-testimonial-section" id="testimonials">
            <div class="container">
                <div class="wx-section-head reveal" data-reveal>
                    <h2>What Our Clients Say</h2>
                </div>
                <div class="wx-testimonial-wrap reveal" data-reveal data-reveal-delay="1">
                    <div class="wx-testimonial-carousel owl-carousel">
                        @foreach ($testimonials as $testimonial)
                            <div class="wx-testimonial-item">
                                <div class="wx-testimonial-bubble">
                                    <div class="wx-testimonial-stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa-{{ $i <= $testimonial->rating ? 'solid' : 'regular' }} fa-star"></i>
                                        @endfor
                                    </div>
                                    <p>&ldquo;{{ $testimonial->review }}&rdquo;</p>
                                </div>
                                <div class="wx-testimonial-author">
                                    <p class="wx-testimonial-name">{{ $testimonial->name }}</p>
                                    @if ($testimonial->role_line)
                                        <p class="wx-testimonial-role">{{ $testimonial->role_line }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection

@push ('styles')
    <style>
        .wx-founder-signature {
            display: block;
            max-height: 52px;
            width: auto;
            margin: 4px 0 18px;
        }

        .wx-insta-section {
            padding: 60px 0;
        }

        .wx-insta-carousel .owl-stage-outer {
            padding: 4px 2px 10px;
        }

        .wx-insta-item {
            position: relative;
            display: block;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            border-radius: 10px;
        }

        .wx-insta-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .wx-insta-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            background: rgba(11, 55, 109, 0.55);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .wx-insta-item:hover img {
            transform: scale(1.08);
        }

        .wx-insta-item:hover .wx-insta-overlay {
            opacity: 1;
        }
    </style>
@endpush
