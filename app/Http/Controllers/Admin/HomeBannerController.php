<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomeBannerRequest;
use App\Models\DimensionCard;
use App\Models\HomeSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeBannerController extends Controller
{
    public function edit(): View
    {
        return view('admin.home.banner', [
            'home' => HomeSetting::current(),
            'cards' => DimensionCard::query()->ordered()->get(),
        ]);
    }

    public function update(UpdateHomeBannerRequest $request): RedirectResponse
    {
        HomeSetting::current()->update($request->validated());

        return back()->with('status', 'Top banner heading saved.');
    }
}
