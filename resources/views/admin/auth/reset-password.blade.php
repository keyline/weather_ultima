<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Choose a new password · Weather Ultima</title>
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
                    ><i class="fa-solid fa-shield-halved"></i
                ></span>
                <h1 class="text-4xl font-bold leading-tight text-white">
                    Set a new password and you&rsquo;re done.
                </h1>
                <p class="mt-5 text-lg leading-8 text-sky-100">Choose a strong password you don&rsquo;t use anywhere else. You&rsquo;ll be signed out of other sessions.</p>
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
                            ><i class="fa-solid fa-lock-open"></i
                        ></span>
                        <h2
                            class="mt-5 text-2xl font-bold tracking-tight text-slate-900"
                        >
                            Choose a new password
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Enter a new password for your administrator account.</p>
                    </div>
                    @if ($errors->any())
                        <div
                            class="mt-6 rounded border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
                        >
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <form
                        method="POST"
                        action="{{ route('admin.password.update') }}"
                        class="mt-7 space-y-5"
                        id="reset-password-form"
                    >
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}" />
                        <div>
                            <label
                                for="email"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                                >Email address</label
                            ><input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $email) }}"
                                autocomplete="email"
                                required
                                readonly
                                class="admin-input bg-slate-50 @error('email') admin-input--invalid @enderror"
                                placeholder="you@example.com"
                            />
                            @error ('email')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label
                                for="password"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                                >New password</label
                            >
                            <div class="relative">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="new-password"
                                    required
                                    autofocus
                                    class="admin-input pr-12 @error('password') admin-input--invalid @enderror"
                                    placeholder="At least 8 characters"
                                /><button
                                    id="password-toggle"
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-slate-400 transition hover:text-[#0b376d]"
                                    aria-label="Show password"
                                >
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            @error ('password')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label
                                for="password_confirmation"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                                >Confirm new password</label
                            ><input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                required
                                class="admin-input"
                                placeholder="Re-enter your new password"
                            />
                        </div>
                        <x-recaptcha action="reset_password" />
                        <button
                            id="reset-submit"
                            type="submit"
                            class="admin-btn admin-btn--primary w-full disabled:cursor-wait"
                        >
                            <span>Reset password</span
                            ><i class="fa-solid fa-arrow-right"></i>
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
        const password = document.getElementById("password");
        const passwordToggle = document.getElementById("password-toggle");
        passwordToggle.addEventListener("click", () => {
            const visible = password.type === "text";
            password.type = visible ? "password" : "text";
            passwordToggle.setAttribute(
                "aria-label",
                visible ? "Show password" : "Hide password",
            );
            passwordToggle.firstElementChild.className = visible
                ? "fa-regular fa-eye"
                : "fa-regular fa-eye-slash";
        });
        document
            .getElementById("reset-password-form")
            .addEventListener("submit", () => {
                const button = document.getElementById("reset-submit");
                button.disabled = true;
                button.querySelector("span").textContent = "Resetting...";
                button.querySelector("i").className =
                    "fa-solid fa-spinner animate-spin";
            });
    </script>
    @stack ('scripts')
</body>
</html>
