<?php

namespace App\Services;

use App\Models\InstagramPost;
use App\Models\InstagramSetting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

/**
 * Reads the latest media from the Instagram Graph API
 * (https://developers.facebook.com/docs/instagram-platform/instagram-graph-api)
 * for the homepage "Follow Us on Instagram" section. Requires an Instagram
 * Business/Creator account and a long-lived access token, configured at
 * Settings → Instagram Feed.
 */
class InstagramFeedService
{
    private const API_VERSION = 'v21.0';

    private const CACHE_KEY = 'instagram_feed.media';

    /**
     * Live posts don't need to be second-by-second fresh, and the Graph API
     * rate-limits by app — cache for half an hour.
     */
    private const CACHE_TTL = 1800;

    /**
     * The latest posts for the homepage grid: live Instagram media when
     * configured and reachable, otherwise the manually curated fallback
     * photos — so the section always has something to show once an admin
     * has set either one up.
     *
     * @return Collection<int, object{image_url: string, link_url: ?string}>
     */
    public function homepageItems(int $limit = 10): Collection
    {
        $live = $this->latest($limit);

        if ($live->isNotEmpty()) {
            return $live;
        }

        return InstagramPost::query()->enabled()->ordered()->take($limit)->get()
            ->map(fn (InstagramPost $post): object => (object) [
                'image_url' => $post->image_url,
                'link_url' => $post->link_url,
            ]);
    }

    /**
     * @return Collection<int, object{image_url: string, link_url: ?string}>
     */
    public function latest(int $limit = 10): Collection
    {
        $settings = InstagramSetting::current();

        if (! $settings->isConfigured()) {
            return collect();
        }

        try {
            return $this->cachedMedia($settings, $limit);
        } catch (Throwable $exception) {
            report($exception);

            return collect();
        }
    }

    /**
     * Live connectivity check for the admin settings screen. Bypasses the
     * cache and confirms the saved token/account can reach the API.
     *
     * @return array{ok: bool, message: string}
     */
    public function probe(): array
    {
        $settings = InstagramSetting::current();

        if (! $settings->hasCredentials()) {
            return ['ok' => false, 'message' => 'Save an access token and an Instagram Business Account ID first, then test the connection.'];
        }

        try {
            $response = Http::timeout(8)
                ->retry(2, 500, throw: false)
                ->get($this->endpoint($settings->instagram_user_id), [
                    'fields' => 'username,media_count',
                    'access_token' => $settings->access_token,
                ]);
        } catch (ConnectionException $exception) {
            return ['ok' => false, 'message' => 'Could not reach the Instagram Graph API: '.$exception->getMessage()];
        }

        if ($response->failed()) {
            return ['ok' => false, 'message' => 'Instagram Graph API error: '.$this->apiErrorMessage($response->json())];
        }

        $username = $response->json('username');
        $mediaCount = $response->json('media_count');

        if (filled($username)) {
            $settings->update(['username' => $username]);
        }

        $this->flush();

        return ['ok' => true, 'message' => "Connected as @{$username}. {$mediaCount} post(s) available."];
    }

    /**
     * Drop the cached media payload — called after the credentials change.
     */
    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return Collection<int, object{image_url: string, link_url: ?string}>
     */
    private function cachedMedia(InstagramSetting $settings, int $limit): Collection
    {
        $payload = Cache::remember(
            self::CACHE_KEY,
            self::CACHE_TTL,
            fn (): array => $this->fetchMedia($settings, $limit)->all(),
        );

        return collect($payload);
    }

    /**
     * @return Collection<int, object{image_url: string, link_url: ?string}>
     */
    private function fetchMedia(InstagramSetting $settings, int $limit): Collection
    {
        try {
            $response = Http::timeout(8)
                ->retry(2, 500, throw: false)
                ->get($this->endpoint($settings->instagram_user_id.'/media'), [
                    'fields' => 'media_type,media_url,thumbnail_url,permalink',
                    'limit' => $limit,
                    'access_token' => $settings->access_token,
                ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException($exception->getMessage(), previous: $exception);
        }

        if ($response->failed()) {
            throw new RuntimeException('Instagram Graph API returned: '.$this->apiErrorMessage($response->json()));
        }

        return collect($response->json('data', []))
            ->filter(fn ($item): bool => is_array($item))
            ->map(function (array $item): ?object {
                $image = $item['media_type'] === 'VIDEO'
                    ? ($item['thumbnail_url'] ?? null)
                    : ($item['media_url'] ?? null);

                if (blank($image)) {
                    return null;
                }

                return (object) [
                    'image_url' => $image,
                    'link_url' => $item['permalink'] ?? null,
                ];
            })
            ->filter()
            ->take($limit)
            ->values();
    }

    private function endpoint(string $path): string
    {
        return 'https://graph.facebook.com/'.self::API_VERSION.'/'.$path;
    }

    /**
     * @param  mixed  $payload
     */
    private function apiErrorMessage($payload): string
    {
        return is_array($payload) ? data_get($payload, 'error.message', 'unknown error') : 'unknown error';
    }
}
