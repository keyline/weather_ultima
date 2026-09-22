<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkDeleteInstagramPostsRequest;
use App\Http\Requests\Admin\StoreInstagramPostRequest;
use App\Http\Requests\Admin\UpdateInstagramPostRequest;
use App\Models\InstagramPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InstagramPostController extends Controller
{
    /**
     * @var list<int>
     */
    private const PER_PAGE_OPTIONS = [10, 20, 50, 100];

    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 20);
        $perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 20;

        return view('admin.home.instagram.index', [
            'posts' => InstagramPost::query()->ordered()->paginate($perPage)->withQueryString(),
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    public function create(): View
    {
        return view('admin.home.instagram.create');
    }

    public function store(StoreInstagramPostRequest $request): RedirectResponse
    {
        InstagramPost::query()->create([
            'link_url' => $request->validated('link_url'),
            'display_order' => $request->validated('display_order'),
            'is_enabled' => $request->boolean('is_enabled'),
            'image' => $request->file('image')->store('instagram-posts', 'public'),
        ]);

        return redirect()->route('admin.home.instagram.index')->with('status', 'Photo added.');
    }

    public function edit(InstagramPost $instagramPost): View
    {
        return view('admin.home.instagram.edit', ['post' => $instagramPost]);
    }

    public function update(UpdateInstagramPostRequest $request, InstagramPost $instagramPost): RedirectResponse
    {
        $data = [
            'link_url' => $request->validated('link_url'),
            'display_order' => $request->validated('display_order'),
            'is_enabled' => $request->boolean('is_enabled'),
        ];

        if ($request->hasFile('image')) {
            $this->deleteImage($instagramPost->image);
            $data['image'] = $request->file('image')->store('instagram-posts', 'public');
        }

        $instagramPost->update($data);

        return redirect()->route('admin.home.instagram.index')->with('status', 'Photo updated.');
    }

    public function destroy(InstagramPost $instagramPost): RedirectResponse
    {
        $this->deleteImage($instagramPost->image);
        $instagramPost->delete();

        return redirect()->route('admin.home.instagram.index')->with('status', 'Photo deleted.');
    }

    public function bulkDestroy(BulkDeleteInstagramPostsRequest $request): RedirectResponse
    {
        $posts = InstagramPost::query()->whereKey($request->validated('selected'))->get();

        foreach ($posts as $post) {
            $this->deleteImage($post->image);
            $post->delete();
        }

        return back()->with('status', "{$posts->count()} photo(s) deleted.");
    }

    public function toggle(InstagramPost $instagramPost): RedirectResponse
    {
        $instagramPost->update(['is_enabled' => ! $instagramPost->is_enabled]);

        return back()->with('status', $instagramPost->is_enabled ? 'Photo enabled.' : 'Photo disabled.');
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
