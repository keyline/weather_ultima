# Project Rules Index

Before planning or editing, find the row whose globs match the file's path and read that rule file.

| Applies to | Rule file |
| --- | --- |
| resources/views/admin/** | .ai/rules/admin.md |
| resources/views/admin/**, resources/views/components/admin/**, resources/css/app.css | .ai/rules/admin-forms.md |
| resources/views/admin/contact-enquiries/**, resources/views/admin/product-enquiries/** | .ai/rules/enquiries.md |
| app/Models/**, database/migrations/** | .ai/rules/settings.md |
| app/Mail/**, app/Rules/**, app/Http/Requests/Concerns/**, app/Http/Controllers/Admin/{Smtp,Brevo,Recaptcha,Settings}*.php, resources/views/admin/settings/**, resources/views/components/recaptcha.blade.php | .ai/rules/settings.md |
| resources/views/layouts/**, resources/views/products.blade.php, resources/views/contact.blade.php, resources/views/home.blade.php | .ai/rules/frontend.md |
| app/Http/Controllers/Admin/{Product,Testimonial}Controller.php, resources/views/admin/{products,testimonials}/** | .ai/rules/content-crud.md |
| app/Http/Controllers/{Home,Admin/Home,Admin/BrandLogo,Admin/CoreValue}*.php, resources/views/admin/home/**, resources/views/home.blade.php | .ai/rules/home.md |
| app/Http/Controllers/{ServicesPageController,Admin/Service}*.php, resources/views/admin/services/**, resources/views/services.blade.php | .ai/rules/services.md |
| routes/web.php | .ai/rules/routes.md |
