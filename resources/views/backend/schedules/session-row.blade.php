<div class="session-row">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <strong class="session-number">Session</strong>
        <div class="text-nowrap">
            <button type="button" class="btn btn-xs btn-outline-secondary" data-action="up" title="Move up">&uarr;</button>
            <button type="button" class="btn btn-xs btn-outline-secondary" data-action="down" title="Move down">&darr;</button>
            <button type="button" class="btn btn-xs btn-outline-secondary" data-action="copy">Duplicate</button>
            <button type="button" class="btn btn-xs btn-outline-danger" data-action="remove">Remove</button>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-6 col-md-2">
            <label class="small mb-0">From</label>
            <input type="text" class="form-control form-control-sm" data-field="from" name="sessions[{{ $i }}][from]"
                value="{{ $session['from'] ?? '' }}" maxlength="20" placeholder="10:00 AM">
        </div>
        <div class="form-group col-6 col-md-2">
            <label class="small mb-0">To</label>
            <input type="text" class="form-control form-control-sm" data-field="to" name="sessions[{{ $i }}][to]"
                value="{{ $session['to'] ?? '' }}" maxlength="20" placeholder="10:45 AM">
        </div>
        <div class="form-group col-md-5">
            <label class="small mb-0">Title</label>
            <input type="text" class="form-control form-control-sm" data-field="title" name="sessions[{{ $i }}][title]"
                value="{{ $session['title'] ?? '' }}" maxlength="255" placeholder="e.g. AI in Dispute Resolution">
        </div>
        <div class="form-group col-md-3">
            <label class="small mb-0">Type</label>
            <input type="text" class="form-control form-control-sm" data-field="subheadline" name="sessions[{{ $i }}][subheadline]"
                value="{{ $session['subheadline'] ?? '' }}" maxlength="100" placeholder="Panel Discussion" list="sessionTypes">
        </div>
        <div class="form-group col-md-7">
            <label class="small mb-0">Speakers <span class="text-muted">– one per line: Name, Designation</span></label>
            <textarea class="form-control form-control-sm" rows="4" data-field="speakers" name="sessions[{{ $i }}][speakers]"
                placeholder="Mr A Kumar, Partner, XYZ Law&#10;Ms B Rao, General Counsel, ABC Ltd">{{ $session['speakers'] ?? '' }}</textarea>
        </div>
        <div class="form-group col-md-5">
            <label class="small mb-0">Notes <span class="text-muted">(optional)</span></label>
            <textarea class="form-control form-control-sm" rows="4" data-field="notes" name="sessions[{{ $i }}][notes]"
                placeholder="e.g. Networking lunch in the foyer">{{ $session['notes'] ?? '' }}</textarea>
        </div>
    </div>
</div>
