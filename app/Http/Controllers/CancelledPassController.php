<?php

namespace App\Http\Controllers;

use App\Models\CancelledPass;
use Illuminate\Http\Request;

/** Admin > Cancelled passes: every pass that was cancelled or deleted. */
class CancelledPassController extends Controller
{
    public function index(Request $request)
    {
        $passes = $this->query($request)->paginate(50)->withQueryString();

        return view('backend.cancelled-passes.index', [
            'passes' => $passes,
            'kinds' => CancelledPass::distinct()->orderBy('kind')->pluck('kind'),
            'events' => CancelledPass::whereNotNull('event')->distinct()->orderBy('event')->pluck('event'),
            'total' => CancelledPass::count(),
        ]);
    }

    public function export(Request $request)
    {
        $query = $this->query($request);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Pass no.', 'Name', 'Email', 'Phone', 'Type', 'Pass', 'Event', 'Order', 'Issued', 'Cancelled', 'Cancelled by', 'Where']);
            $query->chunk(1000, function ($rows) use ($out) {
                foreach ($rows as $p) {
                    fputcsv($out, [$p->booking_id, $p->name, $p->email, $p->phone, $p->kind, $p->pass_name, $p->event, $p->order_no,
                        $p->issued_at?->format('Y-m-d H:i'), $p->cancelled_at?->format('Y-m-d H:i'), $p->cancelled_by_name, $p->source]);
                }
            });
            fclose($out);
        }, 'cancelled-passes-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function query(Request $request)
    {
        $q = trim((string) $request->input('q'));

        return CancelledPass::query()
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('booking_id', 'like', "%{$q}%")
                ->orWhere('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")->orWhere('order_no', 'like', "%{$q}%")))
            ->when($request->filled('kind'), fn ($query) => $query->where('kind', $request->input('kind')))
            ->when($request->filled('event'), fn ($query) => $query->where('event', $request->input('event')))
            ->orderByDesc('cancelled_at')->orderByDesc('id');
    }
}
