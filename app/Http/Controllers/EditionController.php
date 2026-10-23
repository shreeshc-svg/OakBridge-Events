<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Service;
use App\Support\Uploads;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Admin > Years: the editions that speakers, sponsors, gallery and schedules belong to. */
class EditionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));

        $editions = Edition::with('scheduleService')
            ->withCount(['teams as speakers_count' => fn ($q) => $q->where('year', 'Speaker'), 'teams', 'sponsors', 'galleries', 'videos'])
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('year', 'like', "%{$q}%")->orWhere('title', 'like', "%{$q}%")))
            ->orderByDesc('year')
            ->get();

        return view('backend.editions.index', [
            'editions' => $editions,
            'events' => $this->events(),
            'suggestedYear' => (int) max(now()->year, (int) Edition::max('year') + 1),
        ]);
    }

    public function create()
    {
        return redirect()->route('editions.index');
    }

    public function store(Request $request)
    {
        $edition = Edition::create($this->validated($request));

        return redirect()->route('editions.index')->with('success', 'Year ' . $edition->year . ' added.');
    }

    public function show(Edition $edition)
    {
        return redirect()->route('editions.edit', $edition);
    }

    public function edit(Edition $edition)
    {
        $edition->loadCount(['teams as speakers_count' => fn ($q) => $q->where('year', 'Speaker'), 'teams', 'sponsors', 'galleries', 'videos']);

        return view('backend.editions.edit', ['edition' => $edition, 'events' => $this->events()]);
    }

    public function update(Request $request, Edition $edition)
    {
        $data = $this->validated($request, $edition);

        // Speakers page banner for this year: replace or remove the uploaded image
        if ($request->hasFile('speakers_banner')) {
            Uploads::delete($edition->speakers_banner);
            $data['speakers_banner'] = Uploads::store($request->file('speakers_banner'), 'uploads/images/banners');
        } elseif ($request->boolean('remove_speakers_banner')) {
            Uploads::delete($edition->speakers_banner);
            $data['speakers_banner'] = null;
        }

        $edition->update($data);

        return redirect()->route('editions.index')->with('success', 'Year ' . $edition->year . ' saved.');
    }

    public function destroy(Edition $edition)
    {
        $counts = array_filter($edition->contentCounts());
        if ($counts) {
            $list = collect($counts)->map(fn ($n, $kind) => $n . ' ' . ($kind === 'speakers' ? 'speaker/team' : rtrim($kind, 's')) . ($n === 1 ? '' : 's'))->implode(', ');

            return back()->withErrors(['edition' => 'Year ' . $edition->year . ' still has ' . $list
                . '. Move or delete them first (tick them in their list and use "Move to year").']);
        }

        Uploads::delete($edition->speakers_banner);
        $edition->delete();

        return redirect()->route('editions.index')->with('success', 'Year ' . $edition->year . ' deleted.');
    }

    /** Hide or show a year everywhere on the site, straight from the list. */
    public function toggle(Request $request, Edition $edition)
    {
        $request->validate(['visible' => 'required|boolean']);
        $edition->update(['is_visible' => $request->boolean('visible')]);

        $message = 'Year ' . $edition->year . ' is now ' . ($edition->is_visible ? 'shown' : 'hidden') . ' on the website.';
        if ($request->expectsJson()) {
            return response()->json(['visible' => $edition->is_visible, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    private function validated(Request $request, ?Edition $edition = null): array
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'between:2000,2100', Rule::unique('editions', 'year')->ignore($edition?->id)],
            'title' => 'nullable|string|max:191',
            'schedule_service_id' => 'nullable|exists:services,id',
            'speakers_banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'speakers_banner.max' => 'The banner must be 4 MB or smaller.',
            'speakers_banner.mimes' => 'Use a JPG, PNG or WebP image for the banner.',
            'year.unique' => 'That year already exists.',
            'year.between' => 'Enter a four-digit year, e.g. 2027.',
        ]);

        unset($data['speakers_banner']); // the file itself is handled in update()
        $data['title'] = trim((string) ($data['title'] ?? '')) ?: null;
        $data['is_visible'] = $edition && ! $request->has('is_visible') ? $edition->is_visible : $request->boolean('is_visible');

        return $data;
    }

    private function events()
    {
        return Service::orderByDesc('date')->get(['id', 'title', 'date', 'published']);
    }
}
