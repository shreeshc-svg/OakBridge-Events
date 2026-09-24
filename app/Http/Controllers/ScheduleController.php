<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Support\Schedule;
use App\Support\Uploads;
use Carbon\Carbon;
use Illuminate\Http\Request;

/** Admin > Events > Schedules: everything shown on an event's schedule page. */
class ScheduleController extends Controller
{
    public function index()
    {
        $events = Service::withCount('bookings')->orderByDesc('date')->get();

        return view('backend.schedules.index', compact('events'));
    }

    public function edit(Service $service)
    {
        $sessions = old('sessions');
        if ($sessions === null) {
            $sessions = array_map(fn ($s) => [
                'from' => $s['from'],
                'to' => $s['to'],
                'title' => $s['title'],
                'subheadline' => $s['subheadline'],
                'speakers' => Schedule::speakersToText($s['speakers']),
                'notes' => $s['notes'],
            ], $service->sessions());
        }

        return view('backend.schedules.edit', compact('service', 'sessions'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'event_date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'place_of_supply' => ['nullable', \Illuminate\Validation\Rule::in(\App\Support\GstStates::names())],
            'seat_target' => 'nullable|integer|min:1|max:1000000',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'agenda_file' => 'nullable|file|mimes:pdf|max:20480',
            'sessions' => 'nullable|array|max:' . Schedule::MAX_SESSIONS,
            'sessions.*.from' => 'nullable|string|max:20',
            'sessions.*.to' => 'nullable|string|max:20',
            'sessions.*.title' => 'nullable|string|max:255',
            'sessions.*.subheadline' => 'nullable|string|max:100',
            'sessions.*.speakers' => 'nullable|string|max:5000',
            'sessions.*.notes' => 'nullable|string|max:2000',
        ], [
            'end_time.after' => 'The end time must be after the start time.',
        ], [
            'event_date' => 'date',
            'sessions.*.title' => 'session title',
            'sessions.*.speakers' => 'speakers',
        ]);

        $sessions = [];
        foreach ((array) $request->input('sessions', []) as $row) {
            $session = [
                'from' => trim((string) ($row['from'] ?? '')),
                'to' => trim((string) ($row['to'] ?? '')),
                'title' => trim((string) ($row['title'] ?? '')),
                'subheadline' => trim((string) ($row['subheadline'] ?? '')),
                'speakers' => Schedule::textToSpeakers((string) ($row['speakers'] ?? '')),
                'notes' => trim((string) ($row['notes'] ?? '')),
            ];
            if ($session['title'] === '' && $session['from'] === '' && ! $session['speakers'] && $session['notes'] === '') {
                continue; // empty row
            }
            $sessions[] = $session;
        }

        $service->title = $request->input('title');
        $service->date = Carbon::createFromFormat('Y-m-d H:i', $request->input('event_date') . ' ' . $request->input('start_time'));
        $service->end_time = $request->input('end_time') ?: null;
        $service->venue = trim((string) $request->input('venue')) ?: null;
        $service->place_of_supply = $request->input('place_of_supply') ?: null;
        $service->seat_target = $request->input('seat_target') ?: null;
        $service->meta_title = $request->input('meta_title');
        $service->meta_description = $request->input('meta_description');
        $service->published = $request->boolean('published') ? '1' : '0';
        $service->timeline = Schedule::toJson($sessions);

        if ($request->hasFile('agenda_file')) {
            Uploads::delete($service->agenda_file);
            $service->agenda_file = Uploads::store($request->file('agenda_file'), 'uploads/files');
        } elseif ($request->boolean('remove_agenda')) {
            Uploads::delete($service->agenda_file);
            $service->agenda_file = null;
        }

        $service->save();

        return redirect()->route('schedules.edit', $service)->with('success', 'Schedule saved.');
    }
}
