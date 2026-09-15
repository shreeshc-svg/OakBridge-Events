<?php

namespace App\Http\Controllers;

use App\Models\PageSection;
use App\Support\PageContent;
use App\Support\Uploads;
use Illuminate\Http\Request;

class PageContentController extends Controller
{
    public function index()
    {
        return view('backend.page-content.index', ['pages' => PageContent::pages()]);
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
                } elseif ($request->boolean('reset_' . $name)) {
                    Uploads::delete($data[$name] ?? null);
                    unset($data[$name]);
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
}
