@extends ('admin.layouts.app')
@section ('title', 'Maintenance')
@section ('page-title', 'Maintenance')

@section ('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <p class="text-sm text-slate-500">
            Housekeeping tools for this site. Run these after uploading updated files to the server so your
            changes appear straight away instead of waiting on cached copies.
        </p>

        @if (session('status'))
            <div class="admin-alert admin-alert--success">
                <i class="fa-solid fa-circle-check mt-0.5"></i> <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('maintenance_error'))
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>{{ session('maintenance_error') }}</span>
            </div>
        @endif

        <section class="admin-section space-y-4">
            <h2 class="admin-section-title">Update the database</h2>
            <p class="admin-hint">
                Applies database changes that came with newly uploaded files (new tables or columns). Use it if a page
                shows an &ldquo;Unknown column&rdquo; or &ldquo;table doesn&rsquo;t exist&rdquo; error after an update.
                It only <span class="font-semibold">adds</span> what is missing &mdash; existing data is never deleted, and
                running it when nothing is pending does nothing.
            </p>
            <form method="POST" action="{{ route('admin.settings.maintenance.migrate') }}" onsubmit="return confirm('Apply pending database updates now?');">
                @csrf
                <button type="submit" class="admin-btn admin-btn--primary" data-submit>
                    <i class="fa-solid fa-database"></i> Update database now
                </button>
            </form>
        </section>

        <section class="admin-section space-y-4">
            <h2 class="admin-section-title">Clear application caches</h2>
            <p class="admin-hint">
                Deletes the compiled Blade views, cached configuration, cached routes and the application cache.
                This is safe &mdash; nothing is lost, the files are rebuilt automatically on the next page load.
                Use it whenever a text or layout change you uploaded is not showing on the website.
            </p>
            <form method="POST" action="{{ route('admin.settings.maintenance.clear-cache') }}">
                @csrf
                <button type="submit" class="admin-btn admin-btn--primary" data-submit>
                    <i class="fa-solid fa-broom"></i> Clear caches now
                </button>
            </form>
        </section>
    </div>
@endsection
