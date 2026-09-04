<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkDeleteServicesRequest;
use App\Http\Requests\Admin\StoreServiceImageRequest;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceImagesRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\ServicePageSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * @var list<int>
     */
    private const PER_PAGE_OPTIONS = [10, 20, 50, 100];

    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 20);
        $perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 20;

        $services = Service::query()
            ->withCount('images')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $term = $request->string('search')->toString();
                $query->where(fn ($query) => $query
                    ->where('name', 'like', '%'.$term.'%')
                    ->orWhere('category', 'like', '%'.$term.'%')
                    ->orWhere('tags', 'like', '%'.$term.'%'));
            })
            ->ordered()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.services.index', [
            'services' => $services,
            'page' => ServicePageSetting::current(),
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): View
    {
        return view('admin.services.create');
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $service = Service::query()->create($request->safe()->except('is_enabled') + ['is_enabled' => $request->boolean('is_enabled')]);

        return redirect()->route('admin.services.edit', $service)->with('status', 'Service created. Add images below.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', ['service' => $service->load('images')]);
    }

    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->safe()->except('is_enabled') + ['is_enabled' => $request->boolean('is_enabled')]);

        return redirect()->route('admin.services.index')->with('status', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        foreach ($service->images as $image) {
            $this->deleteImage($image->image);
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }

    public function bulkDestroy(BulkDeleteServicesRequest $request): RedirectResponse
    {
        $services = Service::query()->with('images')->whereKey($request->validated('selected'))->get();

        foreach ($services as $service) {
            foreach ($service->images as $image) {
                $this->deleteImage($image->image);
            }
            $service->delete();
        }

        return back()->with('status', "{$services->count()} service(s) deleted.");
    }

    public function toggle(Service $service): RedirectResponse
    {
        $service->update(['is_enabled' => ! $service->is_enabled]);

        return back()->with('status', $service->is_enabled ? 'Service enabled.' : 'Service disabled.');
    }

    public function storeImage(StoreServiceImageRequest $request, Service $service): RedirectResponse
    {
        $service->images()->create([
            'image' => $request->file('image')->store('services', 'public'),
            'alt_text' => $request->validated('alt_text'),
            'display_order' => (int) $service->images()->max('display_order') + 1,
        ]);

        return redirect()->route('admin.services.edit', $service)->with('status', 'Image added.');
    }

    public function updateImages(UpdateServiceImagesRequest $request, Service $service): RedirectResponse
    {
        foreach ($request->validated('images', []) as $row) {
            $service->images()->whereKey($row['id'])->update([
                'display_order' => $row['display_order'] ?? 0,
                'alt_text' => $row['alt_text'] ?? null,
            ]);
        }

        return redirect()->route('admin.services.edit', $service)->with('status', 'Image order and captions saved.');
    }

    public function destroyImage(Service $service, ServiceImage $serviceImage): RedirectResponse
    {
        abort_unless($serviceImage->service_id === $service->id, 404);

        $this->deleteImage($serviceImage->image);
        $serviceImage->delete();

        return redirect()->route('admin.services.edit', $service)->with('status', 'Image removed.');
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
