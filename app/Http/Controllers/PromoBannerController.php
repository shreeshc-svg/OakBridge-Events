<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\PromoBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Admin > Marketing Popup: the promo that opens over the home page.
 */
class PromoBannerController extends Controller
{
    /** Folder (inside public/) where the uploaded files live. */
    private const DIR = 'uploads/images/promo';

    public function edit()
    {
        $setting = Setting::findOrFail(1);

        return view('backend.promo.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $accepted = implode(',', array_merge(PromoBanner::IMAGE_EXTENSIONS, PromoBanner::VIDEO_EXTENSIONS));

        $request->validate([
            'promo_media' => ['nullable', 'file', 'mimes:' . $accepted, $this->sizeRule($request, 'promo_media')],
            'promo_media_mobile' => ['nullable', 'file', 'mimes:' . $accepted, $this->sizeRule($request, 'promo_media_mobile')],
            'promo_alt' => 'nullable|string|max:255',
            'promo_link' => 'nullable|url|max:500',
            'promo_delay' => 'required|integer|min:' . PromoBanner::MIN_DELAY . '|max:' . PromoBanner::MAX_DELAY,
            'promo_starts_at' => 'nullable|date',
            'promo_ends_at' => 'nullable|date|after_or_equal:promo_starts_at',
        ], [
            'promo_media.mimes' => 'The popup must be a JPG, PNG, WebP or GIF image, or an MP4 / WebM video.',
            'promo_media_mobile.mimes' => 'The mobile popup must be a JPG, PNG, WebP or GIF image, or an MP4 / WebM video.',
            'promo_media.max' => 'That file is too large – 5 MB for an image, 15 MB for a GIF, 50 MB for a video.',
            'promo_media_mobile.max' => 'That file is too large – 5 MB for an image, 15 MB for a GIF, 50 MB for a video.',
            'promo_media.uploaded' => 'The file did not upload – it is probably larger than the server allows (see the note under the upload box).',
            'promo_media_mobile.uploaded' => 'The mobile file did not upload – it is probably larger than the server allows.',
            'promo_link.url' => 'Enter the full destination address, including https://',
            'promo_delay.max' => 'The delay can be at most ' . PromoBanner::MAX_DELAY . ' seconds.',
            'promo_ends_at.after_or_equal' => 'The end date cannot be before the start date.',
        ]);

        $setting = Setting::findOrFail(1);
        $mediaChanged = false;

        if ($request->hasFile('promo_media')) {
            $this->deleteFile($setting->promo_media);
            $setting->promo_media = $this->storeFile($request->file('promo_media'), 'popup');
            [$setting->promo_width, $setting->promo_height] = $this->dimensions($setting->promo_media);
            $mediaChanged = true;
        } elseif ($request->boolean('remove_promo_media')) {
            $this->deleteFile($setting->promo_media);
            $setting->promo_media = null;
            $setting->promo_width = null;
            $setting->promo_height = null;
            $setting->promo_enabled = false;   // nothing left to show
            $mediaChanged = true;
        }

        if ($request->hasFile('promo_media_mobile')) {
            $this->deleteFile($setting->promo_media_mobile);
            $setting->promo_media_mobile = $this->storeFile($request->file('promo_media_mobile'), 'popup-mobile');
            $mediaChanged = true;
        } elseif ($request->boolean('remove_promo_media_mobile')) {
            $this->deleteFile($setting->promo_media_mobile);
            $setting->promo_media_mobile = null;
            $mediaChanged = true;
        }

        // a popup with no file cannot be switched on
        if (! $request->boolean('remove_promo_media')) {
            $setting->promo_enabled = $request->boolean('promo_enabled') && (bool) $setting->promo_media;
        }

        $setting->promo_alt = $request->input('promo_alt') ?: null;
        $setting->promo_link = $request->input('promo_link') ?: null;
        $setting->promo_new_tab = $request->boolean('promo_new_tab');
        $setting->promo_dismissible = $request->boolean('promo_dismissible');
        $setting->promo_delay = (int) $request->input('promo_delay');
        $setting->promo_starts_at = $request->input('promo_starts_at') ?: null;
        $setting->promo_ends_at = $request->input('promo_ends_at') ?: null;

        // a new file is a new campaign: everyone who ticked "don't show again" sees this one
        if ($mediaChanged || $request->boolean('reset_dismissals')) {
            $setting->promo_version = (int) ($setting->promo_version ?? 1) + 1;
        }

        $setting->save();

        return redirect()->route('promo.edit')->with('success', 'Marketing popup updated. ' . PromoBanner::statusNote($setting));
    }

    /** Images 5 MB, GIFs 15 MB, videos 50 MB (Laravel wants kilobytes). */
    private function sizeRule(Request $request, string $field): string
    {
        $file = $request->file($field);

        return 'max:' . PromoBanner::maxKbFor($file ? $file->extension() : null);
    }

    private function storeFile($file, string $prefix): string
    {
        $name = $prefix . '-' . time() . '-' . Str::random(6) . '.' . $file->extension();
        $file->move(public_path(self::DIR), $name);

        return $name;
    }

    private function deleteFile(?string $name): void
    {
        if (! $name) {
            return;
        }

        $path = public_path(self::DIR . '/' . basename($name));

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    /**
     * Natural pixel size of an uploaded image, so the popup can show it at
     * its own size rather than stretching it. Videos report nothing.
     */
    private function dimensions(?string $name): array
    {
        if (! $name || PromoBanner::isVideo($name)) {
            return [null, null];
        }

        $size = @getimagesize(public_path(self::DIR . '/' . basename($name)));

        if (! $size) {
            return [null, null];
        }

        return [min((int) $size[0], 65535), min((int) $size[1], 65535)];
    }
}
