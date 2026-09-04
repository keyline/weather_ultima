<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * @var list<int>
     */
    private const PER_PAGE_OPTIONS = [10, 20, 50, 100];

    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 20);
        $perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 20;

        $products = Product::query()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search').'%'))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::query()->create([
            'name' => $request->validated('name'),
            'short_description' => $request->validated('short_description'),
            'is_active' => $request->boolean('is_active'),
            'image' => $request->file('image')->store('products', 'public'),
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', ['product' => $product]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = [
            'name' => $request->validated('name'),
            'short_description' => $request->validated('short_description'),
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            $this->deleteImage($product->image);
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteImage($product->image);
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Product deleted.');
    }

    public function toggle(Product $product): RedirectResponse
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('status', $product->is_active ? 'Product enabled.' : 'Product disabled.');
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
