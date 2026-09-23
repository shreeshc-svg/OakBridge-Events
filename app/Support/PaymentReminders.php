<?php

namespace App\Support;

use App\Models\Order;
use App\Models\OrderReminder;
use App\Notifications\PaymentReminderNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Chasing unpaid orders.
 *
 * Three automatic reminders - a day, three days and a week after the order was
 * placed - and then nothing more. Admin can always send one by hand, but not
 * twice within a day, so a buyer is never chased twice over.
 */
class PaymentReminders
{
    /** Stage => hours after the order was placed. */
    public const SCHEDULE = [1 => 24, 2 => 72, 3 => 168];

    /** No second reminder within this many hours, however it is triggered. */
    public const COOLDOWN_HOURS = 24;

    /** Only an unpaid order with somewhere to write to can be chased. */
    public static function isChaseable(Order $order): bool
    {
        return $order->status === 'pending' && filter_var($order->buyer_email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /** Reads the loaded relation, so a list of orders is one query, not one per row. */
    private static function delivered(Order $order)
    {
        return $order->reminders->where('failed', false);
    }

    public static function lastSentAt(Order $order): ?Carbon
    {
        // the relation is ordered newest first
        return self::delivered($order)->first()?->sent_at;
    }

    public static function sentCount(Order $order): int
    {
        return self::delivered($order)->count();
    }

    /** Hours left before another reminder is allowed, or 0 if one can go now. */
    public static function hoursUntilAllowed(Order $order): float
    {
        $last = self::lastSentAt($order);

        if (! $last) {
            return 0;
        }

        $ready = $last->copy()->addHours(self::COOLDOWN_HOURS);

        return $ready->isPast() ? 0 : round(now()->floatDiffInHours($ready), 1);
    }

    /** Why a reminder cannot go right now, in words for the admin screen. */
    public static function blockedReason(Order $order): ?string
    {
        if ($order->status === 'paid') {
            return 'This order is paid – there is nothing to chase.';
        }

        if ($order->status !== 'pending') {
            return 'This order is ' . strtolower($order->statusLabel()) . ' – reminders only go to orders awaiting payment.';
        }

        if (filter_var($order->buyer_email, FILTER_VALIDATE_EMAIL) === false) {
            return 'There is no usable email address on this order.';
        }

        $hours = self::hoursUntilAllowed($order);

        if ($hours > 0) {
            $last = self::lastSentAt($order);

            return 'A reminder went out ' . $last->diffForHumans() . '. The next one can go in '
                . ($hours >= 1 ? round($hours) . ' hour' . (round($hours) === 1 ? '' : 's') : 'under an hour') . '.';
        }

        return null;
    }

    public static function canSendNow(Order $order): bool
    {
        return self::blockedReason($order) === null;
    }

    /** The next automatic reminder that is still to come, or null if they are done. */
    public static function nextAutomatic(Order $order): ?array
    {
        if (! self::isChaseable($order) || ! $order->created_at) {
            return null;
        }

        $sent = self::delivered($order)->whereNotNull('stage')->pluck('stage')->all();
        $last = self::lastSentAt($order);

        foreach (self::SCHEDULE as $stage => $hours) {
            if (in_array($stage, $sent, true)) {
                continue;
            }

            $at = $order->created_at->copy()->addHours($hours);

            // a reminder sent by hand holds the next automatic one off, so show
            // the time it will really go rather than the time it was due
            if ($last && $at->lt($ready = $last->copy()->addHours(self::COOLDOWN_HOURS))) {
                $at = $ready;
            }

            return ['stage' => $stage, 'at' => $at];
        }

        return null;
    }

    /**
     * Which automatic reminder is due for this order right now, if any.
     *
     * If several are overdue - the site was quiet for a week, say - only the
     * latest is sent, so nobody wakes up to three emails at once.
     */
    public static function dueStage(Order $order): ?int
    {
        if (! self::isChaseable($order) || ! $order->created_at || self::hoursUntilAllowed($order) > 0) {
            return null;
        }

        $sent = self::delivered($order)->whereNotNull('stage')->pluck('stage')->all();
        $due = null;

        foreach (self::SCHEDULE as $stage => $hours) {
            if (! in_array($stage, $sent, true) && $order->created_at->copy()->addHours($hours)->isPast()) {
                $due = $stage;
            }
        }

        return $due;
    }

    /** Unpaid orders with an automatic reminder waiting to go out. */
    public static function dueOrders()
    {
        return Order::with('reminders')
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subDays(30))   // nothing older is worth chasing
            ->orderBy('id')
            ->get()
            ->filter(fn (Order $order) => self::dueStage($order) !== null);
    }

    /**
     * Send one reminder and write down that it went.
     *
     * Returns the record either way - a failure is recorded too, so the admin
     * screen can show that the mail did not get out.
     */
    public static function send(Order $order, string $kind = 'manual', ?int $stage = null, ?string $by = null): OrderReminder
    {
        $subject = self::subjectFor($order, $stage);

        $reminder = $order->reminders()->create([
            'kind' => $kind,
            'stage' => $stage,
            'sent_to' => $order->buyer_email,
            'subject' => $subject,
            'sent_by' => $by,
            'sent_at' => now(),
        ]);

        try {
            Notification::route('mail', $order->buyer_email)->notify(
                new PaymentReminderNotification(self::payload($order, $stage, $subject))
            );
        } catch (\Throwable $e) {
            Log::warning('Payment reminder failed for ' . $order->order_no . ': ' . $e->getMessage());
            $reminder->update(['failed' => true, 'error' => mb_substr($e->getMessage(), 0, 500)]);
        }

        $order->load('reminders');   // so the screen that called us sees this one

        return $reminder->fresh();
    }

    public static function subjectFor(Order $order, ?int $stage = null): string
    {
        return $stage === 3
            ? 'Last reminder – your passes for ' . $order->event . ' are still unpaid'
            : 'Your passes for ' . $order->event . ' are waiting – complete your payment';
    }

    private static function payload(Order $order, ?int $stage, string $subject): array
    {
        return [
            'order' => $order,
            'stage' => $stage,
            'subject' => $subject,
            'is_last' => $stage === 3,
            'pay_url' => route('order.pay', $order->order_no),
            'quote' => $order->quoteSummary(),
        ];
    }
}
