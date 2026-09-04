---
paths:
  - 'resources/views/admin/contact-enquiries/**'
  - 'resources/views/admin/product-enquiries/**'
  - 'app/Http/Controllers/Admin/ContactEnquiryController.php'
  - 'app/Http/Controllers/Admin/ProductEnquiryController.php'
---

# Enquiries admin

Contact and Product enquiry lists share a pattern:

- Detail view is a **client-side modal** (`resources/views/admin/partials/enquiry-modal.blade.php`), populated from each row's `data-enquiry` JSON (`@json`) by `admin/partials/enquiry-scripts.blade.php`. No `show` route.
- Bulk delete + Excel export: `export` (GET) and `bulk` (DELETE) routes are declared **before** the `Route::resource(...)` in `routes/web.php` so `{...}` params don't swallow them. "Export selected" is a submit button with `formaction`/`formmethod="GET"` on the bulk form.
- Controllers: `index` paginates (per_page 10/20/50/100, default 20, invalid → 20), `bulkDestroy` via a `BulkDelete*Request` (admin role + `exists:` rule), `export` builds an `*Export` (Maatwebsite\Excel, `WithMapping`) named `<type>-YYYY-MM-DD-His.xlsx`.
- **No filter UI** on either list (owner decision) — just the table, "Showing X–Y of Z", pagination and Export All. The controllers still honour `?search=`, `?from=`, `?to=`, `?per_page=` query params (used by tests and deep links); do not re-add the filter form without being asked.
- **No Status column / field** on either. `contact_enquiries` and `product_enquiries` have no `status`.

## Unread notifications (`is_read`)
Both tables have `is_read` (bool, default false, indexed) — notification tracking only, **not** a visible column. New submissions are unread.
- `EnquiryNotificationController::summary()` → `['contact'=>N,'product'=>M,'total'=>X]` (unread counts). Injected into every `admin.*` view as `$enquiryNotifications` by a composer, and served as JSON at `route('admin.enquiry-notifications')`.
- Sidebar badges (`data-badge="contact|product|products-group"`), header bell (`data-badge="bell*"`), and the row unread-dot (`[data-unread-dot]`) all read from it. Hidden when count is 0.
- Opening the detail modal fires `PATCH admin/{type}-enquiries/{id}/read` (`*.read` routes, before the resource) which sets `is_read=true` via `forceFill` and returns the fresh summary; `enquiry-scripts.blade.php` then calls `window.applyEnquiryCounts(...)` to update every badge without a reload. The admin layout also polls the JSON endpoint every 45s and on tab focus.
- Dashboard: `$enquiryNotifications` drives the "New enquiries received" banner; `AdminDashboardController` also passes `$recentEnquiries` (latest 6 across both tables) for the "Recent enquiries" list.
