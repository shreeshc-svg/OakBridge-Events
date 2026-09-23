@php
    $setting = \App\Models\Setting::find(1);
    $order = $data['order'];
    $quote = $data['quote'];
    $money = fn ($amount) => \App\Support\Pricing::money((float) $amount);
    $eventDate = $order->service?->date;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .summary { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 5px; padding: 16px; margin: 18px 0; }
        .row { display: block; margin: 4px 0; }
        .total { font-size: 20px; font-weight: bold; }
        .cta { text-align: center; margin: 26px 0; }
        .cta a { background: #D3181F; color: #ffffff !important; padding: 13px 30px; border-radius: 6px;
            text-decoration: none; font-size: 17px; display: inline-block; }
        .muted { color: #666; font-size: 13px; }
        .footer { margin-top: 22px; text-align: center; font-size: 0.9em; color: #555; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>

<body>
    <div class="container">
        <p>Dear {{ $order->buyer_name }},</p>

        @if ($data['is_last'])
            <p>
                We are holding {{ $order->quantity }} {{ $order->pass_name }}{{ $order->quantity > 1 ? 'es' : '' }}
                for <strong>{{ $order->event }}</strong>, but the payment has still not come through. This is the
                last reminder we will send.
            </p>
        @else
            <p>
                You registered for <strong>{{ $order->event }}</strong>@if ($eventDate), on
                    {{ $eventDate->format('l, j F Y') }}@endif, but the payment was not completed, so your
                {{ $order->quantity > 1 ? 'passes are' : 'pass is' }} not confirmed yet.
            </p>
        @endif

        <div class="summary">
            <span class="row"><strong>Order</strong> {{ $order->order_no }}</span>
            <span class="row">{{ $order->pass_name }} &times; {{ $order->quantity }}</span>
            @if ($quote['discount'] > 0)
                <span class="row">{{ $quote['discount_label'] ?: 'Discount' }} &minus;{{ $money($quote['discount']) }}</span>
            @endif
            @if ($quote['tax'] > 0 && !$quote['tax_included'])
                <span class="row">{{ $quote['tax_label'] }} +{{ $money($quote['tax']) }}</span>
            @endif
            <span class="row total">{{ $money($order->total) }}</span>
            @if ($quote['tax'] > 0 && $quote['tax_included'])
                <span class="row muted">Includes {{ $quote['tax_label'] }} of {{ $money($quote['tax']) }}.</span>
            @endif
        </div>

        <div class="cta">
            <a href="{{ $data['pay_url'] }}">Complete your payment</a>
        </div>

        <p class="muted" style="text-align: center;">
            Or paste this into your browser:<br>
            <a href="{{ $data['pay_url'] }}">{{ $data['pay_url'] }}</a>
        </p>

        @if (count($order->attendeeList()) > 1)
            <p class="muted">
                These passes are held for:
                @foreach ($order->attendeeList() as $number => $person)
                    {{ $person['name'] }}@if (!$loop->last), @endif
                @endforeach
            </p>
        @endif

        <p>
            Your {{ $order->quantity > 1 ? 'passes are' : 'pass is' }} confirmed the moment the payment succeeds, and
            {{ $order->quantity > 1 ? 'each pass holder gets' : 'you get' }} an email straight away.
        </p>

        <p class="muted">
            Already paid, or would you rather pay another way? Reply to this email quoting
            <strong>{{ $order->order_no }}</strong> and we will sort it out.
        </p>

        <div class="footer">
            {{ $setting->bname ?? 'OakBridge Events' }}<br>
            <a href="mailto:{{ $setting->email ?? 'info@oakbridge.in' }}">{{ $setting->email ?? 'info@oakbridge.in' }}</a>
        </div>
    </div>
</body>

</html>
