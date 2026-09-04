<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkDeleteTestimonialsRequest;
use App\Http\Requests\Admin\StoreTestimonialRequest;
use App\Http\Requests\Admin\UpdateTestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    /**
     * @var list<int>
     */
    private const PER_PAGE_OPTIONS = [10, 20, 50, 100];

    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 20);
        $perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 20;

        $testimonials = Testimonial::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $term = $request->string('search')->toString();
                $query->where(fn ($query) => $query
                    ->where('name', 'like', '%'.$term.'%')
                    ->orWhere('designation', 'like', '%'.$term.'%')
                    ->orWhere('company', 'like', '%'.$term.'%')
                    ->orWhere('review', 'like', '%'.$term.'%'));
            })
            ->ordered()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.testimonials.index', [
            'testimonials' => $testimonials,
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): View
    {
        return view('admin.testimonials.create');
    }

    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        Testimonial::query()->create($request->safe()->except('is_enabled') + ['is_enabled' => $request->boolean('is_enabled')]);

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial created.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.edit', ['testimonial' => $testimonial]);
    }

    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($request->safe()->except('is_enabled') + ['is_enabled' => $request->boolean('is_enabled')]);

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial deleted.');
    }

    public function bulkDestroy(BulkDeleteTestimonialsRequest $request): RedirectResponse
    {
        $deleted = Testimonial::query()->whereKey($request->validated('selected'))->delete();

        return back()->with('status', "{$deleted} testimonial(s) deleted.");
    }

    public function toggle(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update(['is_enabled' => ! $testimonial->is_enabled]);

        return back()->with('status', $testimonial->is_enabled ? 'Testimonial enabled.' : 'Testimonial disabled.');
    }
}
