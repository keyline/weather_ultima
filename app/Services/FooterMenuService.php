<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use JsonException;

class FooterMenuService
{
    private const ORDER_FILE = 'site-settings/footer-menu-order.json';

    private const DEFAULT_ORDER = ['home', 'about', 'blog', 'products', 'services', 'contact'];

    /**
     * @var array<string, array{label: string, route: ?string, url: ?string}>
     */
    private const MENU_ITEMS = [
        'home' => ['label' => 'Home', 'route' => 'home', 'url' => null],
        'about' => ['label' => 'About', 'route' => 'about', 'url' => null],
        'products' => ['label' => 'Products', 'route' => 'products', 'url' => null],
        'services' => ['label' => 'Services', 'route' => 'services', 'url' => null],
        'contact' => ['label' => 'Contact', 'route' => 'contact', 'url' => null],
        'blog' => ['label' => 'Blog', 'route' => null, 'url' => 'https://weatherultima.com/blog/'],
    ];

    /**
     * @return list<array{key: string, label: string, url: string}>
     */
    public function items(): array
    {
        $items = [];

        foreach ($this->orderFromStorage() as $key) {
            $item = self::MENU_ITEMS[$key];
            $items[] = [
                'key' => $key,
                'label' => $item['label'],
                'url' => $item['route'] ? route($item['route']) : (string) $item['url'],
            ];
        }

        return $items;
    }

    /**
     * @param  array<string, int|string>  $positions
     */
    public function savePositions(array $positions): bool
    {
        $keys = array_keys($positions);

        if (count($positions) !== count(self::DEFAULT_ORDER) || array_diff($keys, self::DEFAULT_ORDER) !== []) {
            return false;
        }

        $values = array_map(static fn (int|string $position): int => (int) $position, array_values($positions));

        if (count(array_unique($values)) !== count(self::DEFAULT_ORDER) || min($values) !== 1 || max($values) !== count(self::DEFAULT_ORDER)) {
            return false;
        }

        usort($keys, static fn (string $left, string $right): int => (int) $positions[$left] <=> (int) $positions[$right]);

        return Storage::disk('local')->put(self::ORDER_FILE, json_encode($keys, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    }

    /**
     * @return list<string>
     */
    private function orderFromStorage(): array
    {
        $disk = Storage::disk('local');

        if (! $disk->exists(self::ORDER_FILE)) {
            return self::DEFAULT_ORDER;
        }

        $contents = $disk->get(self::ORDER_FILE);

        if (! is_string($contents)) {
            return self::DEFAULT_ORDER;
        }

        try {
            $order = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return self::DEFAULT_ORDER;
        }

        if (! is_array($order) || ! array_is_list($order) || count($order) !== count(self::DEFAULT_ORDER)) {
            return self::DEFAULT_ORDER;
        }

        foreach ($order as $key) {
            if (! is_string($key) || ! array_key_exists($key, self::MENU_ITEMS)) {
                return self::DEFAULT_ORDER;
            }
        }

        return count(array_unique($order)) === count(self::DEFAULT_ORDER) ? $order : self::DEFAULT_ORDER;
    }
}
