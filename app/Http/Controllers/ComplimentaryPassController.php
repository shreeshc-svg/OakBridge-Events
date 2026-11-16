<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PassType;
use App\Models\Service;
use App\Support\ComplimentaryPasses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/** Admin > Ticketing > Complimentary passes. */
class ComplimentaryPassController extends Controller
{
    /**
     * Issue passes for every row of the form: one person, many people, several passes
     * for one email, copies to other addresses. Every row is checked first – if any
     * row has a problem, nothing is issued.
     */
    public function store(Request $request)
    {
        $event = $this->event($request);
        $request->validate(['rows' => 'nullable|array', 'notify' => 'nullable|boolean', 'allow_registered' => 'nullable|boolean']);

        [$rows, $errors] = ComplimentaryPasses::validateRows($event, (array) $request->input('rows', []),
            $this->passTypes($event), $request->boolean('allow_registered'));
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        // all passes or none
        $groups = DB::transaction(fn () => collect($rows)->map(fn ($row) => ComplimentaryPasses::issueGroup($event, $row, $request->user()?->id))->values());

        $people = $groups->count();
        $passes = $groups->sum(fn ($group) => $group->count());
        $summary = $passes . ' complimentary ' . Str::plural('pass', $passes) . ' issued to ' . $people . ' ' . Str::plural('person', $people)
            . ' (' . $groups->flatten()->pluck('booking_id')->pipe(fn ($ids) => $ids->count() > 6 ? $ids->first() . ' – ' . $ids->last() : $ids->implode(', ')) . ')';

        if (! $request->boolean('notify')) {
            return $this->back($event, 'success', $summary . '. Not emailed – use "Send" in the list when ready.');
        }

        @set_time_limit(300); // one email per person
        $failed = $groups->reject(fn ($group) => ComplimentaryPasses::send($group))->map(fn ($group) => $group->first()->email);

        return $failed->isEmpty()
            ? $this->back($event, 'success', $summary . ' and emailed.')
            : $this->back($event, 'warning', $summary . ', but the email could not be sent to: ' . $failed->implode(', ')
                . '. Their passes exist – use "Send" in the list below to try again.');
    }

    /** A preview of the pass email to the admin, from the first row of the form – nothing is issued. */
    public function test(Request $request)
    {
        $event = $this->event($request);
        $data = $request->validate([
            'test_email' => 'required|string|email|max:100',
            'rows' => 'nullable|array',
        ], [
            'test_email.required' => 'Enter the email address to send the test to.',
            'test_email.email' => 'The test email address does not look right.',
        ]);

        $first = collect($data['rows'] ?? [])->first(fn ($row) => is_array($row) && trim((string) ($row['name'] ?? '')) !== '') ?? [];
        $pass = ($first['pass_type_id'] ?? '') !== '' ? $this->passTypes($event)->get((int) $first['pass_type_id']) : null;
        $sent = ComplimentaryPasses::sendTest($event, $data['test_email'], $first, $pass);

        return $this->back($event, $sent ? 'success' : 'warning', $sent
            ? 'Test email sent to ' . $data['test_email'] . ' – no pass was issued.'
            : 'The test email could not be sent – check the mail settings.', true);
    }

    /** Email all passes of this person again (one email). */
    public function resend(Booking $booking)
    {
        abort_unless($booking->isComplimentary(), 404);
        $passes = ComplimentaryPasses::group($booking);

        return ComplimentaryPasses::send($passes)
            ? $this->back($booking->service, 'success', $passes->count() === 1
                ? 'Pass ' . $booking->booking_id . ' emailed to ' . $booking->email . '.'
                : 'All ' . $passes->count() . ' passes emailed again to ' . $booking->email . '.')
            : $this->back($booking->service, 'warning', 'The email to ' . $booking->email . ' could not be sent. Please try again later.');
    }

    /** Cancel one pass. */
    public function destroy(Booking $booking)
    {
        abort_unless($booking->isComplimentary(), 404);
        $booking->delete();

        return $this->back($booking->service, 'success', 'Pass ' . $booking->booking_id . ' (' . $booking->name . ') cancelled. Nobody is emailed about this.');
    }

    /** Cancel every pass issued together for this person. */
    public function destroyGroup(Booking $booking)
    {
        abort_unless($booking->isComplimentary(), 404);
        $passes = ComplimentaryPasses::group($booking);
        Booking::whereIn('id', $passes->pluck('id'))->delete();

        return $this->back($booking->service, 'success', 'All ' . $passes->count() . ' ' . Str::plural('pass', $passes->count())
            . ' for ' . $booking->email . ' cancelled (' . $passes->pluck('booking_id')->implode(', ') . '). Nobody is emailed about this.');
    }

    /** The event, which must still be to come. */
    private function event(Request $request): Service
    {
        $data = $request->validate([
            'event_id' => ['required', Rule::exists('services', 'id')->whereNull('deleted_at')],
        ]);

        $event = Service::findOrFail($data['event_id']);
        if (! $event->date || $event->date->lt(today())) {
            throw ValidationException::withMessages(['event_id' => 'This event is over – complimentary passes can only be issued for upcoming events.']);
        }

        return $event;
    }

    private function passTypes(Service $event)
    {
        return PassType::where('service_id', $event->id)->where('is_active', 1)->get()->keyBy('id');
    }

    private function back(?Service $event, string $type, string $message, bool $keepInput = false)
    {
        $redirect = redirect()->to(route('ticketing.index', ['event' => $event?->id]) . '#complimentary')->with($type, $message);

        return $keepInput ? $redirect->withInput() : $redirect;
    }
}
