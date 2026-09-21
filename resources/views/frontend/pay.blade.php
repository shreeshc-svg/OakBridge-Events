@php
    $money = fn ($amount) => \App\Support\Pricing::money((float) $amount);
@endphp
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pay for your passes – {{ $order->event }}</title>
    <link href="{{ asset('public/assets/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/style.css') }}" rel="stylesheet">
    <style>
        body { background: #f4f5f7; }
        .pay-wrap { max-width: 560px; margin: 3rem auto; }
        .pay-card { background: #fff; border-radius: .5rem; box-shadow: 0 2px 14px rgba(0,0,0,.08); padding: 2rem; }
        .pay-total { font-size: 1.6rem; font-weight: 700; }
        .pay-line { display: flex; justify-content: space-between; padding: .35rem 0; }
        .pay-line.total { border-top: 2px solid #222; margin-top: .6rem; padding-top: .6rem; }
        .btn-pay { background: #3395ff; border-color: #3395ff; color: #fff; font-size: 1.1rem; padding: .7rem 1.5rem; }
        .btn-pay:hover { background: #1a7fe8; color: #fff; }
    </style>
</head>

<body>
    <div class="pay-wrap">
        <div class="pay-card">
            <p class="text-muted mb-1">Order {{ $order->order_no }}</p>
            <h4 class="mb-3">{{ $order->event }}</h4>

            @if ($errors->any())
                <div class="alert alert-warning">{{ $errors->first() }}</div>
            @endif

            @if ($testMode)
                <div class="alert alert-info py-2">Test mode – no real money will be charged.</div>
            @endif

            <div class="pay-line">
                <span>{{ $order->pass_name }} &times; {{ $order->quantity }}</span>
                <span>{{ $money($quote['subtotal']) }}</span>
            </div>
            @if ($quote['discount'] > 0)
                <div class="pay-line text-success">
                    <span>{{ $quote['discount_label'] ?: 'Discount' }}</span>
                    <span>&ndash; {{ $money($quote['discount']) }}</span>
                </div>
            @endif
            @if ($quote['tax'] > 0 && !$quote['tax_included'])
                <div class="pay-line">
                    <span>{{ $quote['tax_label'] }} ({{ rtrim(rtrim(number_format($quote['tax_percent'], 2), '0'), '.') }}%)</span>
                    <span>{{ $money($quote['tax']) }}</span>
                </div>
            @endif
            <div class="pay-line total">
                <span>Amount payable</span>
                <span class="pay-total">{{ $money($quote['total']) }}</span>
            </div>

            @if ($quote['tax'] > 0 && $quote['tax_included'])
                <p class="small text-muted mb-0">Includes {{ $quote['tax_label'] }} of {{ $money($quote['tax']) }}.</p>
            @endif

            <ul class="list-unstyled small text-muted mt-3 mb-4">
                @foreach ($order->attendeeList() as $number => $person)
                    <li>Pass {{ $number }}: {{ $person['name'] }} ({{ $person['email'] }})</li>
                @endforeach
            </ul>

            @if ($gatewayOrderId && $keyId)
                <button id="payNow" class="btn btn-pay btn-block">Pay {{ $money($quote['total']) }}</button>
                <p class="small text-muted mt-3 mb-0">
                    Cards, UPI, net banking and wallets. Your passes are confirmed the moment the payment succeeds.
                </p>
            @else
                <div class="alert alert-warning mb-0">
                    We could not open the payment window for order <strong>{{ $order->order_no }}</strong> just now.
                    Your passes are held. Please email
                    <a href="mailto:{{ $setting->email ?? 'info@oakbridge.in' }}">{{ $setting->email ?? 'info@oakbridge.in' }}</a>
                    quoting that number and we will send you payment details.
                </div>
            @endif

            <form id="verifyForm" action="{{ route('order.verify', $order->order_no) }}" method="post" class="d-none">
                @csrf
                <input type="hidden" name="razorpay_order_id">
                <input type="hidden" name="razorpay_payment_id">
                <input type="hidden" name="razorpay_signature">
            </form>
        </div>

        <p class="text-center small text-muted mt-3">
            Questions? Email <a href="mailto:{{ $setting->email ?? 'info@oakbridge.in' }}">{{ $setting->email ?? 'info@oakbridge.in' }}</a>
            quoting {{ $order->order_no }}.
        </p>
    </div>

    @if ($gatewayOrderId && $keyId)
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            (function() {
                var form = document.getElementById('verifyForm');
                var button = document.getElementById('payNow');

                // checkout.js blocked or offline: say so instead of failing silently
                if (typeof Razorpay === 'undefined') {
                    button.disabled = true;
                    button.textContent = 'Payment window unavailable';
                    var warn = document.createElement('div');
                    warn.className = 'alert alert-warning mt-3 mb-0';
                    warn.innerHTML = 'The payment window could not load. Check your internet connection and reload this page, ' +
                        'or email us quoting order <strong>' + @json($order->order_no) + '</strong> and we will send you payment details.';
                    button.parentNode.insertBefore(warn, button.nextSibling);
                    return;
                }

                var options = {
                    key: @json($keyId),
                    order_id: @json($gatewayOrderId),
                    amount: @json(\App\Support\Razorpay::paise((float) $order->total)),
                    currency: 'INR',
                    name: @json($setting->bname ?? 'OakBridge Events'),
                    description: @json($order->quantity . ' × ' . $order->pass_name . ' – ' . $order->event),
                    prefill: {
                        name: @json($order->buyer_name),
                        email: @json($order->buyer_email),
                        contact: @json($order->buyer_phone)
                    },
                    notes: { order_no: @json($order->order_no) },
                    theme: { color: '#b8860b' },
                    handler: function(response) {
                        form.querySelector('[name=razorpay_order_id]').value = response.razorpay_order_id;
                        form.querySelector('[name=razorpay_payment_id]').value = response.razorpay_payment_id;
                        form.querySelector('[name=razorpay_signature]').value = response.razorpay_signature;
                        form.submit();
                    },
                    modal: {
                        ondismiss: function() {
                            button.disabled = false;
                            button.textContent = @json('Pay ' . \App\Support\Pricing::money((float) $order->total));
                        }
                    }
                };

                var checkout = new Razorpay(options);
                checkout.on('payment.failed', function(response) {
                    button.disabled = false;
                    button.textContent = 'Try the payment again';
                    var box = document.createElement('div');
                    box.className = 'alert alert-danger mt-3';
                    box.textContent = (response.error && response.error.description)
                        ? response.error.description
                        : 'The payment could not be completed.';
                    button.parentNode.insertBefore(box, button.nextSibling);
                });

                button.addEventListener('click', function() {
                    button.disabled = true;
                    button.textContent = 'Opening payment…';
                    checkout.open();
                });
            })();
        </script>
    @endif
</body>

</html>
