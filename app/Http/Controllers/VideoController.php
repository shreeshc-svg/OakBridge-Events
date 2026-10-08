<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Support\Editions;
use App\Support\YearBulk;
use Illuminate\Http\Request;

/** Admin > Videos: YouTube links, one year each. */
class VideoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $year = $request->input('year');

        $videos = Video::with('edition')
            ->when($year === 'none', fn ($query) => $query->whereNull('edition_id'))
            ->when($year && $year !== 'none', fn ($query) => $query->where('edition_id', $year))
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('title', 'like', "%{$q}%")->orWhere('video', 'like', "%{$q}%")))
            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        return view('backend.video.index', ['videos' => $videos, 'years' => Editions::options()]);
    }

    public function create()
    {
        return redirect()->route('video.index');
    }

    public function store(Request $request)
    {
        Video::create($this->validated($request));

        return redirect()->route('video.index', ['year' => $request->input('edition_id')])->withSuccess('Video added.');
    }

    public function show(Video $video)
    {
        return redirect()->route('video.edit', $video);
    }

    public function edit(Video $video)
    {
        return view('backend.video.edit', ['video' => $video, 'years' => Editions::options()]);
    }

    public function update(Request $request, Video $video)
    {
        $video->update($this->validated($request));

        return redirect()->route('video.index', ['year' => $video->edition_id])->withSuccess('Video updated.');
    }

    public function destroy(Video $video)
    {
        $video->delete();

        return back()->withSuccess('Video deleted.');
    }

    public function bulk(Request $request)
    {
        ['ids' => $ids, 'action' => $action, 'edition' => $edition] = YearBulk::validate($request);
        $videos = Video::whereIn('id', $ids)->get();

        foreach ($videos as $video) {
            $action === 'move' ? $video->update(['edition_id' => $edition->id]) : $video->delete();
        }

        return back()->with('success', YearBulk::message($action, $videos->count(), $edition, 'video'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'video' => 'required|string|max:250',
            'edition_id' => 'required|exists:editions,id',
        ], [
            'edition_id.required' => 'Choose the year this video belongs to.',
        ]);

        if (! (new Video(['video' => $data['video']]))->youtubeId()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'video' => 'Paste a YouTube link, e.g. https://www.youtube.com/watch?v=… or https://youtu.be/…',
            ]);
        }

        return $data;
    }
}
