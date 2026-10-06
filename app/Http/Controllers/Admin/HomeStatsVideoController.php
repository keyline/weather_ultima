<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomeStatsVideoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeStatsVideoController extends Controller
{
    public function edit(): View
    {
        $videoPath = (string) config('homepage.stats_video_path');
        $publicDisk = Storage::disk('public');
        $statsVideoUrl = $publicDisk->exists($videoPath)
            ? route('storage.file', ['path' => $videoPath, 'v' => $publicDisk->lastModified($videoPath)])
            : null;

        return view('admin.home.stats-video', [
            'statsVideoUrl' => $statsVideoUrl,
        ]);
    }

    public function update(UpdateHomeStatsVideoRequest $request): RedirectResponse
    {
        $videoPath = (string) config('homepage.stats_video_path');
        $storedPath = Storage::disk('public')->putFileAs(
            dirname($videoPath),
            $request->file('video'),
            basename($videoPath),
        );

        if ($storedPath === false) {
            return back()->withErrors(['video' => 'The video could not be saved. Please try again.']);
        }

        return redirect()
            ->route('admin.home.stats-video.edit')
            ->with('status', 'Homepage stats video uploaded successfully.');
    }
}
