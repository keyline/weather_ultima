<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Throwable;

class MaintenanceController extends Controller
{
    /**
     * Cache-clearing Artisan commands run for the admin, in order. All are
     * non-destructive: they only delete generated/compiled files so freshly
     * uploaded views, config and routes take effect immediately.
     *
     * @var list<string>
     */
    private const CACHE_COMMANDS = [
        'config:clear',
        'route:clear',
        'view:clear',
        'cache:clear',
    ];

    public function edit(): View
    {
        return view('admin.settings.maintenance');
    }

    /**
     * Apply any database migrations that were uploaded but not yet run. Only
     * adds new tables/columns — `migrate` never drops or rewrites existing data.
     */
    public function migrate(): RedirectResponse
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('maintenance_error', 'Migration failed: '.$exception->getMessage());
        }

        $output = trim(preg_replace('/\s+/', ' ', strip_tags(Artisan::output())));

        return back()->with('status', 'Database updated. '.$output);
    }

    public function clearCache(): RedirectResponse
    {
        $cleared = [];
        $failed = [];

        foreach (self::CACHE_COMMANDS as $command) {
            try {
                Artisan::call($command);
                $cleared[] = $command;
            } catch (Throwable $exception) {
                report($exception);
                $failed[] = $command;
            }
        }

        if ($failed !== []) {
            return back()->with('maintenance_error', 'Cleared: '.implode(', ', $cleared).'. Failed: '.implode(', ', $failed).'.');
        }

        return back()->with('status', 'Application caches cleared ('.implode(', ', $cleared).'). Your latest uploaded changes are now live.');
    }
}
