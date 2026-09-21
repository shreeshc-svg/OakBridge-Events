<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\OrderFulfiller;
use App\Support\Razorpay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Razorpay checkout for an order (public, addressed by order number).
 */
class PaymentController extends Controller
{
    /** The pay page: opens Razorpay checkout for a pending order. */
    public function pay(string $orderNo)
    {
        $order = Order::where('order_no', $orderNo)->firstOrFail();

        if ($order->isPaid()) {
            return redirect()->route('order.done', $order->order_no);
        }
        if ($order->status === 'cancelled') {
            // an admin cancelled this one; a page view must never revive it
            return view('frontend.pay-done', [
                'order' => $order,
                'quote' => $order->quoteSummary(),
                'bookings' => collect(),
            ]);
        }

        $gatewayOrderId = Razorpay::enabled() ? Razorpay::orderIdFor($order) : null;

        return view('frontend.pay', [
            'order' => $order,
            'quote' => $order->quoteSummary(),
            'gatewayOrderId' => $gatewayOrderId,
            'keyId' => Razorpay::keyId(),
            'testMode' => Razorpay::isTestMode(),
        ]);
    }

    /** Checkout's success callback: verify the signature, then hand over the passes. */
    public function verify(Request $request, string $orderNo)
    {
        $order = Order::where('order_no', $orderNo)->firstOrFail();

        $data = $request->validate([
            'razorpay_order_id' => 'required|string|max:191',
            'razorpay_payment_id' => 'required|string|max:191',
            'razorpay_signature' => 'required|string|max:255',
        ]);

        $signatureOk = $data['razorpay_order_id'] === $order->gateway_order_id
            && Razorpay::paymentIsValid($data['razorpay_order_id'], $data['razorpay_payment_id'], $data['razorpay_signature']);

        if (! $signatureOk) {
            Log::warning('Razorpay signature rejected', ['order' => $order->order_no]);

            return redirect()->route('order.pay', $order->order_no)
                ->withErrors(['payment' => 'We could not confirm that payment. Nothing has been charged twice – please try again, or contact us with your order number.']);
        }

        // ask Razorpay what really happened before trusting the browser
        $payment = Razorpay::fetchPayment($data['razorpay_payment_id']);
        // only a captured payment is money in the bank; 'authorized' is just a hold
        $captured = $payment && ($payment['status'] ?? '') === 'captured'
            && (int) ($payment['amount'] ?? 0) === Razorpay::paise((float) $order->total);

        if (! $captured) {
            Log::warning('Razorpay payment not captured', ['order' => $order->order_no, 'payment' => $payment['status'] ?? null]);

            return redirect()->route('order.pay', $order->order_no)
                ->withErrors(['payment' => 'That payment has not been completed yet. If money has left your account, contact us with your order number and we will sort it out.']);
        }

        OrderFulfiller::markPaid($order, [
            'payment_reference' => $data['razorpay_payment_id'],
            'payment_method' => 'Razorpay' . (isset($payment['method']) ? ' (' . $payment['method'] . ')' : ''),
            'gateway' => 'razorpay',
        ]);

        return redirect()->route('order.done', $order->order_no);
    }

    /** The buyer closed the checkout without paying. */
    public function cancelled(string $orderNo)
    {
        $order = Order::where('order_no', $orderNo)->firstOrFail();

        return redirect()->route('order.pay', $order->order_no)
            ->withErrors(['payment' => 'Payment was not completed. Your passes are held – you can pay whenever you are ready.']);
    }

    /** Confirmation page after a successful payment. */
    public function done(string $orderNo)
    {
        $order = Order::where('order_no', $orderNo)->firstOrFail();

        return view('frontend.pay-done', [
            'order' => $order,
            'quote' => $order->quoteSummary(),
            'bookings' => $order->bookings()->orderBy('attendee_no')->get(),
        ]);
    }

    /**
     * Razorpay's own callback. This is what makes payment reliable when the
     * buyer closes the tab before being sent back to us.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        if (! Razorpay::webhookIsValid($payload, $request->header('X-Razorpay-Signature'))) {
            return response()->json(['ok' => false], 400);
        }

        $body = json_decode($payload, true) ?: [];
        $event = $body['event'] ?? '';
        $payment = $body['payload']['payment']['entity'] ?? [];
        $gatewayOrderId = $payment['order_id'] ?? null;

        if (! $gatewayOrderId || ! in_array($event, ['payment.captured', 'order.paid', 'payment.failed'], true)) {
            return response()->json(['ok' => true, 'ignored' => $event]);
        }

        $order = Order::where('gateway_order_id', $gatewayOrderId)->first();
        if (! $order) {
            return response()->json(['ok' => true, 'unknown_order' => true]);
        }

        // a failed attempt: tell the buyer once, and leave the order payable
        if ($event === 'payment.failed') {
            if (! $order->isPaid() && ! $order->reminded_at) {
                $order->update(['reminded_at' => now()]);
                OrderFulfiller::notifyPaymentFailed($order, $payment['error_description'] ?? null);
            }

            return response()->json(['ok' => true, 'failed_notice' => true]);
        }

        if ((int) ($payment['amount'] ?? 0) !== Razorpay::paise((float) $order->total)) {
            Log::warning('Razorpay webhook amount mismatch', ['order' => $order->order_no]);

            return response()->json(['ok' => false, 'amount_mismatch' => true], 422);
        }

        OrderFulfiller::markPaid($order, [
            'payment_reference' => $payment['id'] ?? null,
            'payment_method' => 'Razorpay' . (isset($payment['method']) ? ' (' . $payment['method'] . ')' : ''),
            'gateway' => 'razorpay',
        ]);

        return response()->json(['ok' => true]);
    }
}
