@extends ('layouts.app')
@section ('title', ($page->banner_title ?: 'Services') . ' | ' . $siteSettings->display_name)
@section ('content')
    @php
        $introHeading = $page->intro_heading ?: "Science Above Us. Technology Around Us.\nSolutions For What Comes Next.";
        $introParagraphs = $page->intro_paragraphs ?: [
            'Weather affects far more than what we wear or whether we carry an umbrella. It influences the food we grow, the games we play, the journeys we take, the energy we generate, the water we manage and the environment we leave behind.',
            'At Weather Ultima, we connect meteorological science with technology and real-world needs.',
            'From forecasting and weather stations to solar energy, meteorological consulting, education, environmental solutions and water management, every Weather Ultima service has one thing in common:',
        ];
        $introStatement = $page->intro_statement ?: 'We measure. We understand. We help you act.';
    @endphp

    <!-- PAGE BANNER -->
    <header class="wx-page-banner wx-page-banner--left">
        <img src="images/cloud.png" class="wx-banner-cloud wx-banner-cloud--top" alt="" aria-hidden="true" />
        <img src="images/cloud3.png" class="wx-banner-cloud wx-banner-cloud--seam" alt="" aria-hidden="true" />
        <div class="container">
            <h1>{{ $page->banner_title ?: 'Services' }}</h1>
        </div>
    </header>

    <!-- SERVICES INTRO -->
    <section class="wx-services-intro-section">
        <img src="images/cloud2.png" class="wx-intro-cloud wx-intro-cloud--left" alt="" aria-hidden="true" />
        <img src="images/cloud.png" class="wx-intro-cloud wx-intro-cloud--right" alt="" aria-hidden="true" />
        <div class="container">
            <div class="wx-services-intro reveal" data-reveal>
                <h2>{!! nl2br(e($introHeading)) !!}</h2>
                @foreach ($introParagraphs as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
                <p class="wx-services-intro-statement">{{ $introStatement }}</p>
            </div>
        </div>
    </section>

    @if ($services->isNotEmpty())
        <!-- SERVICES DETAIL TABS -->
        <section class="wx-service-detail-section">
            <div class="container">
                <div class="wx-service-detail-grid">
                    <div class="wx-service-tab-nav" role="tablist" aria-label="Services">
                        @foreach ($services as $service)
                            <button
                                type="button"
                                @class(['wx-service-tab', 'is-active' => $loop->first])
                                data-service-target="{{ $service->slug }}"
                            >
                                {{ $service->name }}
                            </button>
                        @endforeach
                    </div>

                    <div class="wx-service-panels">
                        @foreach ($services as $service)
                            <article @class(["wx-service-panel", "is-active" => $loop->first]) id="service-{{ $service->slug }}">
                                <h2>{{ $service->name }}</h2>
                                @if ($service->category || $service->tags)
                                    <p class="wx-service-panel-tagline">{{ $service->category }}@if ($service->category && $service->tags)<br>@endif{{ $service->tags }}</p>
                                @endif
                                @if ($service->images->isNotEmpty())
                                    <div class="wx-service-panel-media">
                                        @foreach ($service->images as $image)
                                            <span class="wx-service-media-tile"><img src="{{ $image->image_url }}" alt="{{ $image->alt_text ?: $service->name }}" /></span>
                                        @endforeach
                                    </div>
                                @endif
                                @if ($service->statement)
                                    <p class="wx-service-panel-statement">{!! nl2br(e($service->statement)) !!}</p>
                                @endif
                                @foreach ($service->body_paragraphs as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                                @if ($service->result)
                                    <p class="wx-service-panel-result">{{ $service->result }}</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>

                <!-- Mobile-only accordion: same panels, moved here by JS on small screens. -->
                <div class="wx-service-accordion">
                    @foreach ($services as $service)
                        <div class="wx-service-acc-item">
                            <button
                                type="button"
                                @class(['wx-service-acc-toggle', 'is-active' => $loop->first])
                                data-service-target="{{ $service->slug }}"
                            >
                                {{ $service->name }}
                                <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                            </button>
                            <div class="wx-service-acc-body" data-service-body="{{ $service->slug }}"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- QUOTES -->
    <section class="wx-quotes-section">
        <div class="wx-quotes-grid">
            <div class="wx-quote-card">
                <img src="images/qutaion_yellow.svg" alt="" class="wx-quote-mark" />
                <div class="wx-quote-body">
                    <p class="wx-quote-text">&ldquo;The weather teaches us something every day &ndash; conditions change, challenges arrive, and clear skies return. Business is no different. Understand the change, find the solution, and keep moving forward.&rdquo;</p>
                    <p class="wx-quote-author">- Rabindra Goenka</p>
                </div>
            </div>
            <div class="wx-quote-card">
                <img src="images/qutaion_yellow.svg" alt="" class="wx-quote-mark" />
                <div class="wx-quote-body">
                    <p class="wx-quote-text">When a butterfly flutters its wings in one part of the world, it can eventually cause a hurricane in another.</p>
                    <p class="wx-quote-author">-Edward Norton Lorenz</p>
                </div>
            </div>
            <div class="wx-quote-card">
                <img src="images/qutaion_yellow.svg" alt="" class="wx-quote-mark" />
                <div class="wx-quote-body">
                    <p class="wx-quote-text">Wherever you go, no matter what the weather, always bring your own sunshine.</p>
                    <p class="wx-quote-author">-Anthony J. D&rsquo;Angelo</p>
                </div>
            </div>
        </div>
    </section>
@endsection
