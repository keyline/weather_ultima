@php
    /** @var string $station */
    /** @var array<string, mixed>|null $reading */
    $isLive = is_array($reading ?? null) && ($reading['available'] ?? false);

    $stats = $isLive
        ? [
            ['icon' => 'icon1.svg', 'label' => 'Temperature', 'value' => $reading['temperature']],
            ['icon' => 'icon2.svg', 'label' => 'Feels Like', 'value' => $reading['feels_like']],
            ['icon' => 'icon3.svg', 'label' => 'Humidity', 'value' => $reading['humidity']],
            ['icon' => 'icon4.svg', 'label' => 'Wind Speed', 'value' => $reading['wind_speed']],
            ['icon' => 'icon5.svg', 'label' => 'Hourly Rainfall', 'value' => $reading['hourly_rainfall']],
            ['icon' => 'icon6.svg', 'label' => 'Daily Rainfall', 'value' => $reading['daily_rainfall']],
        ]
        : [];
@endphp

@if ($isLive)
    <div class="wx-station-map">
        <img src="{{ $imageUrl ?? asset('material/images/service1.png') }}" alt="{{ $station }} weather station" />
    </div>
    <div class="wx-station-grid">
        @foreach ($stats as $stat)
            <div class="wx-station-stat">
                <img src="images/{{ $stat['icon'] }}" alt="" class="wx-station-icon" />
                <p class="wx-station-label">{{ $stat['label'] }}</p>
                <p class="wx-station-value">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>
    @if (! empty($reading['observed_for_humans']))
        <p class="wx-station-updated">
            Live from our {{ $station }} station &middot; updated {{ $reading['observed_for_humans'] }}
        </p>
    @endif
@else
    <p class="wx-station-empty">Live station data for {{ $station }} is coming soon.</p>
@endif
