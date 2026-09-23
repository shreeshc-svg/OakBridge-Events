{{-- Payment reminders: what the buyer has been sent, and the button to send another. --}}
@php
    $PR = \App\Support\PaymentReminders::class;
    $reminders = $order->reminders;
    $sentCount = $reminders->where('failed', false)->count();
    $blocked = $PR::blockedReason($order);
    $next = $PR::nextAutomatic($order);
@endphp

<div class="card card-primary card-outline">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Payment reminders</h3>
        @if ($sentCount)
            <span class="badge badge-secondary">{{ $sentCount }} sent</span>
        @endif
    </div>

    <div class="card-body">
        @if ($errors->has('reminder'))
            <div class="alert alert-warning py-2">{{ $errors->first('reminder') }}</div>
        @endif

        @if ($order->status === 'pending')
            <p class="text-muted small">
                Reminders go to <strong>{{ $order->buyer_email }}</strong> with a link straight to the payment page.
            </p>

            <form action="{{ route('orders.remind', $order) }}" method="post"
                onsubmit="return confirm('Email a payment reminder to {{ $order->buyer_email }}?');">
                @csrf
                <button type="submit" class="btn btn-primary btn-block" {{ $blocked ? 'disabled' : '' }}>
                    <i class="fas fa-fw fa-paper-plane"></i>
                    {{ $sentCount ? 'Send another reminder' : 'Send a payment reminder' }}
                </button>
            </form>

            @if ($blocked)
                <p class="text-muted small mt-2 mb-0">{{ $blocked }}</p>
            @endif

            <hr>

            <div class="small">
                @if ($next)
                    <i class="fas fa-fw fa-clock text-muted"></i>
                    Next automatic reminder:
                    <strong>{{ $next['at']->isPast() ? 'due now' : $next['at']->format('j M Y, H:i') }}</strong>
                    <span class="text-muted">
                        ({{ $next['stage'] === 3 ? 'the last one' : 'reminder ' . $next['stage'] . ' of 3' }})
                    </span>
                @else
                    <i class="fas fa-fw fa-flag-checkered text-muted"></i>
                    All three automatic reminders have gone. Anything further is up to you.
                @endif
            </div>
        @elseif ($sentCount)
            <p class="text-muted small mb-0">
                This order is {{ strtolower($order->statusLabel()) }}, so no more reminders will go out.
            </p>
        @else
            <p class="text-muted small mb-0">
                Reminders only go to orders awaiting payment. This one is {{ strtolower($order->statusLabel()) }}.
            </p>
        @endif
    </div>

    @if ($reminders->count())
        <div class="card-body pt-0">
            <h6 class="text-muted text-uppercase small mb-2">What the buyer has been sent</h6>
            <ul class="ob-reminders">
                @foreach ($reminders as $reminder)
                    <li class="{{ $reminder->failed ? 'is-failed' : '' }}">
                        <span class="ob-reminders__dot"></span>
                        <div>
                            <div class="font-weight-bold">
                                {{ $reminder->sent_at?->format('j M Y, H:i') }}
                                @if ($reminder->failed)
                                    <span class="badge badge-danger ml-1">Not delivered</span>
                                @endif
                            </div>
                            <div class="text-muted small">{{ $reminder->label() }}</div>
                            <div class="text-muted small">&ldquo;{{ $reminder->subject }}&rdquo; &rarr; {{ $reminder->sent_to }}</div>
                            @if ($reminder->failed && $reminder->error)
                                <div class="text-danger small">{{ $reminder->error }}</div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<style>
    .ob-reminders { list-style: none; margin: 0; padding: 0 0 0 14px; border-left: 2px solid #e9ecef; }
            .ob-reminders li { position: relative; display: flex; gap: 10px; padding: 0 0 14px; }
            .ob-reminders li:last-child { padding-bottom: 0; }
            .ob-reminders__dot { position: absolute; left: -21px; top: 6px; width: 10px; height: 10px;
                border-radius: 50%; background: #28a745; box-shadow: 0 0 0 3px #fff; }
            .ob-reminders li.is-failed .ob-reminders__dot { background: #dc3545; }
</style>
