<?php

namespace App\Http\Controllers;

use App\Models\PageSeo;
use App\Support\Seo;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function index()
    {
        $saved = PageSeo::all()->keyBy('page');

        return view('backend.seo.index', ['pages' => Seo::PAGES, 'saved' => $saved]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'seo' => 'array',
            'seo.*.title' => 'nullable|string|max:255',
            'seo.*.description' => 'nullable|string|max:1000',
            'seo.*.keywords' => 'nullable|string|max:1000',
        ], [], [
            'seo.*.title' => 'title',
            'seo.*.description' => 'description',
            'seo.*.keywords' => 'keywords',
        ]);

        foreach (Seo::PAGES as $page => $info) {
            $row = $request->input("seo.$page", []);
            $values = [
                'title' => trim((string) ($row['title'] ?? '')) ?: null,
                'description' => trim((string) ($row['description'] ?? '')) ?: null,
                'keywords' => trim((string) ($row['keywords'] ?? '')) ?: null,
            ];
            if (array_filter($values)) {
                PageSeo::updateOrCreate(['page' => $page], $values);
            } else {
                PageSeo::where('page', $page)->delete();
            }
        }

        return back()->with('success', 'SEO settings saved.');
    }
}
