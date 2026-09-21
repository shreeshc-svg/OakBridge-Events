@extends('adminlte::page')

@section('title', 'Orders')

@section('content_header')
    <h1>Orders</h1>
@stop

@section('content')
    @include('backend.partials.alerts')

    @php $money = fn ($amount) => \App\Support\Pricing::money((float) $amount); @endphp

    <div class="row">
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner"><h3>{{ $money($totals['paid']) }}</h3><p>Paid</p></div>
                <div class="icon"><i class="fas fa-rupee-sign"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-warning">
                <div class="inner"><h3>{{ $money($totals['pending']) }}</h3><p>Awaiting payment</p></div>
                <div class="icon"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner"><h3>{{ $totals['passes'] }}</h3><p>Passes paid for</p></div>
                <div class="icon"><i class="fas fa-ticket-alt"></i></div>
            </div>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <form method="get" class="form-inline">
                <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    <option value="">All orders</option>
                    @foreach (\App\Models\Order::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm mr-2"
                    placeholder="Order no, name, email, phone">
                <button class="btn btn-sm btn-primary mr-2">Search</button>
                <a href="{{ route('orders.export', request()->only('status')) }}" class="btn btn-sm btn-outline-secondary">
                    Export CSV
                </a>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Buyer</th>
                            <th>Event / pass</th>
                            <th class="text-center">Passes</th>
                            <th class="text-right">Total</th>
                            <th>Status</th>
                            <th>Placed</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="text-nowrap"><a href="{{ route('orders.show', $order) }}">{{ $order->order_no }}</a></td>
                                <td>
                                    {{ $order->buyer_name }}
                                    <div class="small text-muted">{{ $order->buyer_company }}</div>
                                </td>
                                <td>
                                    {{ $order->event }}
                                    <div class="small text-muted">{{ $order->pass_name }}</div>
                                </td>
                                <td class="text-center">{{ $order->quantity }}</td>
                                <td class="text-right text-nowrap">{{ $money($order->total) }}</td>
                                <td>
                                    <span @class([
                                        'badge',
                                        'badge-success' => $order->status === 'paid',
                                        'badge-warning' => $order->status === 'pending',
                                        'badge-danger' => $order->status === 'failed',
                                        'badge-secondary' => $order->status === 'cancelled',
                                    ])>{{ $order->statusLabel() }}</span>
                                </td>
                                <td class="text-nowrap small">{{ $order->created_at?->format('j M Y, H:i') }}</td>
                                <td><a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-primary">Open</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-muted text-center py-4">
                                No orders yet. Orders appear here as soon as someone registers for a paid event.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($orders->hasPages())
            <div class="card-footer">{{ $orders->links() }}</div>
        @endif
    </div>
@stop
