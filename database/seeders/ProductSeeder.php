<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Weather Station', 'source' => 'product_img1.png', 'short_description' => 'Rugged, field-tested weather stations that capture accurate readings in real conditions.'],
            ['name' => 'Solar Panel', 'source' => 'product_img3.jpg', 'short_description' => 'High-efficiency solar panels engineered for reliable off-grid and hybrid power.'],
            ['name' => 'Solar Equipments', 'source' => 'product_img2.jpg', 'short_description' => 'Charge controllers, mounts and balance-of-system parts to complete any solar build.'],
        ];

        foreach ($products as $product) {
            $source = public_path('material/images/'.$product['source']);
            $imagePath = null;

            if (File::exists($source)) {
                $imagePath = 'products/'.$product['source'];

                if (! Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->put($imagePath, File::get($source));
                }
            }

            Product::query()->firstOrCreate(
                ['name' => $product['name']],
                [
                    'short_description' => $product['short_description'],
                    'image' => $imagePath,
                    'is_active' => true,
                ],
            );
        }
    }
}
