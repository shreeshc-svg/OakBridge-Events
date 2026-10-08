<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use App\Models\SponsorGroup;
use App\Support\Uploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SponsorController extends Controller
{
    private const LOGO_RULES = 'image|mimes:jpg,jpeg,png,webp,gif|max:2048';

    /** One year at a time (?year=edition id; newest year by default), groups are shared by all years. */
    public function index(Request $request)
    {
        $years = \App\Support\Editions::options();
        $year = $request->input('year') === 'none' ? null : ($years->firstWhere('id', (int) $request->input('year')) ?? $years->first());
        $showUntagged = $request->input('year') === 'none';
        $q = trim((string) $request->input('q'));

        $groups = SponsorGroup::orderBy('sort_order')->orderBy('id')
            ->withCount('sponsors')
            ->with(['sponsors' => fn ($query) => $query
                ->when($showUntagged, fn ($w) => $w->whereNull('edition_id'))
                ->when(! $showUntagged && $year, fn ($w) => $w->where('edition_id', $year->id))
                ->when($q !== '', fn ($w) => $w->where('name', 'like', "%{$q}%"))])
            ->get();

        $untagged = Sponsor::whereNull('edition_id')->count();

        return view('backend.sponsors.index', compact('groups', 'years', 'year', 'showUntagged', 'untagged', 'q'));
    }

    // ---------------------------------------------------------------- groups

    public function storeGroup(Request $request)
    {
        $data = $this->validateGroup($request);
        $data['sort_order'] = (int) SponsorGroup::max('sort_order') + 1;
        SponsorGroup::create($data);

        return back()->with('success', 'Group "' . $data['title'] . '" added.');
    }

    public function updateGroup(Request $request, SponsorGroup $group)
    {
        $group->update($this->validateGroup($request));

        return back()->with('success', 'Group "' . $group->title . '" updated.');
    }

    public function destroyGroup(SponsorGroup $group)
    {
        foreach ($group->sponsors as $sponsor) {
            Uploads::delete($sponsor->logo);
        }
        $group->delete();

        return back()->with('success', 'Group "' . $group->title . '" and its logos deleted.');
    }

    private function validateGroup(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100',
            'logo_size' => ['required', Rule::in(array_keys(SponsorGroup::SIZES))],
        ]);
        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    // -------------------------------------------------------------- sponsors

    public function storeSponsor(Request $request)
    {
        $data = $request->validate([
            'sponsor_group_id' => 'required|exists:sponsor_groups,id',
            'edition_id' => 'required|exists:editions,id',
            'name' => 'nullable|string|max:150',
            'url' => 'nullable|url|max:500',
            'logo' => 'required|' . self::LOGO_RULES,
        ], ['logo.required' => 'Choose a logo image to upload.', 'edition_id.required' => 'Choose the year for this logo (add one in Admin › Years if needed).']);

        $data['logo'] = Uploads::store($request->file('logo'), 'uploads/images/sponsors');
        $data['sort_order'] = (int) Sponsor::where('sponsor_group_id', $data['sponsor_group_id'])->max('sort_order') + 1;
        $data['is_active'] = true;
        Sponsor::create($data);

        return back()->with('success', 'Logo added.');
    }

    public function updateSponsor(Request $request, Sponsor $sponsor)
    {
        $data = $request->validate([
            'sponsor_group_id' => 'required|exists:sponsor_groups,id',
            'edition_id' => 'required|exists:editions,id',
            'name' => 'nullable|string|max:150',
            'url' => 'nullable|url|max:500',
            'logo' => 'nullable|' . self::LOGO_RULES,
        ]);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            Uploads::delete($sponsor->logo);
            $data['logo'] = Uploads::store($request->file('logo'), 'uploads/images/sponsors');
        } else {
            unset($data['logo']);
        }
        if ((int) $data['sponsor_group_id'] !== (int) $sponsor->sponsor_group_id) {
            $data['sort_order'] = (int) Sponsor::where('sponsor_group_id', $data['sponsor_group_id'])->max('sort_order') + 1;
        }
        $sponsor->update($data);

        return back()->with('success', 'Logo updated.');
    }

    public function destroySponsor(Sponsor $sponsor)
    {
        Uploads::delete($sponsor->logo);
        $sponsor->delete();

        return back()->with('success', 'Logo deleted.');
    }

    /** Tick several logos: move or copy them to another year (sponsors often return), or delete them. */
    public function bulk(Request $request)
    {
        ['ids' => $ids, 'action' => $action, 'edition' => $edition] = \App\Support\YearBulk::validate($request, ['move', 'copy', 'delete']);
        $sponsors = Sponsor::whereIn('id', $ids)->get();

        foreach ($sponsors as $sponsor) {
            if ($action === 'move') {
                $sponsor->update(['edition_id' => $edition->id]);
            } elseif ($action === 'copy') {
                $copy = $sponsor->replicate();
                $copy->edition_id = $edition->id;
                if (str_starts_with((string) $sponsor->logo, 'public/uploads/')) {
                    $copy->logo = \App\Support\YearBulk::duplicateFile(base_path(), $sponsor->logo);
                }
                $copy->save();
            } else {
                Uploads::delete($sponsor->logo);
                $sponsor->delete();
            }
        }

        return back()->with('success', \App\Support\YearBulk::message($action, $sponsors->count(), $edition, 'logo'));
    }

    /**
     * Saves drag-and-drop order.
     * Body: {"groups": [3,1,2], "sponsors": {"3": [10, 11], "1": [4]}}
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'groups' => 'array',
            'groups.*' => 'integer',
            'sponsors' => 'array',
            'sponsors.*' => 'array',
            'sponsors.*.*' => 'integer',
        ]);

        DB::transaction(function () use ($request) {
            foreach (array_values($request->input('groups', [])) as $i => $id) {
                SponsorGroup::whereKey($id)->update(['sort_order' => $i + 1]);
            }
            foreach ($request->input('sponsors', []) as $groupId => $ids) {
                if (! SponsorGroup::whereKey($groupId)->exists()) {
                    continue;
                }
                foreach (array_values($ids) as $i => $id) {
                    Sponsor::whereKey($id)->update(['sponsor_group_id' => $groupId, 'sort_order' => $i + 1]);
                }
            }
        });

        return response()->json(['message' => 'Order saved.']);
    }
}
