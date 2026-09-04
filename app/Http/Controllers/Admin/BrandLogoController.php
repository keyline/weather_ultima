<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkDeleteBrandLogosRequest;
use App\Http\Requests\Admin\StoreBrandLogoRequest;
use App\Http\Requests\Admin\UpdateBrandLogoRequest;
use App\Models\BrandLogo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BrandLogoController extends Controller
{
    /**
     * @var list<int>
     */
    private const PER_PAGE_OPTIONS = [10, 20, 50, 100];

    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 20);
        $perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 20;

        return view('admin.home.logo.index', [
            'logos' => BrandLogo::query()->ordered()->paginate($perPage)->withQueryString(),
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    public function create(): View
    {
        return view('admin.home.logo.create');
    }

    public function store(StoreBrandLogoRequest $request): RedirectResponse
    {
        BrandLogo::query()->create([
            'alt_text' => $request->validated('alt_text'),
            'display_order' => $request->validated('display_order'),
            'is_enabled' => $request->boolean('is_enabled'),
            'image' => $request->file('image')->store('brand-logos', 'public'),
        ]);

        return redirect()->route('admin.home.logo.index')->with('status', 'Logo added.');
    }

    public function edit(BrandLogo $brandLogo): View
    {
        return view('admin.home.logo.edit', ['brandLogo' => $brandLogo]);
    }

    public function update(UpdateBrandLogoRequest $request, BrandLogo $brandLogo): RedirectResponse
    {
        $data = [
            'alt_text' => $request->validated('alt_text'),
            'display_order' => $request->validated('display_order'),
            'is_enabled' => $request->boolean('is_enabled'),
        ];

        if ($request->hasFile('image')) {
            $this->deleteImage($brandLogo->image);
            $data['image'] = $request->file('image')->store('brand-logos', 'public');
        }

        $brandLogo->update($data);

        return redirect()->route('admin.home.logo.index')->with('status', 'Logo updated.');
    }

    public function destroy(BrandLogo $brandLogo): RedirectResponse
    {
        $this->deleteImage($brandLogo->image);
        $brandLogo->delete();

        return redirect()->route('admin.home.logo.index')->with('status', 'Logo deleted.');
    }

    public function bulkDestroy(BulkDeleteBrandLogosRequest $request): RedirectResponse
    {
        $logos = BrandLogo::query()->whereKey($request->validated('selected'))->get();

        foreach ($logos as $logo) {
            $this->deleteImage($logo->image);
            $logo->delete();
        }

        return back()->with('status', "{$logos->count()} logo(s) deleted.");
    }

    public function toggle(BrandLogo $brandLogo): RedirectResponse
    {
        $brandLogo->update(['is_enabled' => ! $brandLogo->is_enabled]);

        return back()->with('status', $brandLogo->is_enabled ? 'Logo enabled.' : 'Logo disabled.');
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
