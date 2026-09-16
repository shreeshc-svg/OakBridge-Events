<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Booking IDs: "VD" + number (VD9167, VD9168, ...).
 * The last number issued is kept in settings.booking_last_number, so the
 * sequence carries on even after the registrations table is emptied.
 */
class BookingNumber
{
    public const PREFIX = 'VD';
    public const FIRST = 9167;

    /** Reserve and return the next booking ID. */
    public static function next(): string
    {
        return DB::transaction(function () {
            $stored = DB::table('settings')->where('id', 1)->lockForUpdate()->value('booking_last_number');
            $latest = self::numberOf(DB::table('bookings')->latest('id')->value('booking_id'));
            $last = max((int) $stored, (int) $latest);
            $next = $last > 0 ? $last + 1 : self::FIRST;

            DB::table('settings')->where('id', 1)->update(['booking_last_number' => $next]);

            return self::format($next);
        });
    }

    /** Highest number among the saved registrations, or null when there are none. */
    public static function highestInBookings(): ?int
    {
        $highest = null;
        foreach (DB::table('bookings')->pluck('booking_id') as $id) {
            $number = self::numberOf($id);
            if ($number !== null && ($highest === null || $number > $highest)) {
                $highest = $number;
            }
        }

        return $highest;
    }

    public static function format(int $number): string
    {
        return self::PREFIX . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    private static function numberOf(?string $bookingId): ?int
    {
        if ($bookingId === null || ! preg_match('/(\d+)$/', $bookingId, $m)) {
            return null;
        }

        return (int) $m[1];
    }
}
