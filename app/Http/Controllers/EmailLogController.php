<?php

namespace App\Http\Controllers;

use App\Models\MailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin > Emails sent: every email the website has sent. Search by name, email,
 * phone, pass or order number; see each person's emails and what each one said.
 */
class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $person = strtolower(trim((string) $request->input('person')));

        $emails = $this->filtered($request, $q, $person)
            ->select(['id', 'to_email', 'to_name', 'phone', 'cc', 'subject', 'type', 'refs', 'status', 'sent_at', 'created_at'])
            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        return view('backend.emails.index', [
            'emails' => $emails,
            'people' => $q !== '' && $person === '' ? $this->people($q, 12) : collect(),
            'profile' => $person !== '' ? $this->profile($person) : null,
            'types' => MailLog::distinct()->orderBy('type')->pluck('type'),
            'stats' => [
                'total' => MailLog::count(),
                'today' => MailLog::where('status', 'sent')->where('sent_at', '>=', today())->count(),
                'failed' => MailLog::where('status', '!=', 'sent')->where('created_at', '<', now()->subMinutes(5))->count(),
            ],
            'q' => $q,
            'person' => $person,
        ]);
    }

    /** Instant suggestions while typing: matching people (JSON). */
    public function suggest(Request $request)
    {
        return response()->json($this->people(trim((string) $request->input('q')), 8)->map(fn ($p) => [
            'email' => $p->to_email,
            'name' => $p->to_name,
            'phone' => $p->phone,
            'emails' => (int) $p->emails,
            'types' => $p->types,
            'url' => route('emails.index', ['person' => $p->to_email]),
        ])->values());
    }

    /** People whose emails match, newest first, with how many emails and of which kinds. */
    private function people(string $term, int $limit)
    {
        if (mb_strlen($term) < 2) {
            return collect();
        }

        return $this->match(MailLog::query(), $term)
            ->groupBy('to_email')
            ->selectRaw('to_email, MAX(to_name) as to_name, MAX(phone) as phone, COUNT(*) as emails, MAX(created_at) as last_at, '
                . 'GROUP_CONCAT(DISTINCT type ORDER BY type SEPARATOR ", ") as types')
            ->orderByDesc('last_at')
            ->limit($limit)
            ->get();
    }

    public function show(MailLog $email)
    {
        return view('backend.emails.show', ['email' => $email]);
    }

    /** The email as it was sent, shown in a sandboxed frame (no scripts, no forms). */
    public function body(MailLog $email)
    {
        return response($email->html() ?: '<p style="font-family:sans-serif;color:#888">No content was recorded.</p>')
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Security-Policy', "sandbox; default-src 'none'; img-src * data:; style-src 'unsafe-inline' *; font-src * data:")
            ->header('X-Robots-Tag', 'noindex');
    }

    private function filtered(Request $request, string $q, string $person)
    {
        return MailLog::query()
            ->when($person !== '', fn ($query) => $query->where(fn ($w) => $w->where('to_email', $person)->orWhere('cc', 'like', '%' . $person . '%')))
            ->when($person === '' && $q !== '', fn ($query) => $this->match($query, $q))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->input('status') === 'failed', fn ($query) => $query->where('status', '!=', 'sent')->where('created_at', '<', now()->subMinutes(5)))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->input('to')));
    }

    /**
     * Name, email, phone (any format), pass/order number, copied address or subject –
     * all in one compact lower-case column, so one quick pass finds them.
     */
    private function match($query, string $term)
    {
        $term = mb_strtolower(trim($term));
        $digits = preg_replace('/\D/', '', $term);
        // "+91 98733 35087", "98733-35087" -> 9873335087
        $needle = (strlen($digits) >= 6 && ! preg_match('/[a-z@]/', $term)) ? (strlen($digits) > 10 ? substr($digits, -10) : $digits) : $term;

        return $query->where('search', 'like', '%' . addcslashes($needle, '%_\\') . '%');
    }

    private function profile(string $email): array
    {
        $rows = MailLog::where('to_email', $email)->orWhere('cc', 'like', '%' . $email . '%');

        return [
            'email' => $email,
            'name' => (clone $rows)->whereNotNull('to_name')->where('to_email', $email)->orderByDesc('id')->value('to_name'),
            'phone' => (clone $rows)->whereNotNull('phone')->where('to_email', $email)->orderByDesc('id')->value('phone'),
            'count' => (clone $rows)->count(),
            'first' => (clone $rows)->min('created_at'),
            'last' => (clone $rows)->max('created_at'),
            'types' => (clone $rows)->select('type', DB::raw('COUNT(*) as n'))->groupBy('type')->orderByDesc('n')->pluck('n', 'type'),
        ];
    }
}
