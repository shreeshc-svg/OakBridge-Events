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
        .event-line { background: #f8f9fa; border-radius: 5px; padding: 10px 14px; text-align: center; color: #444; }
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

        @php $passes = $data['passes']; $count = count($passes); @endphp
        <p>
            It is our pleasure to welcome you as our valued guest at <strong>{{ $data['event'] }}</strong>.
            @if ($count > 1)
                Here are your <strong>{{ $count }} complimentary passes</strong>.
            @else
                Here is your <strong>complimentary pass</strong>.
            @endif
        </p>

        <p class="event-line">
            <strong>{{ $data['event'] }}</strong>
            @if ($when)<br>{{ $when }}@endif
            @if (!empty($data['venue']))<br>{{ $data['venue'] }}@endif
        </p>

        @foreach ($passes as $i => $pass)
            <div class="pass">
                <div class="label">Complimentary pass{{ $count > 1 ? ' ' . ($i + 1) . ' of ' . $count : '' }}</div>
                <div class="id">{{ $pass['booking_id'] }}</div>
                <div class="type">{{ $pass['pass_name'] ?: 'Complimentary Pass' }} &middot; {{ $pass['holder'] }}</div>
            </div>
        @endforeach

        @if ($count > 1)
            <p>Each guest should quote their own pass number at the registration desk – please forward this email to them, or share their pass number.</p>
        @else
            <p>Please show this email or quote your pass number at the registration desk.</p>
        @endif

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
