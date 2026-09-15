<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HeroBannerController extends Controller
{
    /** Folder (inside public/) where hero banner images are stored. */
    private const DIR = 'uploads/images/hero';

    /** Banner shown when no image has been uploaded yet. */
    public const DEFAULT_IMAGE = 'public/assets/images/website_banner.webp';

    public function edit()
    {
        $setting = Setting::findOrFail(1);

        return view('backend.hero.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hero_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'hero_image_mobile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'hero_alt'          => 'nullable|string|max:255',
            'hero_click'        => ['required', Rule::in(['register', 'link', 'none'])],
            'hero_link'         => 'nullable|required_if:hero_click,link|url|max:500',
        ], [
            'hero_link.required_if' => 'Enter the link the banner should open.',
            'hero_image.max'        => 'The banner image must be 5 MB or smaller.',
            'hero_image_mobile.max' => 'The mobile image must be 5 MB or smaller.',
        ]);

        $setting = Setting::findOrFail(1);

        $setting->hero_enabled = $request->boolean('hero_enabled');
        $setting->hero_alt     = $request->input('hero_alt');
        $setting->hero_click   = $request->input('hero_click');
        $setting->hero_link    = $request->input('hero_link');
        $setting->hero_new_tab = $request->boolean('hero_new_tab');

        if ($request->hasFile('hero_image')) {
            $this->deleteImage($setting->hero_image);
            $setting->hero_image = $this->storeImage($request->file('hero_image'), 'banner');
        } elseif ($request->boolean('reset_hero_image')) {
            $this->deleteImage($setting->hero_image);
            $setting->hero_image = null;
        }

        if ($request->hasFile('hero_image_mobile')) {
            $this->deleteImage($setting->hero_image_mobile);
            $setting->hero_image_mobile = $this->storeImage($request->file('hero_image_mobile'), 'banner-mobile');
        } elseif ($request->boolean('remove_hero_image_mobile')) {
            $this->deleteImage($setting->hero_image_mobile);
            $setting->hero_image_mobile = null;
        }

        $setting->save();

        return redirect()->route('hero.edit')->with('success', 'Hero banner updated.');
    }

    private function storeImage($file, string $prefix): string
    {
        $name = $prefix . '-' . time() . '-' . Str::random(6) . '.' . $file->extension();
        $file->move(public_path(self::DIR), $name);

        return $name;
    }

    private function deleteImage(?string $name): void
    {
        if (! $name) {
            return;
        }
        $path = public_path(self::DIR . '/' . basename($name));
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
