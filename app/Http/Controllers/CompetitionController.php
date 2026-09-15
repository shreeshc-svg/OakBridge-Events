<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Support\Uploads;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompetitionController extends Controller
{
    public const BUTTON_STYLES = ['one' => 'Red', 'two' => 'Purple', 'three' => 'Pink'];
    private const MAX_BUTTONS = 3;

    public function index()
    {
        $competitions = Competition::orderBy('sort_order')->orderBy('id')->get();

        return view('backend.competitions.index', compact('competitions'));
    }

    public function create()
    {
        $competition = new Competition(['is_active' => true, 'sort_order' => (int) Competition::max('sort_order') + 1, 'buttons' => []]);

        return view('backend.competitions.form', compact('competition'));
    }

    public function store(Request $request)
    {
        $competition = new Competition();
        $this->save($request, $competition);

        return redirect()->route('competitions.index')->with('success', 'Competition added.');
    }

    public function edit(Competition $competition)
    {
        return view('backend.competitions.form', compact('competition'));
    }

    public function update(Request $request, Competition $competition)
    {
        $this->save($request, $competition);

        return redirect()->route('competitions.index')->with('success', 'Competition updated.');
    }

    public function destroy(Competition $competition)
    {
        Uploads::delete($competition->image);
        $competition->delete();

        return redirect()->route('competitions.index')->with('success', 'Competition deleted.');
    }

    private function save(Request $request, Competition $competition): void
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'subtitle' => 'nullable|string|max:200',
            'description' => 'nullable|string|max:100000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'buttons' => 'nullable|array|max:' . self::MAX_BUTTONS,
            'buttons.*.label' => 'nullable|string|max:60',
            'buttons.*.url' => 'nullable|string|max:500|required_with:buttons.*.label',
            'buttons.*.style' => ['nullable', Rule::in(array_keys(self::BUTTON_STYLES))],
        ], [
            'buttons.*.url.required_with' => 'Each button needs a link.',
        ]);

        $competition->title = $request->input('title');
        $competition->subtitle = $request->input('subtitle');
        $competition->description = $request->input('description');
        $competition->sort_order = (int) $request->input('sort_order', 0);
        $competition->is_active = $request->boolean('is_active');

        $buttons = [];
        foreach ((array) $request->input('buttons', []) as $button) {
            $label = trim((string) ($button['label'] ?? ''));
            $url = trim((string) ($button['url'] ?? ''));
            if ($label === '' || $url === '') {
                continue;
            }
            $buttons[] = [
                'label' => $label,
                'url' => $url,
                'style' => $button['style'] ?? 'one',
                'new_tab' => ! empty($button['new_tab']),
            ];
        }
        $competition->buttons = $buttons;

        if ($request->hasFile('image')) {
            Uploads::delete($competition->image);
            $competition->image = Uploads::store($request->file('image'), 'uploads/images/competitions');
        }

        $competition->save();
    }
}
