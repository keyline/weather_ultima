@extends ('layouts.app')
@section ('title', $about['banner_title'] . ' | ' . $siteSettings->display_name)

@section ('content')
    <header class="wx-page-banner wx-page-banner--left">
        <img src="{{ asset('material/images/cloud.png') }}" class="wx-banner-cloud wx-banner-cloud--top" alt="" aria-hidden="true" />
        <img src="{{ asset('material/images/cloud3.png') }}" class="wx-banner-cloud wx-banner-cloud--seam" alt="" aria-hidden="true" />
        <div class="container"><h1>{{ $about['banner_title'] }}</h1></div>
    </header>

    <div class="wx-about-page">
        <section class="wx-about-section" aria-labelledby="about-intro-title">
            <div class="container">
                <div class="row g-4 g-lg-5 align-items-center">
                    <div class="col-lg-7">
                        <span class="wx-about-eyebrow">About Weather Ultima</span>
                        <h2 id="about-intro-title">{{ $about['intro']['title'] }}</h2>
                        @foreach ($about['intro']['paragraphs'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                    <div class="col-lg-5">
                        <img class="wx-about-intro-image" src="{{ asset($about['intro']['image']) }}" alt="Weather observation and environmental intelligence" width="640" height="640" loading="lazy" />
                    </div>
                </div>
            </div>
        </section>

        @foreach (['mission', 'vision'] as $section)
            <section class="wx-about-section {{ $section === 'mission' ? 'wx-about-section--muted' : '' }}" aria-labelledby="about-{{ $section }}-title">
                <div class="container">
                    <div class="wx-section-head">
                        <span class="wx-about-eyebrow">{{ $about[$section]['label'] }}</span>
                        <h2 id="about-{{ $section }}-title">{{ $about[$section]['title'] }}</h2>
                    </div>
                    <div class="wx-about-carousel wx-testimonial-carousel owl-carousel" role="region" aria-label="{{ $about[$section]['label'] }} carousel">
                        @foreach ($about[$section]['cards'] as $card)
                            <article class="wx-about-card">
                                <img src="{{ asset($card['image']) }}" alt="{{ $card['title'] }}" width="640" height="400" loading="lazy" />
                                <div class="wx-about-card-body">
                                    <span class="wx-about-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <h3>{{ $card['title'] }}</h3>
                                    @foreach ($card['paragraphs'] as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endforeach

        <section class="wx-about-section wx-about-section--muted" aria-labelledby="about-founder-title">
            <div class="container">
                <div class="row g-4 g-lg-5">
                    <div class="col-lg-4">
                        <figure class="wx-about-founder-image">
                            <img src="{{ asset($about['founder']['image']) }}" alt="{{ $about['founder']['name'] }}" width="640" height="720" loading="lazy" />
                            <figcaption>{{ $about['founder']['name'] }}</figcaption>
                        </figure>
                    </div>
                    <div class="col-lg-8">
                        <span class="wx-about-eyebrow">{{ $about['founder']['label'] }}</span>
                        <h2 id="about-founder-title">{{ $about['founder']['title'] }}</h2>
                        @foreach ($about['founder']['paragraphs'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                        <p class="wx-about-signature">{{ $about['founder']['signature'] }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="wx-about-section" aria-labelledby="about-team-title">
            <div class="container">
                <div class="wx-section-head"><h2 id="about-team-title">Our Team</h2></div>
                <div class="row g-4 justify-content-center">
                    @foreach ($about['team'] as $member)
                        <div class="col-md-6 col-lg-4">
                            <article class="wx-about-card h-100">
                                <img class="wx-about-portrait" src="{{ asset($member['image']) }}" alt="Photo placeholder for {{ $member['name'] }}" width="640" height="720" loading="lazy" />
                                <div class="wx-about-card-body">
                                    <h3>{{ $member['name'] }}</h3>
                                    <span class="wx-about-role">{{ $member['role'] }}@if ($member['organisation']) · {{ $member['organisation'] }}@endif</span>
                                    @foreach ($member['paragraphs'] as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="wx-about-section wx-about-section--muted" aria-labelledby="about-northstar-title">
            <div class="container">
                <div class="wx-section-head"><h2 id="about-northstar-title">Northstar</h2></div>
                <div class="row g-4">
                    @foreach ($about['northstar'] as $person)
                        <div class="col-6 col-md-4 col-lg-3">
                            <figure class="wx-about-northstar">
                                <img src="{{ asset($person['image']) }}" alt="Northstar portrait placeholder" width="640" height="720" loading="lazy" />
                                <figcaption>{{ $person['name'] }}</figcaption>
                            </figure>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection

@push ('styles')
    <link rel="stylesheet" href="{{ asset('material/css/about.css') }}?v={{ filemtime(public_path('material/css/about.css')) }}" />
@endpush

@push ('scripts')
    <script>
        $(function () {
            $('.wx-about-carousel').owlCarousel({
                loop: false,
                margin: 20,
                nav: true,
                dots: false,
                autoplay: false,
                smartSpeed: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 500,
                navText: ['<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>', '<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>'],
                responsive: { 0: { items: 1 }, 768: { items: 2 }, 1200: { items: 3 } }
            });
            $('.wx-about-carousel .owl-prev').attr('aria-label', 'Previous slide');
            $('.wx-about-carousel .owl-next').attr('aria-label', 'Next slide');
        });
    </script>
@endpush
