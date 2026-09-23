<?php

namespace App\Http\Controllers;

use App\Models\PageSection;
use App\Support\PageContent;
use App\Support\SiteSections;
use App\Support\Uploads;
use Illuminate\Http\Request;

class PageContentController extends Controller
{
    public function index()
    {
        return view('backend.page-content.index', [
            'pages' => PageContent::pages(),
            'toggles' => SiteSections::all(),
        ]);
    }

    public function edit(string $key)
    {
        $def = PageContent::definition($key) ?? abort(404);

        return view('backend.page-content.edit', [
            'key' => $key,
            'def' => $def,
            'values' => PageContent::get($key),
            'customised' => PageContent::isCustomised($key),
        ]);
    }

    public function update(Request $request, string $key)
    {
        $def = PageContent::definition($key) ?? abort(404);

        $rules = [];
        $names = [];
        foreach ($def['fields'] as $name => $field) {
            $names[$name] = strtolower($field['label']);
            $rules[$name] = match ($field['type']) {
                'text' => 'nullable|string|max:255',
                'textarea' => 'nullable|string|max:3000',
                'richtext' => 'nullable|string|max:200000',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
                'file' => 'nullable|file|mimes:pdf|max:20480',
                'toggle' => 'nullable|boolean',
            };
        }
        if (isset($def['items'])) {
            $rules['items'] = 'nullable|array|max:' . $def['items']['max'];
            foreach ($def['items']['fields'] as $name => $field) {
                $rules['items.*.' . $name] = 'nullable|string|max:' . ($field['type'] === 'textarea' ? 1000 : 255);
            }
        }
        $request->validate($rules, [], $names);

        $section = PageSection::firstOrNew(['key' => $key]);
        $data = $section->data ?? [];

        foreach ($def['fields'] as $name => $field) {
            if (in_array($field['type'], ['image', 'file'], true)) {
                $folder = $field['type'] === 'image' ? 'uploads/images/content' : 'uploads/files';
                if ($request->hasFile($name)) {
                    Uploads::delete($data[$name] ?? null);
                    $data[$name] = Uploads::store($request->file($name), $folder);
                } elseif ($request->boolean('clear_' . $name)) {
                    // explicitly nothing: the page shows the "coming soon" note instead
                    Uploads::delete($data[$name] ?? null);
                    $data[$name] = '';
                } elseif ($request->boolean('reset_' . $name)) {
                    Uploads::delete($data[$name] ?? null);
                    unset($data[$name]);
                }
                continue;
            }
            if ($field['type'] === 'toggle') {
                // the form always posts a switch (there is a hidden 0 behind it), so a
                // request that leaves one out is a partial save - don't flip it silently
                if ($request->has($name)) {
                    $data[$name] = $request->boolean($name) ? '1' : '0';
                }
                continue;
            }

            $value = $request->input($name);
            $data[$name] = is_string($value) ? trim($value) : '';
        }

        if (isset($def['items'])) {
            $items = [];
            foreach ((array) $request->input('items', []) as $row) {
                $clean = [];
                foreach ($def['items']['fields'] as $name => $field) {
                    $clean[$name] = trim((string) ($row[$name] ?? ''));
                }
                if (implode('', $clean) !== '') {
                    $items[] = $clean;
                }
            }
            $data['items'] = $items;
        }

        $section->data = $data;
        $section->save();

        return redirect()->route('page-content.edit', $key)->with('success', $def['label'] . ' saved.');
    }

    public function reset(string $key)
    {
        $def = PageContent::definition($key) ?? abort(404);
        $section = PageSection::where('key', $key)->first();
        if ($section) {
            foreach ($def['fields'] as $name => $field) {
                if (in_array($field['type'], ['image', 'file'], true)) {
                    Uploads::delete($section->data[$name] ?? null);
                }
            }
            $section->delete();
        }

        return redirect()->route('page-content.edit', $key)->with('success', $def['label'] . ' reset to the original content.');
    }

    /** Show or hide a section (the switches on Page Content). */
    public function visibility(Request $request, string $key)
    {
        $section = SiteSections::find($key) ?? abort(404);
        $request->validate(['visible' => 'required|boolean']);
        $visible = $request->boolean('visible');
        SiteSections::setVisible($key, $visible);

        $message = $section['label'] . ($visible ? ' is now shown on the site.' : ' is now hidden from the site.');
        if ($request->expectsJson()) {
            return response()->json(['visible' => $visible, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    /** Save the top-to-bottom order of the homepage sections. */
    public function order(Request $request)
    {
        $request->validate([
            'keys' => 'array',
            'keys.*' => 'string|max:60',
        ]);

        // an empty list is the "put it back how it was" button
        $order = SiteSections::setHomeOrder($request->input('keys', []));

        $message = $request->input('keys')
            ? 'Homepage order saved.'
            : 'Homepage order put back to the original.';

        if ($request->expectsJson()) {
            return response()->json([
                'order' => $order,
                'is_default' => SiteSections::homeOrderIsDefault(),
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
