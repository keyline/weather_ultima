@extends ('layouts.app')
@section ('title', 'Products | ' . $siteSettings->display_name)
@section ('content')
    <!-- PAGE BANNER -->
    <header class="wx-page-banner wx-page-banner--left">
        <img src="images/cloud.png" class="wx-banner-cloud wx-banner-cloud--top" alt="" aria-hidden="true" />
        <img src="images/cloud3.png" class="wx-banner-cloud wx-banner-cloud--seam" alt="" aria-hidden="true" />
        <div class="container">
            <h1>Product</h1>
        </div>
    </header>

    <!-- PRODUCT INTRO -->
    <section class="wx-services-intro-section">
        <img src="images/cloud2.png" class="wx-intro-cloud wx-intro-cloud--left" alt="" aria-hidden="true" />
        <img src="images/cloud.png" class="wx-intro-cloud wx-intro-cloud--right" alt="" aria-hidden="true" />
        <div class="container">
            <div class="wx-services-intro reveal" data-reveal>
                <h2>Products</h2>
                <p class="wx-services-intro-subhead">Every reading starts with the right instrument.</p>
                <p>From weather stations and sensors to solar systems and monitoring software, every product we offer is designed, tested and supported by the same team behind our services &ndash; built for real field conditions, not just spec sheets.</p>
            </div>
        </div>
    </section>

    <!-- PRODUCT GRID -->
    <section class="wx-product-grid-section">
        <div class="container">
            @if (session('status'))
                <p class="wx-contact-form-note" style="display:block;margin-bottom:24px;">{{ session('status') }}</p>
            @endif

            @if ($products->isEmpty())
                <div class="wx-services-intro reveal" data-reveal style="text-align:center;">
                    <h2>Products coming soon</h2>
                    <p>Our product catalogue is being updated. Please check back shortly or <a href="{{ route('contact') }}">get in touch</a>.</p>
                </div>
            @else
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-5">
                    @foreach ($products as $product)
                        <div class="col">
                            <div class="wx-product-card reveal" data-reveal @if ($loop->index) data-reveal-delay="{{ min($loop->index, 2) }}" @endif>
                                <div class="wx-product-card-img">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" />
                                </div>
                                <h3 class="wx-product-card-title">{{ $product->name }}</h3>
                                <p class="wx-product-card-desc">{{ $product->short_description }}</p>
                                <button
                                    type="button"
                                    class="wx-product-enquiry-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#wxEnquiryModal"
                                    data-product-name="{{ $product->name }}"
                                    data-enquiry-url="{{ route('products.enquiry', $product) }}"
                                >
                                    Enquire Now <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- PRODUCT ENQUIRY MODAL -->
    <div class="modal fade wx-enquiry-modal" id="wxEnquiryModal" tabindex="-1" aria-labelledby="wxEnquiryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="wxEnquiryModalLabel">Product Enquiry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Product: <strong id="wxEnquiryProductName">This Product</strong></p>
                    <form class="wx-contact-form" id="wxEnquiryForm" method="POST" action="" data-ajax-enquiry>
                        @csrf
                        <div class="alert alert-danger" data-form-error hidden></div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="eName">Full Name</label>
                                <input type="text" id="eName" name="name" required />
                            </div>
                            <div class="col-sm-6">
                                <label for="eEmail">Email Address</label>
                                <input type="email" id="eEmail" name="email" required />
                            </div>
                            <div class="col-sm-6">
                                <label for="ePhone">Phone Number</label>
                                <input type="tel" id="ePhone" name="phone" />
                            </div>
                            <div class="col-12">
                                <label for="eMessage">Message</label>
                                <textarea id="eMessage" name="message" rows="4"></textarea>
                            </div>
                            <div class="col-12">
                                <x-recaptcha action="product_enquiry" />
                            </div>

                            <div class="col-12">
                                <button type="submit" class="wx-contact-submit">
                                    Submit Enquiry <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include ('partials.thank-you-modal')
    @include ('partials.enquiry-ajax')

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

@push ('scripts')
    <script>
        (() => {
            const modal = document.getElementById("wxEnquiryModal");
            if (!modal) return;
            modal.addEventListener("show.bs.modal", (event) => {
                const button = event.relatedTarget;
                const name = button?.dataset.productName || "This Product";
                const url = button?.dataset.enquiryUrl || "";
                modal.querySelector("#wxEnquiryProductName").textContent = name;
                modal.querySelector("#wxEnquiryForm").setAttribute("action", url);
                const box = modal.querySelector("[data-form-error]");
                if (box) { box.hidden = true; box.innerHTML = ""; }
            });
        })();
    </script>
@endpush
