<?php

namespace App\Services;

use App\Http\Requests\Admin\UpdateAboutPageRequest;
use App\Models\AboutPageSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class AboutPageContentUpdater
{
    public function update(UpdateAboutPageRequest $request, AboutPageSetting $setting): void
    {
        $section = $request->validated('section');
        $validated = $request->safe()->only(['content', 'uploads', 'remove_images']);
        $content = $this->mergeContent($setting->pageContent(), $validated['content']);

        if ($section === 'banner-intro') {
            $content['intro']['paragraphs'] = $this->paragraphs($content['intro']['body'] ?? '');
            unset($content['intro']['body']);
        }

        if (in_array($section, ['mission', 'vision'], true)) {
            foreach ($content[$section]['cards'] as &$card) {
                $card['paragraphs'] = $this->paragraphs($card['body'] ?? '');
                unset($card['body']);
            }
            unset($card);
            $content[$section]['cards'] = array_values($content[$section]['cards']);
        }

        if ($section === 'founder') {
            $content['founder']['paragraphs'] = $this->paragraphs($content['founder']['body'] ?? '');
            unset($content['founder']['body']);
        }

        if ($section === 'team') {
            foreach ($content['team'] as &$member) {
                $member['paragraphs'] = $this->paragraphs($member['bio'] ?? '');
                unset($member['bio']);
            }
            unset($member);
            $content['team'] = array_values($content['team']);
        }

        if ($section === 'northstar') {
            $content['northstar'] = array_values($content['northstar']);
        }

        $oldImages = $this->imagePaths($setting->pageContent());
        $newUploads = [];
        $uploads = $validated['uploads'] ?? [];

        try {
            $this->applyUploads($content, $uploads, $newUploads);
            $this->applyRemovals($content, $validated['remove_images'] ?? [], $uploads);
            $setting->fill(['content' => $content])->save();
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newUploads);
            throw $exception;
        }

        $removedImages = array_diff($oldImages, $this->imagePaths($content));
        if ($removedImages !== []) {
            Storage::disk('public')->delete(array_values($removedImages));
        }
    }

    /** @return list<string> */
    private function paragraphs(string $text): array
    {
        $text = trim($text);

        return $text === '' ? [] : preg_split('/\r?\n\s*\r?\n/', $text);
    }

    /**
     * @param  array<string, mixed>  $content
     * @param  array<string, mixed>  $uploads
     * @param  list<string>  $newUploads
     */
    private function applyUploads(array &$content, array $uploads, array &$newUploads): void
    {
        foreach (['banner', 'intro', 'founder'] as $section) {
            if (($uploads[$section] ?? null) instanceof UploadedFile) {
                $content[$section]['image'] = $this->store($uploads[$section], $newUploads);
            }
        }

        foreach (['mission', 'vision'] as $section) {
            foreach (($uploads[$section]['cards'] ?? []) as $index => $cardUploads) {
                if (($cardUploads['image'] ?? null) instanceof UploadedFile && isset($content[$section]['cards'][$index])) {
                    $content[$section]['cards'][$index]['image'] = $this->store($cardUploads['image'], $newUploads);
                }
            }
        }

        foreach (['team', 'northstar'] as $section) {
            foreach (($uploads[$section] ?? []) as $index => $itemUploads) {
                if (($itemUploads['image'] ?? null) instanceof UploadedFile && isset($content[$section][$index])) {
                    $content[$section][$index]['image'] = $this->store($itemUploads['image'], $newUploads);
                }
            }
        }
    }

    /**
     * @param  array<string, mixed>  $existing
     * @param  array<string, mixed>  $submitted
     * @return array<string, mixed>
     */
    private function mergeContent(array $existing, array $submitted): array
    {
        foreach ($submitted as $key => $value) {
            if (is_array($value) && is_array($existing[$key] ?? null)) {
                if (array_is_list($value)) {
                    $existing[$key] = array_map(
                        fn (array $item, int $index): array => $this->mergeContent($existing[$key][$index] ?? [], $item),
                        $value,
                        array_keys($value),
                    );
                } else {
                    $existing[$key] = $this->mergeContent($existing[$key], $value);
                }
            } else {
                $existing[$key] = $value;
            }
        }

        return $existing;
    }

    /** @param list<string> $newUploads */
    private function store(UploadedFile $upload, array &$newUploads): string
    {
        $path = $upload->store('about', 'public');
        if (! is_string($path)) {
            throw new RuntimeException('The uploaded image could not be saved.');
        }

        $newUploads[] = $path;

        return $path;
    }

    /**
     * @param  array<string, mixed>  $content
     * @param  array<string, mixed>  $removals
     * @param  array<string, mixed>  $uploads
     */
    private function applyRemovals(array &$content, array $removals, array $uploads): void
    {
        foreach (['banner', 'intro', 'founder'] as $section) {
            if (($removals[$section] ?? false) && ! isset($uploads[$section])) {
                $content[$section]['image'] = null;
            }
        }

        foreach (['mission', 'vision'] as $section) {
            foreach ($content[$section]['cards'] as $index => &$card) {
                $key = $section.'-cards-'.$index;
                if (($removals[$key] ?? false) && ! isset($uploads[$section]['cards'][$index]['image'])) {
                    $card['image'] = null;
                }
            }
            unset($card);
        }

        foreach (['team', 'northstar'] as $section) {
            foreach ($content[$section] as $index => &$item) {
                $key = $section.'-'.$index;
                if (($removals[$key] ?? false) && ! isset($uploads[$section][$index]['image'])) {
                    $item['image'] = null;
                }
            }
            unset($item);
        }
    }

    /** @return list<string> */
    private function imagePaths(array $content): array
    {
        $images = [];
        $collect = function (mixed $value, ?string $key = null) use (&$collect, &$images): void {
            if (is_array($value)) {
                foreach ($value as $childKey => $item) {
                    $collect($item, (string) $childKey);
                }
            } elseif ($key === 'image' && is_string($value) && Str::startsWith($value, 'about/')) {
                $images[] = $value;
            }
        };
        $collect($content);

        return array_values(array_unique($images));
    }
}
