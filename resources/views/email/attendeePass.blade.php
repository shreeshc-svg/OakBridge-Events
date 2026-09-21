@php
    $setting = \App\Models\Setting::find(1);
    $eventDate = $data['event_date'] ?? null;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your pass – {{ $data['event'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .pass { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 5px; padding: 16px; margin: 18px 0; text-align: center; }
        .pass .id { font-size: 22px; font-weight: bold; letter-spacing: 1px; }
        .footer { margin-top: 20px; text-align: center; font-size: 0.9em; color: #555; }
        .muted { color: #666; font-size: 13px; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>

<body>
    <div class="container">
        <p>Dear {{ $data['name'] }},</p>

        <p>
            You have a confirmed pass for <strong>{{ $data['event'] }}</strong>@if ($eventDate), on
                {{ $eventDate->format('l, j F Y') }}@endif.
            It was booked for you by {{ $data['buyer_name'] }}@if (!empty($data['buyer_company'])) ({{ $data['buyer_company'] }})@endif,
            and the payment has been received.
        </p>

        <div class="pass">
            <div class="muted">Your pass number</div>
            <div class="id">{{ $data['booking_id'] }}</div>
            <div class="muted">Pass {{ $data['attendee_no'] }} of {{ $data['quantity'] }} &middot; {{ $data['pass_name'] }}</div>
        </div>

        <p>Please keep this number – you will need it at the registration desk.</p>

        @if (!empty($setting?->invoice_note))
            <p class="muted">{{ $setting->invoice_note }}</p>
        @endif

        <p>
            Questions? Email
            <a href="mailto:{{ $setting?->email ?: 'info@oakbridge.in' }}">{{ $setting?->email ?: 'info@oakbridge.in' }}</a>@if ($setting?->phone)
                or WhatsApp <a href="https://wa.me/{{ preg_replace('/\D/', '', $setting->phone) }}">{{ $setting->phone }}</a>@endif.
        </p>

        <p>Warm regards,<br><strong>Team OakBridge</strong></p>

        <div class="footer">
            <p>Website: <a href="{{ url('/') }}">{{ url('/') }}</a></p>
        </div>
    </div>
</body>

</html>
