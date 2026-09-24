@php
    $setting = \App\Models\Setting::find(1);
    $contact = $setting?->email ?: 'info@oakbridge.in';
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax invoice {{ $s['number'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .box { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 5px; padding: 14px 16px; margin: 18px 0; }
        .row { display: block; margin: 3px 0; }
        .muted { color: #666; font-size: 13px; }
        .footer { margin-top: 20px; text-align: center; font-size: 0.9em; color: #555; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>

<body>
    <div class="container">
        <p>Dear {{ $s['buyer']['name'] }},</p>

        <p>Thank you for your payment. Your tax invoice for <strong>{{ $s['event']['title'] }}</strong> is attached.</p>

        <div class="box">
            <span class="row"><strong>Invoice</strong> {{ $s['number'] }}</span>
            <span class="row"><strong>Order</strong> {{ $s['order_no'] }}</span>
            <span class="row"><strong>Amount</strong> INR {{ \App\Support\InvoicePdf::amount($s['total']) }}</span>
            @if ($s['buyer']['gstin'])
                <span class="row"><strong>Your GSTIN</strong> {{ $s['buyer']['gstin'] }}</span>
            @endif
        </div>

        <p class="muted">
            Need a detail on the invoice corrected? Reply to this email quoting {{ $s['number'] }}.
        </p>

        <div class="footer">
            {{ $s['seller']['name'] }}<br>
            <a href="mailto:{{ $contact }}">{{ $contact }}</a>
        </div>
    </div>
</body>

</html>
