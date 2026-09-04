---
paths:
  - 'app/Models/**'
  - 'database/migrations/**'
  - 'app/Mail/**'
  - 'app/Rules/**'
  - 'app/Http/Requests/Concerns/**'
  - 'app/Http/Controllers/Admin/EmailSettingController.php'
  - 'app/Http/Controllers/Admin/SiteSettingController.php'
  - 'app/Http/Controllers/Admin/SmtpSettingController.php'
  - 'app/Http/Controllers/Admin/BrevoSettingController.php'
  - 'app/Http/Controllers/Admin/RecaptchaSettingController.php'
  - 'app/Http/Controllers/Admin/SettingsController.php'
  - 'resources/views/admin/settings/**'
  - 'resources/views/components/recaptcha.blade.php'
---

# Settings & data model

## Single-row settings tables
`EmailSetting`, `SmtpSetting`, `BrevoSetting`, `RecaptchaSetting` and `SiteSetting` are one-row config tables. Always read via `Model::current()` (`firstOrCreate`). `EmailSetting` / `SmtpSetting` seed `Model::defaults()` because their columns are `NOT NULL`; the others allow nulls. The admin **Settings** menu is a group: Overview (`admin.settings.index` — card grid) · Email · SMTP · Brevo · Google reCAPTCHA · Site.

- **EmailSetting** columns: `contact_notification_email`, `product_notification_email`, `sender_name`, `contact_subject`, `product_subject`, `contact_notifications_enabled`, `product_notifications_enabled`. Notification recipients come from here — never hardcode them in controllers.
- **SmtpSetting** columns: `host`, `port`, `username`, `password` (`encrypted`), `encryption` (`tls`/`ssl`/`null`), `from_address`, `from_name`, `is_active`. `admin.settings.smtp.{edit,update,test}`.
- **BrevoSetting** columns: `api_key` (`encrypted`), `sender_name`, `sender_email`, `reply_to_email`, `is_active`. Delivers via the Brevo HTTP API through a custom transport (`App\Mail\Transport\BrevoApiTransport`, registered with `Mail::extend('brevo', …)` in `AppServiceProvider`; `config/mail.php` has a `brevo` mailer). `admin.settings.brevo.{edit,update,test}`.
- **RecaptchaSetting** columns: `site_key`, `secret_key` (`encrypted`), `version` (`v2`/`v3` — see `RecaptchaSetting::VERSIONS`), `minimum_score` (v3), `is_active`. `isEnforced()` = active **and** both keys present. `admin.settings.recaptcha.{edit,update}` (no test action).
- **SiteSetting** ("General Settings") columns: `site_name`, `header_logo_path`, `footer_logo_path`, `favicon_path`, `contact_email`, `contact_phone`, `contact_address`, `social_*` (5). Accessors: `display_name`, `header_logo_url`, `footer_logo_url`, `favicon_url`, `social_links`. Uploads go to the `public` disk under `site/`; replacing/removing deletes the old file.

## Encrypted credential fields
`password` / `api_key` / `secret_key` use the `encrypted` cast. On the form the input is always blank with a `maskedXxx()` hint (last 4 chars); the controller only writes the field when `filled()`, so a blank submit keeps the stored value. `->safe()->except([...])` the encrypted + `is_active` keys, then set `is_active` from `$request->boolean(...)`. Never echo the raw value — tests `assertDontSee` it. Read raw storage with `DB::table(...)->value(...)`, not `Model::query()->value(...)` (the query builder applies the cast and decrypts).

## Outgoing mail precedence
`AppServiceProvider::boot()` → `App\Mail\MailChannelConfigurator::configure()` (wrapped in try/catch for fresh installs): active **Brevo** (with key) wins, else active **SMTP**, else `.env`. Each model has `applyToMailConfig(bool $force = false)`; the test-email actions call it with `force: true` + `Mail::forgetMailers()` so an unactivated config can still be tried.

## reCAPTCHA on forms
Blade component `<x-recaptcha :action="'contact'" />` renders nothing unless `isEnforced()`; v2 → widget div, v3 → hidden `g-recaptcha-response` input refreshed by JS. Both push the Google `api.js` to `@stack('scripts')` (the admin login layout has its own `@stack('scripts')`). FormRequests add the check with `use VerifiesRecaptcha` + `...$this->recaptchaRules('<action>')` in `rules()`; the `App\Rules\Recaptcha` rule is `public bool $implicit = true` so a missing token fails. Controllers persisting request data must `->safe()->except('g-recaptcha-response')`. Wired into: contact form, product enquiry modal, admin login.

## Enquiry / product models
`ContactEnquiry` (table `contact_enquiries`, has `search` scope), `Product` (`active` scope, `image_url` accessor, `PLACEHOLDER_IMAGE`, images under `products/` on the `public` disk), `ProductEnquiry` (stores `product_id` + `product_name` snapshot, `nullOnDelete`).

## Migrations are additive
The DB has real data — never edit a migration that has run; add a new one and run `php artisan migrate` (not `migrate:fresh`).
