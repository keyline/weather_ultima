<?php

namespace Tests\Feature;

use App\Exports\ProductEnquiriesExport;
use App\Models\ProductEnquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ProductEnquiryManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_non_admins_cannot_reach_the_list(): void
    {
        $this->get(route('admin.product-enquiries.index'))->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get(route('admin.product-enquiries.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_list_paginates_twenty_per_page_by_default(): void
    {
        ProductEnquiry::factory()->count(25)->create();

        $enquiries = $this->actingAs($this->admin())
            ->get(route('admin.product-enquiries.index'))
            ->assertOk()
            ->viewData('enquiries');

        $this->assertCount(20, $enquiries->items());
        $this->assertSame(25, $enquiries->total());
    }

    public function test_per_page_can_be_changed(): void
    {
        ProductEnquiry::factory()->count(30)->create();

        $this->actingAs($this->admin())
            ->get(route('admin.product-enquiries.index', ['per_page' => 10]))
            ->assertViewHas('perPage', 10);
    }

    public function test_bulk_delete_removes_only_selected_rows(): void
    {
        $keep = ProductEnquiry::factory()->count(2)->create();
        $remove = ProductEnquiry::factory()->count(3)->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.product-enquiries.bulk-destroy'), ['selected' => $remove->pluck('id')->all()])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseCount('product_enquiries', 2);
        $this->assertDatabaseHas('product_enquiries', ['id' => $keep->first()->id]);
    }

    public function test_bulk_delete_requires_a_selection(): void
    {
        ProductEnquiry::factory()->count(2)->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.product-enquiries.bulk-destroy'), ['selected' => []])
            ->assertSessionHasErrors('selected');
    }

    public function test_export_selected_and_all(): void
    {
        Excel::fake();
        $this->freezeTime();
        $rows = ProductEnquiry::factory()->count(4)->create();
        $filename = 'product-enquiries-'.now()->format('Y-m-d-His').'.xlsx';

        $this->actingAs($this->admin())
            ->get(route('admin.product-enquiries.export', ['selected' => $rows->take(2)->pluck('id')->all()]))
            ->assertOk();

        Excel::assertDownloaded($filename, fn (ProductEnquiriesExport $export): bool => $export->collection()->count() === 2);
    }
}
