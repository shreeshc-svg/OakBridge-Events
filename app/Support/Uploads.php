<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Stores admin uploads under public/uploads/... and returns the path from the
 * site root (e.g. "public/uploads/images/sponsors/abc.webp"), ready for asset().
 */
class Uploads
{
    public static function store(UploadedFile $file, string $folder): string
    {
        $folder = trim($folder, '/');
        $name = Str::random(6) . '-' . time() . '.' . $file->extension();
        $file->move(base_path('public/' . $folder), $name);

        return 'public/' . $folder . '/' . $name;
    }

    /** Deletes a previously uploaded file. Built-in theme files are never touched. */
    public static function delete(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'public/uploads/') || str_contains($path, '..')) {
            return;
        }
        $full = base_path($path);
        if (File::exists($full)) {
            File::delete($full);
        }
    }

    /** Public URL for a stored path, a site-relative URL or a full URL. */
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'mailto:') || str_starts_with($path, 'tel:')) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }
}
