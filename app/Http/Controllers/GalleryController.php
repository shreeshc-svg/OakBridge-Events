<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Support\Editions;
use App\Support\YearBulk;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/** Admin > Gallery: images, one year each. Files live in public/uploads/images/our-gallery. */
class GalleryController extends Controller
{
    private const FOLDER = 'uploads/images/our-gallery';

    private const IMAGE_RULES = 'image|mimes:jpg,jpeg,png,webp,gif|max:5120';

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $year = $request->input('year');

        $images = Gallery::with('edition')
            ->when($year === 'none', fn ($query) => $query->whereNull('edition_id'))
            ->when($year && $year !== 'none', fn ($query) => $query->where('edition_id', $year))
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderByDesc('id')
            ->paginate(60)
            ->withQueryString();

        return view('backend.gallery.index', ['images' => $images, 'years' => Editions::options()]);
    }

    /** Several images at once, all into one year. */
    public function store(Request $request)
    {
        $request->validate([
            'edition_id' => 'required|exists:editions,id',
            'src' => 'required|array|min:1|max:50',
            'src.*' => self::IMAGE_RULES,
        ], [
            'src.required' => 'Choose one or more images to upload.',
            'src.*.max' => 'Each image must be 5 MB or smaller.',
            'src.*.image' => 'Only image files can be uploaded.',
            'edition_id.required' => 'Choose the year these images belong to.',
        ]);

        foreach ($request->file('src') as $file) {
            Gallery::create($this->saveFile($file) + ['edition_id' => $request->input('edition_id')]);
        }

        $count = count($request->file('src'));

        return redirect()->route('gallery.index', ['year' => $request->input('edition_id')])
            ->with('success', $count . ' ' . Str::plural('image', $count) . ' uploaded.');
    }

    /** Change the year, or swap the picture for a new one. */
    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'edition_id' => 'required|exists:editions,id',
            'image' => 'nullable|' . self::IMAGE_RULES,
        ]);

        $data = ['edition_id' => $request->input('edition_id')];
        if ($request->hasFile('image')) {
            $this->deleteFile($gallery->name);
            $data += $this->saveFile($request->file('image'));
        }
        $gallery->update($data);

        return back()->with('success', 'Image updated.');
    }

    public function destroy(Gallery $gallery)
    {
        $this->deleteFile($gallery->name);
        $gallery->delete();

        return back()->with('success', 'Image deleted.');
    }

    public function bulk(Request $request)
    {
        ['ids' => $ids, 'action' => $action, 'edition' => $edition] = YearBulk::validate($request);
        $images = Gallery::whereIn('id', $ids)->get();

        foreach ($images as $image) {
            if ($action === 'move') {
                $image->update(['edition_id' => $edition->id]);
            } else {
                $this->deleteFile($image->name);
                $image->delete();
            }
        }

        return back()->with('success', YearBulk::message($action, $images->count(), $edition, 'image'));
    }

    /** Old bookmark (admin/gallery-edit) goes to the new list. */
    public function legacy()
    {
        return redirect()->route('gallery.index');
    }

    private function saveFile(UploadedFile $file): array
    {
        // extension from the file's contents, not its name
        $name = Str::random(6) . '-' . time() . '.' . ($file->extension() ?: 'jpg');
        $file->move(public_path(self::FOLDER), $name);

        return ['name' => $name, 'src' => 'public/' . self::FOLDER . '/' . $name];
    }

    private function deleteFile(?string $name): void
    {
        if ($name && basename($name) === $name) {
            File::delete(public_path(self::FOLDER . '/' . $name));
        }
    }
}
