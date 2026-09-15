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

    public function index()
    {
        $groups = SponsorGroup::orderBy('sort_order')->orderBy('id')->with('sponsors')->get();

        return view('backend.sponsors.index', compact('groups'));
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
            'name' => 'nullable|string|max:150',
            'url' => 'nullable|url|max:500',
            'logo' => 'required|' . self::LOGO_RULES,
        ], ['logo.required' => 'Choose a logo image to upload.']);

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
