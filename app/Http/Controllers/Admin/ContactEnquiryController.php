<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ContactEnquiriesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkDeleteContactEnquiriesRequest;
use App\Models\ContactEnquiry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContactEnquiryController extends Controller
{
    /**
     * @var list<int>
     */
    private const PER_PAGE_OPTIONS = [10, 20, 50, 100];

    public function index(Request $request): View
    {
        $perPage = $this->resolvePerPage($request);

        $enquiries = $this->filteredQuery($request)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.contact-enquiries.index', [
            'enquiries' => $enquiries,
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'filters' => $request->only(['search', 'from', 'to']),
            'hasAnyEnquiries' => ContactEnquiry::query()->exists(),
        ]);
    }

    public function markRead(ContactEnquiry $contactEnquiry): JsonResponse
    {
        if (! $contactEnquiry->is_read) {
            $contactEnquiry->forceFill(['is_read' => true])->save();
        }

        return response()->json(EnquiryNotificationController::summary());
    }

    public function destroy(ContactEnquiry $contactEnquiry): RedirectResponse
    {
        $contactEnquiry->delete();

        return redirect()
            ->route('admin.contact-enquiries.index')
            ->with('status', 'Enquiry deleted.');
    }

    public function bulkDestroy(BulkDeleteContactEnquiriesRequest $request): RedirectResponse
    {
        $deleted = ContactEnquiry::query()
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
            new ContactEnquiriesExport($enquiries),
            'contact-enquiries-'.now()->format('Y-m-d-His').'.xlsx',
        );
    }

    private function filteredQuery(Request $request): Builder
    {
        return ContactEnquiry::query()
            ->search($request->string('search')->toString())
            ->when(
                $request->filled('from'),
                fn (Builder $query) => $query->whereDate('created_at', '>=', $request->date('from')),
            )
            ->when(
                $request->filled('to'),
                fn (Builder $query) => $query->whereDate('created_at', '<=', $request->date('to')),
            );
    }

    private function resolvePerPage(Request $request): int
    {
        $perPage = $request->integer('per_page', 20);

        return in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 20;
    }
}
