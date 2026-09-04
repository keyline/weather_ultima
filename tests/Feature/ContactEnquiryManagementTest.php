<?php

namespace Tests\Feature;

use App\Exports\ContactEnquiriesExport;
use App\Models\ContactEnquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ContactEnquiryManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_guests_and_non_admins_cannot_reach_the_list(): void
    {
        $this->get(route('admin.contact-enquiries.index'))->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get(route('admin.contact-enquiries.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_list_paginates_twenty_per_page_by_default(): void
    {
        ContactEnquiry::factory()->count(25)->create();

        $enquiries = $this->actingAs($this->admin())
            ->get(route('admin.contact-enquiries.index'))
            ->assertOk()
            ->viewData('enquiries');

        $this->assertCount(20, $enquiries->items());
        $this->assertSame(25, $enquiries->total());
        $this->assertSame(2, $enquiries->lastPage());
    }

    public function test_per_page_can_be_changed_and_invalid_values_fall_back(): void
    {
        ContactEnquiry::factory()->count(60)->create();
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.contact-enquiries.index', ['per_page' => 50]))->assertViewHas('perPage', 50);
        $this->actingAs($admin)->get(route('admin.contact-enquiries.index', ['per_page' => 999]))->assertViewHas('perPage', 20);
    }

    public function test_search_matches_across_columns(): void
    {
        $needle = ContactEnquiry::factory()->create(['name' => 'Priya Sen']);
        ContactEnquiry::factory()->create(['name' => 'Someone Else']);

        $items = $this->actingAs($this->admin())
            ->get(route('admin.contact-enquiries.index', ['search' => 'priya']))
            ->viewData('enquiries');

        $this->assertSame([$needle->id], $items->pluck('id')->all());
    }

    public function test_results_can_be_filtered_by_date_range(): void
    {
        $match = ContactEnquiry::factory()->create(['created_at' => '2026-05-10 09:00:00']);
        ContactEnquiry::factory()->create(['created_at' => '2026-01-01 09:00:00']);

        $items = $this->actingAs($this->admin())
            ->get(route('admin.contact-enquiries.index', ['from' => '2026-05-01', 'to' => '2026-05-31']))
            ->viewData('enquiries');

        $this->assertSame([$match->id], $items->pluck('id')->all());
    }

    public function test_bulk_delete_removes_only_selected_rows(): void
    {
        $keep = ContactEnquiry::factory()->count(2)->create();
        $remove = ContactEnquiry::factory()->count(3)->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.contact-enquiries.bulk-destroy'), ['selected' => $remove->pluck('id')->all()])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseCount('contact_enquiries', 2);
        $this->assertDatabaseHas('contact_enquiries', ['id' => $keep->first()->id]);
    }

    public function test_bulk_delete_requires_a_selection(): void
    {
        ContactEnquiry::factory()->count(2)->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.contact-enquiries.bulk-destroy'), ['selected' => []])
            ->assertSessionHasErrors('selected');

        $this->assertDatabaseCount('contact_enquiries', 2);
    }

    public function test_single_delete_removes_the_row(): void
    {
        $enquiry = ContactEnquiry::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.contact-enquiries.destroy', $enquiry))
            ->assertRedirect(route('admin.contact-enquiries.index'));

        $this->assertDatabaseMissing('contact_enquiries', ['id' => $enquiry->id]);
    }

    public function test_export_downloads_selected_rows(): void
    {
        Excel::fake();
        $this->freezeTime();
        $rows = ContactEnquiry::factory()->count(4)->create();
        $filename = 'contact-enquiries-'.now()->format('Y-m-d-His').'.xlsx';

        $this->actingAs($this->admin())
            ->get(route('admin.contact-enquiries.export', ['selected' => $rows->take(2)->pluck('id')->all()]))
            ->assertOk();

        Excel::assertDownloaded($filename, fn (ContactEnquiriesExport $export): bool => $export->collection()->count() === 2);
    }

    public function test_export_all_downloads_every_row(): void
    {
        Excel::fake();
        $this->freezeTime();
        ContactEnquiry::factory()->count(5)->create();
        $filename = 'contact-enquiries-'.now()->format('Y-m-d-His').'.xlsx';

        $this->actingAs($this->admin())
            ->get(route('admin.contact-enquiries.export'))
            ->assertOk();

        Excel::assertDownloaded($filename, fn (ContactEnquiriesExport $export): bool => $export->collection()->count() === 5);
    }

    public function test_non_admins_cannot_export_or_bulk_delete(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        ContactEnquiry::factory()->count(2)->create();

        $this->actingAs($user)->get(route('admin.contact-enquiries.export'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->delete(route('admin.contact-enquiries.bulk-destroy'), ['selected' => [1]])->assertRedirect(route('admin.login'));

        $this->assertDatabaseCount('contact_enquiries', 2);
    }
}
