<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include ('layouts.head')

<body class="wx-preloading">
    <div id="wxPreloader" class="wx-preloader" aria-hidden="true">
        <div class="wx-preloader-bg-weather">
            <div class="wx-preloader-bg-cloud wx-preloader-bg-cloud-1"></div>
            <div class="wx-preloader-bg-cloud wx-preloader-bg-cloud-2"></div>
            <div class="wx-preloader-bg-cloud wx-preloader-bg-cloud-3"></div>
            <div class="wx-preloader-bg-cloud wx-preloader-bg-cloud-4"></div>
            <div class="wx-preloader-bg-cloud wx-preloader-bg-cloud-5"></div>

            <div class="wx-preloader-bg-rain">
                <i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>
                <i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>
                <i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>
                <i></i><i></i><i></i>
            </div>
        </div>

        <div class="wx-preloader-glow"></div>

        <div class="wx-preloader-particles">
            <span class="wx-preloader-particle"></span>
            <span class="wx-preloader-particle"></span>
            <span class="wx-preloader-particle"></span>
            <span class="wx-preloader-particle"></span>
            <span class="wx-preloader-particle"></span>
            <span class="wx-preloader-particle"></span>
            <span class="wx-preloader-particle"></span>
            <span class="wx-preloader-particle"></span>
        </div>

        <div class="wx-preloader-content">
            <div class="wx-preloader-scene">
                <div class="wx-preloader-sun">
                    <div class="wx-preloader-sun-rays">
                        <span></span><span></span><span></span><span></span>
                        <span></span><span></span><span></span><span></span>
                    </div>
                </div>

                <div class="wx-preloader-cloud"></div>

                <div class="wx-preloader-rain">
                    <span></span><span></span><span></span><span></span><span></span>
                </div>
            </div>

            <div class="wx-preloader-brand">WEATHER <span>ULTIMA</span></div>

            <div class="wx-preloader-tagline">Real Time Weather Intelligence</div>

            <div class="wx-preloader-progress-area">
                <div class="wx-preloader-progress-track">
                    <div class="wx-preloader-progress-bar" id="wxPreloaderProgressBar"></div>
                </div>
                <div class="wx-preloader-status">
                    <span id="wxPreloaderStatusText">Initializing weather data</span>
                    <span class="wx-preloader-percent" id="wxPreloaderPercent">0%</span>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Runs immediately, independent of jQuery/main.js/WOW — so the
        // preloader still clears itself even if one of those fails to load.
        // Animates a simulated progress readout while real assets load in the
        // background, then hides once the real "load" event fires — same
        // messages/timing as the design this was built from, just wired to
        // the site's existing body.wx-preloading scroll-lock class instead of
        // a permanent overflow:hidden.
        (function () {
            var preloader = document.getElementById('wxPreloader');
            if (!preloader) {
                return;
            }

            var progressBar = document.getElementById('wxPreloaderProgressBar');
            var percent = document.getElementById('wxPreloaderPercent');
            var statusText = document.getElementById('wxPreloaderStatusText');

            var messages = [
                'Initializing weather data',
                'Connecting satellites',
                'Reading atmosphere',
                'Analyzing cloud patterns',
                'Processing temperature',
                'Checking wind conditions',
                'Preparing weather intelligence',
                'Almost ready'
            ];

            var progress = 0;
            var duration = 4800;
            var interval = 50;
            var increment = 100 / (duration / interval);

            var progressTimer = setInterval(function () {
                progress += increment;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(progressTimer);
                }

                if (progressBar) {
                    progressBar.style.width = progress + '%';
                }
                if (percent) {
                    percent.textContent = Math.floor(progress) + '%';
                }
                if (statusText) {
                    var messageIndex = Math.min(
                        messages.length - 1,
                        Math.floor(progress / (100 / messages.length))
                    );
                    statusText.textContent = messages[messageIndex];
                }
            }, interval);

            function hidePreloader() {
                setTimeout(function () {
                    preloader.classList.add('is-hidden');
                    document.body.classList.remove('wx-preloading');
                    setTimeout(function () {
                        preloader.remove();
                    }, 1000);
                }, 5000);
            }

            if (document.readyState === 'complete') {
                hidePreloader();
            } else {
                window.addEventListener('load', hidePreloader);
            }
        })();
    </script>

    @include ('layouts.header')

    <main>
        @yield ('content')
    </main>

    @include ('layouts.footer')
    @include ('partials.whatsapp-button')

    <script src="{{ asset('material/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('material/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('material/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('material/js/main.js') }}?v={{ filemtime(public_path('material/js/main.js')) }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script>
        // WOW.js caches each element's CSS animation-name once, the moment it first
        // hides that element (during its own start()), then reuses that cached name
        // forever afterwards. If it runs before the Animate.css stylesheet has
        // finished loading, or before image-heavy sections (e.g. the product grid)
        // have settled their layout, the box gets cached with an empty animation
        // name and will snap straight to visible with no animation, permanently —
        // it's a WOW.js quirk, not something a rebuild step fixes. Waiting for the
        // window "load" event (fires only once the stylesheet AND every image has
        // finished loading) avoids that race.
        window.addEventListener('load', function () {
            if (typeof WOW !== 'undefined') {
                new WOW({
                    offset: 50,
                    mobile: true,
                    live: true
                }).init();
            }
        });
    </script>

    @stack ('scripts')
</body>
</html>
