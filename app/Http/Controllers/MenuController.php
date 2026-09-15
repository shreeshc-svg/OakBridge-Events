<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Support\SiteMenu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index()
    {
        $menus = [];
        foreach (array_keys(SiteMenu::LOCATIONS) as $location) {
            $menus[$location] = MenuItem::where('location', $location)
                ->whereNull('parent_id')
                ->orderBy('sort_order')->orderBy('id')
                ->with('children')
                ->get();
        }

        return view('backend.menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['sort_order'] = $data['sort_order']
            ?? (int) MenuItem::where('location', $data['location'])->where('parent_id', $data['parent_id'])->max('sort_order') + 1;
        MenuItem::create($data);

        return back()->with('success', 'Link "' . $data['label'] . '" added.')->with('tab', $data['location']);
    }

    public function update(Request $request, MenuItem $item)
    {
        $data = $this->validated($request, $item);
        $data['sort_order'] = $data['sort_order'] ?? $item->sort_order;
        $item->update($data);

        return back()->with('success', 'Link "' . $item->label . '" saved.')->with('tab', $item->location);
    }

    /** Show or hide one link straight away (the switch in each row). */
    public function toggle(Request $request, MenuItem $item)
    {
        $request->validate(['visible' => 'required|boolean']);
        $item->update(['is_active' => $request->boolean('visible')]);

        $message = '"' . $item->label . '" is now ' . ($item->is_active ? 'shown' : 'hidden') . '.';
        if ($request->expectsJson()) {
            return response()->json(['visible' => (bool) $item->is_active, 'message' => $message]);
        }

        return back()->with('success', $message)->with('tab', $item->location);
    }

    public function destroy(MenuItem $item)
    {
        $item->delete();

        return back()->with('success', 'Link "' . $item->label . '" deleted.')->with('tab', $item->location);
    }

    private function validated(Request $request, ?MenuItem $item = null): array
    {
        $data = $request->validate([
            'location' => ['required', Rule::in(array_keys(SiteMenu::LOCATIONS))],
            'label' => 'required|string|max:60',
            'url' => 'nullable|string|max:500',
            'parent_id' => [
                'nullable',
                Rule::exists('menu_items', 'id')->whereNull('parent_id')->where('location', $request->input('location')),
            ],
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ], [
            'parent_id.exists' => 'Pick a top-level link from the same menu as the parent.',
        ]);

        $data['url'] = trim((string) ($data['url'] ?? '')) ?: '#';
        $data['new_tab'] = $request->boolean('new_tab');
        // the Show switch on existing links saves on its own (see toggle), so keep the current value here
        $data['is_active'] = ($item && ! $request->has('is_active')) ? (bool) $item->is_active : $request->boolean('is_active');
        $data['parent_id'] = $data['parent_id'] ?? null;

        if ($item && (int) $data['parent_id'] === (int) $item->id) {
            $data['parent_id'] = null;
        }
        // an item that has its own sub-links stays top level
        if ($item && $data['parent_id'] && $item->children()->exists()) {
            $data['parent_id'] = null;
        }

        return $data;
    }
}
