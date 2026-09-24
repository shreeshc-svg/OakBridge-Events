<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** The tax invoice, as a PDF attachment, to the buyer. */
class InvoiceNotification extends Notification
{
    use Queueable;

    public function __construct(public Invoice $invoice, public string $pdf)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $s = $this->invoice->snapshot;

        return (new MailMessage)
            ->subject('Tax invoice ' . $this->invoice->number . ' – ' . $s['event']['title'])
            ->replyTo(Setting::find(1)?->email ?: 'info@oakbridge.in')
            ->view('email.invoice', ['s' => $s])
            ->attachData($this->pdf, $this->invoice->filename(), ['mime' => 'application/pdf']);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
