@extends ('admin.layouts.app')
@section ('title', 'Homepage stats video')
@section ('page-title', 'Home · Stats video')

@section ('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <p class="text-sm text-slate-600">Manage the autoplaying video beside the homepage statistics. Uploading a new MP4 replaces the current clip without changing database content.</p>

        @if (session('status'))
            <div class="admin-alert admin-alert--success"><i class="fa-solid fa-circle-check mt-0.5"></i> <span>{{ session('status') }}</span></div>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert--error"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> <span>Please fix the video field below.</span></div>
        @endif

        <section class="admin-section space-y-5">
            <div>
                <h2 class="admin-section-title">Homepage numbers video</h2>
                <p class="admin-hint">The video plays muted and loops automatically on the public homepage.</p>
            </div>

            @if ($statsVideoUrl)
                <div class="space-y-2">
                    <p class="admin-label">Current uploaded video</p>
                    <video src="{{ $statsVideoUrl }}" controls muted playsinline preload="metadata" class="block aspect-video w-full max-w-3xl rounded-md border border-slate-200 bg-slate-950 object-contain"></video>
                </div>
            @else
                <p class="rounded border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">No custom video is uploaded yet. The homepage is showing the weather-station demo image.</p>
            @endif

            <form method="POST" action="{{ route('admin.home.stats-video.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method ('PUT')

                <div>
                    <label for="stats-video" class="admin-label">Upload MP4 video <span class="admin-required">*</span></label>
                    <input id="stats-video" name="video" type="file" accept="video/mp4,.mp4" required class="admin-file" />
                    @error ('video')
                        <p class="admin-error">{{ $message }}</p>
                    @enderror
                    <p class="admin-hint">MP4 only, up to 50 MB. A new upload replaces the current homepage video.</p>
                </div>

                <div class="flex justify-end border-t border-slate-100 pt-5">
                    <button type="submit" class="admin-btn admin-btn--primary"><i class="fa-solid fa-upload"></i> Upload video</button>
                </div>
            </form>
        </section>
    </div>
@endsection
