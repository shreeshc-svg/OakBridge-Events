<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One email sent by the website (Admin > Emails sent). */
class MailLog extends Model
{
    protected $guarded = [];

    protected $casts = ['sent_at' => 'datetime'];

    /** The HTML as sent. */
    public function html(): ?string
    {
        return \Illuminate\Support\Facades\DB::table('mail_log_bodies')->where('mail_log_id', $this->id)->value('body');
    }

    /** Lower-case email, name, phone (digits), copies, pass/order numbers and subject in one field. */
    public static function searchText(array $row): string
    {
        $phone = preg_replace('/\D/', '', (string) ($row['phone'] ?? ''));

        return mb_substr(mb_strtolower(implode(' ', array_filter([
            $row['to_email'] ?? null, $row['to_name'] ?? null, $phone, strlen($phone) > 10 ? substr($phone, -10) : null,
            $row['cc'] ?? null, $row['refs'] ?? null, $row['subject'] ?? null,
        ]))), 0, 1200);
    }

    public function failed(): bool
    {
        return $this->status !== 'sent' && $this->created_at && $this->created_at->lt(now()->subMinutes(5));
    }
}
