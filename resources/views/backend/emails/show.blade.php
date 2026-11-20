@extends('adminlte::page')

@section('title', 'Email – ' . $email->subject)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 class="h3">{{ $email->subject ?: '(no subject)' }}</h1>
        <div>
            <a href="{{ route('emails.index', ['person' => $email->to_email]) }}" class="btn btn-outline-primary btn-sm">All emails to {{ $email->to_name ?: $email->to_email }}</a>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('emails.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Back</a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <dl class="mb-0 small">
                        <dt>To</dt><dd>{{ $email->to_name }} &lt;{{ $email->to_email }}&gt;@if ($email->phone)<br>{{ $email->phone }}@endif</dd>
                        @if ($email->cc)<dt>Copy to</dt><dd>{{ $email->cc }}</dd>@endif
                        <dt>Kind</dt><dd>{{ $email->type }}</dd>
                        <dt>{{ $email->status === 'sent' ? 'Sent' : 'Created' }}</dt><dd>{{ ($email->sent_at ?? $email->created_at)->format('l, j M Y, H:i:s') }}</dd>
                        <dt>Status</dt>
                        <dd>
                            @if ($email->status === 'sent')<span class="text-success">Accepted by the mail server</span>
                            @elseif ($email->failed())<span class="text-danger">Not sent – the mail server did not accept it</span>
                            @else Sending…@endif
                        </dd>
                        @if ($email->refs)<dt>Pass / order numbers</dt><dd>{{ $email->refs }}</dd>@endif
                        @if ($email->message_id)<dt>Message ID</dt><dd class="text-muted text-break">{{ $email->message_id }}</dd>@endif
                    </dl>
                    <p class="small text-muted mt-3 mb-0">"Sent" means the mail server accepted it. Whether it reached the inbox or the spam folder is decided by the person's email provider.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header py-2"><strong>What they received</strong></div>
                <iframe src="{{ route('emails.body', $email->id) }}" sandbox title="Email content" style="width:100%; height:75vh; border:0; background:#fff"></iframe>
            </div>
        </div>
    </div>
@stop
