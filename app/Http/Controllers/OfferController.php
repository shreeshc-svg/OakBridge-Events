<?php

namespace App\Http\Controllers;

use App\Models\OfferInvite;
use App\Models\Order;
use App\Support\OrderBuilder;
use App\Support\OrderFulfiller;
use App\Support\Pricing;
use App\Support\Razorpay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * /offer/{token}: a personal special-price link (Ticketing > Special price offers).
 * The invited person checks out at the offer price; everything after that – payment,
 * passes, invoice, reminders – is the normal order flow.
 */
class OfferController extends Controller
{
    public function show(Request $request, string $token)
    {
        $invite = OfferInvite::with('offer.service', 'offer.passType')->where('token', $token)->firstOrFail();
        $offer = $invite->offer;
        // admins can look at the page without it counting as opened, and without paying
        $preview = $request->boolean('preview') && auth()->check();

        $paid = $invite->orders()->where('status', 'paid')->latest('id')->first();
        $pending = $invite->orders()->where('status', 'pending')->latest('id')->first();

        if (! $preview && ! $invite->clicked_at) {
            $invite->forceFill(['clicked_at' => now()])->save();
        }

        return response()->view('frontend.offer', [
            'invite' => $invite,
            'offer' => $offer,
            'paid' => $paid,
            'pending' => $pending,
            'closed' => $paid ? null : $offer->closedReason(),
            'preview' => $preview,
            'unit' => (float) $offer->unit_price,
            'max' => max(1, (int) $offer->max_per_person),
            'taxPercent' => $offer->charge_tax ? (float) (Pricing::payload($offer->service)['taxPercent'] ?? 0) : 0.0,
            'taxIncluded' => $offer->charge_tax ? (bool) (Pricing::payload($offer->service)['taxIncluded'] ?? true) : true,
        ])->header('X-Robots-Tag', 'noindex, nofollow'); // personal links stay out of search engines
    }

    public function book(Request $request, string $token)
    {
        $invite = OfferInvite::with('offer.service', 'offer.passType')->where('token', $token)->firstOrFail();
        $offer = $invite->offer;

        if ($invite->orders()->where('status', 'paid')->exists()) {
            return redirect()->route('offer.show', $token);
        }
        if ($reason = $offer->closedReason()) {
            throw ValidationException::withMessages(['offer' => $reason]);
        }

        // the offer decides the event, the pass and who is buying
        $request->merge([
            'email' => $invite->email,
            'event' => $offer->service->title,
            'pass_type_id' => $offer->pass_type_id,
        ]);

        $order = DB::transaction(function () use ($request, $invite, $offer) {
            // changing their mind (more passes, other details): the earlier unpaid checkout makes way
            $invite->orders()->where('status', 'pending')->update(['status' => 'cancelled']);

            return OrderBuilder::fromRequest($request, [
                'unit_price' => (float) $offer->unit_price,
                'no_tax' => ! $offer->charge_tax,
                'max_quantity' => max(1, (int) $offer->max_per_person),
                'offer_invite_id' => $invite->id,
                'require_paid' => true,
            ])['order'];
        });

        if (Razorpay::enabled()) {
            return redirect()->route('order.pay', $order->order_no);
        }

        // no online payment: the bank details email, as on the website
        OrderFulfiller::notify($order, true);
        $request->session()->flash('eventName', $offer->service->title);
        $request->session()->flash('orderNo', $order->order_no);
        $request->session()->flash('orderTotal', Pricing::money((float) $order->total));
        $request->session()->flash('orderPasses', $order->quantity);

        return redirect()->route('thanks');
    }
}
