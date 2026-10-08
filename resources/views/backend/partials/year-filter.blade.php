{{--
    Filter bar for the year-wise admin lists.
    Needs: $action (url), $years (Edition collection). Optional: $searchPlaceholder, $extraFilters
    (['name' => ['label' => 'Type', 'options' => ['value' => 'Label']]]), $allowNoYear (bool).
--}}
@php
    $extraFilters = $extraFilters ?? [];
    $currentYear = request('year');
@endphp
<form method="get" action="{{ $action }}" class="card card-body py-2 mb-3">
    <div class="form-row align-items-end">
        <div class="col-md-2 col-6 my-1">
            <label class="small mb-0" for="f_year">Year</label>
            <select name="year" id="f_year" class="form-control form-control-sm" onchange="this.form.submit()">
                <option value="">All years</option>
                @foreach ($years as $y)
                    <option value="{{ $y->id }}" @selected((string) $currentYear === (string) $y->id)>{{ $y->year }}</option>
                @endforeach
                @if ($allowNoYear ?? true)
                    <option value="none" @selected($currentYear === 'none')>No year set</option>
                @endif
            </select>
        </div>
        @foreach ($extraFilters as $name => $filter)
            <div class="col-md-2 col-6 my-1">
                <label class="small mb-0" for="f_{{ $name }}">{{ $filter['label'] }}</label>
                <select name="{{ $name }}" id="f_{{ $name }}" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach ($filter['options'] as $value => $label)
                        <option value="{{ $value }}" @selected((string) request($name) === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endforeach
        <div class="col-md-4 col-12 my-1">
            <label class="small mb-0" for="f_q">Search</label>
            <div class="input-group input-group-sm">
                <input type="search" name="q" id="f_q" value="{{ request('q') }}" class="form-control"
                    placeholder="{{ $searchPlaceholder ?? 'Search…' }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
        @if (request()->hasAny(array_merge(['year', 'q'], array_keys($extraFilters))))
            <div class="col-auto my-1">
                <a href="{{ $action }}" class="btn btn-sm btn-link">Clear filters</a>
            </div>
        @endif
    </div>
</form>
