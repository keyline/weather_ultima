@once
    @push ('scripts')
        <script>
            (() => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

                const showThanks = (message) => {
                    const modalEl = document.getElementById("wxThankYouModal");
                    if (!modalEl) {
                        window.alert(message);
                        return;
                    }
                    const textEl = document.getElementById("wxThankYouText");
                    if (textEl && message) textEl.textContent = message;
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                };

                const renderErrors = (box, errors) => {
                    if (!box) return;
                    const messages = Object.values(errors || {}).flat();
                    box.innerHTML = messages.join("<br>");
                    box.hidden = messages.length === 0;
                };

                document.querySelectorAll("form[data-ajax-enquiry]").forEach((form) => {
                    const errorBox = form.querySelector("[data-form-error]");
                    const submitBtn = form.querySelector('button[type="submit"], [type="submit"]');
                    const submitHtml = submitBtn ? submitBtn.innerHTML : "";

                    form.addEventListener("submit", async (event) => {
                        event.preventDefault();
                        renderErrors(errorBox, {});

                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = 'Sending… <i class="fa-solid fa-spinner fa-spin"></i>';
                        }

                        try {
                            const response = await fetch(form.action, {
                                method: "POST",
                                headers: {
                                    Accept: "application/json",
                                    "X-Requested-With": "XMLHttpRequest",
                                    "X-CSRF-TOKEN": csrf,
                                },
                                body: new FormData(form),
                            });

                            const data = await response.json().catch(() => ({}));

                            if (response.ok) {
                                const parentModal = form.closest(".modal");
                                if (parentModal) bootstrap.Modal.getOrCreateInstance(parentModal).hide();
                                form.reset();
                                if (window.grecaptcha && form.querySelector(".g-recaptcha")) {
                                    try { window.grecaptcha.reset(); } catch (e) {}
                                }
                                if (typeof window.wxRefreshRecaptcha === "function") window.wxRefreshRecaptcha();
                                showThanks(data.message || "Your enquiry has been submitted successfully.");
                            } else if (response.status === 422) {
                                renderErrors(errorBox, data.errors);
                            } else if (response.status === 429) {
                                renderErrors(errorBox, { rate: ["Too many attempts. Please wait a minute and try again."] });
                            } else {
                                renderErrors(errorBox, { general: ["Something went wrong. Please try again in a moment."] });
                            }
                        } catch (error) {
                            renderErrors(errorBox, { general: ["Network error. Please check your connection and try again."] });
                        } finally {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = submitHtml;
                            }
                        }
                    });
                });
            })();
        </script>
    @endpush
@endonce
