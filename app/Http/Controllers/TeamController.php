<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Requests\Team\TeamRequest;
use App\Http\Requests\Team\TeamUpdateRequest;
use File;

class TeamController extends Controller
{
    /** Types a team member can have (stored in the old "year" column). */
    public const ROLES = ['Speaker' => 'Speaker', 'Advisor' => 'Advisor', 'Organizer' => 'Organizer team (About page)'];

    /** Search, filter by year and type, 50 per page. */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $year = $request->input('year');

        $role = $request->input('role');
        $byType = $role && isset(self::ROLES[$role]);
        // drag to reorder: one type (speakers: of one year), no search – exactly a list the website shows
        $canReorder = $byType && $q === '' && ($role !== 'Speaker' || $year);

        $teams = Team::with('edition')
            ->when($year === 'none', fn ($query) => $query->whereNull('edition_id'))
            ->when($year && $year !== 'none', fn ($query) => $query->where('edition_id', $year))
            ->when($request->filled('role'), fn ($query) => $query->where('year', $role))
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('position', 'like', "%{$q}%")))
            // one type picked: the order the website shows; otherwise newest first
            ->when($byType, fn ($query) => $query->ordered(), fn ($query) => $query->orderByDesc('id'))
            ->paginate($canReorder ? 1000 : 50)
            ->withQueryString();

        return view('backend.team.index', [
            'teams' => $teams,
            'years' => \App\Support\Editions::options(),
            'canReorder' => $canReorder,
            'role' => $byType ? $role : null,
        ]);
    }

    /** Drag and drop on the list: save the new website order. */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1|max:1000',
            'ids.*' => 'integer|distinct|exists:teams,id',
        ]);

        Team::reorder($data['ids']);

        return response()->json(['message' => 'Order saved – the website shows it now.']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.team.create', ['years' => \App\Support\Editions::options()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       // dd($request->all());

        $data = $request->validate([
            'name' => 'required|string|max:75',
            'position' => 'nullable|string|max:75',
            'image' => 'nullable|image|mimes:png,jpg,webp,jpeg|max:2048',
            'social' => 'nullable',
            'bio' => 'nullable',
            'year' => 'required|in:Speaker,Advisor,Organizer',
            'edition_id' => 'nullable|required_if:year,Speaker|exists:editions,id',
        ], [
            'edition_id.required_if' => 'Choose the year this speaker belongs to.',
        ]);


        if($request->file('image'))
        {
            $imageName = uniqid().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/images/team/'),$imageName);
            $data['image'] = $imageName;
        }

        Team::create($data);

        return redirect()->route('team.index')->with('success','Team member created sucessfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        return redirect()->route('team.edit', $team);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        return view('backend.team.edit', ['team' => $team, 'years' => \App\Support\Editions::options()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeamUpdateRequest $request, Team $team)
    {
       $data =  $request->validated();

        if($request->file('image'))
        {
            $this->deleteImage($team->image);

            $imageName = uniqid().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/images/team/'),$imageName);
            $data['image'] = $imageName;
        }

        $team->update($data);

        return redirect()->route('team.index')->with('success','Team member updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $this->deleteImage($team->image);
        $team->delete();

        return redirect()->route('team.index')->with('success','team member has been deleted successfully');
    }

    /** Tick several: move to a year, copy to a year (e.g. returning speakers), or delete. */
    public function bulk(Request $request)
    {
        ['ids' => $ids, 'action' => $action, 'edition' => $edition] = \App\Support\YearBulk::validate($request, ['move', 'copy', 'delete']);
        $teams = Team::whereIn('id', $ids)->get();

        foreach ($teams as $team) {
            if ($action === 'move') {
                $team->update(['edition_id' => $edition->id]);
            } elseif ($action === 'copy') {
                $copy = $team->replicate();
                $copy->edition_id = $edition->id;
                $copy->sort_order = 0; // the end of that year's list
                if ($team->image) {
                    $copy->image = \App\Support\YearBulk::duplicateFile(public_path('uploads/images/team'), $team->image);
                }
                $copy->save();
            } else {
                $this->deleteImage($team->image);
                $team->delete();
            }
        }

        return back()->with('success', \App\Support\YearBulk::message($action, $teams->count(), $edition, 'team member'));
    }

    private function deleteImage(?string $image): void
    {
        if ($image && basename($image) === $image) {
            File::delete(public_path('uploads/images/team/' . $image));
        }
    }
}
