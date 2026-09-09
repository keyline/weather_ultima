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
