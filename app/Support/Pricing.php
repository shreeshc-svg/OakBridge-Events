<?php

namespace App\Support;

use App\Models\PassType;
use App\Models\PricingTier;
use App\Models\Service;
use App\Models\Setting;

/**
 * Works out what a buyer pays (Admin > Ticketing).
 *
 * One place does the maths: the booking form shows it live, the server
 * recalculates it on submit, and the order stores the result.
 * An event with no active pass type is free to register, as before.
 */
class Pricing
{
    public const MAX_PASSES = 50;

    /** Active pass types for an event, cheapest slot first. */
    public static function passTypes(?Service $event): \Illuminate\Support\Collection
    {
        if (! $event) {
            return collect();
        }

        try {
            return PassType::where('service_id', $event->id)
                ->where('is_active', true)
                ->orderBy('sort_order')->orderBy('id')
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public static function isPaid(?Service $event): bool
    {
        return self::passTypes($event)->isNotEmpty();
    }

    /** Discount slabs that apply to an event: its own if it has any, otherwise the site-wide ones. */
    public static function tiers(?Service $event = null): \Illuminate\Support\Collection
    {
        try {
            $own = $event
                ? PricingTier::where('service_id', $event->id)->orderBy('min_quantity')->get()
                : collect();

            return $own->isNotEmpty()
                ? $own
                : PricingTier::whereNull('service_id')->orderBy('min_quantity')->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /** The best slab for this many passes. */
    public static function tierFor(int $quantity, ?Service $event = null): ?PricingTier
    {
        return self::tiers($event)
            ->filter(fn ($tier) => $quantity >= $tier->min_quantity)
            ->sortByDesc('min_quantity')
            ->first();
    }

    /**
     * Full breakdown for a purchase.
     *
     * @return array{unit:float,is_early:bool,quantity:int,subtotal:float,discount:float,
     *               discount_label:?string,net:float,tax_percent:float,tax:float,
     *               tax_included:bool,tax_label:string,total:float,per_pass:float,currency:string}
     */
    public static function quote(PassType $passType, int $quantity, ?Service $event = null): array
    {
        $quantity = max(1, min($quantity, self::maxPasses()));
        $unit = round($passType->priceOn(), 2);
        $subtotal = round($unit * $quantity, 2);

        $tier = self::tierFor($quantity, $event ?: $passType->service);
        $discount = $tier ? round($tier->discountPerPass($unit) * $quantity, 2) : 0.0;
        $net = round($subtotal - $discount, 2);

        $setting = self::setting();
        $taxPercent = (float) ($setting->tax_percent ?? 0);
        $included = (bool) ($setting->prices_include_tax ?? true);

        if ($taxPercent <= 0) {
            $tax = 0.0;
            $total = $net;
        } elseif ($included) {
            // the price already contains the tax; show how much of it is tax
            $tax = round($net - ($net / (1 + $taxPercent / 100)), 2);
            $total = $net;
        } else {
            $tax = round($net * $taxPercent / 100, 2);
            $total = round($net + $tax, 2);
        }

        return [
            'unit' => $unit,
            'is_early' => $passType->isEarlyOn(),
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'discount_label' => $tier?->label(),
            'net' => $net,
            'tax_percent' => $taxPercent,
            'tax' => $tax,
            'tax_included' => $included,
            'tax_label' => (string) ($setting->tax_label ?: 'Tax'),
            'total' => $total,
            'per_pass' => $quantity ? round($total / $quantity, 2) : 0.0,
            'currency' => 'INR',
        ];
    }

    public static function maxPasses(): int
    {
        $max = (int) (self::setting()->max_passes_per_order ?? 10);

        return max(1, min($max ?: 10, self::MAX_PASSES));
    }

    /** Everything the booking form's script needs, so the live total matches the server. */
    public static function payload(Service $event): array
    {
        return [
            'max' => self::maxPasses(),
            'currency' => '₹',
            'taxPercent' => (float) (self::setting()->tax_percent ?? 0),
            'taxIncluded' => (bool) (self::setting()->prices_include_tax ?? true),
            'taxLabel' => (string) (self::setting()->tax_label ?: 'Tax'),
            'passes' => self::passTypes($event)->map(fn ($pass) => [
                'id' => $pass->id,
                'name' => $pass->name,
                'price' => $pass->priceOn(),
                'early' => $pass->isEarlyOn(),
            ])->values()->all(),
            'tiers' => self::tiers($event)->map(fn ($tier) => [
                'min' => (int) $tier->min_quantity,
                'type' => $tier->discount_type,
                'value' => (float) $tier->discount_value,
                'label' => $tier->label(),
            ])->values()->all(),
        ];
    }

    /** Money as ₹1,23,456.78 (Indian grouping), without decimals when round. */
    public static function money(float $amount): string
    {
        $rounded = round($amount, 2);
        $decimals = fmod($rounded, 1) == 0.0 ? 0 : 2;
        $number = number_format(abs($rounded), $decimals, '.', '');
        [$whole, $fraction] = array_pad(explode('.', $number), 2, null);

        if (strlen($whole) > 3) {
            $last3 = substr($whole, -3);
            $rest = substr($whole, 0, -3);
            $whole = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest) . ',' . $last3;
        }

        return ($rounded < 0 ? '-' : '') . '₹' . $whole . ($fraction !== null ? '.' . $fraction : '');
    }

    private static function setting(): Setting
    {
        if (! app()->bound('site.pricing.setting')) {
            try {
                $setting = Setting::find(1) ?: new Setting();
            } catch (\Throwable $e) {
                $setting = new Setting();
            }
            app()->instance('site.pricing.setting', ['row' => $setting]);
        }

        return app('site.pricing.setting')['row'];
    }
}
