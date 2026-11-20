<?php

namespace App\Support;

use App\Models\Booking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Removing passes always goes through here: each pass is copied to the
 * cancelled-passes log (number, holder, who removed it, when, from where)
 * before it is deleted, so no pass number ever disappears without a trace.
 */
class CancelledPasses
{
    /** Log and delete these passes. Returns how many were removed. */
    public static function remove(iterable $bookings, string $source, ?string $by = null): int
    {
        $bookings = collect($bookings)->filter();
        if ($bookings->isEmpty()) {
            return 0;
        }

        return DB::transaction(function () use ($bookings, $source, $by) {
            self::record($bookings, $source, $by);

            // a query delete: the model's own "deleting" safety net must not log them twice
            return Booking::whereIn('id', $bookings->pluck('id'))->delete();
        });
    }

    /** Write the log rows (without deleting). */
    public static function record(iterable $bookings, string $source, ?string $by = null): void
    {
        $user = auth()->user();
        $now = now();
        $orders = DB::table('orders')->whereIn('id', collect($bookings)->pluck('order_id')->filter()->unique())
            ->get(['id', 'order_no', 'pass_name'])->keyBy('id');

        $rows = collect($bookings)->map(function ($b) use ($orders, $source, $by, $user, $now) {
            $order = $b->order_id ? $orders->get($b->order_id) : null;

            return [
                'booking_id' => (string) $b->booking_id,
                'name' => $b->name,
                'email' => $b->email,
                'phone' => $b->phone,
                'company' => $b->company,
                'kind' => self::kind($b),
                'pass_name' => $b->pass_name ?? ($order->pass_name ?? null),
                'amount' => $b->amount,
                'service_id' => $b->service_id,
                'event' => $b->event,
                'order_id' => $b->order_id,
                'order_no' => $order->order_no ?? null,
                'issued_at' => $b->created_at,
                'source' => mb_substr($source, 0, 80),
                'cancelled_by' => $user?->id,
                'cancelled_by_name' => mb_substr($by ?? ($user ? ($user->name ?: $user->email) : 'System'), 0, 120),
                'cancelled_at' => $now,
            ];
        })->values()->all();

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('cancelled_passes')->insert($chunk);
        }
    }

    public static function kind($booking): string
    {
        if (! empty($booking->complimentary_at)) {
            return 'Complimentary';
        }
        if (! empty($booking->order_id)) {
            return 'Paid order';
        }

        return (float) ($booking->amount ?? 0) > 0 ? 'Paid' : 'Free registration';
    }
}
