@extends ('layouts.app')
@section ('title', $about['banner_title'] . ' | ' . $siteSettings->display_name)

@section ('content')
    @php
        $aboutImageUrl = static fn (?string $image, string $fallback = ''): string => asset($image
            ? (str_starts_with($image, 'about/') ? 'storage/'.$image : $image)
            : $fallback);
    @endphp

    <header class="wx-page-banner wx-page-banner--left">
        @if ($about['banner']['image'] ?? false)
            <img src="{{ $aboutImageUrl($about['banner']['image']) }}" class="wx-about-banner-image" alt="{{ $about['banner']['image_alt'] ?? '' }}" />
        @endif
        <img src="{{ asset('material/images/cloud.png') }}" class="wx-banner-cloud wx-banner-cloud--top" alt="" aria-hidden="true" />
        <img src="{{ asset('material/images/cloud3.png') }}" class="wx-banner-cloud wx-banner-cloud--seam" alt="" aria-hidden="true" />
        <div class="container"><h1>{{ $about['banner_title'] }}</h1></div>
    </header>

    <div class="wx-about-page">
        <section class="wx-about-section" aria-labelledby="about-intro-title">
            <div class="container">
                
                <div class="row g-4 g-lg-5 align-items-center">
                    <div class="col-lg-7">
                        <div class="wx-section-head inner_sidetextalign">
                            <h2 id="about-intro-title">{{ $about['intro']['label'] }}</h2>
                            <p>{{ $about['intro']['title'] }}</p>
                        </div>
                        @foreach ($about['intro']['paragraphs'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                    <div class="col-lg-5">
                        <img class="wx-about-intro-image" src="{{ $aboutImageUrl($about['intro']['image'] ?? null, 'material/images/service1.png') }}" alt="Weather observation and environmental intelligence" width="640" height="640" loading="lazy" />
                    </div>
                </div>
            </div>
        </section>

        @foreach (['mission', 'vision'] as $section)
            <section class="wx-about-section {{ $section === 'mission' ? 'wx-about-section--muted' : '' }}" aria-labelledby="about-{{ $section }}-title">
                <div class="container">
                    <div class="wx-section-head">
                        <h2 id="about-{{ $section }}-title">{{ $about[$section]['label'] }}</h2>
                        <p>{{ $about[$section]['title'] }}</p>
                    </div>
                    <div class="wx-about-carousel wx-testimonial-carousel owl-carousel" role="region" aria-label="{{ $about[$section]['label'] }} carousel">
                        @foreach ($about[$section]['cards'] as $card)
                            <article class="wx-about-card">
                                <img src="{{ $aboutImageUrl($card['image'] ?? null, 'material/images/service1.png') }}" alt="{{ $card['title'] }}" width="640" height="400" loading="lazy" />
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
                            <img src="{{ $aboutImageUrl($about['founder']['image'] ?? null, 'material/images/owner_img.png') }}" alt="{{ $about['founder']['name'] }}" width="640" height="720" loading="lazy" />
                            <figcaption>{{ $about['founder']['name'] }}</figcaption>
                        </figure>
                    </div>
                    <div class="col-lg-8">
                        <div class="wx-section-head inner_sidetextalign">
                            <h2 id="about-founder-title">{{ $about['founder']['label'] }}</h2>
                            <p>{{ $about['founder']['title'] }}</p>
                        </div>
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
                <div class="wx-section-head"><h2 id="about-team-title">{{ $about['team_title'] }}</h2>
                    
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach ($about['team'] as $member)
                        <div class="col-md-6 col-lg-4">
                            <article class="wx-about-card h-100">
                                <img class="wx-about-portrait" src="{{ $aboutImageUrl($member['image'] ?? null, 'material/images/person-placeholder.svg') }}" alt="Photo of {{ $member['name'] }}" width="640" height="720" loading="lazy" />
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
                <div class="wx-section-head"><h2 id="about-northstar-title">{{ $about['northstar_title'] }}</h2>
                <p>THE PEOPLE WHO ALWAYS STAND BY US</p>
            </div>
                <div class="row g-4">
                    @foreach ($about['northstar'] as $person)
                        <div class="col-6 col-md-4 col-lg-3">
                            <figure class="wx-about-northstar">
                                <img src="{{ $aboutImageUrl($person['image'] ?? null, 'material/images/person-placeholder.svg') }}" alt="Photo of {{ $person['name'] }}" width="640" height="720" loading="lazy" />
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
    <link rel="stylesheet" href="{{ asset('material/css/about.css') }}?v={{ is_file(public_path('material/css/about.css')) ? filemtime(public_path('material/css/about.css')) : '1' }}" />
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
