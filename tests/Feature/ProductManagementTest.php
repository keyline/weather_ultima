<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_non_admins_cannot_manage_products(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.products.index'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->get(route('admin.products.create'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_a_product_with_an_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.products.store'), [
                'name' => 'Rain Gauge',
                'short_description' => 'Tipping-bucket rain gauge for accurate rainfall data.',
                'image' => UploadedFile::fake()->image('gauge.png'),
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.products.index'));

        $product = Product::firstWhere('name', 'Rain Gauge');
        $this->assertNotNull($product);
        $this->assertTrue($product->is_active);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_image_is_required_when_creating(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.products.store'), [
                'name' => 'No Image',
                'short_description' => 'Missing the image field.',
            ])
            ->assertSessionHasErrors('image');
    }

    public function test_image_must_be_a_valid_type_and_size(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.products.store'), [
                'name' => 'Bad Image',
                'short_description' => 'Wrong file type.',
                'image' => UploadedFile::fake()->create('brochure.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('image');
    }

    public function test_updating_the_image_replaces_and_deletes_the_old_file(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Sensor',
            'short_description' => 'A sensor.',
            'image' => UploadedFile::fake()->image('old.png'),
        ]);
        $product = Product::firstWhere('name', 'Sensor');
        $oldImage = $product->image;

        $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => 'Sensor',
            'short_description' => 'A better sensor.',
            'image' => UploadedFile::fake()->image('new.png'),
        ])->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertNotSame($oldImage, $product->image);
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($product->image);
        $this->assertSame('A better sensor.', $product->short_description);
    }

    public function test_deleting_a_product_removes_its_image(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Temporary',
            'short_description' => 'Delete me.',
            'image' => UploadedFile::fake()->image('temp.png'),
        ]);
        $product = Product::firstWhere('name', 'Temporary');
        $image = $product->image;

        $this->actingAs($admin)->delete(route('admin.products.destroy', $product))->assertRedirect(route('admin.products.index'));

        $this->assertModelMissing($product);
        Storage::disk('public')->assertMissing($image);
    }

    public function test_admin_can_toggle_a_product_active_state(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($this->admin())
            ->patch(route('admin.products.toggle', $product))
            ->assertRedirect();

        $this->assertFalse($product->fresh()->is_active);
    }
}
