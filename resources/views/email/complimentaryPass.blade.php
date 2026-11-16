@php
    $setting = \App\Models\Setting::find(1);
    $eventDate = $data['event_date'] ?? null;
    $contact = $setting?->email ?: 'info@oakbridge.in';
    $when = $eventDate ? $eventDate->format('l, j F Y') : null;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your complimentary pass – {{ $data['event'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .test { background: #fdecea; border: 1px dashed #d9534f; color: #a94442; padding: 8px 12px; margin-bottom: 16px; text-align: center; font-weight: bold; }
        .pass { background: #f8f9fa; border: 2px solid #1a3d6d; border-radius: 8px; padding: 18px; margin: 18px 0; text-align: center; }
        .pass .label { color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        .pass .id { font-size: 26px; font-weight: bold; letter-spacing: 2px; color: #1a3d6d; }
        .pass .type { margin-top: 4px; font-size: 15px; }
        .pass .event { margin-top: 10px; font-size: 14px; color: #444; }
        .footer { margin-top: 20px; text-align: center; font-size: 0.9em; color: #555; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>

<body>
    <div class="container">
        @if (!empty($data['test']))
            <div class="test">TEST EMAIL – this is a preview, not a real pass.</div>
        @endif

        <p>Dear {{ $data['name'] }},</p>

        <p>
            It is our pleasure to welcome you as our special guest at <strong>{{ $data['event'] }}</strong>.
            Here is your <strong>complimentary pass</strong> – there is nothing to pay.
        </p>

        <div class="pass">
            <div class="label">Complimentary pass</div>
            <div class="id">{{ $data['booking_id'] }}</div>
            <div class="type">{{ $data['pass_name'] ?: 'Complimentary Pass' }} &middot; {{ $data['name'] }}</div>
            <div class="event">
                {{ $data['event'] }}
                @if ($when)<br>{{ $when }}@endif
                @if (!empty($data['venue']))<br>{{ $data['venue'] }}@endif
            </div>
        </div>

        <p>Please show this email or quote your pass number at the registration desk.</p>

        <p>
            Questions? Email <a href="mailto:{{ $contact }}">{{ $contact }}</a>@if ($setting?->phone)
                or WhatsApp <a href="https://wa.me/{{ preg_replace('/\D/', '', $setting->phone) }}">{{ $setting->phone }}</a>@endif.
        </p>

        <p>We look forward to welcoming you.</p>

        <p>Warm regards,<br><strong>Team OakBridge</strong></p>

        <div class="footer">
            <p>Website: <a href="{{ url('/') }}">{{ url('/') }}</a></p>
        </div>
    </div>
</body>

</html>
