<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkDeleteCoreValuesRequest;
use App\Http\Requests\Admin\StoreCoreValueRequest;
use App\Http\Requests\Admin\UpdateCoreValueRequest;
use App\Models\CoreValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoreValueController extends Controller
{
    /**
     * @var list<int>
     */
    private const PER_PAGE_OPTIONS = [10, 20, 50, 100];

    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 20);
        $perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 20;

        $values = CoreValue::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $term = $request->string('search')->toString();
                $query->where(fn ($query) => $query
                    ->where('title', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%'));
            })
            ->ordered()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.home.core-values.index', [
            'values' => $values,
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): View
    {
        return view('admin.home.core-values.create');
    }

    public function store(StoreCoreValueRequest $request): RedirectResponse
    {
        CoreValue::query()->create($request->safe()->except('is_enabled') + ['is_enabled' => $request->boolean('is_enabled')]);

        return redirect()->route('admin.home.core-values.index')->with('status', 'Core value created.');
    }

    public function edit(CoreValue $coreValue): View
    {
        return view('admin.home.core-values.edit', ['coreValue' => $coreValue]);
    }

    public function update(UpdateCoreValueRequest $request, CoreValue $coreValue): RedirectResponse
    {
        $coreValue->update($request->safe()->except('is_enabled') + ['is_enabled' => $request->boolean('is_enabled')]);

        return redirect()->route('admin.home.core-values.index')->with('status', 'Core value updated.');
    }

    public function destroy(CoreValue $coreValue): RedirectResponse
    {
        $coreValue->delete();

        return redirect()->route('admin.home.core-values.index')->with('status', 'Core value deleted.');
    }

    public function bulkDestroy(BulkDeleteCoreValuesRequest $request): RedirectResponse
    {
        $deleted = CoreValue::query()->whereKey($request->validated('selected'))->delete();

        return back()->with('status', "{$deleted} core value(s) deleted.");
    }

    public function toggle(CoreValue $coreValue): RedirectResponse
    {
        $coreValue->update(['is_enabled' => ! $coreValue->is_enabled]);

        return back()->with('status', $coreValue->is_enabled ? 'Core value enabled.' : 'Core value disabled.');
    }
}
