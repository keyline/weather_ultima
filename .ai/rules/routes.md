---
paths:
  - routes/web.php
---

# Routes

## Enquiry collection routes go before the resource
For both `contact-enquiries` and `product-enquiries`, the `export` (GET) and `bulk` (DELETE) routes must be declared **before** `Route::resource(...)` or the `{...}` param patterns swallow them.

## Map
- Public: `home` (`/`), `products` (`ProductController@index`), `products.enquiry` (`POST products/{product}/enquiry`, `throttle:enquiry`), `services`, `contact` + `contact.store` (`throttle:enquiry`).
- Admin (`auth`+`admin`): `admin.dashboard`; `admin.products.*` (resource minus show) + `admin.products.toggle` (PATCH); `admin.product-enquiries.{index,destroy,export,bulk-destroy}`; `admin.contact-enquiries.{index,destroy,export,bulk-destroy}`; `admin.settings.email.{edit,update}` + `admin.settings.smtp.{edit,update,test}` + `admin.settings.site.{edit,update}` (under `settings/` prefix); `admin.logout`.

The `enquiry` rate limiter (3/min by email+ip) is defined in `AppServiceProvider` and shared by both public enquiry endpoints.
