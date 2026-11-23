<?php

namespace App\Support;

use App\Models\Coupon;
use App\Models\CouponCode;
use App\Models\CouponRedemption;
use App\Models\Order;
use App\Models\PassType;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Coupon codes on the registration form (Ticketing > Coupons).
 *
 * A code counts as used while its order is awaiting payment or paid; if that order is
 * cancelled, fails or is deleted, the code is free again. Claiming happens inside the
 * same database transaction that creates the order, with the code and coupon rows
 * locked – two people submitting the same one-time code at the same moment cannot
 * both get it.
 */
class Coupons
{
    /** Letters and digits that cannot be mistaken for each other (no 0/O, 1/I/L). */
    private const ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    public static function normalize(?string $code): string
    {
        return strtoupper(preg_replace('/\s+/', '', (string) $code));
    }

    /**
     * Check a code without using it (the "Apply" button). Returns the coupon details,
     * or throws a ValidationException on 'coupon_code' saying why not.
     */
    public static function check(string $code, Service $event, ?PassType $pass, int $quantity, ?string $email): array
    {
        $codeRow = CouponCode::with('coupon.passType')->where('code', self::normalize($code))->first();

        return self::verify($codeRow, $event, $pass, $quantity, $email);
    }

    /**
     * Claim a code for an order about to be created. Call inside a DB transaction.
     * Locks the coupon and the code, releases this person's own earlier unpaid order
     * on the same coupon (they are starting again), then re-checks everything.
     */
    public static function claim(string $code, Service $event, PassType $pass, int $quantity, string $email): array
    {
        $codeRow = CouponCode::where('code', self::normalize($code))->lockForUpdate()->first();
        if ($codeRow) {
            Coupon::whereKey($codeRow->coupon_id)->lockForUpdate()->first();
            $codeRow->load('coupon.passType');

            // the same person trying again: their earlier unpaid order on this coupon makes way
            Order::where('status', 'pending')
                ->whereRaw('LOWER(buyer_email) = ?', [strtolower($email)])
                ->whereIn('coupon_code_id', CouponCode::where('coupon_id', $codeRow->coupon_id)->select('id'))
                ->update(['status' => 'cancelled']);
        }

        return self::verify($codeRow, $event, $pass, $quantity, $email);
    }

    /** Write the redemption for a new order. */
    public static function record(array $claim, Order $order): CouponRedemption
    {
        return CouponRedemption::create([
            'coupon_id' => $claim['coupon']->id,
            'coupon_code_id' => $claim['code']->id,
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'email' => strtolower($order->buyer_email),
            'name' => $order->buyer_name,
            'quantity' => $order->quantity,
            'discount' => (float) ($order->discount_total ?? 0),
        ]);
    }

    /** Every rule, in the order a customer would want to hear about it. */
    private static function verify(?CouponCode $codeRow, Service $event, ?PassType $pass, int $quantity, ?string $email): array
    {
        $fail = fn (string $message) => throw ValidationException::withMessages(['coupon_code' => $message]);
        $email = $email ? strtolower(trim($email)) : null;

        if (! $codeRow || ! $codeRow->coupon) {
            $fail('This code is not valid. Please check it and try again.');
        }
        $coupon = $codeRow->coupon;
        if (! $codeRow->is_active) {
            $fail('This code is no longer active.');
        }
        if ($reason = $coupon->closedReason()) {
            $fail($reason);
        }
        if ((int) $coupon->service_id !== (int) $event->id) {
            $fail('This code is for ' . ($coupon->service?->title ?? 'another event') . '.');
        }
        if ($coupon->pass_type_id && $pass && (int) $coupon->pass_type_id !== (int) $pass->id) {
            $fail('This code is for the ' . ($coupon->passType?->name ?? 'other') . ' only.');
        }
        if ($quantity > $coupon->max_passes) {
            $fail('This code is for ' . $coupon->max_passes . ' ' . Str::plural('pass', $coupon->max_passes)
                . ' per booking – please choose ' . ($coupon->max_passes === 1 ? '1 pass' : 'up to ' . $coupon->max_passes . ' passes') . '.');
        }

        if ($email) {
            if ($codeRow->lock_to_email && $codeRow->assigned_email && strtolower($codeRow->assigned_email) !== $email) {
                $fail('This code was issued to a different email address – please register with the email it was sent to.');
            }

            // already used by this person (on this code or any other code of the same coupon)?
            if ($coupon->once_per_email) {
                $mine = CouponRedemption::where('coupon_id', $coupon->id)->where('email', $email)
                    ->whereHas('order', fn ($q) => $q->where('status', 'paid'))->exists();
                if ($mine) {
                    $fail('You have already used a code from this offer (' . $email . ').');
                }
            }
        }

        // uses that count: orders awaiting payment or paid (this person's own unpaid one is about to be replaced)
        $used = CouponRedemption::where('coupon_code_id', $codeRow->id)
            ->whereHas('order', fn ($q) => $q->whereIn('status', ['pending', 'paid']))
            ->when($email, fn ($q) => $q->where(fn ($w) => $w->where('email', '!=', $email)
                ->orWhereHas('order', fn ($o) => $o->where('status', 'paid'))))
            ->count();
        if ($used >= $codeRow->max_uses) {
            $fail($codeRow->max_uses === 1 ? 'This code has already been used.' : 'This code has reached its limit of ' . $codeRow->max_uses . ' uses.');
        }

        return [
            'coupon' => $coupon,
            'code' => $codeRow,
            'label' => 'Coupon ' . $codeRow->code . ' – ' . $coupon->label(),
        ];
    }

    /**
     * Make one-time codes. $people: [['name' => …, 'email' => …], …] for codes meant
     * for someone (optional); otherwise $count blank codes.
     *
     * @return \Illuminate\Support\Collection<int, CouponCode>
     */
    public static function generate(Coupon $coupon, string $prefix, int $count, array $people = [], bool $lock = false)
    {
        $prefix = trim(preg_replace('/[^A-Z0-9]+/', '-', self::normalize($prefix)), '-');
        $rows = $people ?: array_fill(0, $count, ['name' => null, 'email' => null]);
        $made = collect();

        DB::transaction(function () use ($coupon, $prefix, $rows, $lock, &$made) {
            foreach ($rows as $person) {
                do {
                    $code = ($prefix !== '' ? $prefix . '-' : '') . self::random(8);
                } while (CouponCode::where('code', $code)->exists());

                $made->push(CouponCode::create([
                    'coupon_id' => $coupon->id,
                    'code' => $code,
                    'max_uses' => 1,
                    'assigned_name' => $person['name'] ? mb_substr($person['name'], 0, 100) : null,
                    'assigned_email' => $person['email'] ? strtolower(mb_substr($person['email'], 0, 100)) : null,
                    'lock_to_email' => $lock && ! empty($person['email']),
                    'is_active' => true,
                ]));
            }
        });

        return $made;
    }

    private static function random(int $length): string
    {
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
        }

        return $out;
    }

    /** used / reserved / free counts and the paid total, for the admin page. */
    public static function stats(Coupon $coupon): array
    {
        $codes = CouponCode::where('coupon_id', $coupon->id);
        $redemptions = CouponRedemption::where('coupon_id', $coupon->id);
        $paid = (clone $redemptions)->whereHas('order', fn ($q) => $q->where('status', 'paid'));

        return [
            'codes' => (clone $codes)->count(),
            'used' => (clone $paid)->count(),
            'reserved' => (clone $redemptions)->whereHas('order', fn ($q) => $q->where('status', 'pending'))->count(),
            'passes' => (int) (clone $paid)->sum('quantity'),
            'discount' => (float) (clone $paid)->sum('discount'),
            'revenue' => (float) Order::whereIn('id', (clone $paid)->select('order_id'))->sum('total'),
        ];
    }
}
