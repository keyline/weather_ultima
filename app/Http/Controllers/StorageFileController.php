<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves uploaded files from the `public` disk when the `public/storage`
 * symlink is missing (typical on manually-uploaded hosting). When the symlink
 * exists the web server hands the file out directly and this is never reached.
 */
class StorageFileController extends Controller
{
    /**
     * Extension => MIME type. Set explicitly so serving never depends on the
     * PHP `fileinfo` extension being enabled.
     *
     * @var array<string, string>
     */
    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
    ];

    public function __invoke(string $path): BinaryFileResponse
    {
        $disk = Storage::disk('public');
        $root = realpath($disk->path(''));
        $file = realpath($disk->path($path));

        abort_unless($root && $file && str_starts_with($file, $root.DIRECTORY_SEPARATOR) && is_file($file), 404);

        $mime = self::MIME_TYPES[strtolower(pathinfo($file, PATHINFO_EXTENSION))] ?? null;

        abort_if($mime === null, 404);

        return response()->file($file, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=604800',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
