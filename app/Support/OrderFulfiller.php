<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Turns a paid order into passes: one booking (VD number) per attendee,
 * then the confirmation emails. Safe to call twice - it never duplicates.
 */
class OrderFulfiller
{
    /**
     * Mark an order paid and create its bookings.
     *
     * @return bool true when this call did the work, false when it was already done
     */
    public static function markPaid(Order $order, array $payment = []): bool
    {
        if ($order->status === 'paid') {
            return false;
        }

        $issued = DB::transaction(function () use ($order, $payment) {
            // lock the row: the browser return and the webhook arrive together
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();
            if (! $locked || $locked->status === 'paid') {
                return false;
            }
            $order->refresh();
            $order->status = 'paid';
            $order->paid_at = $order->paid_at ?: now();
            foreach (['payment_reference', 'payment_method', 'gateway'] as $field) {
                if (! empty($payment[$field])) {
                    $order->{$field} = $payment[$field];
                }
            }
            $order->save();

            self::createBookings($order);

            return true;
        });

        if (! $issued) {
            return false;
        }

        $order = $order->fresh('bookings');
        self::notify($order);          // buyer receipt + admin copy
        self::notifyAttendees($order); // one email per pass holder
        \App\Support\InvoiceIssuer::autoIssue($order);   // GST invoice, emailed if switched on

        return true;
    }

    /** One booking per attendee, in order. Does nothing when they already exist. */
    public static function createBookings(Order $order): void
    {
        if ($order->bookings()->exists()) {
            return;
        }

        $event = $order->service;
        $date = $event?->date?->format('Y-m-d H:i:s');
        $perPass = $order->quantity ? round((float) $order->total / $order->quantity, 2) : null;

        foreach ($order->attendeeList() as $number => $person) {
            Booking::create([
                'order_id' => $order->id,
                'booking_id' => BookingNumber::next(),
                'attendee_no' => $number,
                'name' => $person['name'],
                'email' => $person['email'],
                'phone' => $order->buyer_phone,
                'company' => $order->buyer_company,
                'designation' => $number === 1 ? $order->buyer_designation : null,
                'amount' => $perPass,
                'date' => $date,
                'service_id' => $order->service_id,
                'event' => $order->event,
            ]);
        }
    }

    /** Every pass holder gets their own pass number (the buyer gets the receipt instead). */
    public static function notifyAttendees(Order $order): void
    {
        foreach ($order->bookings()->orderBy('attendee_no')->get() as $booking) {
            if (! $booking->email || strtolower($booking->email) === strtolower((string) $order->buyer_email)) {
                continue;   // the buyer already has the full receipt
            }

            try {
                \Notification::route('mail', $booking->email)->notify(new \App\Notifications\AttendeePassNotification([
                    'name' => $booking->name,
                    'email' => $booking->email,
                    'booking_id' => $booking->booking_id,
                    'attendee_no' => $booking->attendee_no,
                    'quantity' => $order->quantity,
                    'pass_name' => $order->pass_name,
                    'event' => $order->event,
                    'event_date' => $order->service?->date,
                    'buyer_name' => $order->buyer_name,
                    'buyer_company' => $order->buyer_company,
                ]));
            } catch (\Throwable $e) {
                report($e);   // one bad address never stops the rest
            }
        }
    }

    /** A payment attempt failed: tell the buyer how to try again. */
    public static function notifyPaymentFailed(Order $order, ?string $reason = null): void
    {
        self::notify($order, true, ['payment_failed' => true, 'failure_reason' => $reason]);
    }

    /** Confirmation to the buyer (and the admin copy), plus the WhatsApp message. */
    public static function notify(Order $order, bool $awaitingPayment = false, array $extra = []): void
    {
        $bookings = $order->bookings()->orderBy('attendee_no')->get();

        $payload = [
            'name' => $order->buyer_name,
            'email' => $order->buyer_email,
            'phone' => $order->buyer_phone,
            'company' => $order->buyer_company,
            'designation' => $order->buyer_designation,
            'event' => $order->event,
            'event_date' => $order->service?->date,
            'order' => $order,
            'quote' => $order->quoteSummary(),
            'awaiting_payment' => $awaitingPayment,
            'pay_url' => $awaitingPayment ? route('order.pay', $order->order_no) : null,
            'booking_id' => $bookings->first()?->booking_id,
            'attendees' => $bookings->map(fn ($b) => [
                'name' => $b->name, 'email' => $b->email, 'booking_id' => $b->booking_id,
            ])->all(),
            'payment_instructions' => Setting::find(1)?->payment_instructions,
            'ip' => request()?->ip(),
        ] + $extra;

        try {
            event(new \App\Events\BookingCreated($payload));
        } catch (\Throwable $e) {
            report($e);
        }

        if (! $awaitingPayment) {
            self::whatsApp($order);
        }
    }

    /** WhatsApp confirmation through Interakt; never blocks anything. */
    public static function whatsApp(Order $order): void
    {
        $token = config('services.interakt.token');
        if (! $token || ! $order->buyer_phone) {
            return;
        }

        try {
            Http::withHeaders([
                'Authorization' => 'Basic ' . $token,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post('https://api.interakt.ai/v1/public/message/', [
                'countryCode' => '+91',
                'phoneNumber' => $order->buyer_phone,
                'callbackData' => $order->order_no,
                'type' => 'Template',
                'template' => [
                    'name' => config('services.interakt.template', 'new_reg_tba_events'),
                    'languageCode' => 'en',
                    'bodyValues' => [$order->buyer_name],
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
