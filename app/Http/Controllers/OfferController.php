<?php

namespace App\Http\Controllers;

use App\Models\OfferInvite;
use App\Models\SpecialOffer;
use App\Support\OrderBuilder;
use App\Support\OrderFulfiller;
use App\Support\Pricing;
use App\Support\Razorpay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Special-price offers (Ticketing > Special price offers), two kinds of link:
 *  - /offer/o/{code}   one link for the whole campaign (pasted into the bulk-mail tool);
 *                      the person types their email, or the tool fills it in (?email=…)
 *  - /offer/{token}    a personal link for one person on the offer's list
 * Both check out at the offer price; payment, passes, invoice and reminders are the
 * normal order flow.
 */
class OfferController extends Controller
{
    /** The campaign link. */
    public function shared(Request $request, string $code)
    {
        $offer = SpecialOffer::with('service', 'passType')->where('code', $code)->firstOrFail();
        $preview = $request->boolean('preview') && auth()->check();
        if (! $preview) {
            SpecialOffer::whereKey($offer->id)->increment('views');
        }

        // the mail tool may fill these in: ?email={{email}}&name={{name}}
        $email = strtolower(trim((string) $request->query('email', '')));
        $email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
        $name = Str::limit(trim(strip_tags((string) $request->query('name', ''))), 100, '');
        if (str_contains($name, '{') || str_contains($name, '%')) {
            $name = ''; // an unfilled merge tag such as {{name}}
        }

        return $this->page($offer, null, $preview, [
            'action' => route('offer.shared.book', $offer->code),
            'name' => $name,
            'email' => $email,
            'phone' => '',
        ]);
    }

    public function sharedBook(Request $request, string $code)
    {
        $offer = SpecialOffer::with('service', 'passType')->where('code', $code)->firstOrFail();
        $request->validate(['email' => 'required|string|email|max:100'], [
            'email.required' => 'Please enter your email – your passes are sent there.',
        ]);
        $email = strtolower(trim($request->input('email')));

        $invite = OfferInvite::where('special_offer_id', $offer->id)->where('email', $email)->first();
        if (! $invite && ! $offer->open_to_anyone) {
            throw ValidationException::withMessages(['email' => 'This offer is for invited guests only – please use the email address our invitation was sent to.']);
        }
        if ($invite && ($paid = $invite->orders()->where('status', 'paid')->latest('id')->first())) {
            throw ValidationException::withMessages(['email' => $email . ' has already used this offer (order ' . $paid->order_no . '). Your passes were emailed to you.']);
        }

        return $this->checkout($request, $offer, $invite, $email);
    }

    /** A personal link. */
    public function show(Request $request, string $token)
    {
        $invite = OfferInvite::with('offer.service', 'offer.passType')->where('token', $token)->firstOrFail();
        $preview = $request->boolean('preview') && auth()->check();
        if (! $preview && ! $invite->clicked_at) {
            $invite->forceFill(['clicked_at' => now()])->save();
        }

        return $this->page($invite->offer, $invite, $preview, [
            'action' => route('offer.book', $invite->token),
            'name' => $invite->name,
            'email' => $invite->email,
            'phone' => preg_replace('/^(\+?91)(?=\d{10}$)/', '', (string) $invite->phone),
        ]);
    }

    public function book(Request $request, string $token)
    {
        $invite = OfferInvite::with('offer.service', 'offer.passType')->where('token', $token)->firstOrFail();
        if ($invite->orders()->where('status', 'paid')->exists()) {
            return redirect()->route('offer.show', $token);
        }

        return $this->checkout($request, $invite->offer, $invite, $invite->email);
    }

    private function page(SpecialOffer $offer, ?OfferInvite $invite, bool $preview, array $form)
    {
        $paid = $invite?->orders()->where('status', 'paid')->latest('id')->first();
        $payload = $offer->charge_tax ? Pricing::payload($offer->service) : null;

        return response()->view('frontend.offer', [
            'invite' => $invite,
            'offer' => $offer,
            'form' => $form,
            'paid' => $paid,
            'pending' => $invite?->orders()->where('status', 'pending')->latest('id')->first(),
            'closed' => $paid ? null : $offer->closedReason(),
            'preview' => $preview,
            'unit' => (float) $offer->unit_price,
            'max' => $offer->maxPasses(),
            'min' => $offer->minPasses(),
            'taxPercent' => $payload ? (float) ($payload['taxPercent'] ?? 0) : 0.0,
            'taxIncluded' => $payload ? (bool) ($payload['taxIncluded'] ?? true) : true,
        ])->header('X-Robots-Tag', 'noindex, nofollow'); // offer links stay out of search engines
    }

    /** Check out at the offer price for this email; the person's earlier unpaid checkout makes way. */
    private function checkout(Request $request, SpecialOffer $offer, ?OfferInvite $invite, string $email)
    {
        if ($reason = $offer->closedReason()) {
            throw ValidationException::withMessages(['offer' => $reason]);
        }
        $min = $offer->minPasses();
        if ((int) $request->input('quantity', 1) < $min) {
            throw ValidationException::withMessages(['quantity' => 'This offer is for ' . $min . ' or more passes – please choose at least ' . $min . '.']);
        }

        // the offer decides the event, the pass and who is buying
        $request->merge([
            'email' => $email,
            'event' => $offer->service->title,
            'pass_type_id' => $offer->pass_type_id,
        ]);

        $order = DB::transaction(function () use ($request, $offer, $invite, $email) {
            // someone using the campaign link joins the offer's list (kept only if the checkout succeeds)
            $invite = $invite ?? OfferInvite::create([
                'special_offer_id' => $offer->id,
                'token' => Str::random(32),
                'name' => Str::limit(trim((string) $request->input('name')) ?: Str::before($email, '@'), 100, ''),
                'email' => $email,
                'phone' => null,
                'clicked_at' => now(),
            ]);
            $invite->orders()->where('status', 'pending')->update(['status' => 'cancelled']);

            return OrderBuilder::fromRequest($request, [
                'unit_price' => (float) $offer->unit_price,
                'no_tax' => ! $offer->charge_tax,
                'max_quantity' => $offer->maxPasses(),
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
