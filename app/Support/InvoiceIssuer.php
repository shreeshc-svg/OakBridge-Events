<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Setting;
use App\Notifications\InvoiceNotification;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * GST tax invoices for paid orders.
 *
 * Numbers run PREFIX/FY/NNNN with no gaps and no repeats inside a financial
 * year (April to March). An invoice copies every printed detail into its
 * snapshot when issued, so later edits never alter it.
 */
class InvoiceIssuer
{
    /** Seller fields an invoice cannot go out without. */
    public const REQUIRED = [
        'seller_name' => 'company name',
        'seller_address' => 'address',
        'seller_gstin' => 'GSTIN',
        'seller_state' => 'state',
    ];

    public const DEFAULT_DECLARATION = 'We declare that this invoice shows the actual price of the services described and that all particulars are true and correct.';

    public static function missingSellerDetails(?Setting $setting = null): array
    {
        $setting ??= Setting::find(1);
        $missing = [];

        foreach (self::REQUIRED as $field => $label) {
            if (! trim((string) ($setting?->{$field} ?? ''))) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    /** Why an invoice cannot be issued for this order right now, or null if it can. */
    public static function blockedReason(Order $order): ?string
    {
        if ($order->status !== 'paid') {
            return 'An invoice is issued once the order is paid.';
        }

        if ((float) $order->total <= 0) {
            return 'Nothing was charged for this order, so there is nothing to invoice.';
        }

        if ($missing = self::missingSellerDetails()) {
            return 'Fill in Invoice details first – missing ' . implode(', ', $missing) . '.';
        }

        return null;
    }

    /** 2026-09-20 -> 2026-27, 2027-02-01 -> 2026-27 (April to March). */
    public static function financialYear(Carbon $date): string
    {
        $start = $date->month >= 4 ? $date->year : $date->year - 1;

        return $start . '-' . substr((string) ($start + 1), -2);
    }

    /** IGST unless the event is in the seller's own state; no place of supply set means IGST. */
    public static function taxType(?string $sellerState, ?string $placeOfSupply): string
    {
        return $placeOfSupply && $sellerState && $placeOfSupply === $sellerState ? 'cgst_sgst' : 'igst';
    }

    /**
     * Issue the invoice for a paid order, or hand back the one it already has.
     *
     * The order row and the year's numbers are locked while the number is
     * chosen, so two requests at once (browser return plus webhook, or a
     * double click) can neither issue twice nor take the same number.
     */
    public static function issue(Order $order): Invoice
    {
        if ($existing = Invoice::where('order_id', $order->id)->first()) {
            return $existing;
        }

        if ($reason = self::blockedReason($order)) {
            throw new \RuntimeException($reason);
        }

        $setting = Setting::find(1);
        $prefix = strtoupper(trim((string) ($setting->invoice_prefix ?: 'OBE')));

        for ($attempt = 1; ; $attempt++) {
            try {
                return DB::transaction(function () use ($order, $prefix) {
                    Order::whereKey($order->id)->lockForUpdate()->first();

                    if ($existing = Invoice::where('order_id', $order->id)->first()) {
                        return $existing;
                    }

                    $issuedAt = now();
                    $fy = self::financialYear($issuedAt);
                    $seq = (int) Invoice::where('fy', $fy)->lockForUpdate()->max('seq') + 1;
                    $number = $prefix . '/' . $fy . '/' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

                    return Invoice::create([
                        'order_id' => $order->id,
                        'number' => $number,
                        'fy' => $fy,
                        'seq' => $seq,
                        'issued_at' => $issuedAt,
                        'snapshot' => self::snapshot($order->fresh(['service', 'bookings']), $number, $issuedAt),
                    ]);
                });
            } catch (QueryException $e) {
                // someone else took that number a moment earlier - go again
                if ($attempt >= 3) {
                    throw $e;
                }
            }
        }
    }

    /** Everything that is printed on the invoice. */
    public static function snapshot(Order $order, string $number, Carbon $issuedAt): array
    {
        $setting = Setting::find(1);
        $event = $order->service;

        $qty = max(1, (int) $order->quantity);
        $total = round((float) $order->total, 2);
        $tax = round((float) $order->tax_total, 2);
        $taxable = round($total - $tax, 2);

        // Work in amounts before tax whether prices were entered with tax included or not.
        $gross = round((float) $order->unit_price * $qty, 2);
        $listedNet = round($gross - (float) $order->discount_total, 2);
        $factor = $listedNet > 0 ? $taxable / $listedNet : 1;
        $amount = round($gross * $factor, 2);
        $discount = round($amount - $taxable, 2);

        $sellerState = $setting->seller_state;
        $placeOfSupply = $event?->place_of_supply ?: null;
        $taxType = self::taxType($sellerState, $placeOfSupply);
        $rate = (float) $order->tax_percent;

        if ($tax <= 0) {
            $taxes = [];
        } elseif ($taxType === 'cgst_sgst') {
            $half = round($tax / 2, 2);
            $taxes = [
                ['label' => 'CGST', 'rate' => $rate / 2, 'amount' => $half],
                ['label' => 'SGST', 'rate' => $rate / 2, 'amount' => round($tax - $half, 2)],
            ];
        } else {
            $taxes = [['label' => 'IGST', 'rate' => $rate, 'amount' => $tax]];
        }

        $buyerState = $order->billing_state ?: GstStates::stateFromGstin($order->buyer_gstin);

        return [
            'number' => $number,
            'date' => $issuedAt->toDateString(),
            'order_no' => $order->order_no,
            'payment_ref' => $order->payment_reference,
            'payment_mode' => $order->gateway === 'razorpay'
                ? 'Prepaid (Razorpay)'
                : ($order->payment_method ?: 'Prepaid'),
            'paid_at' => $order->paid_at?->toDateTimeString(),

            'seller' => [
                'name' => $setting->seller_name,
                'address' => $setting->seller_address,
                'gstin' => $setting->seller_gstin,
                'state' => $sellerState,
                'state_code' => GstStates::codeFor($sellerState),
                'phone' => $setting->seller_phone,
                'email' => $setting->seller_email,
                'bank_name' => $setting->bank_name,
                'bank_account' => $setting->bank_account,
                'bank_branch_ifsc' => $setting->bank_branch_ifsc,
                'declaration' => $setting->invoice_declaration ?: self::DEFAULT_DECLARATION,
            ],

            'buyer' => [
                'name' => $order->buyer_name,
                'company' => $order->buyer_company,
                'email' => $order->buyer_email,
                'phone' => $order->buyer_phone,
                'gstin' => $order->buyer_gstin,
                'address' => $order->billing_address,
                'state' => $buyerState,
                'state_code' => GstStates::codeFor($buyerState),
                'pin' => $order->billing_pin,
            ],

            'event' => [
                'title' => $event?->title ?: $order->event,
                'date' => $event?->date?->toDateTimeString(),
                'venue' => $event?->venue ?: $setting->address,
            ],

            'place_of_supply' => $placeOfSupply ? [
                'state' => $placeOfSupply,
                'code' => GstStates::codeFor($placeOfSupply),
            ] : null,
            'tax_type' => $taxType,

            'line' => [
                'description' => $order->pass_name . ' – ' . ($event?->title ?: $order->event),
                'sac' => $setting->invoice_sac,
                'qty' => $qty,
                'rate' => round($amount / $qty, 2),
                'amount' => $amount,
            ],
            'discount' => $discount > 0 ? [
                'label' => $order->discount_label ?: 'Discount',
                'amount' => $discount,
            ] : null,
            'taxable' => $taxable,
            'taxes' => $taxes,
            'total' => $total,
            'total_words' => self::amountInWords($total),

            'attendees' => $order->bookings->sortBy('attendee_no')->map(fn ($b) => [
                'name' => $b->name,
                'pass' => $b->booking_id,
            ])->values()->all(),
        ];
    }

    /** Email the invoice, PDF attached, to the buyer. Throws if it cannot be sent. */
    public static function send(Invoice $invoice): void
    {
        $email = $invoice->snapshot['buyer']['email'] ?? null;

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('There is no usable email address on this order.');
        }

        $pdf = app(InvoicePdf::class)->render($invoice->snapshot);

        Notification::route('mail', $email)->notify(new InvoiceNotification($invoice, $pdf));

        $invoice->increment('sent_count');
        $invoice->forceFill(['last_sent_at' => now()])->save();
    }

    /**
     * Called when an order is paid: issue the invoice and, if switched on,
     * email it. Never lets an invoice problem get in the way of the payment.
     */
    public static function autoIssue(Order $order): ?Invoice
    {
        if (self::blockedReason($order) !== null) {
            return null;
        }

        try {
            $invoice = self::issue($order);

            if (Setting::find(1)?->invoice_auto_send && ! $invoice->sent_count) {
                self::send($invoice);
            }

            return $invoice;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /** 9434.10 -> "INR Nine Thousand Four Hundred Thirty Four and Ten Paise Only" (Indian grouping). */
    public static function amountInWords(float $amount): string
    {
        $rupees = (int) floor(round($amount, 2));
        $paise = (int) round(($amount - $rupees) * 100);

        $words = 'INR ' . ($rupees ? self::words($rupees) : 'Zero');

        if ($paise) {
            $words .= ' and ' . self::words($paise) . ' Paise';
        }

        return $words . ' Only';
    }

    private static function words(int $n): string
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve',
            'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $below100 = fn (int $x) => $x < 20 ? $ones[$x] : trim($tens[intdiv($x, 10)] . ' ' . $ones[$x % 10]);

        $parts = [];
        foreach ([[10000000, 'Crore'], [100000, 'Lakh'], [1000, 'Thousand'], [100, 'Hundred']] as [$size, $name]) {
            if ($n >= $size) {
                $count = intdiv($n, $size);
                $parts[] = ($count >= 100 ? self::words($count) : $below100($count)) . ' ' . $name;
                $n %= $size;
            }
        }

        if ($n) {
            $parts[] = $below100($n);
        }

        return implode(' ', $parts);
    }
}
