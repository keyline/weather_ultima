<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reset your password · Weather Ultima</title>
    @vite (['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('material/css/all.min.css') }}" />
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-900 antialiased">
    <main class="grid min-h-screen lg:grid-cols-2">
        <section
            class="relative hidden overflow-hidden bg-[#0b376d] p-12 lg:flex lg:flex-col lg:justify-between"
        >
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(89,191,229,0.45),_transparent_40%),radial-gradient(circle_at_bottom_left,_rgba(251,191,36,0.25),_transparent_35%)]"
            ></div>
            <a
                href="{{ route('home') }}"
                class="relative flex items-center gap-3 text-white"
                ><img
                    src="{{ asset('material/images/logo.png') }}"
                    alt="Weather Ultima"
                    class="h-12 rounded bg-white p-1.5"
                /><span class="text-xl font-semibold">Weather Ultima</span></a
            >
            <div class="relative max-w-lg">
                <span
                    class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-white/15 text-xl text-amber-300"
                    ><i class="fa-solid fa-key"></i
                ></span>
                <h1 class="text-4xl font-bold leading-tight text-white">
                    Locked out? We&rsquo;ll get you back in.
                </h1>
                <p class="mt-5 text-lg leading-8 text-sky-100">Enter the email on your administrator account and we&rsquo;ll send a secure link to choose a new password.</p>
            </div>
            <p class="relative text-sm text-sky-200">Science. Service. Sustainability.</p>
        </section>
        <section
            class="flex items-center justify-center bg-slate-50 px-5 py-10 sm:px-8"
        >
            <div class="w-full max-w-md">
                <a
                    href="{{ route('home') }}"
                    class="mb-10 flex items-center gap-3 lg:hidden"
                    ><img
                        src="{{ asset('material/images/logo.png') }}"
                        alt="Weather Ultima"
                        class="h-11 rounded bg-white p-1 shadow-sm"
                    /><span class="font-semibold text-[#0b376d]"
                        >Weather Ultima</span
                    ></a
                >
                <div
                    class="rounded-lg border border-slate-200 bg-white p-7 shadow-lg sm:p-9"
                >
                    <div>
                        <span
                            class="inline-flex h-11 w-11 items-center justify-center rounded-lg bg-sky-50 text-[#0b376d]"
                            ><i class="fa-solid fa-envelope-open-text"></i
                        ></span>
                        <h2
                            class="mt-5 text-2xl font-bold tracking-tight text-slate-900"
                        >
                            Forgot your password?
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">We&rsquo;ll email you a link to reset it. The link expires in 60 minutes.</p>
                    </div>
                    @if (session('status'))
                        <div
                            class="mt-6 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
                        >
                            {{ session('status') }}
                        </div>
                    @endif
                    <form
                        method="POST"
                        action="{{ route('admin.password.email') }}"
                        class="mt-7 space-y-5"
                        id="forgot-password-form"
                    >
                        @csrf
                        <div>
                            <label
                                for="email"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                                >Email address</label
                            ><input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                                autofocus
                                class="admin-input @error('email') admin-input--invalid @enderror"
                                placeholder="you@example.com"
                            />
                            @error ('email')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <x-recaptcha action="forgot_password" />
                        <button
                            id="forgot-submit"
                            type="submit"
                            class="admin-btn admin-btn--primary w-full disabled:cursor-wait"
                        >
                            <span>Email password reset link</span
                            ><i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
                <p class="mt-6 text-center text-sm text-slate-500">
                    <a href="{{ route('admin.login') }}" class="font-semibold text-[#0b376d] hover:underline">
                        <i class="fa-solid fa-arrow-left text-xs"></i> Back to sign in
                    </a>
                </p>
            </div>
        </section>
    </main>
    <script>
        document
            .getElementById("forgot-password-form")
            .addEventListener("submit", () => {
                const button = document.getElementById("forgot-submit");
                button.disabled = true;
                button.querySelector("span").textContent = "Sending...";
                button.querySelector("i").className =
                    "fa-solid fa-spinner animate-spin";
            });
    </script>
    @stack ('scripts')
</body>
</html>
