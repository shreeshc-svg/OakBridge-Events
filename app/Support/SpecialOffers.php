<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\OfferInvite;
use App\Models\Order;
use App\Models\SpecialOffer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Ticketing > Special price offers: the people on an offer's list, their personal
 * links and how far each has got (opened the link, started paying, paid).
 */
class SpecialOffers
{
    /** People added in one upload. */
    public const MAX_UPLOAD = 20000;

    /**
     * A pasted list or CSV, one person per line: Name, Email, Phone (phone optional;
     * columns in any order when there is a header row; the email is found wherever it is).
     *
     * @return array{0: array<int, array{name: string, email: string, phone: ?string}>, 1: array<int, string>} people, problems
     */
    public static function parseList(string $text): array
    {
        $text = preg_replace('/^\xEF\xBB\xBF/', '', $text); // Excel's BOM
        $people = [];
        $problems = [];
        $columns = null;
        $first = true;

        foreach (preg_split('/\r\n|\r|\n/', $text) as $i => $line) {
            if (trim($line) === '') {
                continue;
            }
            $isFirst = $first;
            $first = false;
            $delimiter = str_contains($line, "\t") ? "\t" : (substr_count($line, ';') > substr_count($line, ',') ? ';' : ',');
            $cells = array_map(fn ($c) => trim((string) $c, " \t\"'"), str_getcsv($line, $delimiter));
            $hasEmail = collect($cells)->contains(fn ($c) => str_contains($c, '@'));

            // a header row (only ever the first line) names the columns
            if ($isFirst && ! $hasEmail && collect($cells)->contains(fn ($c) => stripos($c, 'mail') !== false)) {
                $columns = [];
                foreach ($cells as $index => $title) {
                    $title = strtolower($title);
                    $key = str_contains($title, 'mail') ? 'email'
                        : (str_contains($title, 'name') ? 'name'
                        : (str_contains($title, 'phone') || str_contains($title, 'mobile') || str_contains($title, 'whatsapp') ? 'phone' : null));
                    if ($key && ! isset($columns[$key])) {
                        $columns[$key] = $index;
                    }
                }
                continue;
            }

            // the email: the named column, else whichever cell holds one
            $email = isset($columns['email']) ? strtolower($cells[$columns['email']] ?? '') : '';
            $emailAt = $columns['email'] ?? null;
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = '';
                foreach ($cells as $index => $cell) {
                    if (filter_var(strtolower($cell), FILTER_VALIDATE_EMAIL)) {
                        $email = strtolower($cell);
                        $emailAt = $index;
                        break;
                    }
                }
            }
            if ($email === '' || mb_strlen($email) > 100) {
                $problems[] = 'Line ' . ($i + 1) . ': no valid email ("' . mb_strimwidth(trim($line), 0, 50, '…') . '")';
                continue;
            }

            $name = isset($columns['name']) ? ($cells[$columns['name']] ?? '') : '';
            $phone = isset($columns['phone']) ? ($cells[$columns['phone']] ?? '') : '';
            if ($columns === null) {
                foreach ($cells as $index => $cell) {
                    if ($index === $emailAt || $cell === '') {
                        continue;
                    }
                    if ($phone === '' && preg_match('/^\+?[\d\s\-()]{10,20}$/', $cell)) {
                        $phone = $cell;
                    } elseif ($name === '' && ! filter_var($cell, FILTER_VALIDATE_EMAIL)) {
                        $name = $cell;
                    }
                }
            }
            $phone = preg_replace('/[\s\-().]/', '', $phone);

            $people[] = [
                // no name: greet them by the part of the email before @ (shown so the admin can fix it)
                'name' => mb_substr($name !== '' ? $name : Str::of(Str::before($email, '@'))->replace(['.', '_', '-'], ' ')->title(), 0, 100),
                'email' => $email,
                'phone' => preg_match('/^\+?\d{10,15}$/', $phone) ? $phone : null,
            ];
        }

        return [$people, $problems];
    }

    /**
     * Put people on the offer's list. Someone already on it keeps their link; someone who
     * already holds a pass or a paid order for the event is left out (they need no offer).
     *
     * @return array{added: int, already: int, registered: int, duplicates: int}
     */
    public static function addPeople(SpecialOffer $offer, array $people): array
    {
        $result = ['added' => 0, 'already' => 0, 'registered' => 0, 'duplicates' => 0];

        $onList = array_flip(OfferInvite::where('special_offer_id', $offer->id)->pluck('email')->map(fn ($e) => strtolower($e))->all());
        $registered = array_flip(self::registeredEmails($offer));
        $rows = [];
        $now = now();

        foreach ($people as $person) {
            $email = strtolower($person['email']);
            if (isset($rows[$email])) {
                $result['duplicates']++;
                continue;
            }
            if (isset($onList[$email])) {
                $result['already']++;
                continue;
            }
            if (isset($registered[$email])) {
                $result['registered']++;
                continue;
            }
            $rows[$email] = [
                'special_offer_id' => $offer->id,
                'token' => self::newToken(),
                'name' => $person['name'],
                'email' => $email,
                'phone' => $person['phone'] ?? null,
                'is_test' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // thousands at a time: insert in chunks
        foreach (array_chunk(array_values($rows), 500) as $chunk) {
            DB::table('offer_invites')->insert($chunk);
        }
        $result['added'] = count($rows);

        return $result;
    }

    /** A link for the admin to try the whole offer themselves; never counted or exported. */
    public static function testInvite(SpecialOffer $offer, string $name, string $email): OfferInvite
    {
        $email = strtolower(trim($email));
        $invite = OfferInvite::where('special_offer_id', $offer->id)->where('email', $email)->first();
        if ($invite) {
            return $invite;
        }

        return OfferInvite::create([
            'special_offer_id' => $offer->id,
            'token' => self::newToken(),
            'name' => $name,
            'email' => $email,
            'is_test' => true,
        ]);
    }

    /** Emails that already hold a pass, or a paid order, for the offer's event. */
    public static function registeredEmails(SpecialOffer $offer): array
    {
        $passes = Booking::where('service_id', $offer->service_id)->pluck('email')->filter()->map(fn ($e) => strtolower(trim($e)));
        $paid = Order::where('service_id', $offer->service_id)->where('status', 'paid')->pluck('attendees')
            ->flatMap(fn ($list) => collect((array) $list)->pluck('email'))->filter()->map(fn ($e) => strtolower(trim($e)));

        return $passes->merge($paid)->unique()->values()->all();
    }

    /**
     * Where each person on the list has got to, keyed by invite id:
     * 'paid' | 'checkout' (started, not paid) | 'opened' | 'sent'.
     */
    public static function statuses(SpecialOffer $offer): array
    {
        $statuses = [];
        $orders = Order::whereIn('offer_invite_id', OfferInvite::where('special_offer_id', $offer->id)->select('id'))
            ->get(['offer_invite_id', 'status']);
        foreach ($orders as $order) {
            $id = $order->offer_invite_id;
            if ($order->status === 'paid') {
                $statuses[$id] = 'paid';
            } elseif (($statuses[$id] ?? null) !== 'paid' && $order->status === 'pending') {
                $statuses[$id] = 'checkout';
            }
        }

        return $statuses;
    }

    /** Counts for the offer's summary (test links left out). */
    public static function stats(SpecialOffer $offer): array
    {
        $real = OfferInvite::where('special_offer_id', $offer->id)->where('is_test', false);
        $realIds = (clone $real)->select('id');
        $paidOrders = Order::whereIn('offer_invite_id', $realIds)->where('status', 'paid');

        return [
            'people' => (clone $real)->count(),
            'opened' => (clone $real)->whereNotNull('clicked_at')->count(),
            'checkout' => Order::whereIn('offer_invite_id', $realIds)->where('status', 'pending')->distinct()->count('offer_invite_id'),
            'paid_people' => (clone $paidOrders)->distinct()->count('offer_invite_id'),
            'paid_passes' => (int) (clone $paidOrders)->sum('quantity'),
            'revenue' => (float) (clone $paidOrders)->sum('total'),
        ];
    }

    /** 32 random letters and digits: unguessable, and the column is unique anyway. */
    private static function newToken(): string
    {
        return Str::random(32);
    }
}
