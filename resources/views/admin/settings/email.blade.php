@extends ('admin.layouts.app')
@section ('title', 'Email Settings')
@section ('page-title', 'Email settings')

@section ('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <p class="text-sm text-slate-500">
            Configure where enquiry notifications are sent. The outgoing mail server is configured under
            <a href="{{ route('admin.settings.smtp.edit') }}" class="font-semibold text-[#0b376d] underline">SMTP Settings</a>.
        </p>

        @if (session('status'))
            <div class="admin-alert admin-alert--success">
                <i class="fa-solid fa-circle-check mt-0.5"></i> <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>Please fix the highlighted fields below.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.email.update') }}" class="space-y-6">
            @csrf
            @method ('PUT')

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Sender</h2>
                <x-admin.input
                    name="sender_name"
                    label="Email sender name"
                    :value="$settings->sender_name"
                    placeholder="Name shown as the sender, e.g. Weather Ultima"
                    required
                />
            </section>

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Contact enquiries</h2>
                <x-admin.input
                    type="email"
                    name="contact_notification_email"
                    label="Notification email"
                    :value="$settings->contact_notification_email"
                    placeholder="Enter email address for contact enquiry alerts"
                    required
                />
                <x-admin.input
                    name="contact_subject"
                    label="Email subject"
                    :value="$settings->contact_subject"
                    placeholder="Enter the subject line for contact enquiry emails"
                    required
                />
                <label class="flex items-center gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="contact_notifications_enabled" value="1" @checked(old('contact_notifications_enabled', $settings->contact_notifications_enabled)) class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
                    <span>Send an email when a contact enquiry is submitted</span>
                </label>
            </section>

            <section class="admin-section space-y-4">
                <h2 class="admin-section-title">Product enquiries</h2>
                <x-admin.input
                    type="email"
                    name="product_notification_email"
                    label="Notification email"
                    :value="$settings->product_notification_email"
                    placeholder="Enter email address for product enquiry alerts"
                    required
                />
                <x-admin.input
                    name="product_subject"
                    label="Email subject"
                    :value="$settings->product_subject"
                    placeholder="Enter the subject line for product enquiry emails"
                    required
                />
                <label class="flex items-center gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="product_notifications_enabled" value="1" @checked(old('product_notifications_enabled', $settings->product_notifications_enabled)) class="h-4 w-4 rounded border-slate-300 text-[#0b376d] focus:ring-[#0b376d]" />
                    <span>Send an email when a product enquiry is submitted</span>
                </label>
            </section>

            <div class="flex justify-end">
                <button type="submit" class="admin-btn admin-btn--primary" data-submit>Save settings</button>
            </div>
        </form>
    </div>
@endsection
