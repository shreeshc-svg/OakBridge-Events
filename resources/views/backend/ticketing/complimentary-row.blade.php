{{--
    One person in the complimentary passes form. Needs: $key (rows[key]), $row (old input), $activePasses.
    Rendered once per row and once as the <template> for "+ Add person" (key "__KEY__").
--}}
@php
    $qty = max(1, min(\App\Support\ComplimentaryPasses::MAX_PER_PERSON, (int) ($row['qty'] ?? 1)));
    $guests = is_array($row['guests'] ?? null) ? $row['guests'] : [];
    $err = fn ($field) => $key === '__KEY__' ? null : $errors->first("rows.{$key}.{$field}");
@endphp
<tbody class="comp-row" data-key="{{ $key }}">
    <tr>
        <td class="text-muted small pt-3 row-no"></td>
        <td>
            <input type="text" name="rows[{{ $key }}][name]" class="form-control form-control-sm f-name {{ $err('name') ? 'is-invalid' : '' }}"
                maxlength="100" value="{{ $row['name'] ?? '' }}" placeholder="Full name" aria-label="Name">
        </td>
        <td>
            <input type="tel" name="rows[{{ $key }}][phone]" class="form-control form-control-sm f-phone {{ $err('phone') ? 'is-invalid' : '' }}"
                maxlength="25" inputmode="tel" value="{{ $row['phone'] ?? '' }}" placeholder="98100 12345" aria-label="Phone">
        </td>
        <td>
            <input type="email" name="rows[{{ $key }}][email]" class="form-control form-control-sm f-email {{ $err('email') ? 'is-invalid' : '' }}"
                maxlength="100" value="{{ $row['email'] ?? '' }}" placeholder="guest@example.com" aria-label="Email">
        </td>
        <td>
            <input type="number" name="rows[{{ $key }}][qty]" class="form-control form-control-sm f-qty text-center {{ $err('qty') ? 'is-invalid' : '' }}"
                min="1" max="{{ \App\Support\ComplimentaryPasses::MAX_PER_PERSON }}" step="1" value="{{ $row['qty'] ?? 1 }}" aria-label="Number of passes">
        </td>
        <td>
            <select name="rows[{{ $key }}][pass_type_id]" class="form-control form-control-sm f-pass {{ $err('pass_type_id') ? 'is-invalid' : '' }}" aria-label="Pass">
                <option value="">Complimentary Pass</option>
                @foreach ($activePasses as $p)
                    <option value="{{ $p->id }}" @selected((string) ($row['pass_type_id'] ?? '') === (string) $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" name="rows[{{ $key }}][cc]" class="form-control form-control-sm f-cc {{ $err('cc') ? 'is-invalid' : '' }}"
                maxlength="400" value="{{ $row['cc'] ?? '' }}" placeholder="assistant@firm.com" aria-label="Copy the email to">
        </td>
        <td class="text-right">
            <button type="button" class="btn btn-link btn-sm text-danger p-1 remove-row" title="Remove this row" aria-label="Remove this row"><i class="fas fa-times"></i></button>
        </td>
    </tr>
    <tr class="guest-line {{ $qty > 1 ? '' : 'd-none' }}">
        <td></td>
        <td colspan="7" class="pt-0">
            <div class="small text-muted mb-1">
                <i class="fas fa-users mr-1"></i> All <span class="qty-text">{{ $qty }}</span> passes go to this person's email. Names on passes 2+ (optional – left empty they read "Guest of <span class="guest-of">{{ ($row['name'] ?? '') ?: 'this person' }}</span>"):
            </div>
            <div class="d-flex flex-wrap guest-inputs">
                @for ($n = 2; $n <= $qty; $n++)
                    <input type="text" name="rows[{{ $key }}][guests][{{ $n }}]" data-n="{{ $n }}" class="form-control form-control-sm mr-2 mb-1 f-guest"
                        maxlength="100" value="{{ $guests[$n] ?? '' }}" placeholder="Pass {{ $n }} name" style="width: 170px">
                @endfor
            </div>
        </td>
    </tr>
    @php $rowErrors = $key === '__KEY__' ? [] : collect(['name', 'phone', 'email', 'qty', 'pass_type_id', 'cc', 'guests'])->map($err)->filter(); @endphp
    @if (count($rowErrors))
        <tr class="row-errors">
            <td></td>
            <td colspan="7" class="pt-0 text-danger small">
                @foreach ($rowErrors as $message)<div>{{ $message }}</div>@endforeach
            </td>
        </tr>
    @endif
</tbody>
