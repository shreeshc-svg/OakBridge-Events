<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\PassType;
use App\Models\Service;
use App\Notifications\ComplimentaryPassNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * Admin > Ticketing > Complimentary passes: free passes for special guests.
 *
 * Each row of the form is one contact: one or more passes (each its own pass
 * number, a Booking at ₹0 – no order, no invoice), all sent in one email to the
 * contact's address and, optionally, copied to up to three more addresses.
 * Passes issued together for one contact share a comp_group.
 */
class ComplimentaryPasses
{
    public const MAX_PER_PERSON = 20;
    public const MAX_ROWS = 50;
    public const MAX_PASSES = 200;
    public const MAX_CC = 3;

    /**
     * Check the rows of the form. Blank rows are ignored.
     *
     * @param array $rows rows[i] = [name, phone, email, qty, pass_type_id, cc, guests[n]]
     * @param Collection $passTypes this event's active passes, keyed by id
     * @return array{0: array<int|string, array>, 1: array<string, string>} clean rows (same keys), errors keyed rows.i.field
     */
    public static function validateRows(Service $event, array $rows, Collection $passTypes, bool $allowRegistered): array
    {
        $clean = [];
        $errors = [];
        $seen = [];   // email => row number
        $taken = array_flip(OrderBuilder::takenEmails($event));
        $number = 0;

        foreach ($rows as $key => $row) {
            $row = is_array($row) ? $row : [];
            $name = trim((string) ($row['name'] ?? ''));
            $phoneRaw = trim((string) ($row['phone'] ?? ''));
            $email = strtolower(trim((string) ($row['email'] ?? '')));
            $ccRaw = trim((string) ($row['cc'] ?? ''));
            $guests = array_map(fn ($g) => trim((string) $g), is_array($row['guests'] ?? null) ? $row['guests'] : []);

            if ($name === '' && $phoneRaw === '' && $email === '' && $ccRaw === '' && ! array_filter($guests)) {
                continue; // an empty row
            }
            $number++;
            $at = fn (string $field) => "rows.{$key}.{$field}";
            $label = 'Row ' . $number . ': ';

            if ($name === '') {
                $errors[$at('name')] = $label . 'enter the name.';
            } elseif (mb_strlen($name) > 100) {
                $errors[$at('name')] = $label . 'the name is too long (100 characters at most).';
            }

            $phone = self::cleanPhone($phoneRaw);
            if ($phoneRaw === '') {
                $errors[$at('phone')] = $label . 'enter the phone number.';
            } elseif (! $phone) {
                $errors[$at('phone')] = $label . 'the phone number needs 10 to 15 digits (e.g. 9810012345 or +91 98100 12345).';
            }

            if ($email === '') {
                $errors[$at('email')] = $label . 'enter the email – the passes are sent there.';
            } elseif (mb_strlen($email) > 100 || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[$at('email')] = $label . '"' . $email . '" is not a valid email address.';
            } elseif (isset($seen[$email])) {
                $errors[$at('email')] = $label . 'same email as row ' . $seen[$email] . ' – give row ' . $seen[$email]
                    . ' more passes instead (Passes column).';
            } elseif (isset($taken[$email]) && ! $allowRegistered) {
                $existing = Booking::where('service_id', $event->id)->whereRaw('LOWER(email) = ?', [$email])->orderBy('id')->value('booking_id');
                $errors[$at('email')] = $label . $email . ' is already registered for this event'
                    . ($existing ? ' (pass ' . $existing . ')' : ' (an order is in progress)')
                    . '. To give them more passes anyway, tick "Allow extra passes for people already registered".';
            }
            if ($email !== '' && ! isset($seen[$email])) {
                $seen[$email] = $number;
            }

            $qty = filter_var($row['qty'] ?? 1, FILTER_VALIDATE_INT);
            if ($qty === false || $qty < 1 || $qty > self::MAX_PER_PERSON) {
                $errors[$at('qty')] = $label . 'passes must be a whole number from 1 to ' . self::MAX_PER_PERSON . '.';
                $qty = 1;
            }

            $passId = ($row['pass_type_id'] ?? '') === '' ? null : (int) $row['pass_type_id'];
            if ($passId !== null && ! $passTypes->has($passId)) {
                $errors[$at('pass_type_id')] = $label . 'choose one of this event\'s passes.';
            }

            // copies: up to three more addresses, separated by commas, semicolons or spaces
            $cc = [];
            foreach (preg_split('/[\s,;]+/', strtolower($ccRaw), -1, PREG_SPLIT_NO_EMPTY) as $address) {
                if (! filter_var($address, FILTER_VALIDATE_EMAIL) || mb_strlen($address) > 100) {
                    $errors[$at('cc')] = $label . '"' . $address . '" in "Copy to" is not a valid email address.';
                    continue;
                }
                if ($address !== $email && ! in_array($address, $cc, true)) {
                    $cc[] = $address;
                }
            }
            if (count($cc) > self::MAX_CC) {
                $errors[$at('cc')] = $label . 'copy the email to at most ' . self::MAX_CC . ' addresses.';
            }

            // names for passes 2, 3…: optional, "Guest of <name>" when left empty
            $holders = [1 => $name];
            for ($n = 2; $n <= $qty; $n++) {
                $guest = $guests[$n] ?? '';
                if (mb_strlen($guest) > 100) {
                    $errors[$at('guests')] = $label . 'a guest name is too long (100 characters at most).';
                }
                $holders[$n] = $guest !== '' ? mb_substr($guest, 0, 100) : 'Guest of ' . $name;
            }

            $clean[$key] = [
                'number' => $number,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'qty' => $qty,
                'pass' => $passId !== null ? $passTypes->get($passId) : null,
                'cc' => $cc,
                'holders' => $holders,
                'already_registered' => isset($taken[$email]),
            ];
        }

        if ($number === 0) {
            $errors['rows'] = 'Add at least one person (name, phone and email).';
        } elseif ($number > self::MAX_ROWS) {
            $errors['rows'] = 'That is ' . $number . ' people – please issue at most ' . self::MAX_ROWS . ' at a time.';
        } elseif (($total = array_sum(array_column($clean, 'qty'))) > self::MAX_PASSES) {
            $errors['rows'] = 'That is ' . $total . ' passes – please issue at most ' . self::MAX_PASSES . ' at a time.';
        }

        return [$clean, $errors];
    }

    /** Create the passes for one checked row. */
    public static function issueGroup(Service $event, array $row, ?int $adminId): Collection
    {
        $group = (string) Str::uuid();
        $passes = collect();

        foreach ($row['holders'] as $n => $holder) {
            $passes->push(Booking::create([
                'booking_id' => BookingNumber::next(),
                'attendee_no' => $n,
                'name' => $holder,
                'email' => $row['email'],
                'phone' => $row['phone'],
                'amount' => 0,
                'date' => $event->date->format('Y-m-d H:i:s'),
                'service_id' => $event->id,
                'event' => $event->title,
                'pass_name' => $row['pass']?->name,
                'complimentary_at' => now(),
                'complimentary_by' => $adminId,
                'comp_group' => $group,
                'comp_cc' => $row['cc'] ? implode(', ', $row['cc']) : null,
            ]));
        }

        return $passes;
    }

    /** All passes issued together with this one (just itself for a single pass). */
    public static function group(Booking $booking): Collection
    {
        if (! $booking->comp_group) {
            return collect([$booking]);
        }

        return Booking::where('comp_group', $booking->comp_group)->orderBy('attendee_no')->orderBy('id')->get();
    }

    /** One email with every pass of the group, to the contact (and copies). False when it could not be sent. */
    public static function send(Collection $passes): bool
    {
        $first = $passes->first();
        if (! $first) {
            return false;
        }

        try {
            Notification::route('mail', $first->email)->notify(new ComplimentaryPassNotification(self::mailData($passes)));
            Booking::whereIn('id', $passes->pluck('id'))->update(['emailed_at' => now()]);

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    /**
     * A preview of the email to the admin – nothing is issued. Uses the first row
     * of the form when filled in, so a several-pass email can be previewed too.
     */
    public static function sendTest(Service $event, string $to, array $row, ?PassType $pass): bool
    {
        $name = trim((string) ($row['name'] ?? '')) ?: 'Guest Name';
        $qty = max(1, min(self::MAX_PER_PERSON, (int) ($row['qty'] ?? 1)));
        $guests = is_array($row['guests'] ?? null) ? $row['guests'] : [];
        $passes = [];
        for ($n = 1; $n <= $qty; $n++) {
            $holder = $n === 1 ? $name : (trim((string) ($guests[$n] ?? '')) ?: 'Guest of ' . $name);
            $passes[] = ['booking_id' => BookingNumber::PREFIX . str_pad((string) ($n - 1), 4, '0', STR_PAD_LEFT), 'holder' => $holder, 'pass_name' => $pass?->name];
        }

        try {
            Notification::route('mail', $to)->notify(new ComplimentaryPassNotification(
                self::eventData($event) + ['name' => $name, 'passes' => $passes, 'cc' => [], 'test' => true]
            ));

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    public static function mailData(Collection $passes): array
    {
        $first = $passes->first();
        $event = $first->service;
        // greet the contact (pass 1); if that pass was cancelled, the name behind "Guest of …"
        $contact = $passes->firstWhere('attendee_no', 1)?->name
            ?? (str_starts_with($first->name, 'Guest of ') ? substr($first->name, 9) : $first->name);

        return ($event ? self::eventData($event) : ['event' => $first->event, 'event_date' => null, 'venue' => null]) + [
            'name' => $contact,
            'passes' => $passes->map(fn (Booking $b) => ['booking_id' => $b->booking_id, 'holder' => $b->name, 'pass_name' => $b->pass_name])->values()->all(),
            'cc' => $first->comp_cc ? array_map('trim', explode(',', $first->comp_cc)) : [],
        ];
    }

    private static function eventData(Service $event): array
    {
        return ['event' => $event->title, 'event_date' => $event->date, 'venue' => $event->venue];
    }

    /** "+91 98100 00000" -> "+919810000000"; null when it is not a phone number. */
    public static function cleanPhone(?string $phone): ?string
    {
        $clean = preg_replace('/[\s\-().]/', '', (string) $phone);

        return preg_match('/^\+?\d{10,15}$/', $clean) ? $clean : null;
    }
}
