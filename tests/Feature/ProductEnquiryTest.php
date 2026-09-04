<?php

namespace Tests\Feature;

use App\Mail\ProductEnquiryNotification;
use App\Models\EmailSetting;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProductEnquiryTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ravi Kapoor',
            'email' => 'ravi@example.com',
            'phone' => '9998887776',
            'message' => 'Please share pricing and lead time.',
        ], $overrides);
    }

    private function seedSettings(bool $productEnabled = true): void
    {
        EmailSetting::query()->create([
            'contact_notification_email' => 'contact@example.com',
            'product_notification_email' => 'sales@example.com',
            'sender_name' => 'Weather Ultima',
            'contact_subject' => 'Contact enquiry',
            'product_subject' => 'Product enquiry',
            'contact_notifications_enabled' => true,
            'product_notifications_enabled' => $productEnabled,
        ]);
    }

    public function test_enquiry_is_stored_with_the_product_snapshot_and_notified(): void
    {
        $this->seedSettings();
        Mail::fake();
        $product = Product::factory()->create(['name' => 'Weather Station Pro']);

        $this->postJson(route('products.enquiry', $product), $this->payload())
            ->assertCreated()
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('product_enquiries', [
            'product_id' => $product->id,
            'product_name' => 'Weather Station Pro',
            'email' => 'ravi@example.com',
        ]);

        Mail::assertSent(ProductEnquiryNotification::class, fn (ProductEnquiryNotification $mail): bool => $mail->hasTo('sales@example.com'));
    }

    public function test_notification_is_skipped_when_product_emails_are_disabled(): void
    {
        $this->seedSettings(productEnabled: false);
        Mail::fake();
        $product = Product::factory()->create();

        $this->postJson(route('products.enquiry', $product), $this->payload())->assertCreated();

        Mail::assertNothingSent();
        $this->assertDatabaseCount('product_enquiries', 1);
    }

    public function test_validation_errors_are_returned(): void
    {
        $product = Product::factory()->create();

        $this->postJson(route('products.enquiry', $product), ['message' => 'hi'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_enquiries_are_rejected_for_inactive_products(): void
    {
        $this->seedSettings();
        $product = Product::factory()->inactive()->create();

        $this->postJson(route('products.enquiry', $product), $this->payload())->assertNotFound();
    }
}
