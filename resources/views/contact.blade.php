@extends ('layouts.app')

@section ('title', 'Contact | ' . $siteSettings->display_name)

@section ('content')
    <header class="wx-page-banner wx-page-banner--left">
        <img
            src="images/cloud.png"
            class="wx-banner-cloud wx-banner-cloud--top"
            alt=""
        />
        <img
            src="images/cloud3.png"
            class="wx-banner-cloud wx-banner-cloud--seam"
            alt=""
        />

        <div class="container">
            <h1>Contact Us</h1>
        </div>
    </header>

    <section class="wx-contact-info-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <article class="wx-contact-card">
                        <span class="wx-contact-card-icon"
                            ><i class="fa-solid fa-location-dot"></i
                        ></span>
                        <h3>Head Office</h3>
                        <p>{{ $siteSettings->contact_address ?: 'Kolkata, West Bengal, India' }}</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="wx-contact-card">
                        <span class="wx-contact-card-icon"
                            ><i class="fa-solid fa-phone"></i
                        ></span>
                        <h3>Call Us</h3>
                        <p><a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $siteSettings->contact_phone) }}">{{ $siteSettings->contact_phone }}</a></p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="wx-contact-card">
                        <span class="wx-contact-card-icon"
                            ><i class="fa-solid fa-envelope"></i
                        ></span>
                        <h3>Email Us</h3>
                        <p><a href="mailto:{{ $siteSettings->contact_email }}">{{ $siteSettings->contact_email }}</a></p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="wx-contact-main-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6 d-flex">
                    <div class="wx-contact-form-card">
                        <h2>Send Us a Message</h2>
                        <p>Have a question? Fill out the form and our team will get back to you shortly.</p>

                        <form
                            class="wx-contact-form"
                            method="POST"
                            action="{{ route('contact.store') }}"
                            id="contact-form"
                            data-ajax-enquiry
                        >
                            @csrf
                            <div class="alert alert-danger" data-form-error hidden></div>

                            <div class="row g-3">
                                @foreach (['name' => 'Full Name', 'email' => 'Email Address', 'phone' => 'Phone Number', 'subject' => 'Subject'] as $field => $label)
                                    <div class="col-sm-6">
                                        <label
                                            for="{{ $field }}"
                                            >{{ $label }}</label
                                        >
                                        <input
                                            id="{{ $field }}"
                                            type="{{ $field === 'email' ? 'email' : 'text' }}"
                                            name="{{ $field }}"
                                            value="{{ old($field) }}"
                                            @required ($field !== 'phone')
                                        />
                                        @error ($field)
                                            <small
                                                class="text-danger"
                                                >{{ $message }}</small
                                            >
                                        @enderror
                                    </div>
                                @endforeach

                                <div class="col-12">
                                    <label for="message">Message</label>
                                    <textarea
                                        id="message"
                                        name="message"
                                        rows="5"
                                        required
                                        >{{ old('message') }}</textarea
                                    >
                                    @error ('message')
                                        <small
                                            class="text-danger"
                                            >{{ $message }}</small
                                        >
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <x-recaptcha action="contact" />
                                </div>

                                <div class="col-12">
                                    <button
                                        id="contact-submit"
                                        type="submit"
                                        class="wx-contact-submit"
                                    >
                                        Send Message
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6 d-flex">
                    <div class="wx-contact-map">
                        <iframe
                            src="https://www.google.com/maps?q=Kolkata&output=embed"
                            width="100%"
                            height="100%"
                            style="border: 0"
                            loading="lazy"
                            title="Weather Ultima location"
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include ('partials.thank-you-modal')
    @include ('partials.enquiry-ajax')
@endsection
