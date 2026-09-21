@php
    $order = $data['order'] ?? null;
    $quote = $data['quote'] ?? null;
    $attendees = $data['attendees'] ?? [];
    $eventDate = $data['event_date'] ?? null;
    $setting = \App\Models\Setting::find(1);
    $payUrl = $data['pay_url'] ?? null;
    $isPaid = $order && $order->isPaid();
    $money = fn ($amount) => \App\Support\Pricing::money((float) $amount);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $order ? 'Complete your payment' : 'Registration received' }} – {{ $data['event'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .footer { margin-top: 20px; text-align: center; font-size: 0.9em; color: #555; }
        a { color: #007bff; text-decoration: none; }
        table.summary { width: 100%; border-collapse: collapse; margin: 16px 0; }
        table.summary th, table.summary td { padding: 8px 6px; border-bottom: 1px solid #eee; text-align: left; font-size: 14px; }
        table.summary td.amount, table.summary th.amount { text-align: right; white-space: nowrap; }
        table.summary tr.total td { border-top: 2px solid #333; border-bottom: none; font-weight: bold; font-size: 16px; }
        .box { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 5px; padding: 12px 16px; margin: 16px 0; }
        .muted { color: #666; font-size: 13px; }
    </style>
</head>

<body>
    <div class="container">
        <p>Dear {{ $data['name'] }},</p>

        <p>
            Thank you for registering for <strong>{{ $data['event'] }}</strong>@if ($eventDate), scheduled for
                {{ $eventDate->format('l, j F Y') }}@endif.
        </p>

        @if ($order && $quote)
            @if ($isPaid)
                <p><strong>Payment received &ndash; your {{ $order->quantity > 1 ? 'passes are' : 'pass is' }} confirmed.</strong></p>
            @else
                <p><strong>To confirm your {{ $order->quantity > 1 ? 'passes' : 'pass' }}, please complete the payment below.</strong></p>
            @endif

            <table class="summary">
                <tr>
                    <th>{{ $order->pass_name }} &times; {{ $order->quantity }}</th>
                    <th class="amount">{{ $money($quote['subtotal']) }}</th>
                </tr>
                <tr>
                    <td class="muted">{{ $money($quote['unit']) }} per pass{{ $quote['is_early'] ? ' (early bird)' : '' }}</td>
                    <td class="amount muted">&nbsp;</td>
                </tr>
                @if ($quote['discount'] > 0)
                    <tr>
                        <td>{{ $quote['discount_label'] }}</td>
                        <td class="amount">&ndash; {{ $money($quote['discount']) }}</td>
                    </tr>
                @endif
                @if ($quote['tax'] > 0 && ! $quote['tax_included'])
                    <tr>
                        <td>{{ $quote['tax_label'] }} ({{ rtrim(rtrim(number_format($quote['tax_percent'], 2), '0'), '.') }}%)</td>
                        <td class="amount">{{ $money($quote['tax']) }}</td>
                    </tr>
                @endif
                <tr class="total">
                    <td>Amount payable</td>
                    <td class="amount">{{ $money($quote['total']) }}</td>
                </tr>
            </table>

            @if ($quote['tax'] > 0 && $quote['tax_included'])
                <p class="muted">Includes {{ $quote['tax_label'] }} of {{ $money($quote['tax']) }}.</p>
            @endif

            <p class="muted">
                Order reference: <strong>{{ $order->order_no }}</strong>{!! $isPaid ? '' : ' &ndash; please quote it with your payment' !!}.
            </p>

            @if ($payUrl && !$isPaid)
                <p style="text-align:center;margin:24px 0">
                    <a href="{{ $payUrl }}"
                        style="display:inline-block;background:#b8860b;color:#fff;padding:12px 28px;border-radius:5px;
                               text-decoration:none;font-weight:bold;font-size:16px">
                        Pay {{ $money($quote['total']) }} now
                    </a>
                </p>
                <p class="muted" style="text-align:center">Cards, UPI, net banking and wallets. Your passes are held until then.</p>
            @endif

            @if (!$isPaid && !empty($data['payment_instructions']))
                <div class="box">
                    <strong>How to pay</strong>
                    <div>{!! nl2br(e($data['payment_instructions'])) !!}</div>
                </div>
            @endif

            @if (count($attendees) > 1)
                <p><strong>{{ $isPaid ? 'Your passes:' : 'Passes in this order:' }}</strong></p>
                <ul>
                    @foreach ($attendees as $attendee)
                        <li>{{ $attendee['name'] }} ({{ $attendee['email'] }}) &ndash; {{ $attendee['booking_id'] }}</li>
                    @endforeach
                </ul>
            @endif

            @if (!empty($setting?->invoice_note))
                <p class="muted">{{ $setting->invoice_note }}</p>
            @endif
        @else
            <p>Your registration has been received. We will be in touch with the joining details closer to the date.</p>
            @if (!empty($data['booking_id']))
                <p class="muted">Registration reference: <strong>{{ $data['booking_id'] }}</strong></p>
            @endif
        @endif

        <p>
            For any queries, please reach out to us at
            <a href="mailto:{{ $setting?->email ?: 'info@oakbridge.in' }}">{{ $setting?->email ?: 'info@oakbridge.in' }}</a>
            @if ($setting?->phone)
                or WhatsApp: <a href="https://wa.me/{{ preg_replace('/\D/', '', $setting->phone) }}">{{ $setting->phone }}</a>
            @endif
        </p>

        <p>Warm regards,<br>
            <strong>Team OakBridge</strong>
        </p>

        <div class="footer">
            <p>Email: <a href="mailto:{{ $setting?->email ?: 'info@oakbridge.in' }}">{{ $setting?->email ?: 'info@oakbridge.in' }}</a></p>
            <p>Website: <a href="{{ url('/') }}">{{ url('/') }}</a></p>
        </div>
    </div>
</body>

</html>
