---
paths:
  - 'resources/views/layouts/**'
  - 'resources/views/products.blade.php'
  - 'resources/views/contact.blade.php'
  - 'resources/views/partials/**'
---

# Public frontend

The live site uses the **top-level** views (`resources/views/{home,products,services,contact}.blade.php` + `layouts/`). The old `resources/views/frontend/` duplicate tree has been **deleted** — do not recreate it.

## Site settings everywhere
`$siteSettings` is injected into `layouts.*`, `errors.*` / `errors::*`, `home`, `products`, `services`, `contact` by the `AppServiceProvider` composer. Use `$siteSettings->display_name` for titles, `favicon_url` in `layouts/head.blade.php`, `header_logo_url`/`footer_logo_url` in header/footer, contact fields + `social_links` in the footer and contact cards.

## Error pages
`resources/views/errors/404.blade.php` extends `layouts.app` (full site header + footer) — the framework renders it as the namespaced view `errors::404`, so the composer needs **both** `errors.*` and `errors::*`. Error-specific styles are scoped via `@push('styles')` (frontend has no Vite/Tailwind — never `@vite` here). Add sibling `errors/{403,419,429,500,503}.blade.php` the same way if needed.

## Products page is dynamic
`/products` → `ProductController@index` passes active products. Cards are rendered from the DB (`$product->image_url`, `name`, `short_description`); the "Enquire Now" button carries `data-product-name` + `data-enquiry-url` (`route('products.enquiry', $product)`). Empty state when there are none.

## AJAX enquiry forms + Thank-You modal
Both the contact form and the product-enquiry modal form carry `data-ajax-enquiry` and a `<div data-form-error>`. `partials/enquiry-ajax.blade.php` (`@once @push('scripts')`) fetches with `Accept: application/json`, on success closes any parent `.modal` and shows `#wxThankYouModal` (`partials/thank-you-modal.blade.php`, branded, animated), on 422 lists field errors, on 429/5xx shows a generic message. Controllers return `response()->json(['message' => ...], 201)` when `$request->expectsJson()`, else redirect-with-status.
