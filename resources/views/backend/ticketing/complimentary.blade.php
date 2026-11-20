{{--
    Ticketing > Complimentary passes for the chosen event. Needs: $event, $passTypes, $complimentary (passes grouped per person).
--}}
@php
    $compOpen = $event->date && $event->date->gte(today());
    $activePasses = $passTypes->where('is_active', true);
    $issuedPasses = $complimentary->sum(fn ($group) => $group->count());
    $oldRows = old('rows');
    $rows = is_array($oldRows) && $oldRows ? $oldRows : [0 => []];
@endphp
<div class="card card-success card-outline" id="complimentary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-gift mr-2"></i>Complimentary passes <small class="text-muted ml-1">for special guests – free, emailed to them</small></h3>
        <div class="card-tools">
            <a href="{{ route('cancelled-passes.index', ['kind' => 'Complimentary', 'event' => $event->title]) }}" class="small mr-2"><i class="fas fa-ban"></i> cancelled ones</a>
            <span class="badge badge-success">{{ $issuedPasses }} {{ \Illuminate\Support\Str::plural('pass', $issuedPasses) }} · {{ $complimentary->count() }} {{ \Illuminate\Support\Str::plural('person', $complimentary->count()) }}</span>
        </div>
    </div>
    <div class="card-body">
        @if (! $compOpen)
            <div class="alert alert-light border mb-3">This event is over – pick an upcoming event above to issue complimentary passes.</div>
        @else
            <form action="{{ route('ticketing.comp.store') }}" method="post" id="compForm" autocomplete="off" novalidate>
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <div class="comp-help small mb-2">
                    <strong>One row per person.</strong>
                    Several passes for one person (e.g. a firm): set <strong>Passes</strong> – all their pass numbers go to their email in one message.
                    <strong>Copy to</strong>: also send that email to an assistant or colleague.
                    Many people: <strong>+ Add person</strong>, or <strong>Paste a list</strong> from Excel.
                </div>

                <div class="d-flex flex-wrap align-items-center mb-2">
                    <button type="button" class="btn btn-outline-success btn-sm mr-2 mb-1" id="compAddRow"><i class="fas fa-user-plus mr-1"></i> Add person</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm mr-3 mb-1" id="compPasteToggle" aria-expanded="false" aria-controls="compPaste">
                        <i class="fas fa-paste mr-1"></i> Paste a list
                    </button>
                    @if ($activePasses->isNotEmpty())
                        <div class="form-inline mb-1">
                            <label class="small mr-2 mb-0" for="compAllPass">Pass for every row</label>
                            <select id="compAllPass" class="form-control form-control-sm">
                                <option value="__keep">– choose –</option>
                                <option value="">Complimentary Pass</option>
                                @foreach ($activePasses as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div id="compPaste" class="border rounded p-2 mb-2 bg-light d-none">
                    <label for="compPasteText" class="small mb-1">
                        One person per line – <code>Name, Phone, Email, Passes, Copy to</code> (only Name and Email are needed). Paste straight from Excel; a header row is skipped.
                    </label>
                    <textarea id="compPasteText" rows="4" class="form-control form-control-sm"
                        placeholder="Justice A. Sharma, 9810012345, a.sharma@court.in&#10;Big Law LLP, 9820012345, events@biglaw.in, 5, partner@biglaw.in"></textarea>
                    <div class="d-flex align-items-center mt-2">
                        <button type="button" class="btn btn-sm btn-success mr-2" id="compPasteAdd">Add to the list</button>
                        <span class="small" id="compPasteMsg" role="status" aria-live="polite"></span>
                    </div>
                </div>

                @error('rows')<div class="alert alert-danger py-2 small">{{ $message }}</div>@enderror

                <div class="table-responsive">
                    <table class="table table-sm mb-2 comp-table" id="compRows">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 28px">#</th>
                                <th>Name <span class="text-danger">*</span></th>
                                <th style="width: 150px">Phone <small class="text-muted">(optional)</small></th>
                                <th>Email <span class="text-danger">*</span> <small class="text-muted">passes sent here</small></th>
                                <th style="width: 78px">Passes</th>
                                <th style="width: 170px">Pass</th>
                                <th>Copy to <small class="text-muted">(optional)</small></th>
                                <th style="width: 34px"></th>
                            </tr>
                        </thead>
                        @foreach ($rows as $key => $row)
                            @include('backend.ticketing.complimentary-row', ['key' => $key, 'row' => is_array($row) ? $row : [], 'activePasses' => $activePasses])
                        @endforeach
                    </table>
                </div>
                <template id="compRowTemplate">
                    @include('backend.ticketing.complimentary-row', ['key' => '__KEY__', 'row' => [], 'activePasses' => $activePasses])
                </template>

                <div class="d-flex flex-wrap align-items-center border-top pt-2">
                    <div class="mr-4 mb-2">
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" name="notify" value="0">
                            <input type="checkbox" class="custom-control-input" id="comp_notify" name="notify" value="1" @checked(old('notify', '1') === '1')>
                            <label class="custom-control-label" for="comp_notify">Email the passes <small class="text-muted">(one email per person)</small></label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" name="allow_registered" value="0">
                            <input type="checkbox" class="custom-control-input" id="comp_allow" name="allow_registered" value="1" @checked(old('allow_registered') === '1')>
                            <label class="custom-control-label" for="comp_allow">Allow extra passes for people already registered for this event</label>
                        </div>
                    </div>
                    <div class="ml-auto mb-2 text-right">
                        <div class="small text-muted mb-1" id="compSummary" role="status" aria-live="polite"></div>
                        <button type="submit" class="btn btn-success" id="compIssue"><i class="fas fa-paper-plane mr-1"></i> Issue &amp; email</button>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center">
                    <div class="input-group input-group-sm mb-1 mr-3" style="max-width: 440px">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-vial mr-1"></i> Test email to</span></div>
                        <input type="email" name="test_email" id="comp_test_email" maxlength="100"
                            class="form-control @error('test_email') is-invalid @enderror" value="{{ old('test_email', auth()->user()?->email) }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-primary" id="compTest" formaction="{{ route('ticketing.comp.test') }}" formnovalidate>Send test</button>
                        </div>
                    </div>
                    <small class="text-muted mb-1">Sends you the email for the first row (or a sample) marked TEST – nothing is issued.</small>
                </div>
                <small class="text-muted d-block mt-1">
                    Each pass gets its own pass number at ₹0 – no payment, no invoice. Every row is checked before anything is issued.
                    Up to {{ \App\Support\ComplimentaryPasses::MAX_PER_PERSON }} passes per person, {{ \App\Support\ComplimentaryPasses::MAX_ROWS }} people and {{ \App\Support\ComplimentaryPasses::MAX_PASSES }} passes at a time.
                </small>
            </form>
        @endif

        @if ($complimentary->isNotEmpty())
            <div class="d-flex align-items-center mt-4 mb-2">
                <h6 class="mb-0 mr-auto">Issued for this event</h6>
                <input type="search" id="compSearch" class="form-control form-control-sm" style="max-width: 260px" placeholder="Search name, email, pass no.…" aria-label="Search issued passes">
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" id="compList">
                    <thead class="thead-light">
                        <tr><th>Person</th><th>Phone</th><th>Passes</th><th>Pass</th><th>Issued</th><th>Emailed</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach ($complimentary as $group)
                            @php
                                $lead = $group->firstWhere('attendee_no', 1) ?? $group->first();
                                $leadName = $group->firstWhere('attendee_no', 1)?->name
                                    ?? (str_starts_with($lead->name, 'Guest of ') ? substr($lead->name, 9) : $lead->name);
                                $notSent = $group->whereNull('emailed_at')->count();
                                $lastSent = $group->max('emailed_at');
                            @endphp
                            <tr data-search="{{ strtolower($leadName . ' ' . $lead->email . ' ' . $lead->comp_cc . ' ' . $group->pluck('booking_id')->implode(' ') . ' ' . $group->pluck('name')->implode(' ')) }}">
                                <td>
                                    <strong>{{ $leadName }}</strong>
                                    <div class="small">{{ $lead->email }}</div>
                                    @if ($lead->comp_cc)<div class="small text-muted">copy: {{ $lead->comp_cc }}</div>@endif
                                </td>
                                <td class="text-nowrap small">{{ $lead->phone }}</td>
                                <td>
                                    @foreach ($group as $pass)
                                        <span class="comp-pass" title="{{ $pass->name }}">
                                            <strong>{{ $pass->booking_id }}</strong>
                                            @if ($group->count() > 1)<span class="small text-muted">{{ $pass->name }}</span>@endif
                                            <form action="{{ route('ticketing.comp.destroy', $pass) }}" method="post" class="d-inline"
                                                onsubmit="return confirm('Cancel pass {{ $pass->booking_id }} ({{ addslashes($pass->name) }})? It stays in the Cancelled passes log. Nobody is emailed about this.');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-link btn-sm p-0 text-danger" title="Cancel this pass" aria-label="Cancel pass {{ $pass->booking_id }}"><i class="fas fa-times-circle"></i></button>
                                            </form>
                                        </span>
                                    @endforeach
                                </td>
                                <td class="small">{{ $lead->pass_name ?: 'Complimentary Pass' }}</td>
                                <td class="small text-nowrap">{{ $lead->complimentary_at->format('j M Y, H:i') }}</td>
                                <td class="small text-nowrap">
                                    @if ($notSent === 0 && $lastSent)
                                        <span class="text-success"><i class="fas fa-check"></i> {{ \Illuminate\Support\Carbon::parse($lastSent)->format('j M, H:i') }}</span>
                                    @elseif ($notSent < $group->count())
                                        <span class="text-warning">{{ $notSent }} not sent</span>
                                    @else
                                        <span class="text-muted">not sent</span>
                                    @endif
                                </td>
                                <td class="text-nowrap text-right">
                                    <form action="{{ route('ticketing.comp.resend', $lead) }}" method="post" class="d-inline">
                                        @csrf
                                        <button class="btn btn-outline-primary btn-xs" title="Email {{ $group->count() > 1 ? 'all passes' : 'the pass' }} to {{ $lead->email }}">
                                            <i class="fas fa-redo"></i> {{ $lastSent ? 'Resend' : 'Send' }}
                                        </button>
                                    </form>
                                    @if ($group->count() > 1)
                                        <form action="{{ route('ticketing.comp.destroy-group', $lead) }}" method="post" class="d-inline"
                                            onsubmit="return confirm('Cancel all {{ $group->count() }} passes for {{ addslashes($lead->email) }}? It stays in the Cancelled passes log. Nobody is emailed about this.');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-xs" title="Cancel all {{ $group->count() }} passes">Cancel all</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr id="compNoMatch" class="d-none"><td colspan="7" class="text-center text-muted small py-3">Nobody matches the search.</td></tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
