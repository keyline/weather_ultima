<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateServicePageRequest;
use App\Models\ServicePageSetting;
use Illuminate\Http\RedirectResponse;

class ServicePageController extends Controller
{
    public function update(UpdateServicePageRequest $request): RedirectResponse
    {
        ServicePageSetting::current()->update($request->validated());

        return back()->with('status', 'Services page content saved.');
    }
}
