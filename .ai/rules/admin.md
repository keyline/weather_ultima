---
paths:
  - 'resources/views/admin/**'
---

# Admin

## Sidebar is route-driven with collapsible groups
`admin/layouts/app.blade.php` builds a `$navigation` array. Leaf items have `url` + `active` (`request()->routeIs(...)`). Group items have `children` (array of leaves) and render as a native `<details>` that is `open` when any child is active. To add a menu, append to the array — never hardcode active classes.

- Active leaf `<a>` gets `data-nav="{Label}"`, class `nav-link--active`, and `aria-current="page"`. Groups get `data-nav-group="{Label}"`. `AdminNavigationTest` asserts on these hooks.
- There is **no** Users/Reports menu and **no** status concept anywhere in the admin.
- `$siteSettings` is available in every `admin.*` view (composer in `AppServiceProvider`) — use `$siteSettings->display_name`, `header_logo_url`, `favicon_url`.

## Route names
`admin.dashboard`, `admin.products.*` (+ `admin.products.toggle`), `admin.product-enquiries.*`, `admin.contact-enquiries.*`, `admin.testimonials.*` (+ `admin.testimonials.toggle` / `.bulk-destroy`), `admin.settings.email.*`, `admin.settings.smtp.*`, `admin.settings.site.*`, `admin.enquiry-notifications`.

Sidebar order: Dashboard · Home (Brand Logo, Top Banner, About Founder, Core Values) · Products (Products, Product Enquiries) · Testimonials · Services · Contact Enquiries · Settings (Email, SMTP, Site). Home routes: `admin.home.{banner,founder,logo,core-values}.*` — see `.ai/rules/home.md`.
