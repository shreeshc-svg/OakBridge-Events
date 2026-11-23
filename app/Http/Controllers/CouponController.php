<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponCode;
use App\Models\CouponRedemption;
use App\Models\Order;
use App\Models\PassType;
use App\Models\Service;
use App\Support\Coupons;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/** Admin > Ticketing > Coupons: discounts, their codes, and who used which code. */
class CouponController extends Controller
{
    public function store(Request $request)
    {
        // the Ticketing page has other forms with "name" and "pass_type_id"
        $request->merge([
            'name' => $request->input('coupon_name', $request->input('name')),
            'pass_type_id' => $request->input('coupon_pass', $request->input('pass_type_id')),
        ]);
        $event = Service::findOrFail($request->input('service_id'));
        if (! $event->date || $event->date->lt(today())) {
            throw ValidationException::withMessages(['service_id' => 'This event is over – coupons can only be made for upcoming events.']);
        }
        $coupon = Coupon::create($this->validated($request, $event) + [
            'service_id' => $event->id, 'is_active' => true, 'created_by' => $request->user()?->id,
        ]);

        return redirect()->route('coupons.show', $coupon)
            ->with('success', 'Coupon created. Next: generate one-time codes for the members (or add a shared code), then share them.');
    }

    public function show(Request $request, Coupon $coupon)
    {
        $coupon->load('service', 'passType');
        $q = Coupons::normalize($request->input('q'));
        $qRaw = trim((string) $request->input('q'));
        $show = (string) $request->input('show', 'all');

        $codes = CouponCode::where('coupon_id', $coupon->id)
            ->with(['redemptions' => fn ($r) => $r->with('order:id,order_no,status,total,paid_at')->latest('id')])
            ->when($qRaw !== '', fn ($query) => $query->where(fn ($w) => $w->where('code', 'like', "%{$q}%")
                ->orWhere('assigned_name', 'like', "%{$qRaw}%")->orWhere('assigned_email', 'like', "%{$qRaw}%")
                ->orWhereHas('redemptions', fn ($r) => $r->where(fn ($m) => $m->where('email', 'like', "%{$qRaw}%")
                    ->orWhere('name', 'like', "%{$qRaw}%")->orWhere('order_no', 'like', "%{$qRaw}%")))))
            ->when($show === 'used', fn ($query) => $query->whereHas('redemptions.order', fn ($o) => $o->where('status', 'paid')))
            ->when($show === 'reserved', fn ($query) => $query->whereHas('redemptions.order', fn ($o) => $o->where('status', 'pending')))
            ->when($show === 'unused', fn ($query) => $query->where('is_active', true)
                ->whereDoesntHave('redemptions.order', fn ($o) => $o->whereIn('status', ['pending', 'paid'])))
            ->when($show === 'revoked', fn ($query) => $query->where('is_active', false))
            ->orderBy('id')
            ->paginate(100)
            ->withQueryString();

        return view('backend.coupons.show', [
            'coupon' => $coupon,
            'codes' => $codes,
            'stats' => Coupons::stats($coupon),
            'passTypes' => PassType::where('service_id', $coupon->service_id)->orderBy('sort_order')->orderBy('id')->get(),
            'q' => $qRaw,
            'show' => $show,
        ]);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->validated($request, $coupon->service) + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Coupon saved.' . ($coupon->is_active ? '' : ' It is switched off – no code of it works now.'));
    }

    /** One code everyone can use, up to a number of times (each email once). */
    public function addShared(Request $request, Coupon $coupon)
    {
        $request->merge(['code' => Coupons::normalize($request->input('code'))]);
        $data = $request->validate([
            'code' => ['required', 'regex:/^[A-Z0-9][A-Z0-9\-]{2,38}[A-Z0-9]$/', Rule::unique('coupon_codes', 'code')],
            'max_uses' => 'required|integer|min:1|max:100000',
        ], [
            'code.regex' => 'Use 4–40 letters, digits or dashes, e.g. ALITA20.',
            'code.unique' => 'That code already exists.',
            'max_uses.required' => 'How many times can this code be used in total?',
        ]);
        CouponCode::create(['coupon_id' => $coupon->id, 'code' => $data['code'], 'max_uses' => $data['max_uses'], 'is_active' => true]);

        return back()->with('success', 'Code ' . $data['code'] . ' added – it can be used ' . $data['max_uses'] . ' ' . \Illuminate\Support\Str::plural('time', $data['max_uses']) . ' in total'
            . ($coupon->once_per_email ? ', once per email.' : '.'));
    }

    /** One-time codes: a number of blank codes, or one per person from a pasted list. */
    public function generate(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'prefix' => 'nullable|string|max:20',
            'count' => 'nullable|integer|min:1|max:5000',
            'people' => 'nullable|string|max:500000',
            'lock_to_email' => 'nullable|boolean',
        ]);

        $people = [];
        $problems = [];
        foreach (preg_split('/\r\n|\r|\n/', (string) ($data['people'] ?? '')) as $i => $line) {
            if (trim($line) === '') {
                continue;
            }
            $cells = array_map('trim', str_getcsv($line, str_contains($line, "\t") ? "\t" : ','));
            $email = collect($cells)->first(fn ($c) => filter_var($c, FILTER_VALIDATE_EMAIL));
            $name = collect($cells)->first(fn ($c) => $c !== '' && ! filter_var($c, FILTER_VALIDATE_EMAIL));
            // a header row: the first line, made only of column titles
            $titles = ['name', 'full name', 'member', 'member name', 'email', 'e-mail', 'email id', 'email address', 'mail', 'phone', 'mobile'];
            if (! $email && $i === 0 && collect($cells)->every(fn ($c) => in_array(strtolower($c), $titles, true))) {
                continue;
            }
            if (! $email) {
                $problems[] = 'Line ' . ($i + 1) . ': no valid email';
                continue;
            }
            $people[strtolower($email)] = ['name' => $name, 'email' => strtolower($email)];
        }
        if ($problems) {
            throw ValidationException::withMessages(['people' => array_slice($problems, 0, 10)]);
        }
        if (! $people && empty($data['count'])) {
            throw ValidationException::withMessages(['count' => 'Enter how many codes to make, or paste the members (name, email).']);
        }
        if (count($people) > 5000) {
            throw ValidationException::withMessages(['people' => 'Please make at most 5,000 codes at a time.']);
        }

        @set_time_limit(300);
        $made = Coupons::generate($coupon, (string) ($data['prefix'] ?? ''), (int) ($data['count'] ?? 0), array_values($people), $request->boolean('lock_to_email'));

        return back()->with('success', $made->count() . ' one-time ' . \Illuminate\Support\Str::plural('code', $made->count()) . ' made'
            . ($people ? ' – one per member' . ($request->boolean('lock_to_email') ? ', each usable only with that member\'s email' : '') : '')
            . '. Download them with "Download codes".');
    }

    /** Switch a code off (or back on). A used code stays on record either way. */
    public function toggle(Coupon $coupon, CouponCode $code)
    {
        abort_unless((int) $code->coupon_id === (int) $coupon->id, 404);
        $code->update(['is_active' => ! $code->is_active]);

        return back()->with('success', 'Code ' . $code->code . ($code->is_active ? ' switched on again.' : ' switched off – it can no longer be used.'));
    }

    /** Delete a code that was never used (a used one can only be switched off). */
    public function destroyCode(Coupon $coupon, CouponCode $code)
    {
        abort_unless((int) $code->coupon_id === (int) $coupon->id, 404);
        if ($code->redemptions()->exists()) {
            return back()->withErrors(['code' => $code->code . ' has been used on an order, so it stays on record. Switch it off instead.']);
        }
        $code->delete();

        return back()->with('success', 'Code ' . $code->code . ' deleted.');
    }

    /** All codes with who used them – to share the codes, or for your records. */
    public function export(Coupon $coupon)
    {
        $name = 'coupon-' . $coupon->id . '-' . \Illuminate\Support\Str::slug($coupon->name) . '-codes.csv';

        return response()->streamDownload(function () use ($coupon) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Code', 'For (name)', 'For (email)', 'Only this email', 'Uses allowed', 'Status', 'Used by', 'Used by email', 'Order', 'Order status', 'Date']);
            CouponCode::where('coupon_id', $coupon->id)->with(['redemptions.order:id,status'])->orderBy('id')
                ->chunk(500, function ($codes) use ($out) {
                    foreach ($codes as $code) {
                        $rows = $code->redemptions->isEmpty() ? [null] : $code->redemptions->all();
                        foreach ($rows as $r) {
                            fputcsv($out, [
                                $code->code, $code->assigned_name, $code->assigned_email, $code->lock_to_email ? 'yes' : 'no', $code->max_uses,
                                self::status($code), $r?->name, $r?->email, $r?->order_no,
                                $r ? ['used' => 'Paid', 'reserved' => 'Awaiting payment', 'released' => 'Cancelled / not paid'][$r->state()] : '',
                                $r?->created_at?->format('Y-m-d H:i'),
                            ]);
                        }
                    }
                });
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function destroy(Coupon $coupon)
    {
        if (CouponRedemption::where('coupon_id', $coupon->id)->exists()) {
            return back()->withErrors(['coupon' => 'Codes of this coupon have been used, so it stays on record. Switch it off instead.']);
        }
        $serviceId = $coupon->service_id;
        $coupon->delete();

        return redirect()->to(route('ticketing.index', ['event' => $serviceId]) . '#coupons')->with('success', 'Coupon "' . $coupon->name . '" deleted.');
    }

    /** Unused / Reserved / Used / 3 of 100 used / Switched off */
    public static function status(CouponCode $code): string
    {
        $paid = $code->redemptions->filter(fn ($r) => $r->state() === 'used')->count();
        $pending = $code->redemptions->filter(fn ($r) => $r->state() === 'reserved')->count();
        $prefix = $code->is_active ? '' : 'Switched off · ';

        if ($code->max_uses === 1) {
            return $prefix . ($paid ? 'Used' : ($pending ? 'Reserved – awaiting payment' : 'Unused'));
        }

        return $prefix . ($paid + $pending) . ' of ' . $code->max_uses . ' used' . ($pending ? ' (' . $pending . ' awaiting payment)' : '');
    }

    private function validated(Request $request, Service $event): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'pass_type_id' => ['nullable', Rule::exists('pass_types', 'id')->where('service_id', $event->id)],
            'discount_type' => ['required', Rule::in(['percent', 'amount', 'price'])],
            'discount_value' => 'required|numeric|min:0.01|max:10000000',
            'max_passes' => 'required|integer|min:1|max:20',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'once_per_email' => 'nullable|boolean',
        ], [
            'name.required' => 'Give the coupon a name, e.g. "ALITA members – 20% off".',
            'discount_value.required' => 'Enter the discount.',
            'valid_until.after_or_equal' => 'The last day cannot be before the first day.',
        ]);
        if ($data['discount_type'] === 'percent' && $data['discount_value'] > 100) {
            throw ValidationException::withMessages(['discount_value' => 'A percentage discount cannot be more than 100%.']);
        }
        // a member price must be below what the pass costs now, or the code would do nothing
        if ($data['discount_type'] === 'price') {
            $passes = ! empty($data['pass_type_id'])
                ? PassType::whereKey($data['pass_type_id'])->get()
                : PassType::where('service_id', $event->id)->where('is_active', 1)->get();
            $cheapest = $passes->map(fn ($p) => $p->priceOn())->min();
            if ($cheapest !== null && (float) $data['discount_value'] >= $cheapest) {
                throw ValidationException::withMessages(['discount_value' => 'The member price must be below the pass\'s current price ('
                    . \App\Support\Pricing::money((float) $cheapest) . ').']);
            }
        }
        $data['pass_type_id'] = $data['pass_type_id'] ?? null;
        $data['once_per_email'] = $request->boolean('once_per_email');

        return $data;
    }
}
