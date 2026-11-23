<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\MailLog;
use App\Models\Order;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Mime\Email;

/**
 * Keeps a copy of every email the website sends (Admin > Emails sent):
 * who it went to, what it was, the content and when. Logging never stops an email.
 */
class MailLogger
{
    private const HEADER = 'X-OB-Log';

    /** Before sending: write the row (status "sending") and tag the message with its id. */
    public static function sending(MessageSending $event): void
    {
        try {
            if (! self::ready()) {
                return;
            }
            $message = $event->message;
            $to = $message->getTo()[0] ?? null;
            if (! $to) {
                return;
            }
            $html = self::html($message);
            $email = strtolower($to->getAddress());

            $row = [
                'to_email' => mb_substr($email, 0, 191),
                'to_name' => mb_substr($to->getName() ?: self::nameFor($email) ?: '', 0, 191) ?: null,
                'phone' => self::phoneFor($email),
                'cc' => mb_substr(collect(array_merge(array_slice($message->getTo(), 1), $message->getCc()))
                    ->map(fn ($a) => $a->getAddress())->implode(', '), 0, 500) ?: null,
                'subject' => mb_substr((string) $message->getSubject(), 0, 300),
                'type' => self::type($event->data, (string) $message->getSubject()),
                'refs' => self::refs($html . ' ' . $message->getSubject()),
                'status' => 'sending',
            ];
            $log = MailLog::create($row + ['search' => MailLog::searchText($row)]);
            DB::table('mail_log_bodies')->insert(['mail_log_id' => $log->id, 'body' => $html]);
            $message->getHeaders()->addTextHeader(self::HEADER, (string) $log->id);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** After sending: mark it sent. */
    public static function sent(MessageSent $event): void
    {
        try {
            $header = $event->message->getHeaders()->get(self::HEADER);
            if (! $header) {
                return;
            }
            MailLog::whereKey((int) $header->getBodyAsString())->update([
                'status' => 'sent',
                'sent_at' => now(),
                'message_id' => mb_substr((string) $event->sent->getMessageId(), 0, 191) ?: null,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** What kind of email this is, in words the team uses. */
    public static function type(array $data, string $subject): string
    {
        if (str_starts_with($subject, '[TEST]')) {
            return 'Test email';
        }
        $class = class_basename((string) ($data['__laravel_notification'] ?? ''));

        return match ($class) {
            'AttendeePassNotification' => 'Pass',
            'ComplimentaryPassNotification' => 'Complimentary pass',
            'PaymentReminderNotification' => 'Payment reminder',
            'InvoiceNotification' => 'Invoice',
            'BookingCreatedUserNotificationUpdated' => match (true) {
                stripos($subject, 'payment received') !== false => 'Pass / payment received',
                stripos($subject, 'could not be completed') !== false => 'Payment failed',
                stripos($subject, 'complete your payment') !== false => 'Payment link',
                default => 'Booking confirmation',
            },
            'BookingCreatedAdminNotification' => 'Admin copy',
            'InviteeConfirmationNotification' => 'Invitee pass',
            '' => stripos($subject, 'contact') !== false ? 'Contact form' : 'Other',
            default => \Illuminate\Support\Str::headline(str_replace('Notification', '', $class)),
        };
    }

    /** Pass numbers (VD…) and order numbers (OB…) in the email, for searching. */
    public static function refs(string $text): ?string
    {
        preg_match_all('/\b(VD\d{3,}|OB\d{6}-[A-Z0-9]{4,})\b/', strip_tags($text), $m);
        $refs = array_values(array_unique($m[1] ?? []));

        return $refs ? mb_substr(implode(' ', $refs), 0, 500) : null;
    }

    private static function html(Email $message): ?string
    {
        $body = $message->getHtmlBody() ?? $message->getTextBody();
        if (is_resource($body)) {
            $body = stream_get_contents($body);
        }

        return $body === null ? null : (string) $body;
    }

    private static function phoneFor(string $email): ?string
    {
        $phone = Booking::whereRaw('LOWER(email) = ?', [$email])->whereNotNull('phone')->where('phone', '!=', '')->latest('id')->value('phone')
            ?: Order::whereRaw('LOWER(buyer_email) = ?', [$email])->latest('id')->value('buyer_phone');

        return $phone ? mb_substr((string) $phone, 0, 30) : null;
    }

    private static function nameFor(string $email): ?string
    {
        // the person themselves (pass 1), never a "Guest of …" pass held under their email
        return Booking::whereRaw('LOWER(email) = ?', [$email])->where('name', 'not like', 'Guest of %')
            ->orderBy('attendee_no')->orderByDesc('id')->value('name')
            ?: Order::whereRaw('LOWER(buyer_email) = ?', [$email])->latest('id')->value('buyer_name');
    }

    /** The table exists (so a deploy before "migrate" never breaks email). */
    private static function ready(): bool
    {
        static $ready = null;

        return $ready ??= Schema::hasTable('mail_logs') && Schema::hasTable('mail_log_bodies');
    }
}
