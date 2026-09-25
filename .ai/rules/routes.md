---
paths:
  - routes/web.php
---

# Routes

## Enquiry collection routes go before the resource
For both `contact-enquiries` and `product-enquiries`, the `export` (GET) and `bulk` (DELETE) routes must be declared **before** `Route::resource(...)` or the `{...}` param patterns swallow them.

## Uploaded images work without a `public/storage` symlink
The site is deployed by manual file upload, so the `storage:link` symlink often doesn't exist on the server and every `asset('storage/...')` upload URL 404s. `Route::get('storage/{path}')` and `public/storage/{path}` → `StorageFileController` serve files straight from the `public` disk as a fallback (only reached when no physical file/symlink exists; MIME by extension map, never `fileinfo`; realpath-guarded against traversal). This depends on the `local` disk having `'serve' => false` in `config/filesystems.php` — Laravel's own `storage.local` route owns `/storage/{path}` otherwise and answers 403. Keep both routes declared before `/`.

## Map
- Public: `home` (`/`), `products` (`ProductController@index`), `products.enquiry` (`POST products/{product}/enquiry`, `throttle:enquiry`), `services`, `contact` + `contact.store` (`throttle:enquiry`).
- Admin (`auth`+`admin`): `admin.dashboard`; `admin.products.*` (resource minus show) + `admin.products.toggle` (PATCH); `admin.product-enquiries.{index,destroy,export,bulk-destroy}`; `admin.contact-enquiries.{index,destroy,export,bulk-destroy}`; `admin.settings.email.{edit,update}` + `admin.settings.smtp.{edit,update,test}` + `admin.settings.site.{edit,update}` (under `settings/` prefix); `admin.logout`.

The `enquiry` rate limiter (3/min by email+ip) is defined in `AppServiceProvider` and shared by both public enquiry endpoints.
