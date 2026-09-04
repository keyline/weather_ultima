<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomeFounderRequest;
use App\Models\HomeSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeFounderController extends Controller
{
    public function edit(): View
    {
        return view('admin.home.founder', ['home' => HomeSetting::current()]);
    }

    public function update(UpdateHomeFounderRequest $request): RedirectResponse
    {
        $home = HomeSetting::current();

        $data = $request->safe()->only(['founder_name', 'founder_designation', 'founder_intro', 'founder_description']);

        foreach (['founder_image' => 'founder_image_path', 'founder_signature' => 'founder_signature_path'] as $field => $column) {
            if ($request->hasFile($field)) {
                $this->deleteFile($home->{$column});
                $data[$column] = $request->file($field)->store('home', 'public');
            } elseif ($request->boolean('remove_'.$field)) {
                $this->deleteFile($home->{$column});
                $data[$column] = null;
            }
        }

        $home->update($data);

        return back()->with('status', 'About the founder content saved.');
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
