<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkDeleteDimensionCardsRequest;
use App\Http\Requests\Admin\StoreDimensionCardRequest;
use App\Http\Requests\Admin\UpdateDimensionCardRequest;
use App\Models\DimensionCard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DimensionCardController extends Controller
{
    public function create(): View
    {
        return view('admin.home.cards.create');
    }

    public function store(StoreDimensionCardRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'is_enabled']);
        $data['is_enabled'] = $request->boolean('is_enabled');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('dimension-cards', 'public');
        }

        DimensionCard::query()->create($data);

        return redirect()->route('admin.home.banner.edit')->with('status', 'Dimension card created.');
    }

    public function edit(DimensionCard $dimensionCard): View
    {
        return view('admin.home.cards.edit', ['card' => $dimensionCard]);
    }

    public function update(UpdateDimensionCardRequest $request, DimensionCard $dimensionCard): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'is_enabled']);
        $data['is_enabled'] = $request->boolean('is_enabled');

        if ($request->hasFile('image')) {
            $this->deleteImage($dimensionCard->image);
            $data['image'] = $request->file('image')->store('dimension-cards', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($dimensionCard->image);
            $data['image'] = null;
        }

        $dimensionCard->update($data);

        return redirect()->route('admin.home.banner.edit')->with('status', 'Dimension card updated.');
    }

    public function destroy(DimensionCard $dimensionCard): RedirectResponse
    {
        $this->deleteImage($dimensionCard->image);
        $dimensionCard->delete();

        return redirect()->route('admin.home.banner.edit')->with('status', 'Dimension card deleted.');
    }

    public function bulkDestroy(BulkDeleteDimensionCardsRequest $request): RedirectResponse
    {
        $cards = DimensionCard::query()->whereKey($request->validated('selected'))->get();

        foreach ($cards as $card) {
            $this->deleteImage($card->image);
            $card->delete();
        }

        return redirect()->route('admin.home.banner.edit')->with('status', "{$cards->count()} card(s) deleted.");
    }

    public function toggle(DimensionCard $dimensionCard): RedirectResponse
    {
        $dimensionCard->update(['is_enabled' => ! $dimensionCard->is_enabled]);

        return back()->with('status', $dimensionCard->is_enabled ? 'Card enabled.' : 'Card disabled.');
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
