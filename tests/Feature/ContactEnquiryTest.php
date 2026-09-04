<?php

namespace Tests\Feature;

use App\Mail\ContactEnquiryNotification;
use App\Models\ContactEnquiry;
use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactEnquiryTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Asha Roy',
            'email' => 'asha@example.com',
            'phone' => '1234567890',
            'subject' => 'Station enquiry',
            'message' => 'Please contact me about a weather station.',
        ], $overrides);
    }

    public function test_enquiry_is_stored_and_a_notification_is_sent(): void
    {
        EmailSetting::query()->create([
            'contact_notification_email' => 'admin@example.com',
            'product_notification_email' => 'admin@example.com',
            'sender_name' => 'Weather Ultima',
            'contact_subject' => 'New enquiry',
            'product_subject' => 'New product enquiry',
            'contact_notifications_enabled' => true,
        ]);
        Mail::fake();

        $this->post('/contact', $this->payload())
            ->assertRedirect('/contact')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('contact_enquiries', ['email' => 'asha@example.com']);
        Mail::assertSent(ContactEnquiryNotification::class, fn (ContactEnquiryNotification $mail): bool => $mail->hasTo('admin@example.com'));
    }

    public function test_ajax_request_receives_a_json_confirmation(): void
    {
        EmailSetting::query()->create([
            'contact_notification_email' => 'admin@example.com',
            'product_notification_email' => 'admin@example.com',
            'sender_name' => 'Weather Ultima',
            'contact_subject' => 'New enquiry',
            'product_subject' => 'New product enquiry',
            'contact_notifications_enabled' => false,
        ]);
        Mail::fake();

        $this->postJson('/contact', $this->payload())
            ->assertCreated()
            ->assertJsonStructure(['message']);

        Mail::assertNothingSent();
    }

    public function test_validation_errors_are_returned(): void
    {
        $this->postJson('/contact', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);
    }

    public function test_notifications_are_skipped_when_disabled(): void
    {
        EmailSetting::query()->create([
            'contact_notification_email' => 'admin@example.com',
            'product_notification_email' => 'admin@example.com',
            'sender_name' => 'Weather Ultima',
            'contact_subject' => 'New enquiry',
            'product_subject' => 'New product enquiry',
            'contact_notifications_enabled' => false,
        ]);
        Mail::fake();

        $this->post('/contact', $this->payload());

        $this->assertDatabaseHas('contact_enquiries', ['email' => 'asha@example.com']);
        Mail::assertNothingSent();
    }

    public function test_admin_can_open_the_enquiries_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        ContactEnquiry::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('admin.contact-enquiries.index'))
            ->assertOk();
    }
}
