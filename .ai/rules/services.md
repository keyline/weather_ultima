---
paths:
  - 'app/Http/Controllers/ServicesPageController.php'
  - 'app/Http/Controllers/Admin/Service*.php'
  - 'resources/views/admin/services/**'
  - 'resources/views/services.blade.php'
---

# Services page (§Services)

`/services` → `ServicesPageController@show` → `resources/views/services.blade.php`. **Existing design +
dynamic content** — never redesign a section, only swap hardcoded text/loops for DB values with a
`?:` / default fallback. The tab/accordion JS in `public/material/js/main.js` keys entirely off
`data-service-target` (tab + accordion toggle), `data-service-body` (accordion body) and panel
`id="service-<slug>"` — all four MUST use `$service->slug` consistently per render.

## Data
- **`service_page_settings`** — single row (`ServicePageSetting::current()`, all nullable): `banner_title`, `intro_heading`, `intro_body`, `intro_statement`. Accessor `intro_paragraphs` (split on blank lines). Edited by the form at the top of the admin Services page (`admin.services.page.update`).
- **`services`** — repeatable. Fields: `name`, `slug` (auto — `booted()` sets `Str::slug(name)` + numeric suffix when taken, on create or when `name` changes; **not** user input), `category`, `tags`, `statement` (italic lead), `body` (blank-line paragraphs → `body_paragraphs` accessor), `result` (italic close), `display_order`, `is_enabled`. `enabled()` + `ordered()` scopes.
- **`service_images`** — flexible gallery per service (`Service::images()` ordered). Files on the `public` disk under `services/`.

## Admin
Route names `admin.services.*` (nav "Services" leaf, active `routeIs('admin.services.*')`). One menu:
the index page carries the page-content form **and** the services table (Add / Edit / Delete /
Enable-Disable / bulk / search / paginate 20). Service `create`/`edit` share `_form.blade.php`;
`edit` also has an **Images** panel — `POST .images.store` (add one), `PUT .images.update` (bulk
reorder + alt via an `images[i][id|display_order|alt_text]` form), `DELETE .images.destroy` (one;
`destroyImage` does `abort_unless($serviceImage->service_id === $service->id, 404)` — no scoped
binding because the relation is `images()` not `serviceImages()`). Deleting a service deletes its
image files first (FK cascade handles rows).

Seeders `ServicePageSettingSeeder` + `ServiceSeeder` carry the original 7 services + text + 4 shared
theme images each so nothing changes visually.
