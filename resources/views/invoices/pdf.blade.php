{{-- GST tax invoice. Built from tables only: dompdf does not do flexbox or grid. --}}
@php
    $A = fn ($v) => \App\Support\InvoicePdf::amount((float) $v);
    $seller = $s['seller'];
    $buyer = $s['buyer'];
    $event = $s['event'];
    $pos = $s['place_of_supply'];
    $date = \Illuminate\Support\Carbon::parse($s['date']);
    $eventDate = $event['date'] ? \Illuminate\Support\Carbon::parse($event['date']) : null;
    $rate = fn ($r) => rtrim(rtrim(number_format((float) $r, 2), '0'), '.');
    // older snapshots may predate these two keys
    $gstRate = $s['gst_rate'] ?? array_sum(array_column($s['taxes'], 'rate'));
    $gstTotal = $s['gst_total'] ?? array_sum(array_column($s['taxes'], 'amount'));
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $s['number'] }}</title>
    <style>
        @page { margin: 26px 30px; }
        * { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; }
        body { font-size: 9.5px; color: #1f2937; margin: 0; }
        .navy { color: #0b2d5c; }
        h1 { text-align: center; font-size: 18px; color: #0b2d5c; margin: 0 0 10px; letter-spacing: .5px; }
        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }
        .frame { border: 1px solid #c7ccd6; }
        .pad { padding: 8px 10px; }
        .muted { color: #4b5563; }
        .b { font-weight: bold; }
        .right { text-align: right; }
        .center { text-align: center; }
        .meta td { border: 1px solid #c7ccd6; padding: 5px 7px; }
        .meta td.k { color: #4b5563; width: 42%; }
        .meta td.v { color: #0b2d5c; }
        .label { color: #4b5563; margin-bottom: 2px; }
        .who { font-weight: bold; color: #0b2d5c; font-size: 10.5px; }
        .items th { background: #0b2d5c; color: #fff; text-align: left; padding: 6px 7px; font-size: 9px; border: 1px solid #0b2d5c; }
        .items td { border: 1px solid #c7ccd6; padding: 7px; }
        .items .num { text-align: right; white-space: nowrap; }
        .sums td { padding: 4px 7px; }
        .sums .total td { border-top: 1.2px solid #1f2937; font-weight: bold; color: #0b2d5c; font-size: 11px; padding-top: 6px; }
        .foot td { border: 1px solid #c7ccd6; padding: 8px 10px; }
        .small { font-size: 8.5px; }
    </style>
</head>

<body>
    <h1>TAX INVOICE</h1>

    {{-- seller + invoice details --}}
    <table class="frame">
        <tr>
            <td class="pad" style="width: 52%;">
                @if ($logo)
                    <img src="{{ $logo }}" alt="" style="height: 64px; margin-bottom: 6px;"><br>
                @endif
                <div class="who">{{ $seller['name'] }}</div>
                <div class="muted" style="margin-top: 2px; line-height: 1.45;">
                    {!! nl2br(e($seller['address'])) !!}<br>
                    GSTIN/UIN: {{ $seller['gstin'] }}<br>
                    State Name: {{ $seller['state'] }}@if ($seller['state_code']), Code: {{ $seller['state_code'] }}@endif
                    @if ($seller['phone'])<br>Contact: {{ $seller['phone'] }}@endif
                    @if ($seller['email'])<br>E-Mail: {{ $seller['email'] }}@endif
                </div>
            </td>
            <td style="width: 48%; padding: 8px 8px 8px 0;">
                <table class="meta">
                    @if ($seller['phone'])
                        <tr><td class="k">Contact</td><td class="v b">{{ $seller['phone'] }}</td></tr>
                    @endif
                    <tr><td class="k">Invoice No.</td><td class="v b">{{ $s['number'] }}</td></tr>
                    <tr><td class="k">Dated</td><td class="v b">{{ $date->format('d-M-y') }}</td></tr>
                    <tr><td class="k">Order No.</td><td class="v">{{ $s['order_no'] }}</td></tr>
                    @if ($s['payment_ref'])
                        <tr><td class="k">Payment Ref</td><td class="v">{{ $s['payment_ref'] }}</td></tr>
                    @endif
                    <tr><td class="k">Mode of Payment</td><td class="v">{{ $s['payment_mode'] }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- buyer + event (an event has no "ship to") --}}
    <table class="frame" style="border-top: 0;">
        <tr>
            <td class="pad" style="width: 50%; border-right: 1px solid #c7ccd6;">
                <div class="label">Buyer (Bill to)</div>
                <div class="who">{{ $buyer['company'] ?: $buyer['name'] }}</div>
                <div class="muted" style="line-height: 1.45;">
                    @if ($buyer['company'])Attn: {{ $buyer['name'] }}<br>@endif
                    @if ($buyer['address']){!! nl2br(e($buyer['address'])) !!}<br>@endif
                    @if ($buyer['state'] || $buyer['pin']){{ $buyer['state'] }}@if ($buyer['pin']) – {{ $buyer['pin'] }}@endif<br>@endif
                    @if ($buyer['phone'])Tel: {{ $buyer['phone'] }}<br>@endif
                    @if ($buyer['email'])E-Mail: {{ $buyer['email'] }}<br>@endif
                    @if ($buyer['gstin'])GSTIN/UIN: <span class="b" style="color: #1f2937;">{{ $buyer['gstin'] }}</span><br>@endif
                    @if ($buyer['state'])State Name: {{ $buyer['state'] }}@if ($buyer['state_code']), Code: {{ $buyer['state_code'] }}@endif @endif
                </div>
            </td>
            <td class="pad" style="width: 50%;">
                <div class="label">Event</div>
                <div class="who">{{ $event['title'] }}</div>
                <div class="muted" style="line-height: 1.45;">
                    @if ($eventDate){{ $eventDate->format('l, j F Y') }}<br>@endif
                    @if ($event['venue']){{ $event['venue'] }}<br>@endif
                    @if (count($s['attendees']))
                        <span style="display: block; margin-top: 4px;">Pass holders:</span>
                        @foreach ($s['attendees'] as $a)
                            {{ $a['name'] }}@if ($a['pass']) ({{ $a['pass'] }})@endif<br>
                        @endforeach
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="muted" style="padding: 5px 2px 8px;">
        @if ($pos)
            Place of Supply: {{ $pos['state'] }}@if ($pos['code']) ({{ $pos['code'] }})@endif
        @endif
    </div>

    {{-- the line --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width: 5%;">Sl</th>
                <th>Description of Services</th>
                <th style="width: 11%;">SAC</th>
                <th style="width: 10%;" class="num">Qty</th>
                <th style="width: 12%;" class="num">Rate</th>
                <th style="width: 8%;" class="num">GST</th>
                <th style="width: 14%;" class="num">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td class="navy">{{ $s['line']['description'] }}</td>
                <td>{{ $s['line']['sac'] ?: '' }}</td>
                <td class="num">{{ $s['line']['qty'] }} {{ $s['line']['qty'] > 1 ? 'Passes' : 'Pass' }}</td>
                <td class="num">{{ $A($s['line']['rate']) }}</td>
                <td class="num">{{ $rate($gstRate) }}%</td>
                <td class="num">{{ $A($s['line']['amount']) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="sums" style="width: 58%; margin-left: 42%; margin-top: 6px;">
        @if ($s['discount'])
            <tr><td class="muted">Less: {{ $s['discount']['label'] }}</td><td class="right">-{{ $A($s['discount']['amount']) }}</td></tr>
        @endif
        <tr><td class="muted">Taxable Value</td><td class="right">{{ $A($s['taxable']) }}</td></tr>
        @foreach ($s['taxes'] as $t)
            <tr><td class="muted">{{ $t['label'] }} @ {{ $rate($t['rate']) }}%</td><td class="right">{{ $A($t['amount']) }}</td></tr>
        @endforeach
        @if ($gstTotal > 0)
            <tr><td class="b navy">Total GST @ {{ $rate($gstRate) }}%</td><td class="right b navy">{{ $A($gstTotal) }}</td></tr>
        @endif
        <tr class="total"><td>Total</td><td class="right">INR {{ $A($s['total']) }}</td></tr>
    </table>

    <p style="margin: 10px 0 12px;">
        <span class="b navy">Amount Chargeable (in words):</span>
        <span class="navy">{{ $s['total_words'] }}</span>
    </p>

    <table class="foot">
        <tr>
            <td style="width: 54%;">
                <div class="b navy" style="margin-bottom: 3px;">Declaration</div>
                <div class="muted">{{ $seller['declaration'] }}</div>
                <div class="muted" style="margin-top: 16px;">for {{ $seller['name'] }}</div>
                <div class="muted" style="margin-top: 20px;">Authorised Signatory</div>
            </td>
            <td style="width: 46%;">
                @if ($seller['bank_name'] || $seller['bank_account'])
                    <div class="b navy" style="margin-bottom: 3px;">Company's Bank Details</div>
                    <div class="muted" style="line-height: 1.45;">
                        @if ($seller['bank_name'])Bank Name: {{ $seller['bank_name'] }}<br>@endif
                        @if ($seller['bank_account'])A/c No.: {{ $seller['bank_account'] }}<br>@endif
                        @if ($seller['bank_branch_ifsc'])Branch &amp; IFS Code: {{ $seller['bank_branch_ifsc'] }}@endif
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <p class="center muted small" style="margin-top: 10px;">This is a Computer Generated Invoice</p>
</body>

</html>
