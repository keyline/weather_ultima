<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductEnquiriesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkDeleteProductEnquiriesRequest;
use App\Models\ProductEnquiry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductEnquiryController extends Controller
{
    /**
     * @var list<int>
     */
    private const PER_PAGE_OPTIONS = [10, 20, 50, 100];

    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 20);
        $perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 20;

        $enquiries = $this->filteredQuery($request)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.product-enquiries.index', [
            'enquiries' => $enquiries,
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'filters' => $request->only(['search']),
        ]);
    }

    public function markRead(ProductEnquiry $productEnquiry): JsonResponse
    {
        if (! $productEnquiry->is_read) {
            $productEnquiry->forceFill(['is_read' => true])->save();
        }

        return response()->json(EnquiryNotificationController::summary());
    }

    public function destroy(ProductEnquiry $productEnquiry): RedirectResponse
    {
        $productEnquiry->delete();

        return redirect()
            ->route('admin.product-enquiries.index')
            ->with('status', 'Enquiry deleted.');
    }

    public function bulkDestroy(BulkDeleteProductEnquiriesRequest $request): RedirectResponse
    {
        $deleted = ProductEnquiry::query()
            ->whereKey($request->validated('selected'))
            ->delete();

        return back()->with('status', "{$deleted} enquiry(ies) deleted.");
    }

    public function export(Request $request): BinaryFileResponse
    {
        $selected = collect($request->input('selected', []))
            ->filter(fn ($id): bool => is_numeric($id))
            ->map(fn ($id): int => (int) $id);

        $enquiries = $this->filteredQuery($request)
            ->when($selected->isNotEmpty(), fn (Builder $query) => $query->whereKey($selected))
            ->latest()
            ->get();

        return Excel::download(
            new ProductEnquiriesExport($enquiries),
            'product-enquiries-'.now()->format('Y-m-d-His').'.xlsx',
        );
    }

    private function filteredQuery(Request $request): Builder
    {
        return ProductEnquiry::query()->when($request->filled('search'), function (Builder $query) use ($request): void {
            $term = $request->string('search')->toString();

            $query->where(function (Builder $query) use ($term): void {
                foreach (['product_name', 'name', 'email', 'phone'] as $column) {
                    $query->orWhere($column, 'like', '%'.$term.'%');
                }
            });
        });
    }
}
