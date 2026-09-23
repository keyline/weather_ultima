---
paths:
  - 'app/Services/**'
  - 'app/Models/WeatherSetting.php'
  - 'app/Models/InstagramSetting.php'
  - 'app/Http/Controllers/Admin/WeatherSettingController.php'
  - 'app/Http/Controllers/Admin/InstagramSettingController.php'
  - 'resources/views/partials/weather-station-panel.blade.php'
  - 'resources/views/admin/settings/instagram.blade.php'
  - 'resources/views/home.blade.php'
---

# Partials

## Homepage weather station uses Ambient Weather API directly
The homepage "Our Weather Station" section (home.blade.php → partials/weather-station-panel.blade.php, one .wx-station-body panel per tab, toggled by main.js) is fed by App\Services\WeatherStationService, which calls the Ambient Weather REST endpoint https://rt.ambientweather.net/v1/devices via Http:: and caches for 5 min (key weather_station.devices; call ->flush() after credential changes).

Do NOT try to add the jafo232/ambientapi (Jafo232/ambient_api) composer package — it pins guzzle ^6.2 and cannot resolve against Laravel 13.

Credentials + per-station device MAC addresses live in the single-row weather_settings table (WeatherSetting::current(), keys encrypted), managed at Settings → Weather Station (admin.settings.weather.{edit,update,test}). STATIONS const maps tab label → MAC column. A station with no MAC, or is_active off / missing keys, renders the "coming soon" placeholder. Ambient reports imperial; the service converts to °C / km/h / mm / hPa.

## Homepage Instagram section: three sources, in priority order
The homepage "Follow Us on Instagram" section (home.blade.php, `#instagram`) picks its content in this order — the first one that's set up wins:
1. **`InstagramSetting::current()->embed_code`** (`hasEmbedCode()`) — a raw widget snippet (SnapWidget/Elfsight/Behold.so/etc.) pasted by an admin, rendered **unescaped** via `{!! $instagramEmbedCode !!}` in a `.wx-insta-embed` wrapper. This exists because getting a real Meta Graph API token is genuinely painful for non-developers (App + Graph Explorer + `instagram_basic` permission) — a third-party widget only needs a normal "connect your account" login on their site. `$instagramEmbedCode` is passed straight from `HomeController@index` (`InstagramSetting::current()->embed_code`), no escaping/sanitising — safe **only** because it's admin-only input (same trust boundary as SMTP/Brevo credentials), never end-user-supplied. Don't add HTML-escaping here; that would break every widget's `<script>` tag.
2. **Instagram Graph API**, via `App\Services\InstagramFeedService::homepageItems(10)` (called from `HomeController@index`, result in `$instagramPosts`, rendered as `.wx-insta-carousel owl-carousel`) — **not** the `InstagramPost` model directly. `latest()` pulls live media from `https://graph.facebook.com/{version}/{ig-user-id}/media` via `Http::`, cached 30 min (key `instagram_feed.media`; call `->flush()` after credential changes, done automatically in `InstagramSettingController@update`/`test`).
3. The manually curated `InstagramPost::enabled()->ordered()` fallback records (see `.ai/rules/home.md`) — used when the Graph API isn't configured, `is_active` is off, or the request fails (failures are caught and reported, never break the homepage), normalised to the same `{image_url, link_url}` shape as the Graph API result so `home.blade.php`'s carousel branch doesn't care which one it got.

All three fields (`embed_code`, `access_token` encrypted, `instagram_user_id`, cached `username`, optional `token_expires_at` for an admin renewal warning) live in the single-row `instagram_settings` table (`InstagramSetting::current()`), managed at Settings → Instagram Feed (`admin.settings.instagram.{edit,update,test}`) — one form, one save button, both the embed textarea and the Graph API fields. Getting a Graph API token requires a Meta developer app + Graph API Explorer (`instagram_basic` permission) against an Instagram Business/Creator account linked to a Facebook Page — there is no in-app OAuth flow, the admin generates and pastes the token manually and must renew it before the ~60-day expiry. That's why the embed-code path exists and is checked first.
