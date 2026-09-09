---
paths:
  - app/Http/Controllers/Admin/ProductController.php
  - app/Http/Controllers/Admin/TestimonialController.php
  - 'resources/views/admin/products/**'
  - 'resources/views/admin/testimonials/**'
  - app/Http/Controllers/Admin/AdminNewPasswordController.php
---

# Admin content CRUD (Products, Testimonials)

Both follow the same shape — copy it for any new admin-managed content:

- `Route::resource(..., ...)->except(['show'])` + `PATCH .../{model}/toggle` + `DELETE .../bulk` (bulk **before** the resource). Enable/disable via `toggle`; `is_active` / `is_enabled` bool column.
- Store/Update FormRequests (admin role in `authorize()`); image `nullable|image|mimes:jpeg,jpg,png,webp|max:2048` (required on create for Product, always optional for Testimonial). Files on the `public` disk under `<type>/`; replacing or deleting removes the old file (`deleteImage`/`deletePhoto` helper, mirrors `SiteSettingController`).
- Views: `index` (search + per-page 10/20/50/100 default 20 + "Showing X–Y of Z" + select-all/Delete-Selected bulk form), `create`/`edit` both `@include` a shared `_form.blade.php` with a live client-side image preview.
- Frontend is DB-driven: `Product::active()->latest()`, `Testimonial::enabled()->ordered()` (`ordered` = `display_order` then `created_at`). Seeders (`ProductSeeder`, `TestimonialSeeder`) carry the original hardcoded content so nothing regresses visually. `home` is served by `HomeController@index` so it can pass `$testimonials`.
- `Testimonial` has **no image/photo** (removed by owner) — just name, designation, company, review, rating, order, enabled. Accessor `role_line` = designation + company joined by ", ". The `testimonials.photo` DB column still exists but is unused.

## Admin password reset flow (custom, no Breeze)
Forgot/reset password is hand-rolled, matching the custom admin login. Routes live in the `guest` group: `admin.password.{request,email,reset,update}` (throttle:admin-login on the POSTs). Controllers: `AdminPasswordResetLinkController`, `AdminNewPasswordController`. Both pass `'role' => 'admin'` into `Password::sendResetLink()` / `Password::reset()` so only admin users can use the flow. Reset link URL points at the admin route via `ResetPassword::createUrlUsing(...)` in `AppServiceProvider::boot()`. Forgot-password response is deliberately non-enumerating (always the same status message unless RESET_THROTTLED). FormRequests `ForgotPasswordRequest` / `ResetPasswordRequest` use `VerifiesRecaptcha` (actions `forgot_password` / `reset_password`). Standalone Blade views in `resources/views/admin/auth/` (not a shared layout). No DB change — `password_reset_tokens` ships with the default users migration.
