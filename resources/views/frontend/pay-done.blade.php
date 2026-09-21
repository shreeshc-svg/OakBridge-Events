@php
    $money = fn ($amount) => \App\Support\Pricing::money((float) $amount);
@endphp
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment received – {{ $order->event }}</title>
    <link href="{{ asset('public/assets/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/style.css') }}" rel="stylesheet">
    <style>
        body { background: #f4f5f7; }
        .done-wrap { max-width: 620px; margin: 3rem auto; }
        .done-card { background: #fff; border-radius: .5rem; box-shadow: 0 2px 14px rgba(0,0,0,.08); padding: 2rem; }
        .tick { width: 62px; height: 62px; border-radius: 50%; background: #e8f7ee; color: #1e8e47;
                display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1rem; }
    </style>
</head>

<body>
    <div class="done-wrap">
        <div class="done-card text-center">
            <div class="tick">&#10003;</div>
            @if ($order->isPaid())
                <h3>You're registered!</h3>
                <p class="text-muted">
                    {{ $order->quantity }} {{ $order->quantity > 1 ? 'passes' : 'pass' }} for
                    <strong>{{ $order->event }}</strong>
                </p>
                <p class="mb-1">Paid: <strong>{{ $money($order->total) }}</strong></p>
                <p class="text-muted small">
                    Order {{ $order->order_no }}@if ($order->payment_reference) &middot; Payment {{ $order->payment_reference }}@endif
                </p>

                @if ($bookings->count())
                    <table class="table table-sm mt-4 text-left">
                        <thead><tr><th>Pass</th><th>Attendee</th><th>Email</th></tr></thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td class="text-nowrap">{{ $booking->booking_id }}</td>
                                    <td>{{ $booking->name }}</td>
                                    <td>{{ $booking->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <p class="text-muted small">A confirmation has been emailed to {{ $order->buyer_email }}.</p>
            @else
                <h3>Payment pending</h3>
                <p class="text-muted">Order {{ $order->order_no }} is not paid yet.</p>
                <a href="{{ route('order.pay', $order->order_no) }}" class="btn btn-primary">Complete the payment</a>
            @endif

            <a href="{{ url('/') }}" class="btn btn-outline-secondary mt-3">Back to the website</a>
        </div>
    </div>
</body>

</html>
