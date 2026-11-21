<?php

namespace App\Http\Controllers;

use App\Models\OfferInvite;
use App\Models\Order;
use App\Models\PassType;
use App\Models\Service;
use App\Models\SpecialOffer;
use App\Support\SpecialOffers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Admin > Ticketing > Special price offers: a pass at a special price for a list of
 * people. The site makes a personal link per person (exported as CSV for the bulk mail
 * tool), takes the payments and shows who has paid.
 */
class SpecialOfferController extends Controller
{
    public function store(Request $request)
    {
        $data = $this->validated($request);
        $offer = SpecialOffer::create($data + ['created_by' => $request->user()?->id, 'is_active' => true, 'open_to_anyone' => true]);

        return redirect()->route('offers.show', $offer)
            ->with('success', 'Offer created. Copy the offer link below into your campaign – try it yourself first.');
    }

    public function show(Request $request, SpecialOffer $offer)
    {
        $offer->load('service', 'passType');
        $q = trim((string) $request->input('q'));
        $filter = (string) $request->input('show', 'all');
        $statuses = SpecialOffers::statuses($offer);
        $idsWith = fn (string $status) => array_keys(array_filter($statuses, fn ($s) => $s === $status)) ?: [0];

        $people = OfferInvite::where('special_offer_id', $offer->id)->where('is_test', false)
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")))
            ->when($filter === 'opened', fn ($query) => $query->whereNotNull('clicked_at'))
            ->when($filter === 'not_opened', fn ($query) => $query->whereNull('clicked_at'))
            ->when($filter === 'paid', fn ($query) => $query->whereIn('id', $idsWith('paid')))
            ->when($filter === 'checkout', fn ($query) => $query->whereIn('id', $idsWith('checkout')))
            ->when($filter === 'unpaid', fn ($query) => $query->whereNotIn('id', $idsWith('paid')))
            ->orderBy('id')
            ->paginate(100)
            ->withQueryString();

        return view('backend.offers.show', [
            'offer' => $offer,
            'people' => $people,
            'statuses' => $statuses,
            'stats' => SpecialOffers::stats($offer),
            'tests' => OfferInvite::where('special_offer_id', $offer->id)->where('is_test', true)->orderBy('id')->get(),
            'passTypes' => PassType::where('service_id', $offer->service_id)->orderBy('sort_order')->orderBy('id')->get(),
            'q' => $q,
            'filter' => $filter,
        ]);
    }

    public function update(Request $request, SpecialOffer $offer)
    {
        $data = $this->validated($request, $offer);
        $offer->update($data + ['is_active' => $request->boolean('is_active'), 'open_to_anyone' => $request->boolean('open_to_anyone')]);

        return back()->with('success', 'Offer saved.' . ($offer->is_active ? '' : ' It is closed – the links show "This offer has been closed".'));
    }

    /** Add people: paste and/or CSV. Valid lines are added, the rest are listed. */
    public function upload(Request $request, SpecialOffer $offer)
    {
        $request->validate([
            'list' => 'nullable|string|max:2000000',
            'csv' => 'nullable|file|mimes:csv,txt|max:5120',
        ], [
            'csv.mimes' => 'Upload a .csv file (in Excel: File > Save As > CSV).',
            'csv.max' => 'The CSV file must be 5 MB or smaller.',
        ]);

        $text = (string) $request->input('list', '');
        if ($request->hasFile('csv')) {
            $text .= "\n" . file_get_contents($request->file('csv')->getRealPath());
        }
        [$people, $problems] = SpecialOffers::parseList($text);

        if (! $people && ! $problems) {
            throw ValidationException::withMessages(['list' => 'Paste a list or choose a CSV file first.']);
        }
        if (count($people) > SpecialOffers::MAX_UPLOAD) {
            throw ValidationException::withMessages(['list' => 'That is ' . count($people) . ' people – upload at most ' . SpecialOffers::MAX_UPLOAD . ' at a time.']);
        }

        @set_time_limit(300);
        $result = SpecialOffers::addPeople($offer, $people);

        $parts = [$result['added'] . ' added'];
        if ($result['already']) {
            $parts[] = $result['already'] . ' already on the list (they keep their link)';
        }
        if ($result['duplicates']) {
            $parts[] = $result['duplicates'] . ' repeated in the file';
        }
        if ($result['registered']) {
            $parts[] = $result['registered'] . ' left out – they already have a pass or a paid order for this event';
        }
        if ($problems) {
            $parts[] = count($problems) . ' ' . (count($problems) === 1 ? 'line' : 'lines') . ' skipped (no valid email)';
        }

        return back()->with($result['added'] ? 'success' : 'warning', implode(' · ', $parts) . '.')
            ->with('upload_problems', array_slice($problems, 0, 25));
    }

    /** Take someone off the list (their link stops working). Not once they have paid. */
    public function remove(SpecialOffer $offer, OfferInvite $invite)
    {
        abort_unless((int) $invite->special_offer_id === (int) $offer->id, 404);
        if ($invite->orders()->where('status', 'paid')->exists()) {
            return back()->withErrors(['remove' => $invite->email . ' has already paid – they cannot be removed. Their order is in Orders.']);
        }
        $invite->orders()->where('status', 'pending')->update(['status' => 'cancelled']);
        $invite->delete();

        return back()->with('success', $invite->email . ' removed – their link no longer works.');
    }

    /** CSV for the bulk mail tool: Name, Email, Phone, Offer link, Status. */
    public function export(Request $request, SpecialOffer $offer)
    {
        $only = (string) $request->input('only', 'all');
        $statuses = SpecialOffers::statuses($offer);
        $paidIds = array_keys(array_filter($statuses, fn ($s) => $s === 'paid'));
        $labels = ['paid' => 'Paid', 'checkout' => 'Started payment', 'opened' => 'Opened link', 'sent' => 'Not opened'];
        $name = 'offer-' . $offer->id . '-' . \Illuminate\Support\Str::slug($offer->title) . ($only === 'unpaid' ? '-not-paid' : '') . '.csv';

        return response()->streamDownload(function () use ($offer, $only, $paidIds, $statuses, $labels) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // Excel reads the ₹ and accents correctly
            fputcsv($out, ['Name', 'Email', 'Phone', 'Offer link', 'Status']);
            OfferInvite::where('special_offer_id', $offer->id)->where('is_test', false)
                ->when($only === 'unpaid', fn ($q) => $q->whereNotIn('id', $paidIds ?: [0]))
                ->orderBy('id')
                ->chunk(1000, function ($invites) use ($out, $statuses, $labels) {
                    foreach ($invites as $invite) {
                        $status = $statuses[$invite->id] ?? ($invite->clicked_at ? 'opened' : 'sent');
                        fputcsv($out, [$invite->name, $invite->email, $invite->phone, $invite->link(), $labels[$status]]);
                    }
                });
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** A personal link for the admin, to try the whole thing (including a real payment). */
    public function test(Request $request, SpecialOffer $offer)
    {
        $data = $request->validate([
            'test_email' => 'required|string|email|max:100',
        ], ['test_email.required' => 'Enter your email for the test link.']);

        $invite = SpecialOffers::testInvite($offer, $request->user()?->name ?: 'Test', $data['test_email']);

        return back()->with('success', 'Test link ready for ' . $invite->email . ' – open it below. It is not counted and not exported.');
    }

    /** See the offer page as an invited person would, without counting it or paying. */
    public function preview(Request $request, SpecialOffer $offer)
    {
        $invite = OfferInvite::where('special_offer_id', $offer->id)->orderByDesc('is_test')->orderBy('id')->first()
            ?? SpecialOffers::testInvite($offer, $request->user()?->name ?: 'Test', $request->user()?->email ?: 'test@example.com');

        return redirect()->to($invite->link() . '?preview=1');
    }

    public function destroy(SpecialOffer $offer)
    {
        $serviceId = $offer->service_id;
        if (Order::whereIn('offer_invite_id', $offer->invites()->select('id'))->whereIn('status', ['paid', 'pending'])->exists()) {
            return back()->withErrors(['offer' => 'People have already ordered through this offer, so it cannot be deleted. Untick "Offer is open" to close it instead.']);
        }
        $offer->delete();

        return redirect()->to(route('ticketing.index', ['event' => $serviceId]) . '#offers')->with('success', 'Offer "' . $offer->title . '" deleted.');
    }

    private function validated(Request $request, ?SpecialOffer $offer = null): array
    {
        $serviceId = $offer?->service_id ?? $request->input('service_id');

        $data = $request->validate([
            'service_id' => [$offer ? 'nullable' : 'required', Rule::exists('services', 'id')->whereNull('deleted_at')],
            'title' => 'required|string|max:120',
            'pass_type_id' => ['required', Rule::exists('pass_types', 'id')->where('service_id', $serviceId)],
            'unit_price' => 'required|numeric|min:1|max:10000000',
            'charge_tax' => 'nullable|boolean',
            'max_per_person' => 'required|integer|min:1|max:20',
            'min_per_person' => 'nullable|integer|min:1|max:20|lte:max_per_person',
            'expires_on' => 'nullable|date' . ($offer ? '' : '|after_or_equal:today'),
        ], [
            'title.required' => 'Give the offer a name, e.g. "Delegate ₹7,999".',
            'pass_type_id.required' => 'Choose the pass this offer is for.',
            'pass_type_id.exists' => 'Choose one of this event\'s passes.',
            'unit_price.required' => 'Enter the offer price per pass.',
            'unit_price.min' => 'The offer price must be at least ₹1 (use Complimentary passes for free ones).',
            'expires_on.after_or_equal' => 'The last day cannot be in the past.',
            'min_per_person.lte' => 'The minimum passes cannot be more than the maximum passes per person.',
        ]);
        $data['min_per_person'] = (int) ($data['min_per_person'] ?? 1);

        unset($data['service_id']);
        if (! $offer) {
            $event = Service::findOrFail($request->input('service_id'));
            if (! $event->date || $event->date->lt(today())) {
                throw ValidationException::withMessages(['service_id' => 'This event is over – offers can only be made for upcoming events.']);
            }
            $data['service_id'] = $event->id;
        }
        $data['charge_tax'] = $request->boolean('charge_tax');
        $data['expires_on'] = $data['expires_on'] ?? null;

        return $data;
    }
}
