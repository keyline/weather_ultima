@extends ('layouts.app')

@section ('title', 'Page Not Found | ' . $siteSettings->display_name)

@push ('styles')
    <style>
        .wx-error-section {
            position: relative;
            padding: 72px 0 96px;
            text-align: center;
            background: #f6f9fe;
        }

        .wx-error-code {
            font-size: clamp(5.5rem, 20vw, 12rem);
            font-weight: 700;
            line-height: 0.9;
            letter-spacing: -2px;
            margin: 0;
            background: linear-gradient(180deg, #184a97 0%, #59bfe5 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .wx-error-title {
            font-size: clamp(1.4rem, 3vw, 2rem);
            font-weight: 500;
            color: #12233d;
            margin: 8px 0 12px;
        }

        .wx-error-text {
            max-width: 540px;
            margin: 0 auto 28px;
            color: #5b6b80;
            line-height: 1.7;
        }

        .wx-error-actions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 14px;
        }

        .wx-error-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px 28px;
            margin-top: 34px;
            padding-top: 22px;
            border-top: 1px solid #e2e9f4;
        }

        .wx-error-links a {
            color: #184a97;
            font-weight: 500;
            font-size: 0.95rem;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .wx-error-links a:hover {
            color: #59bfe5;
        }
    </style>
@endpush

@section ('content')
    <header class="wx-page-banner wx-page-banner--left">
        <img src="images/cloud.png" class="wx-banner-cloud wx-banner-cloud--top" alt="" aria-hidden="true" />
        <img src="images/cloud3.png" class="wx-banner-cloud wx-banner-cloud--seam" alt="" aria-hidden="true" />
        <div class="container">
            <h1>Page Not Found</h1>
        </div>
    </header>

    <section class="wx-error-section">
        <div class="container">
            <p class="wx-error-code">404</p>
            <h2 class="wx-error-title">We couldn&rsquo;t find that page</h2>
            <p class="wx-error-text">
                The page you&rsquo;re looking for may have been moved, renamed, or never existed.
                Let&rsquo;s get you back on track.
            </p>

            <div class="wx-error-actions">
                <a href="{{ route('home') }}" class="wx-founder-btn">
                    <i class="fa-solid fa-house" aria-hidden="true"></i> Back to Home
                </a>
            </div>

            <nav class="wx-error-links" aria-label="Popular pages">
                <a href="{{ route('products') }}">Products</a>
                <a href="{{ route('services') }}">Services</a>
                <a href="{{ route('contact') }}">Contact Us</a>
            </nav>
        </div>
    </section>
@endsection
