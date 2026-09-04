<?php

namespace Tests\Feature;

use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class EmailSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'contact_notification_email' => 'contact@weather.test',
            'product_notification_email' => 'sales@weather.test',
            'sender_name' => 'Weather Ultima',
            'contact_subject' => 'New contact enquiry',
            'product_subject' => 'New product enquiry',
            'contact_notifications_enabled' => '1',
            'product_notifications_enabled' => '1',
        ], $overrides);
    }

    public function test_non_admins_cannot_access_email_settings(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.settings.email.edit'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->put(route('admin.settings.email.update'), $this->payload())->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_save_all_seven_fields(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.email.update'), $this->payload([
                'contact_notification_email' => 'desk@weather.test',
                'product_notifications_enabled' => null,
            ]))
            ->assertRedirect()
            ->assertSessionHas('status');

        $settings = EmailSetting::current();
        $this->assertSame('desk@weather.test', $settings->contact_notification_email);
        $this->assertSame('sales@weather.test', $settings->product_notification_email);
        $this->assertTrue($settings->contact_notifications_enabled);
        $this->assertFalse($settings->product_notifications_enabled);
    }

    public function test_emails_and_subjects_are_required(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.email.update'), [
                'contact_notification_email' => 'not-an-email',
                'product_notification_email' => '',
                'sender_name' => '',
                'contact_subject' => '',
                'product_subject' => '',
            ])
            ->assertSessionHasErrors([
                'contact_notification_email',
                'product_notification_email',
                'sender_name',
                'contact_subject',
                'product_subject',
            ]);
    }

    public function test_the_form_shows_current_values(): void
    {
        EmailSetting::query()->create($this->payload(['contact_notification_email' => 'visible@weather.test']));

        $this->actingAs($this->admin())
            ->get(route('admin.settings.email.edit'))
            ->assertOk()
            ->assertSee('visible@weather.test');
    }
}
