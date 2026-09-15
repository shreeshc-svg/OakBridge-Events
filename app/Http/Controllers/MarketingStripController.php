<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MarketingStripController extends Controller
{
    public const MAX_ITEMS = 10;
    public const MAX_ITEM_LENGTH = 60;

    /** Pixels scrolled per second for each speed option. */
    public const SPEEDS = ['slow' => 40, 'normal' => 70, 'fast' => 110];

    public function edit()
    {
        $setting = Setting::findOrFail(1);

        return view('backend.strip.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        // turn the textarea into a clean list before validating
        $items = self::parseItems($request->input('strip_items', ''));
        $request->merge(['strip_item_list' => $items]);

        $request->validate([
            'strip_item_list'   => ['array', 'max:' . self::MAX_ITEMS, Rule::requiredIf($request->boolean('strip_enabled'))],
            'strip_item_list.*' => 'string|max:' . self::MAX_ITEM_LENGTH,
            'strip_bg'          => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'strip_color'       => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'strip_speed'       => ['required', Rule::in(array_keys(self::SPEEDS))],
        ], [
            'strip_item_list.required'  => 'Add at least one phrase to show the strip.',
            'strip_item_list.max'       => 'Use at most ' . self::MAX_ITEMS . ' phrases.',
            'strip_item_list.*.max'     => 'Each phrase can be at most ' . self::MAX_ITEM_LENGTH . ' characters.',
            'strip_bg.regex'            => 'Pick a valid background colour.',
            'strip_color.regex'         => 'Pick a valid text colour.',
        ]);

        $setting = Setting::findOrFail(1);
        $setting->strip_enabled = $request->boolean('strip_enabled');
        $setting->strip_items   = $items ? implode("\n", $items) : null;
        $setting->strip_bg      = strtoupper($request->input('strip_bg'));
        $setting->strip_color   = strtoupper($request->input('strip_color'));
        $setting->strip_speed   = $request->input('strip_speed');
        $setting->save();

        return redirect()->route('strip.edit')->with('success', 'Marketing strip updated.');
    }

    /** Split text into trimmed, non-empty lines. */
    public static function parseItems(?string $text): array
    {
        return array_values(array_filter(
            array_map('trim', preg_split('/\R/', (string) $text)),
            fn ($line) => $line !== ''
        ));
    }
}
