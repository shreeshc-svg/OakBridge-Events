<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

        $orders = Order::with('bookings')
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

    public function show(Order $order)
    {
        $order->load('bookings');

        return view('backend.orders.show', compact('order'));
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
        $orders = Order::with('bookings')
            ->when(in_array($status, array_keys(Order::STATUSES), true), fn ($q) => $q->where('status', $status))
            ->orderBy('id')
            ->get();

        $columns = ['Order', 'Date', 'Status', 'Event', 'Pass', 'Passes', 'Buyer', 'Email', 'Phone', 'Company',
            'Designation', 'Unit price', 'Discount', 'Tax', 'Total', 'Payment reference', 'Attendees'];

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
        $reference = $order->order_no;
        $passes = $order->bookings()->count();

        DB::transaction(function () use ($order) {
            $order->bookings()->delete();   // the passes go with the order
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
        $passes = 0;

        DB::transaction(function () use ($orders, &$passes) {
            foreach ($orders as $order) {
                $passes += $order->bookings()->count();
                $order->bookings()->delete();
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
