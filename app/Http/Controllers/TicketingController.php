<?php

namespace App\Http\Controllers;

use App\Models\PassBundle;
use App\Models\PassType;
use App\Models\PricingTier;
use App\Models\Service;
use App\Models\Setting;
use App\Support\Pricing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin > Ticketing: pass prices, bulk discount slabs and tax / payment settings.
 */
class TicketingController extends Controller
{
    public function index(Request $request)
    {
        $events = Service::orderByRaw('date IS NULL, date DESC')->get();
        // default to the next event coming up, otherwise the most recent one
        $nextUp = $events->filter(fn ($e) => $e->date && $e->date->isFuture())->sortBy('date')->first();
        $eventId = (int) ($request->query('event') ?: optional($nextUp ?: $events->first())->id);
        $event = $events->firstWhere('id', $eventId) ?: $nextUp ?: $events->first();

        return view('backend.ticketing.index', [
            'events' => $events,
            'event' => $event,
            'passTypes' => $event ? PassType::with('bundles')->where('service_id', $event->id)->orderBy('sort_order')->orderBy('id')->get() : collect(),
            'tiers' => PricingTier::whereNull('service_id')->orderBy('min_quantity')->get(),
            'setting' => Setting::findOrFail(1),
        ]);
    }

    public function storePass(Request $request)
    {
        $data = $this->passRules($request);
        $data['sort_order'] = $data['sort_order'] ?? ((int) PassType::where('service_id', $data['service_id'])->max('sort_order') + 1);
        PassType::create($data);

        return $this->backTo($data['service_id'], 'Pass "' . $data['name'] . '" added.');
    }

    public function updatePass(Request $request, PassType $pass)
    {
        $data = $this->passRules($request, $pass);
        $pass->update($data);

        return $this->backTo($pass->service_id, 'Pass "' . $pass->name . '" saved.');
    }

    public function destroyPass(PassType $pass)
    {
        $serviceId = $pass->service_id;
        $name = $pass->name;
        $pass->delete();

        return $this->backTo($serviceId, 'Pass "' . $name . '" deleted. Orders already placed keep their prices.');
    }

    public function storeTier(Request $request)
    {
        $data = $this->tierRules($request);
        PricingTier::create($data + ['service_id' => null]);

        return $this->backTo($request->input('event_id'), 'Discount for ' . $data['min_quantity'] . '+ passes added.');
    }

    public function updateTier(Request $request, PricingTier $tier)
    {
        $tier->update($this->tierRules($request, $tier));

        return $this->backTo($request->input('event_id'), 'Discount saved.');
    }

    public function destroyTier(Request $request, PricingTier $tier)
    {
        $tier->delete();

        return $this->backTo($request->input('event_id'), 'Discount removed.');
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'tax_percent' => 'nullable|numeric|min:0|max:99.99',
            'tax_label' => 'nullable|string|max:30',
            'max_passes_per_order' => 'required|integer|min:1|max:' . Pricing::MAX_PASSES,
            'payment_instructions' => 'nullable|string|max:3000',
            'invoice_note' => 'nullable|string|max:1000',
            'online_payment' => 'nullable|boolean',
        ], [], [
            'tax_percent' => 'tax percentage',
            'max_passes_per_order' => 'maximum passes per order',
        ]);

        $setting = Setting::findOrFail(1);
        $setting->tax_percent = ($data['tax_percent'] ?? null) !== null && $data['tax_percent'] !== '' ? $data['tax_percent'] : null;
        $setting->tax_label = ($data['tax_label'] ?? null) ?: null;
        $setting->prices_include_tax = $request->boolean('prices_include_tax');
        $setting->max_passes_per_order = $data['max_passes_per_order'];
        $setting->payment_instructions = ($data['payment_instructions'] ?? null) ?: null;
        $setting->invoice_note = ($data['invoice_note'] ?? null) ?: null;
        $setting->online_payment = $request->boolean('online_payment');
        $setting->save();

        return $this->backTo($request->input('event_id'), 'Ticketing settings saved.');
    }

    /** Save the whole bundle grid for one pass in a single go. */
    public function updateBundles(Request $request, PassType $pass)
    {
        $max = Pricing::maxPasses();
        $data = $request->validate([
            'bundles' => 'array',
            'bundles.*.early_total' => 'nullable|numeric|min:0|max:99999999',
            'bundles.*.total' => 'nullable|numeric|min:0|max:99999999',
            'early_extra_price' => 'nullable|numeric|min:0|max:9999999',
            'extra_price' => 'nullable|numeric|min:0|max:9999999',
        ], [], [
            'early_extra_price' => 'early bird price per extra pass',
            'extra_price' => 'list price per extra pass',
        ]);

        $pass->update([
            'early_extra_price' => ($data['early_extra_price'] ?? null) !== null && $data['early_extra_price'] !== '' ? $data['early_extra_price'] : null,
            'extra_price' => ($data['extra_price'] ?? null) !== null && $data['extra_price'] !== '' ? $data['extra_price'] : null,
        ]);

        foreach (range(1, $max) as $quantity) {
            $row = $data['bundles'][$quantity] ?? [];
            $early = ($row['early_total'] ?? '') === '' ? null : (float) $row['early_total'];
            $list = ($row['total'] ?? '') === '' ? null : (float) $row['total'];

            if ($early === null && $list === null) {
                PassBundle::where('pass_type_id', $pass->id)->where('quantity', $quantity)->delete();
                continue;
            }
            PassBundle::updateOrCreate(
                ['pass_type_id' => $pass->id, 'quantity' => $quantity],
                ['early_total' => $early, 'total' => $list],
            );
        }

        return $this->backTo($pass->service_id, 'Bundle prices saved for "' . $pass->name . '".');
    }

    private function passRules(Request $request, ?PassType $pass = null): array
    {
        $data = $request->validate([
            'service_id' => ['required', Rule::exists('services', 'id')],
            'name' => 'required|string|max:60',
            'description' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0|max:9999999',
            'early_price' => 'nullable|numeric|min:0|max:9999999|lt:price',
            'early_until' => 'nullable|date|required_with:early_price',
            'sort_order' => 'nullable|integer|min:0|max:999',
        ], [
            'early_price.lt' => 'The early bird price must be lower than the normal price.',
            'early_until.required_with' => 'Set the date the early bird price runs until.',
        ], [
            'early_price' => 'early bird price',
            'early_until' => 'early bird end date',
        ]);

        $data['early_price'] = $data['early_price'] ?? null;
        $data['early_until'] = $data['early_price'] === null ? null : ($data['early_until'] ?? null);
        $data['description'] = $data['description'] ?? null;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function tierRules(Request $request, ?PricingTier $tier = null): array
    {
        $data = $request->validate([
            'min_quantity' => [
                'required', 'integer', 'min:2', 'max:' . Pricing::MAX_PASSES,
                Rule::unique('pricing_tiers', 'min_quantity')->whereNull('service_id')->ignore($tier?->id),
            ],
            'discount_type' => ['required', Rule::in(['percent', 'flat'])],
            'discount_value' => 'required|numeric|min:0|max:9999999',
        ], [
            'min_quantity.unique' => 'There is already a discount for that number of passes.',
            'min_quantity.min' => 'Bulk discounts start from 2 passes.',
        ], [
            'min_quantity' => 'number of passes',
            'discount_value' => 'discount',
        ]);

        if ($data['discount_type'] === 'percent' && $data['discount_value'] > 100) {
            abort(redirect()->back()->withInput()->withErrors(['discount_value' => 'A percentage discount cannot be over 100%.']));
        }

        return $data;
    }

    private function backTo($eventId, string $message)
    {
        return redirect()->route('ticketing.index', ['event' => $eventId])->with('success', $message);
    }
}
