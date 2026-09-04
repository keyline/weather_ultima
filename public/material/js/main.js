$(function () {
    var $body = $("body");
    var $menu = $("#printsaleNavbar");
    var $overlay = $(".printsale-menu-overlay");
    var $toggler = $(".printsale-toggler");

    function openMenu() {
        $menu.addClass("is-open");
        $overlay.addClass("is-open");
        $body.addClass("printsale-menu-open");
        $toggler.attr("aria-expanded", "true");
    }

    function closeMenu() {
        $menu.removeClass("is-open");
        $overlay.removeClass("is-open");
        $body.removeClass("printsale-menu-open");
        $toggler.attr("aria-expanded", "false");
    }

    $toggler.on("click", openMenu);
    $(".printsale-menu-close, .printsale-menu-overlay").on("click", closeMenu);

    $(".printsale-submenu-toggle").on("click", function () {
        $(this).closest(".has-submenu").toggleClass("is-open");
    });

    $(window).on("resize", function () {
        if (window.innerWidth >= 992) {
            closeMenu();
            $(".has-submenu").removeClass("is-open");
        }
    });

    $(".wx-testimonial-carousel").owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 6000,
        smartSpeed: 700,
        navText: [
            '<i class="fa-solid fa-arrow-left"></i>',
            '<i class="fa-solid fa-arrow-right"></i>'
        ],
        responsive: {
            0: { items: 1 },
            576: { items: 2 },
            992: { items: 3 },
            1400: { items: 4 }
        }
    });

    var aboutSection = document.querySelector(".wx-stats-section");
    var hasCounted = false;

    function animateCounts() {
        if (hasCounted) {
            return;
        }

        hasCounted = true;
        $(".count-number").each(function () {
            var $number = $(this);
            var target = parseInt($number.attr("data-count"), 10);

            $({ value: 0 }).animate({ value: target }, {
                duration: 1800,
                easing: "swing",
                step: function (now) {
                    $number.text(Math.floor(now));
                },
                complete: function () {
                    $number.text(target);
                }
            });
        });
    }

    if (aboutSection && "IntersectionObserver" in window) {
        var aboutObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounts();
                }
            });
        }, { threshold: 0.35 });

        aboutObserver.observe(aboutSection);
    } else if (aboutSection) {
        animateCounts();
    }

    var yearEl = document.getElementById("wx-year");
    if (yearEl) {
        yearEl.textContent = new Date().getFullYear();
    }

    var precipLayer = document.getElementById("wxRain");

    function renderPrecipitation(mode) {
        if (!precipLayer) {
            return;
        }
        precipLayer.className = "wx-rain mode-" + mode;
        if (mode === "none") {
            precipLayer.innerHTML = "";
            return;
        }
        var isSmallScreen = window.innerWidth < 576;
        var counts = { petals: [12, 22], rain: [26, 46], snow: [16, 30] };
        var range = counts[mode] || counts.petals;
        var count = isSmallScreen ? range[0] : range[1];
        var html = "";
        for (var i = 0; i < count; i++) {
            var left = Math.random() * 100;
            var duration, delay, scale;
            if (mode === "rain") {
                duration = (0.7 + Math.random() * 0.5).toFixed(2);
                delay = (Math.random() * 3).toFixed(2);
                scale = 1;
            } else if (mode === "snow") {
                duration = (6 + Math.random() * 5).toFixed(2);
                delay = (Math.random() * 10).toFixed(2);
                scale = (0.5 + Math.random() * 0.9).toFixed(2);
            } else {
                duration = (5 + Math.random() * 4).toFixed(2);
                delay = (Math.random() * 9).toFixed(2);
                scale = (0.6 + Math.random() * 0.8).toFixed(2);
            }
            html += '<span style="left:' + left + '%;animation-duration:' + duration + 's;animation-delay:-' + delay + 's;--wx-petal-scale:' + scale + ';"></span>';
        }
        precipLayer.innerHTML = html;
    }

    renderPrecipitation("petals");

    var heroEl = document.getElementById("wxHero");
    if (heroEl) {
        var ticking = false;
        function updateHeroScroll() {
            var offset = Math.max(0, Math.min(window.scrollY, 600));
            heroEl.style.setProperty("--wx-scroll", offset);
            ticking = false;
        }
        updateHeroScroll();
        window.addEventListener("scroll", function () {
            if (!ticking) {
                window.requestAnimationFrame(updateHeroScroll);
                ticking = true;
            }
        });
    }

    if (window.matchMedia && window.matchMedia("(pointer: fine)").matches) {
        var tiltCards = document.querySelectorAll("[data-tilt]");
        tiltCards.forEach(function (card) {
            card.addEventListener("pointermove", function (e) {
                var rect = card.getBoundingClientRect();
                var px = (e.clientX - rect.left) / rect.width - 0.5;
                var py = (e.clientY - rect.top) / rect.height - 0.5;
                card.style.transform = "perspective(1000px) rotateX(" + (py * -7).toFixed(2) + "deg) rotateY(" + (px * 9).toFixed(2) + "deg) translateY(-8px) scale(1.015)";
            });
            card.addEventListener("pointerleave", function () {
                card.style.transform = "";
            });
        });
    }

    var revealEls = document.querySelectorAll(".reveal");
    if (revealEls.length && "IntersectionObserver" in window) {
        var revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        revealEls.forEach(function (el) {
            revealObserver.observe(el);
        });
    } else {
        revealEls.forEach(function (el) {
            el.classList.add("is-visible");
        });
    }

    // Core values list: a per-element observer (like .reveal above) can let
    // several closely-stacked rows cross the visibility threshold in the same
    // scroll frame, so they'd fade in almost together instead of one by one.
    // A single IntersectionObserver also isn't guaranteed to batch multiple
    // targets into one callback call — Chromium often fires it once per row
    // a few ms apart — so indexing within one callback's entries isn't
    // reliable either. Collect newly-visible rows into a queue instead, and
    // only stagger-reveal the queue once it's stopped growing for a beat.
    var valueRows = Array.prototype.slice.call(document.querySelectorAll(".wx-value-row"));
    if (valueRows.length) {
        if ("IntersectionObserver" in window) {
            var pendingValueRows = [];
            var valueRevealTimer = null;

            function flushPendingValueRows() {
                pendingValueRows
                    .sort(function (a, b) {
                        return valueRows.indexOf(a) - valueRows.indexOf(b);
                    })
                    .forEach(function (row, i) {
                        setTimeout(function () {
                            row.classList.add("is-visible");
                        }, i * 140);
                    });
                pendingValueRows = [];
            }

            var valueRowObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        valueRowObserver.unobserve(entry.target);
                        pendingValueRows.push(entry.target);
                    }
                });
                if (pendingValueRows.length) {
                    clearTimeout(valueRevealTimer);
                    valueRevealTimer = setTimeout(flushPendingValueRows, 180);
                }
            }, { threshold: 0.15 });

            valueRows.forEach(function (row) {
                valueRowObserver.observe(row);
            });
        } else {
            valueRows.forEach(function (row) {
                row.classList.add("is-visible");
            });
        }
    }

    // Live weather: geolocate the visitor, fetch current conditions, and
    // reflect them in the hero scene (sun/cloud visibility, precipitation,
    // day/night tint) plus a small "live" pill with place + temperature.
    var liveWidget = document.getElementById("wxLiveWeather");
    var liveText = liveWidget ? liveWidget.querySelector(".wx-live-text") : null;

    // WeatherAPI.com free-tier key. This is a client-side widget key (rate
    // limited per key, not a payment credential) — WeatherAPI's free plan is
    // designed to be called straight from browser JS like this, so it being
    // visible in page source is expected, not a leak.
    var WEATHERAPI_KEY = "c6eea7d479234abfb17140855261808";

    // WeatherAPI's condition set is ~50 codes with a human-readable `text`
    // already attached (e.g. "Haze", "Patchy rain nearby") — classifying off
    // that text is simpler and more robust than transcribing every code.
    function classifyWeather(conditionText, isDay) {
        var t = (conditionText || "").toLowerCase();
        if (t.indexOf("thunder") !== -1) { return "storm"; }
        if (t.indexOf("snow") !== -1 || t.indexOf("sleet") !== -1 || t.indexOf("ice") !== -1 || t.indexOf("blizzard") !== -1) { return "snow"; }
        if (t.indexOf("rain") !== -1 || t.indexOf("drizzle") !== -1 || t.indexOf("shower") !== -1) { return "rain"; }
        if (t.indexOf("fog") !== -1 || t.indexOf("mist") !== -1 || t.indexOf("haze") !== -1) { return "fog"; }
        if (t.indexOf("overcast") !== -1 || t.indexOf("cloud") !== -1) { return "cloudy"; }
        return isDay ? "clear-day" : "clear-night";
    }

    function precipModeFor(state) {
        if (state === "rain" || state === "storm") { return "rain"; }
        if (state === "snow") { return "snow"; }
        if (state === "clear-day" || state === "clear-night") { return "petals"; }
        return "none";
    }

    function setLiveText(text) {
        if (liveText) {
            liveText.textContent = text;
        }
    }

    function hideLiveWidget() {
        if (liveWidget) {
            liveWidget.classList.add("is-error");
        }
    }

    var popoverEls = {
        panel: document.getElementById("wxLivePopover"),
        place: document.getElementById("wxPopoverPlace"),
        range: document.getElementById("wxPopoverRange"),
        feels: document.getElementById("wxPopoverFeels"),
        humidity: document.getElementById("wxPopoverHumidity"),
        wind: document.getElementById("wxPopoverWind"),
        tomorrow: document.getElementById("wxPopoverTomorrow")
    };

    function applyWeather(current, forecastDays, placeName) {
        var state = classifyWeather(current.condition && current.condition.text, current.is_day);
        if (heroEl) {
            heroEl.setAttribute("data-weather", state);
        }
        renderPrecipitation(precipModeFor(state));
        var label = (current.condition && current.condition.text) || "Weather";
        var temp = Math.round(current.temp_c);
        setLiveText((placeName ? placeName + " · " : "") + temp + "°C · " + label);

        if (popoverEls.place) {
            popoverEls.place.textContent = placeName || "Your location";
        }
        if (popoverEls.feels) {
            popoverEls.feels.textContent = Math.round(current.feelslike_c) + "°";
        }
        if (popoverEls.humidity) {
            popoverEls.humidity.textContent = Math.round(current.humidity) + "%";
        }
        if (popoverEls.wind) {
            popoverEls.wind.textContent = Math.round(current.wind_kph) + " km/h";
        }
        if (forecastDays && forecastDays.length) {
            var today = forecastDays[0].day;
            if (popoverEls.range) {
                popoverEls.range.textContent = Math.round(today.mintemp_c) + "° / " + Math.round(today.maxtemp_c) + "°";
            }
            if (popoverEls.tomorrow && forecastDays.length > 1) {
                var tomorrow = forecastDays[1].day;
                popoverEls.tomorrow.textContent = "Tomorrow · " + tomorrow.condition.text + " · "
                    + Math.round(tomorrow.mintemp_c) + "°/" + Math.round(tomorrow.maxtemp_c) + "°"
                    + " · " + Math.round(tomorrow.daily_chance_of_rain) + "% rain";
            }
        }
    }

    function fetchWeather(lat, lon, placeName) {
        fetch("https://api.weatherapi.com/v1/forecast.json?key=" + WEATHERAPI_KEY + "&q=" + lat + "," + lon + "&days=2&aqi=no&alerts=no")
            .then(function (res) { return res.ok ? res.json() : Promise.reject(); })
            .then(function (data) {
                if (data && data.current) {
                    applyWeather(data.current, data.forecast && data.forecast.forecastday, placeName);
                } else {
                    hideLiveWidget();
                }
            })
            .catch(hideLiveWidget);
    }

    // Pick a reverse-geocode zoom that matches how trustworthy the fix is.
    // Nominatim's "zoom" controls how small an area it's willing to name —
    // asking for suburb-level precision (zoom 16) out of a position that's
    // only accurate to a few kilometres just means the result flips between
    // neighbouring suburbs at random. Coarser accuracy gets a coarser (but
    // stable) zoom instead of a falsely precise one.
    function zoomForAccuracy(accuracyMeters) {
        if (!accuracyMeters || accuracyMeters > 20000) { return 8; }
        if (accuracyMeters > 5000) { return 10; }
        if (accuracyMeters > 1500) { return 12; }
        if (accuracyMeters > 500) { return 14; }
        if (accuracyMeters > 150) { return 16; }
        return 18;
    }

    // Reverse-geocode so the result names the actual locality/area around
    // the visitor (village/suburb/neighbourhood), not just the larger
    // administrative town it belongs to — at whatever zoom the fix accuracy
    // actually supports.
    function reverseGeocode(lat, lon, zoom, cb) {
        fetch("https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=" + lat + "&lon=" + lon + "&zoom=" + zoom + "&addressdetails=1")
            .then(function (res) { return res.ok ? res.json() : Promise.reject(); })
            .then(function (d) {
                var a = (d && d.address) || {};
                // Finest-grained first: a village/hamlet/suburb is a more
                // precise "area" than the town or county it sits inside.
                var area = a.neighbourhood || a.suburb || a.quarter || a.borough || a.village || a.hamlet
                    || a.croft || a.isolated_dwelling || a.residential || a.city_district || "";
                var wider = a.town || a.city || a.county || a.state_district || "";
                var country = a.country_code ? a.country_code.toUpperCase() : "";
                var parts = [];
                if (area) { parts.push(area); }
                if (wider && wider !== area) { parts.push(wider); }
                if (!parts.length && a.state) { parts.push(a.state); }
                cb(parts.length ? parts.join(", ") + (country ? ", " + country : "") : "");
            })
            .catch(function () { cb(""); });
    }

    function useIpFallback() {
        fetch("https://get.geojs.io/v1/ip/geo.json")
            .then(function (res) { return res.ok ? res.json() : Promise.reject(); })
            .then(function (d) {
                var lat = d && parseFloat(d.latitude);
                var lon = d && parseFloat(d.longitude);
                if (lat && lon) {
                    // IP-based location is city-level at best, regardless of
                    // what the service claims — keep the zoom coarse so it
                    // doesn't guess at a specific suburb.
                    reverseGeocode(lat, lon, 10, function (place) {
                        fetchWeather(lat, lon, place || [d.city, d.country_code].filter(Boolean).join(", "));
                    });
                } else {
                    hideLiveWidget();
                }
            })
            .catch(hideLiveWidget);
    }

    if (liveWidget) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    var lat = pos.coords.latitude;
                    var lon = pos.coords.longitude;
                    var zoom = zoomForAccuracy(pos.coords.accuracy);
                    reverseGeocode(lat, lon, zoom, function (place) {
                        fetchWeather(lat, lon, place);
                    });
                },
                function () { useIpFallback(); },
                // maximumAge: 0 forces a fresh fix instead of letting the
                // browser hand back a stale cached position (which is what
                // caused the wrong-on-first-load, correct-after-refresh
                // symptom — a stale fix looked valid until the cache aged
                // out a couple of reloads later).
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            useIpFallback();
        }
    }

    // Click the pill to open the detail popover; click outside, press
    // Escape, or click the pill again to close it.
    if (liveWidget && popoverEls.panel) {
        function closePopover() {
            popoverEls.panel.hidden = true;
            liveWidget.setAttribute("aria-expanded", "false");
        }

        function openPopover() {
            popoverEls.panel.hidden = false;
            liveWidget.setAttribute("aria-expanded", "true");
        }

        liveWidget.addEventListener("click", function (e) {
            e.stopPropagation();
            if (popoverEls.panel.hidden) {
                openPopover();
            } else {
                closePopover();
            }
        });

        popoverEls.panel.addEventListener("click", function (e) {
            e.stopPropagation();
        });

        document.addEventListener("click", closePopover);

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                closePopover();
            }
        });
    }

    // Weather station tabs — only Kolkata has live figures for now; the
    // other stations swap in a placeholder until their data is wired up.
    var stationTabs = document.querySelectorAll(".wx-station-tab");
    var stationBody = document.getElementById("wxStationBody");
    var stationDefaultHtml = stationBody ? stationBody.innerHTML : "";

    stationTabs.forEach(function (tab) {
        tab.addEventListener("click", function () {
            stationTabs.forEach(function (t) {
                t.classList.remove("is-active");
                t.setAttribute("aria-selected", "false");
            });
            tab.classList.add("is-active");
            tab.setAttribute("aria-selected", "true");

            if (!stationBody) {
                return;
            }

            if (tab.dataset.station === "Kolkata") {
                stationBody.innerHTML = stationDefaultHtml;
            } else {
                stationBody.innerHTML = '<p class="wx-station-empty">Live station data for '
                    + tab.dataset.station + " is coming soon.</p>";
            }
        });
    });

    // Contact page form — this site has no backend to send it to yet, so we
    // just confirm receipt client-side rather than pretending to submit it.
    var contactForm = document.getElementById("wxContactForm");
    var contactNote = document.getElementById("wxContactNote");
    if (contactForm) {
        contactForm.addEventListener("submit", function (e) {
            e.preventDefault();
            if (contactNote) {
                contactNote.hidden = false;
            }
            contactForm.reset();
        });
    }

    // Product page — enquiry modal shared by every product card. The button
    // that opened it carries the product name, which we drop into the modal
    // title and a hidden field before it's shown.
    var enquiryModal = document.getElementById("wxEnquiryModal");
    if (enquiryModal) {
        enquiryModal.addEventListener("show.bs.modal", function (e) {
            var button = e.relatedTarget;
            var productName = (button && button.dataset.productName) || "This Product";
            var nameEl = document.getElementById("wxEnquiryProductName");
            var fieldEl = document.getElementById("wxEnquiryProductField");
            if (nameEl) {
                nameEl.textContent = productName;
            }
            if (fieldEl) {
                fieldEl.value = productName;
            }
        });
    }

    var enquiryForm = document.getElementById("wxEnquiryForm");
    var enquiryNote = document.getElementById("wxEnquiryNote");
    if (enquiryForm) {
        enquiryForm.addEventListener("submit", function (e) {
            e.preventDefault();
            if (enquiryNote) {
                enquiryNote.hidden = false;
            }
            enquiryForm.reset();
        });
    }

    // Services detail (service.html) — desktop: sticky left tab list + right panel.
    // Mobile: same panels reparented into an accordion, one open at a time.
    var serviceTabs = document.querySelectorAll(".wx-service-tab");
    var serviceAccToggles = document.querySelectorAll(".wx-service-acc-toggle");
    var servicePanelsHome = document.querySelector(".wx-service-panels");

    if (serviceTabs.length || serviceAccToggles.length) {
        var activateService = function (target) {
            serviceTabs.forEach(function (t) {
                t.classList.toggle("is-active", t.dataset.serviceTarget === target);
            });
            serviceAccToggles.forEach(function (t) {
                t.classList.toggle("is-active", t.dataset.serviceTarget === target);
            });
            document.querySelectorAll(".wx-service-panel").forEach(function (panel) {
                panel.classList.toggle("is-active", panel.id === "service-" + target);
            });
        };

        serviceTabs.forEach(function (tab) {
            tab.addEventListener("click", function () {
                activateService(tab.dataset.serviceTarget);
            });
        });

        serviceAccToggles.forEach(function (toggle) {
            toggle.addEventListener("click", function () {
                var isOpen = toggle.classList.contains("is-active");
                activateService(isOpen ? null : toggle.dataset.serviceTarget);
            });
        });

        // Move each panel into its matching accordion body on mobile, and back
        // into the desktop panels column above that breakpoint — same nodes,
        // no duplicated content to keep in sync.
        var serviceMobileQuery = window.matchMedia("(max-width: 767px)");
        var placeServicePanels = function () {
            if (serviceMobileQuery.matches) {
                document.querySelectorAll(".wx-service-acc-body").forEach(function (body) {
                    var panel = document.getElementById("service-" + body.dataset.serviceBody);
                    if (panel) {
                        body.appendChild(panel);
                    }
                });
            } else if (servicePanelsHome) {
                document.querySelectorAll(".wx-service-panel").forEach(function (panel) {
                    servicePanelsHome.appendChild(panel);
                });
            }
        };

        placeServicePanels();
        if (serviceMobileQuery.addEventListener) {
            serviceMobileQuery.addEventListener("change", placeServicePanels);
        } else if (serviceMobileQuery.addListener) {
            serviceMobileQuery.addListener(placeServicePanels);
        }
    }

    // Close the mobile offcanvas menu when a nav link is clicked. This used
    // to be done with data-bs-dismiss="offcanvas" on the links themselves,
    // but Bootstrap's offcanvas plugin calls preventDefault() on any <a>
    // that carries that attribute — which silently killed every nav link's
    // navigation. Closing it manually here avoids that.
    var navOffcanvasEl = document.getElementById("navbarNav");
    if (navOffcanvasEl && window.bootstrap && window.bootstrap.Offcanvas) {
        navOffcanvasEl.querySelectorAll(".nav-link").forEach(function (link) {
            link.addEventListener("click", function () {
                var instance = bootstrap.Offcanvas.getInstance(navOffcanvasEl);
                if (instance) {
                    instance.hide();
                }
            });
        });
    }
});
