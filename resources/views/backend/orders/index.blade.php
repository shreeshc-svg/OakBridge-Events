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
            <form id="bulkOrders" action="{{ route('orders.bulk-delete') }}" method="post"
                onsubmit="return confirmBulkDelete(this, 'order');">
                @csrf
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="hidden" name="q" value="{{ $search }}">
            </form>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th style="width:34px" class="text-center">
                                <input type="checkbox" id="checkAllOrders" aria-label="Select all">
                            </th>
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
                                <td class="text-center">
                                    <input form="bulkOrders" type="checkbox" name="ids[]" value="{{ $order->id }}"
                                        class="bulk-pick" aria-label="Select {{ $order->order_no }}">
                                </td>
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
                                <td class="text-nowrap">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-primary">Open</a>
                                    <form action="{{ route('orders.destroy', $order) }}" method="post" class="d-inline"
                                        onsubmit="return confirm('Delete order {{ $order->order_no }}{{ $order->status === 'paid' ? ' – this order is PAID (' . \App\Support\Pricing::money((float) $order->total) . '). Its passes and payment record will be gone for good.' : ' and its held passes?' }}');">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete order">&times;</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-muted text-center py-4">
                                No orders yet. Orders appear here as soon as someone registers for a paid event.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex align-items-center flex-wrap">
            <button type="submit" form="bulkOrders" class="btn btn-sm btn-outline-danger mr-3" id="bulkOrdersBtn" disabled>
                Delete selected
            </button>
            <span class="text-muted small mr-auto" id="bulkOrdersCount">Nothing selected</span>
            @if ($orders->hasPages())
                {{ $orders->links() }}
            @endif
        </div>
    </div>
@stop

@section('js')
    @include('backend.partials.bulk-delete-js')
@stop
