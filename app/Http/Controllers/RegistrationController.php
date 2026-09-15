<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public const DEFAULT_BUTTON_TEXT = 'Register Now';
    public const DEFAULT_CLOSED_MESSAGE = 'Registration is currently closed. Please check back soon.';

    public function edit()
    {
        $setting = Setting::findOrFail(1);

        return view('backend.registration.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'registration_button_text'    => 'nullable|string|max:40',
            'registration_closed_message' => 'nullable|string|max:255',
        ], [
            'registration_button_text.max'    => 'Button text can be at most 40 characters.',
            'registration_closed_message.max' => 'The closed message can be at most 255 characters.',
        ]);

        $setting = Setting::findOrFail(1);
        $setting->registration_enabled        = $request->boolean('registration_enabled');
        $setting->registration_button_text    = $request->input('registration_button_text');
        $setting->registration_closed_message = $request->input('registration_closed_message');
        $setting->save();

        return redirect()->route('registration.edit')->with(
            'success',
            $setting->registration_enabled ? 'Registration is open.' : 'Registration is closed.'
        );
    }

    /** Values shared with every page (see AppServiceProvider). */
    public static function viewData(?Setting $setting): array
    {
        return [
            'registrationOpen'          => (bool) ($setting?->registration_enabled ?? true),
            'registrationLabel'         => $setting?->registration_button_text ?: self::DEFAULT_BUTTON_TEXT,
            'registrationClosedMessage' => $setting?->registration_closed_message ?: self::DEFAULT_CLOSED_MESSAGE,
        ];
    }
}
