<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PassType;
use App\Models\Service;
use App\Support\ComplimentaryPasses;
use App\Support\OrderBuilder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/** Admin > Ticketing > Complimentary passes. */
class ComplimentaryPassController extends Controller
{
    /** Issue one free pass and (normally) email it. */
    public function store(Request $request)
    {
        [$event, $pass] = $this->eventAndPass($request);

        $data = $request->validate([
            'guest_name' => 'required|string|max:100',
            'guest_email' => 'required|string|email|max:100',
            'guest_phone' => 'required|string|max:25',
            'guest_notify' => 'nullable|boolean',
        ], [
            'guest_name.required' => 'Enter the guest\'s name.',
            'guest_email.required' => 'Enter the guest\'s email – the pass is sent there.',
            'guest_email.email' => 'That email address does not look right.',
            'guest_phone.required' => 'Enter the guest\'s phone number.',
        ]);

        $phone = ComplimentaryPasses::cleanPhone($data['guest_phone']);
        if (! $phone) {
            throw ValidationException::withMessages(['guest_phone' => 'Enter a phone number of 10 to 15 digits (e.g. 9810012345 or +91 98100 12345).']);
        }

        $email = strtolower(trim($data['guest_email']));
        if (in_array($email, OrderBuilder::takenEmails($event), true)) {
            $existing = Booking::where('service_id', $event->id)->whereRaw('LOWER(email) = ?', [$email])->value('booking_id');
            throw ValidationException::withMessages(['guest_email' => $email . ' is already registered for this event'
                . ($existing ? ' (pass ' . $existing . ').' : ' (an order is in progress).')]);
        }

        $booking = ComplimentaryPasses::issue($event, ['name' => trim($data['guest_name']), 'email' => $email, 'phone' => $phone], $pass, $request->user()?->id);

        if (! $request->boolean('guest_notify')) {
            return $this->back($event, 'success', 'Complimentary pass ' . $booking->booking_id . ' issued to ' . $booking->name . ' (not emailed).');
        }

        return ComplimentaryPasses::send($booking)
            ? $this->back($event, 'success', 'Complimentary pass ' . $booking->booking_id . ' issued and emailed to ' . $booking->email . '.')
            : $this->back($event, 'warning', 'Pass ' . $booking->booking_id . ' was issued, but the email to ' . $booking->email
                . ' could not be sent. Use "Resend" in the list below.');
    }

    /** A preview of the pass email to the admin – nothing is issued. */
    public function test(Request $request)
    {
        [$event, $pass] = $this->eventAndPass($request);
        $data = $request->validate([
            'test_email' => 'required|string|email|max:100',
            'guest_name' => 'nullable|string|max:100',
        ], [
            'test_email.required' => 'Enter the email address to send the test to.',
            'test_email.email' => 'The test email address does not look right.',
        ]);

        $sent = ComplimentaryPasses::sendTest($event, $data['test_email'], ['name' => trim((string) ($data['guest_name'] ?? ''))], $pass);

        return $this->back($event, $sent ? 'success' : 'warning', $sent
            ? 'Test email sent to ' . $data['test_email'] . ' – no pass was issued.'
            : 'The test email could not be sent – check the mail settings.', true);
    }

    public function resend(Booking $booking)
    {
        abort_unless($booking->isComplimentary(), 404);
        $event = $booking->service;

        return ComplimentaryPasses::send($booking)
            ? $this->back($event, 'success', 'Pass ' . $booking->booking_id . ' emailed again to ' . $booking->email . '.')
            : $this->back($event, 'warning', 'The email to ' . $booking->email . ' could not be sent. Please try again later.');
    }

    public function destroy(Booking $booking)
    {
        abort_unless($booking->isComplimentary(), 404);
        $event = $booking->service;
        $booking->delete();

        return $this->back($event, 'success', 'Complimentary pass ' . $booking->booking_id . ' for ' . $booking->name . ' cancelled. The guest is not emailed about this.');
    }

    /** The event (still to come) and, optionally, which of its passes the guest gets. */
    private function eventAndPass(Request $request): array
    {
        $data = $request->validate([
            'event_id' => ['required', Rule::exists('services', 'id')->whereNull('deleted_at')],
            'guest_pass_id' => ['nullable', Rule::exists('pass_types', 'id')->where('service_id', $request->input('event_id'))],
        ], [
            'guest_pass_id.exists' => 'Choose a pass of this event.',
        ]);

        $event = Service::findOrFail($data['event_id']);
        if (! $event->date || $event->date->lt(today())) {
            throw ValidationException::withMessages(['event_id' => 'This event is over – complimentary passes can only be issued for upcoming events.']);
        }

        return [$event, isset($data['guest_pass_id']) ? PassType::find($data['guest_pass_id']) : null];
    }

    private function back(?Service $event, string $type, string $message, bool $keepInput = false)
    {
        $redirect = redirect()->to(route('ticketing.index', ['event' => $event?->id]) . '#complimentary')->with($type, $message);

        return $keepInput ? $redirect->withInput() : $redirect;
    }
}
