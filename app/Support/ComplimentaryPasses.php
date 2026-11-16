<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\PassType;
use App\Models\Service;
use App\Notifications\ComplimentaryPassNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Admin > Ticketing > Complimentary passes: a free pass for a special guest.
 * It is a normal pass number (Booking) at ₹0 – no order, no payment, no invoice –
 * and the pass goes to the guest by email.
 */
class ComplimentaryPasses
{
    /** Create the pass. The caller has checked the event and that the email is free. */
    public static function issue(Service $event, array $guest, ?PassType $pass, ?int $adminId): Booking
    {
        return Booking::create([
            'booking_id' => BookingNumber::next(),
            'attendee_no' => 1,
            'name' => $guest['name'],
            'email' => strtolower(trim($guest['email'])),
            'phone' => $guest['phone'],
            'amount' => 0,
            'date' => $event->date->format('Y-m-d H:i:s'),
            'service_id' => $event->id,
            'event' => $event->title,
            'pass_name' => $pass?->name,
            'complimentary_at' => now(),
            'complimentary_by' => $adminId,
        ]);
    }

    /** Email the pass. Returns false (and logs why) when the email could not be sent. */
    public static function send(Booking $booking): bool
    {
        try {
            Notification::route('mail', $booking->email)->notify(new ComplimentaryPassNotification(self::mailData($booking)));
            $booking->forceFill(['emailed_at' => now()])->save();

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    /** A preview of the pass email to the admin – no pass is created. */
    public static function sendTest(Service $event, string $to, array $guest, ?PassType $pass): bool
    {
        try {
            Notification::route('mail', $to)->notify(new ComplimentaryPassNotification([
                'name' => $guest['name'] ?: 'Guest Name',
                'event' => $event->title,
                'event_date' => $event->date,
                'venue' => $event->venue,
                'booking_id' => BookingNumber::PREFIX . '0000',
                'pass_name' => $pass?->name,
                'test' => true,
            ]));

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    public static function mailData(Booking $booking): array
    {
        $event = $booking->service;

        return [
            'name' => $booking->name,
            'event' => $event?->title ?? $booking->event,
            'event_date' => $event?->date,
            'venue' => $event?->venue,
            'booking_id' => $booking->booking_id,
            'pass_name' => $booking->pass_name,
        ];
    }

    /** "+91 98100 00000" -> "+919810000000"; null when it is not a phone number. */
    public static function cleanPhone(?string $phone): ?string
    {
        $clean = preg_replace('/[\s\-().]/', '', (string) $phone);

        return preg_match('/^\+?\d{10,15}$/', $clean) ? $clean : null;
    }
}
