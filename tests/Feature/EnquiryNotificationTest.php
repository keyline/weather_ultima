<?php

namespace Tests\Feature;

use App\Models\ContactEnquiry;
use App\Models\Product;
use App\Models\ProductEnquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class EnquiryNotificationTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_new_enquiries_are_unread_by_default(): void
    {
        $this->post('/contact', [
            'name' => 'Asha', 'email' => 'asha@example.com', 'subject' => 'Hi', 'message' => 'Hello there team',
        ]);

        $this->assertDatabaseHas('contact_enquiries', ['email' => 'asha@example.com', 'is_read' => false]);
    }

    public function test_counts_endpoint_returns_separate_unread_totals(): void
    {
        ContactEnquiry::factory()->count(3)->create(['is_read' => false]);
        ContactEnquiry::factory()->create(['is_read' => true]);
        ProductEnquiry::factory()->count(2)->create(['is_read' => false]);

        $this->actingAs($this->admin())
            ->getJson(route('admin.enquiry-notifications'))
            ->assertOk()
            ->assertExactJson(['contact' => 3, 'product' => 2, 'total' => 5]);
    }

    public function test_counts_endpoint_is_admin_only(): void
    {
        $this->getJson(route('admin.enquiry-notifications'))->assertUnauthorized();

        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get(route('admin.enquiry-notifications'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_marking_a_contact_enquiry_read_updates_the_counts(): void
    {
        $enquiries = ContactEnquiry::factory()->count(3)->create(['is_read' => false]);

        $this->actingAs($this->admin())
            ->patchJson(route('admin.contact-enquiries.read', $enquiries->first()))
            ->assertOk()
            ->assertJson(['contact' => 2, 'product' => 0, 'total' => 2]);

        $this->assertTrue($enquiries->first()->fresh()->is_read);
    }

    public function test_marking_a_product_enquiry_read_updates_the_counts(): void
    {
        $product = Product::factory()->create();
        $enquiries = ProductEnquiry::factory()->count(2)->forProduct($product)->create(['is_read' => false]);

        $this->actingAs($this->admin())
            ->patchJson(route('admin.product-enquiries.read', $enquiries->first()))
            ->assertOk()
            ->assertJson(['product' => 1, 'total' => 1]);

        $this->assertTrue($enquiries->first()->fresh()->is_read);
    }

    public function test_mark_read_is_admin_only(): void
    {
        $enquiry = ContactEnquiry::factory()->create(['is_read' => false]);

        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->patch(route('admin.contact-enquiries.read', $enquiry))
            ->assertRedirect(route('admin.login'));

        $this->assertFalse($enquiry->fresh()->is_read);
    }

    public function test_sidebar_shows_badges_only_when_there_are_unread_enquiries(): void
    {
        $admin = $this->admin();

        $empty = $this->actingAs($admin)->get(route('admin.dashboard'))->getContent();
        $this->assertMatchesRegularExpression('/data-badge="contact"[^>]*\bhidden\b/', $empty);

        ContactEnquiry::factory()->count(3)->create(['is_read' => false]);
        ProductEnquiry::factory()->count(2)->create(['is_read' => false]);

        $filled = $this->actingAs($admin)->get(route('admin.dashboard'))->getContent();
        $this->assertMatchesRegularExpression('/data-badge="contact"(?![^>]*\bhidden\b)[^>]*>\s*3\s*</', $filled);
        $this->assertMatchesRegularExpression('/data-badge="product"(?![^>]*\bhidden\b)[^>]*>\s*2\s*</', $filled);
        $this->assertMatchesRegularExpression('/data-badge="bell"(?![^>]*\bhidden\b)[^>]*>\s*5\s*</', $filled);
    }

    public function test_dashboard_summarises_and_lists_new_enquiries(): void
    {
        ContactEnquiry::factory()->count(3)->create(['is_read' => false]);
        $product = Product::factory()->create(['name' => 'Solar Panel']);
        ProductEnquiry::factory()->forProduct($product)->create(['is_read' => false, 'name' => 'Rahul Sharma']);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('3 new contact enquiries received')
            ->assertSee('1 new product enquiry received')
            ->assertSee('Rahul Sharma')
            ->assertSee('Product enquiry: Solar Panel')
            ->assertSee('View all');
    }
}
