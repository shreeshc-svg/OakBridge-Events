<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\PaymentReminders;
use App\Support\Pricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Admin > Orders: who bought how many passes, what they owe and whether they paid.
 */
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q'));

        $orders = Order::with('bookings', 'reminders', 'invoice')
            ->when(in_array($status, array_keys(Order::STATUSES), true), fn ($q) => $q->where('status', $status))
            ->when($search !== '', fn ($q) => $q->where(function ($sub) use ($search) {
                $sub->where('order_no', 'like', "%{$search}%")
                    ->orWhere('buyer_name', 'like', "%{$search}%")
                    ->orWhere('buyer_email', 'like', "%{$search}%")
                    ->orWhere('buyer_phone', 'like', "%{$search}%")
                    ->orWhere('buyer_company', 'like', "%{$search}%");
            }))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('backend.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
            'totals' => [
                'paid' => (float) Order::where('status', 'paid')->sum('total'),
                'pending' => (float) Order::where('status', 'pending')->sum('total'),
                'passes' => (int) Order::where('status', 'paid')->sum('quantity'),
            ],
        ]);
    }

    /** Admin > Orders > New registration: the website form's fields plus a special price and how it is paid. */
    public function create()
    {
        $events = \App\Models\Service::wherePublished('1')
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->get()
            ->filter(fn ($event) => \App\Support\Pricing::isPaid($event))
            ->values();

        $pricing = $events->mapWithKeys(fn ($event) => [$event->title => \App\Support\Pricing::payload($event)]);

        return view('backend.orders.create', compact('events', 'pricing'));
    }

    public function store(Request $request)
    {
        $admin = $request->validate([
            'unit_price' => 'nullable|numeric|min:0|max:10000000',
            'payment' => ['required', \Illuminate\Validation\Rule::in(['link', 'paid'])],
            'paid_method' => 'nullable|required_if:payment,paid|string|max:40',
            'paid_reference' => 'nullable|string|max:191',
            'notify' => 'nullable|boolean',
        ], [
            'paid_method.required_if' => 'Say how they paid (e.g. Bank transfer, UPI, Cheque).',
            'unit_price.min' => 'The price cannot be negative.',
        ]);

        $special = $request->filled('unit_price') ? (float) $admin['unit_price'] : null;
        $notify = $request->boolean('notify');

        $built = \App\Support\OrderBuilder::fromRequest($request, [
            'unit_price' => $special,
            'created_by' => $request->user()->id,
            'require_paid' => true,
        ]);
        $order = $built['order'];
        $money = \App\Support\Pricing::money((float) $order->total);

        if ((float) $order->total <= 0) {
            // complimentary: nothing to pay, passes straight away
            \App\Support\OrderFulfiller::markPaid($order, ['payment_method' => 'Complimentary', 'gateway' => 'admin'], $notify);
            $message = 'Complimentary ' . \Illuminate\Support\Str::plural('pass', $order->quantity) . ' issued'
                . ($notify ? ' and emailed to ' . $order->buyer_email : ' (not emailed)') . '.';
        } elseif ($admin['payment'] === 'paid') {
            \App\Support\OrderFulfiller::markPaid($order, [
                'payment_method' => $admin['paid_method'],
                'payment_reference' => $admin['paid_reference'] ?? null,
                'gateway' => 'admin',
            ], $notify);
            $message = 'Order marked paid (' . $money . ') – ' . $order->quantity . ' ' . \Illuminate\Support\Str::plural('pass', $order->quantity)
                . ' issued' . ($notify ? ' and emailed to ' . $order->buyer_email : ' (not emailed)') . '.';
        } else {
            if ($notify) {
                \App\Support\OrderFulfiller::notify($order, true);   // the payment link email
            }
            $message = 'Order created for ' . $money . '. '
                . ($notify ? 'The payment link was emailed to ' . $order->buyer_email . '.' : 'Nothing was emailed – copy the payment link below to send it yourself.')
                . ' Passes are issued when it is paid.';
        }

        return redirect()->route('orders.show', $order)->with('success', $message);
    }

    public function show(Order $order)
    {
        $order->load('bookings', 'reminders', 'invoice');

        return view('backend.orders.show', compact('order'));
    }

    /** Email this buyer the pay link again. */
    public function remind(Request $request, Order $order)
    {
        if ($reason = PaymentReminders::blockedReason($order)) {
            return back()->withErrors(['reminder' => $reason]);
        }

        $reminder = PaymentReminders::send($order, 'manual', null, $request->user()?->name);

        if ($reminder->failed) {
            return back()->withErrors([
                'reminder' => 'The reminder could not be sent to ' . $order->buyer_email
                    . '. It is listed below with the reason.',
            ]);
        }

        return back()->with('success', 'Reminder sent to ' . $order->buyer_email . '.');
    }

    /** Chase several unpaid orders at once from the list. */
    public function bulkRemind(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ], [
            'ids.required' => 'Tick the orders you want to remind first.',
        ]);

        $orders = Order::with('reminders')->whereIn('id', $data['ids'])->get();
        $sent = 0;
        $skipped = [];
        $failed = [];

        foreach ($orders as $order) {
            if (PaymentReminders::blockedReason($order) !== null) {
                $skipped[] = $order->order_no;
                continue;
            }

            $reminder = PaymentReminders::send($order, 'manual', null, $request->user()?->name);
            $reminder->failed ? $failed[] = $order->order_no : $sent++;
        }

        $message = $sent . ' reminder(s) sent.';

        if ($skipped) {
            $message .= ' Skipped ' . count($skipped) . ' (already paid, or reminded in the last 24 hours): '
                . implode(', ', array_slice($skipped, 0, 5)) . (count($skipped) > 5 ? '…' : '') . '.';
        }

        if ($failed) {
            $message .= ' ' . count($failed) . ' could not be sent: ' . implode(', ', $failed) . '.';
        }

        return redirect()->route('orders.index', $request->only('status', 'q'))
            ->with('success', $message);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'payment_reference' => 'nullable|string|max:191',
            'payment_method' => 'nullable|string|max:40',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->payment_reference = ($data['payment_reference'] ?? null) ?: null;
        $order->payment_method = ($data['payment_method'] ?? null) ?: null;
        $order->notes = ($data['notes'] ?? null) ?: null;
        $order->save();

        if ($data['status'] === 'paid') {
            // issues the passes and sends the confirmation, once
            $issued = \App\Support\OrderFulfiller::markPaid($order);
            $message = $issued
                ? 'Order ' . $order->order_no . ' marked paid – ' . $order->quantity . ' pass(es) issued and the buyer emailed.'
                : 'Order ' . $order->order_no . ' updated.';
        } else {
            $order->status = $data['status'];
            $order->paid_at = null;
            $order->save();
            $message = 'Order ' . $order->order_no . ' updated.';
        }

        return redirect()->route('orders.show', $order)->with('success', $message);
    }

    /** Spreadsheet of the orders currently filtered. */
    public function export(Request $request)
    {
        $status = $request->query('status');
        $orders = Order::with('bookings', 'reminders')
            ->when(in_array($status, array_keys(Order::STATUSES), true), fn ($q) => $q->where('status', $status))
            ->orderBy('id')
            ->get();

        $columns = ['Order', 'Date', 'Status', 'Event', 'Pass', 'Passes', 'Buyer', 'Email', 'Phone', 'Company',
            'Designation', 'Unit price', 'Discount', 'Tax', 'Total', 'Payment reference',
            'Reminders sent', 'Last reminded', 'Attendees'];

        $callback = function () use ($orders, $columns) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $columns);
            foreach ($orders as $order) {
                fputcsv($out, array_map([$this, 'csvSafe'], [
                    $order->order_no,
                    $order->created_at?->format('Y-m-d H:i'),
                    $order->statusLabel(),
                    $order->event,
                    $order->pass_name,
                    $order->quantity,
                    $order->buyer_name,
                    $order->buyer_email,
                    $order->buyer_phone,
                    $order->buyer_company,
                    $order->buyer_designation,
                    $order->unit_price,
                    $order->discount_total,
                    $order->tax_total,
                    $order->total,
                    $order->payment_reference,
                    PaymentReminders::sentCount($order),
                    PaymentReminders::lastSentAt($order)?->format('Y-m-d H:i'),
                    $order->bookings->map(fn ($b) => $b->name . ' <' . ($b->email ?: '-') . '>')->implode('; '),
                ]));
            }
            fclose($out);
        };

        $name = 'orders-' . ($status ?: 'all') . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload($callback, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Delete one order and the passes it issued. */
    public function destroy(Order $order)
    {
        // a tax invoice is a legal record in an unbroken series: it cannot just vanish
        if ($invoice = $order->invoice) {
            return back()->withErrors(['invoice' => 'Order ' . $order->order_no . ' has tax invoice ' . $invoice->number
                . ', so it cannot be deleted. To reverse it, issue a credit note in your accounts.']);
        }

        $reference = $order->order_no;
        $passes = $order->bookings()->count();

        DB::transaction(function () use ($order) {
            $order->bookings()->delete();    // the passes go with the order
            $order->reminders()->delete();   // and so does its reminder history
            $order->delete();
        });

        return redirect()->route('orders.index')
            ->with('success', 'Order ' . $reference . ' deleted' . ($passes ? ' along with ' . $passes . ' pass(es).' : '.'));
    }

    /** Delete several orders at once (for clearing out test bookings). */
    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ], [
            'ids.required' => 'Tick the orders you want to delete first.',
        ]);

        $orders = Order::whereIn('id', $data['ids'])->get();
        $invoiced = Order::whereIn('id', $orders->pluck('id'))->has('invoice')->pluck('order_no');

        if ($invoiced->isNotEmpty()) {
            return back()->withErrors([
                'ids' => $invoiced->count() . ' of those orders have a tax invoice (' . $invoiced->implode(', ')
                    . ') and cannot be deleted. Untick them.',
            ]);
        }

        $paid = $orders->where('status', 'paid');

        // paid orders are payment records: they need the single delete, with its warning
        if ($paid->isNotEmpty() && ! $request->boolean('include_paid')) {
            return back()->withErrors([
                'ids' => $paid->count() . ' of those orders are PAID (' . $paid->pluck('order_no')->implode(', ')
                    . '). Untick them, or delete a paid order one at a time from its own page.',
            ]);
        }

        $passes = 0;

        DB::transaction(function () use ($orders, &$passes) {
            foreach ($orders as $order) {
                $passes += $order->bookings()->count();
                $order->bookings()->delete();
                $order->reminders()->delete();
                $order->delete();
            }
        });

        return redirect()->route('orders.index', $request->only('status', 'q'))
            ->with('success', $orders->count() . ' order(s) and ' . $passes . ' pass(es) deleted.');
    }

    private function csvSafe($value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
    }
}
