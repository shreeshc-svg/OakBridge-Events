@extends('adminlte::page')

@section('title', 'Order ' . $order->order_no)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>Order {{ $order->order_no }}</h1>
        <div>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">&larr; All orders</a>
            <form action="{{ route('orders.destroy', $order) }}" method="post" class="d-inline"
                onsubmit="return confirm('Delete order {{ $order->order_no }}?{{ $order->isPaid() ? ' This order is PAID (' . \App\Support\Pricing::money((float) $order->total) . ') – the payment record and its issued passes will be gone for good.' : ' Its held passes go with it.' }}');">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-outline-danger btn-sm">Delete order</button>
            </form>
        </div>
    </div>
@stop

@section('content')
    @include('backend.partials.alerts')

    @php $money = fn ($amount) => \App\Support\Pricing::money((float) $amount); @endphp

    <div class="row">
        <div class="col-lg-7">
            <div class="card card-primary card-outline">
                <div class="card-header"><h3 class="card-title">Buyer</h3></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name</dt><dd class="col-sm-8">{{ $order->buyer_name }}</dd>
                        <dt class="col-sm-4">Email</dt><dd class="col-sm-8"><a href="mailto:{{ $order->buyer_email }}">{{ $order->buyer_email }}</a></dd>
                        <dt class="col-sm-4">Phone</dt><dd class="col-sm-8">{{ $order->buyer_phone }}</dd>
                        <dt class="col-sm-4">Organization</dt><dd class="col-sm-8">{{ $order->buyer_company ?: '–' }}</dd>
                        <dt class="col-sm-4">Designation</dt><dd class="col-sm-8">{{ $order->buyer_designation ?: '–' }}</dd>
                        <dt class="col-sm-4">Event</dt><dd class="col-sm-8">{{ $order->event }}</dd>
                        <dt class="col-sm-4">Placed</dt><dd class="col-sm-8">{{ $order->created_at?->format('j M Y, H:i') }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $order->isPaid() ? 'Passes issued' : 'Passes to be issued' }} ({{ $order->quantity }})
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if (!$order->bookings->count())
                        <table class="table table-sm mb-0">
                            <thead><tr><th>#</th><th>Name</th><th>Email</th></tr></thead>
                            <tbody>
                                @foreach ($order->attendeeList() as $number => $person)
                                    <tr><td>{{ $number }}</td><td>{{ $person['name'] }}</td><td>{{ $person['email'] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="text-muted small p-2 mb-0">Booking numbers are issued when the order is paid.</p>
                    @else
                    <table class="table table-sm mb-0">
                        <thead><tr><th>#</th><th>Booking ID</th><th>Name</th><th>Email</th></tr></thead>
                        <tbody>
                            @foreach ($order->bookings->sortBy('attendee_no') as $booking)
                                <tr>
                                    <td>{{ $booking->attendee_no }}</td>
                                    <td>{{ $booking->booking_id }}</td>
                                    <td>{{ $booking->name }}</td>
                                    <td>{{ $booking->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-primary card-outline">
                <div class="card-header"><h3 class="card-title">Amount</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>{{ $order->pass_name }} &times; {{ $order->quantity }}</td>
                            <td class="text-right">{{ $money($order->unit_price * $order->quantity) }}</td>
                        </tr>
                        <tr class="text-muted">
                            <td class="small">{{ $money($order->unit_price) }} per pass</td>
                            <td></td>
                        </tr>
                        @if ($order->discount_total > 0)
                            <tr class="text-success">
                                <td>{{ $order->discount_label ?: 'Discount' }}</td>
                                <td class="text-right">&ndash; {{ $money($order->discount_total) }}</td>
                            </tr>
                        @endif
                        @if ($order->tax_total > 0)
                            <tr>
                                <td>Tax ({{ rtrim(rtrim(number_format((float) $order->tax_percent, 2), '0'), '.') }}%)</td>
                                <td class="text-right">{{ $money($order->tax_total) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Total</th>
                            <th class="text-right">{{ $money($order->total) }}</th>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card card-primary card-outline">
                <div class="card-header"><h3 class="card-title">Payment</h3></div>
                <form action="{{ route('orders.update', $order) }}" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                @foreach (\App\Models\Order::STATUSES as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $order->status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Paid by</label>
                            <input type="text" class="form-control" id="payment_method" name="payment_method" maxlength="40"
                                value="{{ old('payment_method', $order->payment_method) }}" placeholder="Bank transfer, UPI, cash…">
                        </div>
                        <div class="form-group">
                            <label for="payment_reference">Reference</label>
                            <input type="text" class="form-control" id="payment_reference" name="payment_reference" maxlength="191"
                                value="{{ old('payment_reference', $order->payment_reference) }}" placeholder="UTR / transaction id">
                        </div>
                        <div class="form-group mb-0">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                        </div>
                        @if ($order->paid_at)
                            <p class="text-muted small mt-2 mb-0">Paid on {{ $order->paid_at->format('j M Y, H:i') }}.</p>
                        @endif
                        @if ($order->gateway_order_id)
                            <p class="text-muted small mt-1 mb-0">Razorpay order {{ $order->gateway_order_id }}</p>
                        @endif
                        @if (!$order->isPaid())
                            <p class="text-muted small mt-2 mb-0">
                                Setting this to Paid issues {{ $order->quantity }} pass(es) and emails the buyer.
                            </p>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
