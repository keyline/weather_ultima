---
paths:
  - 'app/Http/Controllers/Admin/Home*.php'
  - 'app/Http/Controllers/Admin/BrandLogoController.php'
  - 'app/Http/Controllers/Admin/CoreValueController.php'
  - 'app/Http/Controllers/HomeController.php'
  - 'resources/views/admin/home/**'
  - 'resources/views/home.blade.php'
---

# Homepage content management (§20)

The homepage (`home.blade.php`, served by `HomeController@index`) is **existing design + dynamic content** —
never redesign a section, only swap hardcoded text/loops for DB values with a `?:` / `?? asset(...)` fallback
to the original material asset.

## Data
- **`home_settings`** — single row (`HomeSetting::current()`, all columns nullable). **Banner title + subtitle only** (no banner image — each dimension card carries its own; the shared `banner_image_*` columns still exist in the DB but are unused). Founder name/designation/intro/description/image/signature. Accessors `founder_image_url`, `founder_signature_url`, `founder_paragraphs` (description split on blank lines).
- **`dimension_cards`** — repeatable, the 5 numbered "One Vision" cards (title, description, image nullable, link_url nullable, order, enabled). `enabled()` + `ordered()` scopes, `image_url` accessor (frontend falls back to `asset('material/images/service1.png')`). Managed **on the Top Banner page** (table below the heading form); routes `admin.home.cards.*` (paths under `home/banner/cards/`, no `index` — the banner page is the index; redirects go to `admin.home.banner.edit`). The "Top Banner" sidebar item is active for `admin.home.banner.*` **or** `admin.home.cards.*`. Homepage renders `$dimensionCards->take(3)` in a `row-cols-lg-3` row + `->slice(3)` in a `row-cols-lg-2` row (preserving the 3+2 layout); card number is a running `$cardNumber`.
- **`brand_logos`** — repeatable, powers the "Media Mentions" strip. `enabled()` + `ordered()` scopes, `image_url` accessor. Managed at **Home → Brand Logo** (`admin.home.logo.*`).
- **`core_values`** — repeatable, powers the RAINBOW "Core Values" section. `icon` is a **text glyph** (letter), not an image. `enabled()` + `ordered()`. Managed at **Home → Core Values** (`admin.home.core-values.*`).

## Admin
Routes under `Route::prefix('home')->name('home.')`: `banner.{edit,update}`, `founder.{edit,update}` (single-form),
and `logo` / `core-values` resources (`->except('show')` + `toggle` + `bulk-destroy` declared before the resource,
params `brand_logo` / `core_value`). Banner + founder are single `<form>` pages; logo + core-values clone the
`admin/testimonials/` list+`_form` pattern. Shared bulk JS: `admin/home/partials/bulk-scripts.blade.php`.
Sidebar "Home" group sits right after Dashboard.

## Frontend wiring (in `home.blade.php`)
`HomeController@index` passes `$home`, `$brandLogos`, `$coreValues` (+ `$testimonials`). The five `.dim-card`s all
carry `style="--dim-bg: url('{{ $card->image_url ?? asset('material/images/service1.png') }}')"`. Media
strip uses `@forelse` with a single static fallback. Core Values section is wrapped in `@if ($coreValues->isNotEmpty())`.
Seeders (`HomeSettingSeeder`, `BrandLogoSeeder`, `CoreValueSeeder`) carry the original content so nothing changes visually.
