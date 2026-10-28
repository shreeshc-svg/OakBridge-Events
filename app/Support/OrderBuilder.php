<?php

namespace App\Support;

use App\Http\Controllers\InvoiceController;
use App\Models\Booking;
use App\Models\Order;
use App\Models\PassType;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Checks a registration and turns it into an order (paid events) or a booking
 * (free events). Used by the website form and by Admin > Orders > New
 * registration, so both follow exactly the same rules.
 *
 * Problems are thrown as validation errors, which send the visitor back to
 * the form with their input and the messages.
 */
class OrderBuilder
{
    /**
     * @param array{unit_price?: float|null, created_by?: int|null, require_paid?: bool} $options
     *   unit_price  - a special price per pass instead of the Ticketing price (admin only)
     *   created_by  - the admin user creating the order
     *   require_paid - refuse events without passes (admin only)
     * @return array{event: Service, data: array, order?: Order, booking?: Booking}
     */
    public static function fromRequest(Request $request, array $options = []): array
    {
        $request->merge(['buyer_gstin' => strtoupper(trim((string) $request->input('buyer_gstin'))) ?: null]);

        $data = $request->validate(array_merge([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100',
            'phone' => 'required|digits:10',
            'company' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'event' => 'required|string|max:255',
        ], InvoiceController::billingRules()), InvoiceController::billingMessages());

        // the event actually picked (and its real date); it stays bookable until its day is over
        $chosenEvent = Service::wherePublished('1')
            ->where('title', $data['event'])
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->first();
        if (! $chosenEvent) {
            self::fail('event', 'Please choose an event from the list.');
        }
        $data['date'] = $chosenEvent->date->format('Y-m-d H:i:s');

        $paid = Pricing::isPaid($chosenEvent);
        if (! $paid && ($options['require_paid'] ?? false)) {
            self::fail('event', 'This event has no passes yet – add them in Ticketing first.');
        }

        $passType = null;
        $quantity = 1;
        $attendees = [];

        if ($paid) {
            // a paid order gets a GST invoice, which needs a billing address
            if (! $request->input('billing_state') && ($fromGstin = GstStates::stateFromGstin($data['buyer_gstin'] ?? null))) {
                $request->merge(['billing_state' => $fromGstin]);
                $data['billing_state'] = $fromGstin;
            }

            $extra = $request->validate([
                'billing_address' => 'required|string|max:500',
                'billing_state' => ['required', Rule::in(GstStates::names())],
                'billing_pin' => 'required|digits:6',
                'pass_type_id' => [
                    'required',
                    Rule::exists('pass_types', 'id')->where('service_id', $chosenEvent->id)->where('is_active', 1),
                ],
                'quantity' => 'required|integer|min:1|max:' . Pricing::maxPasses(),
                'attendees' => 'array',
                'attendees.*.name' => 'nullable|string|max:100',
                'attendees.*.email' => 'nullable|string|email|max:100',
            ], [
                'billing_address.required' => 'Please enter your billing address – it goes on your GST invoice.',
                'billing_state.required' => 'Please choose your state.',
                'billing_pin.required' => 'Please enter your PIN code.',
                'billing_pin.digits' => 'The PIN code is 6 digits.',
                'pass_type_id.required' => 'Please choose a pass.',
                'pass_type_id.exists' => 'Please choose a pass from the list.',
                'quantity.max' => 'Please contact us for more than :max passes.',
            ]);

            $gstinState = GstStates::stateFromGstin($data['buyer_gstin'] ?? null);
            if ($gstinState && $gstinState !== $extra['billing_state']) {
                self::fail('billing_state', 'Your GSTIN is registered in ' . $gstinState . ' – choose that state, or check the GSTIN.');
            }
            $data['billing_address'] = $extra['billing_address'];
            $data['billing_state'] = $extra['billing_state'];
            $data['billing_pin'] = $extra['billing_pin'];

            $passType = PassType::find($extra['pass_type_id']);
            $quantity = (int) $extra['quantity'];

            // pass 1 is the buyer; passes 2+ need their own attendee
            for ($number = 2; $number <= $quantity; $number++) {
                $attendee = $extra['attendees'][$number] ?? [];
                $name = trim((string) ($attendee['name'] ?? ''));
                $email = trim((string) ($attendee['email'] ?? ''));
                if ($name === '' || $email === '') {
                    self::fail("attendees.{$number}.name", 'Enter the name and email for pass ' . $number . '.');
                }
                $attendees[$number] = ['name' => $name, 'email' => $email];
            }
        }

        // one email can hold only one pass for the same event
        $emails = [1 => strtolower($data['email'])];
        foreach ($attendees as $number => $attendee) {
            $emails[$number] = strtolower($attendee['email']);
        }
        if (count(array_unique($emails)) !== count($emails)) {
            self::fail('email', 'Each pass needs a different email address.');
        }
        // already registered = holds a pass, or sits in an order that is paid or still awaiting payment
        $taken = Booking::where('service_id', $chosenEvent->id)
            ->pluck('email')->filter()->map(fn ($e) => strtolower($e))->all();
        $heldInOrders = Order::where('service_id', $chosenEvent->id)
            ->whereIn('status', ['pending', 'paid'])
            ->pluck('attendees')
            ->flatMap(fn ($list) => collect((array) $list)->pluck('email'))
            ->filter()->map(fn ($e) => strtolower($e))->all();
        $taken = array_unique(array_merge($taken, $heldInOrders));
        foreach ($emails as $number => $email) {
            if (in_array($email, $taken, true)) {
                self::fail($number === 1 ? 'email' : "attendees.{$number}.email", $email . ' is already registered for this event.');
            }
        }

        // free event: a booking straight away
        if (! $passType) {
            $booking = Booking::create([
                'booking_id' => BookingNumber::next(),
                'attendee_no' => 1,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'company' => ($data['company'] ?? null) ?: null,
                'designation' => ($data['designation'] ?? null) ?: null,
                'date' => $data['date'],
                'service_id' => $chosenEvent->id,
                'event' => $chosenEvent->title,
            ]);

            return ['event' => $chosenEvent, 'data' => $data, 'booking' => $booking];
        }

        // paid event: the price from Ticketing, or a special price set by an admin
        $special = $options['unit_price'] ?? null;
        $quote = $special !== null
            ? Pricing::quoteAt((float) $special, $quantity)
            : Pricing::quote($passType, $quantity, $chosenEvent);

        $attendeeList = [1 => ['name' => $data['name'], 'email' => $data['email']]] + $attendees;

        $order = Order::create([
            'order_no' => Order::newOrderNumber(),
            'service_id' => $chosenEvent->id,
            'pass_type_id' => $passType->id,
            'event' => $chosenEvent->title,
            'pass_name' => $passType->name,
            'buyer_name' => $data['name'],
            'buyer_email' => $data['email'],
            'buyer_phone' => $data['phone'],
            'buyer_company' => ($data['company'] ?? null) ?: null,
            'buyer_designation' => ($data['designation'] ?? null) ?: null,
            'buyer_gstin' => $data['buyer_gstin'] ?? null,
            'billing_address' => trim((string) ($data['billing_address'] ?? '')) ?: null,
            'billing_state' => ($data['billing_state'] ?? null) ?: GstStates::stateFromGstin($data['buyer_gstin'] ?? null),
            'billing_pin' => ($data['billing_pin'] ?? null) ?: null,
            'quantity' => $quantity,
            'attendees' => $attendeeList,
            'unit_price' => $quote['unit'],
            'discount_total' => $quote['discount'],
            'discount_label' => $quote['discount_label'],
            'tax_percent' => $quote['tax_percent'],
            'tax_total' => $quote['tax'],
            'total' => $quote['total'],
            'status' => 'pending',
        ] + (isset($options['created_by']) ? [
            'created_by' => $options['created_by'],
            'list_price' => $special !== null ? round($passType->priceOn(), 2) : null,
        ] : []));

        return ['event' => $chosenEvent, 'data' => $data, 'order' => $order];
    }

    /** Send the visitor back to the form with one message. */
    private static function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
