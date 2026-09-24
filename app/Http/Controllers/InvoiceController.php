<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Service;
use App\Models\Setting;
use App\Support\GstStates;
use App\Support\InvoiceIssuer;
use App\Support\InvoicePdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Admin: the seller details printed on invoices, and invoices for each order. */
class InvoiceController extends Controller
{
    // ---------------------------------------------------------------- settings

    public function settings()
    {
        $setting = Setting::findOrFail(1);
        $fy = InvoiceIssuer::financialYear(now());

        return view('backend.invoices.settings', [
            'setting' => $setting,
            'states' => GstStates::names(),
            'missing' => InvoiceIssuer::missingSellerDetails($setting),
            'issuedThisYear' => Invoice::where('fy', $fy)->count(),
            'fy' => $fy,
            'eventsWithoutPlace' => Service::where('published', '1')->where('date', '>', now())
                ->whereNull('place_of_supply')->pluck('title'),
        ]);
    }

    public function saveSettings(Request $request)
    {
        $setting = Setting::findOrFail(1);
        $prefixLocked = Invoice::where('fy', InvoiceIssuer::financialYear(now()))->exists();

        $request->merge([
            'invoice_prefix' => strtoupper(trim((string) $request->input('invoice_prefix'))),
            'seller_gstin' => strtoupper(trim((string) $request->input('seller_gstin'))) ?: null,
            'seller_pan' => strtoupper(str_replace(' ', '', (string) $request->input('seller_pan'))) ?: null,
        ]);

        $data = $request->validate([
            // PREFIX/2026-27/0001 must fit GST's 16 characters
            'invoice_prefix' => [$prefixLocked ? 'nullable' : 'required', 'regex:/^[A-Z0-9]{1,3}$/'],
            'invoice_auto_send' => 'nullable|boolean',
            'seller_name' => 'nullable|string|max:191',
            'seller_address' => 'nullable|string|max:500',
            'seller_gstin' => ['nullable', 'regex:' . GstStates::GSTIN_PATTERN],
            'seller_pan' => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'seller_state' => ['nullable', Rule::in(GstStates::names())],
            'seller_phone' => 'nullable|string|max:60',
            'seller_email' => 'nullable|email|max:191',
            'invoice_sac' => ['nullable', 'regex:/^[0-9]{4,8}$/'],
            'bank_name' => 'nullable|string|max:191',
            'bank_account' => 'nullable|string|max:40',
            'bank_branch_ifsc' => 'nullable|string|max:191',
            'invoice_declaration' => 'nullable|string|max:500',
        ], [
            'invoice_prefix.regex' => 'Use 1 to 3 letters or digits, e.g. OBE.',
            'seller_gstin.regex' => 'That is not a valid GSTIN – it should be 15 characters, like 06AACCO5406D1ZW.',
            'invoice_sac.regex' => 'A SAC code is 4 to 8 digits.',
            'seller_pan.regex' => 'That is not a valid PAN – it should be 10 characters, like AACCO5406D.',
        ]);

        // characters 3 to 12 of a GSTIN are the PAN it was issued against
        if ($data['seller_gstin'] && $data['seller_pan'] && substr($data['seller_gstin'], 2, 10) !== $data['seller_pan']) {
            return back()->withInput()->withErrors([
                'seller_pan' => 'This PAN does not match your GSTIN, which carries ' . substr($data['seller_gstin'], 2, 10) . '.',
            ]);
        }

        // the GSTIN's first two digits name its state; a mismatch is always a typo
        if ($data['seller_gstin'] && $data['seller_state']
            && GstStates::stateFromGstin($data['seller_gstin']) !== $data['seller_state']) {
            return back()->withInput()->withErrors([
                'seller_state' => 'That GSTIN is registered in ' . GstStates::stateFromGstin($data['seller_gstin'])
                    . ', not ' . $data['seller_state'] . '.',
            ]);
        }

        if (! $prefixLocked) {
            $setting->invoice_prefix = $data['invoice_prefix'];
        }

        $setting->invoice_auto_send = $request->boolean('invoice_auto_send');

        foreach (['seller_name', 'seller_address', 'seller_gstin', 'seller_pan', 'seller_state', 'seller_phone',
                     'seller_email', 'invoice_sac', 'bank_name', 'bank_account', 'bank_branch_ifsc', 'invoice_declaration'] as $field) {
            $value = $data[$field] ?? null;
            $setting->{$field} = is_string($value) ? (trim($value) ?: null) : $value;
        }

        $setting->save();

        $missing = InvoiceIssuer::missingSellerDetails($setting->fresh());

        return redirect()->route('invoices.settings')->with('success', $missing
            ? 'Saved. Invoices stay on hold until you add: ' . implode(', ', $missing) . '.'
            : 'Saved. Invoices will be issued with these details.');
    }

    /** A sample invoice with made-up figures, to check the layout. Not numbered, not saved. */
    public function preview(Request $request)
    {
        $event = Service::where('published', '1')->orderByDesc('date')->first();

        $order = new Order([
            'order_no' => 'OB' . now()->format('ymd') . '-SAMPLE', 'event' => $event?->title ?: 'Sample event',
            'pass_name' => 'Delegate Pass', 'buyer_name' => 'Sample Buyer', 'buyer_email' => 'buyer@example.com',
            'buyer_phone' => '9800000000', 'buyer_company' => 'Sample Law Chambers LLP',
            'buyer_gstin' => '27AAAAA0000A1Z5', 'billing_address' => "Office 203, Town Square\nViman Nagar, Pune",
            'billing_state' => 'Maharashtra', 'billing_pin' => '411014', 'quantity' => 2, 'unit_price' => 7995,
            'discount_total' => 1995, 'discount_label' => '2-pass bundle', 'tax_percent' => 18,
            'tax_total' => 2519.10, 'total' => 16514.10, 'status' => 'paid',
            'payment_reference' => 'pay_SAMPLE0000000', 'gateway' => 'razorpay',
        ]);
        $order->setRelation('service', $event);
        $order->setRelation('bookings', collect([
            new Booking(['name' => 'Sample Buyer', 'booking_id' => 'VD-0001', 'attendee_no' => 1]),
            new Booking(['name' => 'Second Attendee', 'booking_id' => 'VD-0002', 'attendee_no' => 2]),
        ]));

        $snapshot = InvoiceIssuer::snapshot($order, 'SAMPLE', now());
        $pdf = app(InvoicePdf::class);

        if ($request->boolean('html')) {
            return response($pdf->html($snapshot));
        }

        try {
            return response($pdf->render($snapshot), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Invoice-SAMPLE.pdf"',
            ]);
        } catch (\RuntimeException $e) {
            return redirect()->route('invoices.settings')->withErrors(['pdf' => $e->getMessage()]);
        }
    }

    // ---------------------------------------------------------------- per order

    /** Issue the invoice (if it has none yet) and email it. */
    public function issue(Order $order)
    {
        try {
            $invoice = InvoiceIssuer::issue($order);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['invoice' => $e->getMessage()]);
        }

        return $this->deliver($invoice, 'Invoice ' . $invoice->number . ' issued');
    }

    public function send(Order $order)
    {
        $invoice = $order->invoice ?? abort(404);

        return $this->deliver($invoice, 'Invoice ' . $invoice->number);
    }

    public function download(Order $order)
    {
        $invoice = $order->invoice ?? abort(404);

        try {
            $pdf = app(InvoicePdf::class)->render($invoice->snapshot);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['invoice' => $e->getMessage()]);
        }

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $invoice->filename() . '"',
        ]);
    }

    /** GSTIN and billing address, editable until the invoice is issued. */
    public function billing(Request $request, Order $order)
    {
        if ($order->invoice) {
            return back()->withErrors(['invoice' => 'Invoice ' . $order->invoice->number
                . ' is already issued, so its details are fixed.']);
        }

        $request->merge(['buyer_gstin' => strtoupper(trim((string) $request->input('buyer_gstin'))) ?: null]);

        $data = $request->validate(self::billingRules(), self::billingMessages());

        $order->buyer_gstin = $data['buyer_gstin'];
        $order->billing_address = trim((string) ($data['billing_address'] ?? '')) ?: null;
        $order->billing_state = ($data['billing_state'] ?? null) ?: GstStates::stateFromGstin($data['buyer_gstin']);
        $order->billing_pin = ($data['billing_pin'] ?? null) ?: null;
        $order->save();

        return back()->with('success', 'Billing details saved.');
    }

    /** Issue and email invoices for the ticked orders. Unpaid ones are skipped. */
    public function bulkSend(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ], [
            'ids.required' => 'Tick the orders you want to invoice first.',
        ]);

        if ($missing = InvoiceIssuer::missingSellerDetails()) {
            return back()->withErrors(['ids' => 'Fill in Invoice details first – missing ' . implode(', ', $missing) . '.']);
        }

        $sent = 0;
        $skipped = [];
        $failed = [];

        foreach (Order::with('invoice')->whereIn('id', $data['ids'])->orderBy('id')->get() as $order) {
            if (InvoiceIssuer::blockedReason($order) !== null) {
                $skipped[] = $order->order_no;
                continue;
            }

            try {
                InvoiceIssuer::send(InvoiceIssuer::issue($order));
                $sent++;
            } catch (\Throwable $e) {
                report($e);
                $failed[] = $order->order_no;
            }
        }

        $message = $sent . ' invoice(s) emailed.';
        if ($skipped) {
            $message .= ' Skipped ' . count($skipped) . ' unpaid order(s).';
        }
        if ($failed) {
            $message .= ' ' . count($failed) . ' could not be sent: ' . implode(', ', $failed) . '.';
        }

        return redirect()->route('orders.index', $request->only('status', 'q'))->with('success', $message);
    }

    public static function billingRules(): array
    {
        return [
            'buyer_gstin' => ['nullable', 'regex:' . GstStates::GSTIN_PATTERN],
            'billing_address' => 'nullable|string|max:500',
            'billing_state' => ['nullable', Rule::in(GstStates::names())],
            'billing_pin' => 'nullable|digits:6',
        ];
    }

    public static function billingMessages(): array
    {
        return [
            'buyer_gstin.regex' => 'That GSTIN does not look right – it is 15 characters, like 27AAAAA0000A1Z5.',
            'billing_pin.digits' => 'The PIN code is 6 digits.',
        ];
    }

    private function deliver(Invoice $invoice, string $label)
    {
        try {
            InvoiceIssuer::send($invoice);
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['invoice' => $label . ' could not be emailed: ' . $e->getMessage()]);
        }

        return back()->with('success', $label . ' and emailed to ' . $invoice->snapshot['buyer']['email'] . '.');
    }
}
