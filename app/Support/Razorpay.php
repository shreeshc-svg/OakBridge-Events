<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Razorpay checkout, over the plain REST API (no SDK to install on the server).
 *
 * Keys live in .env: RAZORPAY_KEY, RAZORPAY_SECRET, RAZORPAY_WEBHOOK_SECRET.
 * Admin > Ticketing switches online payment on; without keys it stays off.
 */
class Razorpay
{
    public const API = 'https://api.razorpay.com/v1';

    public static function keyId(): ?string
    {
        return config('services.razorpay.key') ?: null;
    }

    private static function secret(): ?string
    {
        return config('services.razorpay.secret') ?: null;
    }

    /** True when keys are present and an admin has switched online payment on. */
    public static function enabled(): bool
    {
        if (! self::keyId() || ! self::secret()) {
            return false;
        }

        try {
            return (bool) Setting::find(1)?->online_payment;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function isTestMode(): bool
    {
        return str_starts_with((string) self::keyId(), 'rzp_test');
    }

    /**
     * Create (or reuse) the Razorpay order for one of our orders.
     * Returns the Razorpay order id, or null when it could not be created.
     */
    public static function orderIdFor(Order $order): ?string
    {
        if ($order->gateway_order_id) {
            return $order->gateway_order_id;
        }
        if (! self::enabled()) {
            return null;
        }

        try {
            $response = Http::withBasicAuth(self::keyId(), self::secret())
                ->asJson()->timeout(20)
                ->post(self::API . '/orders', [
                    'amount' => self::paise((float) $order->total),
                    'currency' => 'INR',
                    'receipt' => $order->order_no,
                    'notes' => [
                        'order_no' => $order->order_no,
                        'event' => (string) $order->event,
                        'passes' => (string) $order->quantity,
                        'buyer' => (string) $order->buyer_email,
                    ],
                ]);

            if (! $response->successful() || ! $response->json('id')) {
                Log::error('Razorpay order failed', ['order' => $order->order_no, 'body' => $response->body()]);

                return null;
            }

            $order->update(['gateway_order_id' => $response->json('id'), 'gateway' => 'razorpay']);

            return $order->gateway_order_id;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /** Checkout returns these three; the signature proves the payment is real. */
    public static function paymentIsValid(string $razorpayOrderId, string $paymentId, string $signature): bool
    {
        $secret = self::secret();
        if (! $secret) {
            return false;
        }

        $expected = hash_hmac('sha256', $razorpayOrderId . '|' . $paymentId, $secret);

        return hash_equals($expected, $signature);
    }

    /** Webhook calls carry their own signature over the raw body. */
    public static function webhookIsValid(string $payload, ?string $signature): bool
    {
        $secret = config('services.razorpay.webhook_secret');
        if (! $secret || ! $signature) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $payload, $secret), $signature);
    }

    /** Razorpay works in paise. */
    public static function paise(float $rupees): int
    {
        return (int) round($rupees * 100);
    }

    /** Confirm with Razorpay what a payment actually is (never trust the browser alone). */
    public static function fetchPayment(string $paymentId): ?array
    {
        if (! self::keyId() || ! self::secret()) {
            return null;
        }

        try {
            $response = Http::withBasicAuth(self::keyId(), self::secret())
                ->timeout(20)->get(self::API . '/payments/' . $paymentId);

            return $response->successful() ? $response->json() : null;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}
