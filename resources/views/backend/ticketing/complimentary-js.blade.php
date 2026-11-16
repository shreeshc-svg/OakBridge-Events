<style>
    .comp-help { background: #f4faf6; border-left: 3px solid #28a745; padding: .4rem .6rem; }
    .comp-table { min-width: 1000px; }
    #compList { min-width: 820px; }
    .comp-table td { vertical-align: top; border-top: 0; }
    .comp-table tbody.comp-row { border-top: 1px solid #e9ecef; }
    .comp-table tbody.comp-row.is-new { background: #f4fff7; }
    .comp-pass { white-space: nowrap; display: inline-flex; align-items: center; gap: .25rem; border: 1px solid #dee2e6; border-radius: .25rem; padding: 0 .35rem; margin: 0 .25rem .25rem 0; background: #fff; }
    .btn-xs { padding: .1rem .4rem; font-size: .75rem; }
</style>
<script>
    (function () {
        var form = document.getElementById('compForm');

        // the issued list: search
        var search = document.getElementById('compSearch');
        if (search) {
            search.addEventListener('input', function () {
                var q = search.value.trim().toLowerCase(), shown = 0;
                document.querySelectorAll('#compList tbody tr[data-search]').forEach(function (tr) {
                    var hit = !q || tr.dataset.search.indexOf(q) !== -1;
                    tr.style.display = hit ? '' : 'none';
                    if (hit) { shown++; }
                });
                document.getElementById('compNoMatch').classList.toggle('d-none', shown > 0);
            });
        }
        if (!form) { return; }

        var table = document.getElementById('compRows');
        var template = document.getElementById('compRowTemplate');
        var issueBtn = document.getElementById('compIssue');
        var notify = document.getElementById('comp_notify');
        var MAX = {{ \App\Support\ComplimentaryPasses::MAX_PER_PERSON }};
        var nextKey = 0;
        table.querySelectorAll('tbody.comp-row').forEach(function (b) {
            var k = parseInt(b.dataset.key, 10);
            if (!isNaN(k) && k >= nextKey) { nextKey = k + 1; }
        });

        function rows() { return Array.prototype.slice.call(table.querySelectorAll('tbody.comp-row')); }
        function val(row, cls) { var el = row.querySelector(cls); return el ? el.value.trim() : ''; }
        function isBlank(row) {
            return !val(row, '.f-name') && !val(row, '.f-phone') && !val(row, '.f-email') && !val(row, '.f-cc');
        }
        function qtyOf(row) {
            var q = parseInt(val(row, '.f-qty'), 10);
            return isNaN(q) ? 1 : Math.max(1, Math.min(MAX, q));
        }

        // passes 2+ for one person: optional names. Only the missing boxes are added and the extra
        // ones removed, so what is typed (and where the cursor is) is never lost.
        function guests(row) {
            var qty = qtyOf(row), line = row.querySelector('.guest-line'), box = row.querySelector('.guest-inputs');
            var key = row.dataset.key;
            box.querySelectorAll('.f-guest').forEach(function (i) {
                if (parseInt(i.dataset.n, 10) > qty) { i.remove(); }
            });
            for (var n = 2; n <= qty; n++) {
                if (box.querySelector('.f-guest[data-n="' + n + '"]')) { continue; }
                var input = document.createElement('input');
                input.type = 'text';
                input.name = 'rows[' + key + '][guests][' + n + ']';
                input.dataset.n = n;
                input.maxLength = 100;
                input.className = 'form-control form-control-sm mr-2 mb-1 f-guest';
                input.style.width = '170px';
                input.placeholder = 'Pass ' + n + ' name';
                box.appendChild(input);
            }
            line.classList.toggle('d-none', qty < 2);
            line.querySelector('.qty-text').textContent = qty;
            line.querySelector('.guest-of').textContent = val(row, '.f-name') || 'this person';
        }

        function refresh() {
            var people = 0, passes = 0, emails = {}, dupes = 0;
            rows().forEach(function (row, i) {
                row.querySelector('.row-no').textContent = i + 1;
                if (isBlank(row)) { return; }
                people++;
                passes += qtyOf(row);
                var e = val(row, '.f-email').toLowerCase();
                if (e) { if (emails[e]) { dupes++; } emails[e] = true; }
            });
            var text = people ? people + (people === 1 ? ' person' : ' people') + ' · ' + passes + (passes === 1 ? ' pass' : ' passes') : 'Nobody added yet';
            if (dupes) { text += ' · ⚠ same email twice – use Passes instead'; }
            document.getElementById('compSummary').textContent = text;
            var verb = notify.checked ? ' & email' : '';
            issueBtn.innerHTML = '<i class="fas ' + (notify.checked ? 'fa-paper-plane' : 'fa-ticket-alt') + ' mr-1"></i> Issue '
                + (passes > 1 ? passes + ' passes' : 'pass') + verb;
            issueBtn.disabled = people === 0;
            rows().forEach(function (row) {
                var btn = row.querySelector('.remove-row');
                btn.style.visibility = rows().length > 1 || !isBlank(row) ? 'visible' : 'hidden';
            });
        }

        function addRow(values) {
            var key = 'n' + (nextKey++);
            var holder = document.createElement('table');
            holder.innerHTML = template.innerHTML.replace(/__KEY__/g, key);
            var row = holder.querySelector('tbody.comp-row');
            if (values) {
                row.querySelector('.f-name').value = values.name || '';
                row.querySelector('.f-phone').value = values.phone || '';
                row.querySelector('.f-email').value = values.email || '';
                row.querySelector('.f-qty').value = values.qty || 1;
                row.querySelector('.f-cc').value = values.cc || '';
                var all = document.getElementById('compAllPass');
                if (all && all.value !== '__keep') { row.querySelector('.f-pass').value = all.value; }
            }
            table.appendChild(row);
            guests(row);
            row.classList.add('is-new');
            setTimeout(function () { row.classList.remove('is-new'); }, 1200);
            return row;
        }

        document.getElementById('compAddRow').addEventListener('click', function () {
            var row = addRow();
            refresh();
            row.querySelector('.f-name').focus();
        });

        table.addEventListener('click', function (e) {
            var btn = e.target.closest('.remove-row');
            if (!btn) { return; }
            var row = btn.closest('tbody.comp-row');
            if (rows().length === 1) {
                row.querySelectorAll('input').forEach(function (i) { i.value = i.classList.contains('f-qty') ? 1 : ''; });
                row.querySelector('.f-pass').value = '';
                row.querySelectorAll('.is-invalid').forEach(function (i) { i.classList.remove('is-invalid'); });
                var errs = row.querySelector('.row-errors'); if (errs) { errs.remove(); }
                guests(row);
            } else {
                row.remove();
            }
            refresh();
        });
        table.addEventListener('input', function (e) {
            var row = e.target.closest('tbody.comp-row');
            if (!row) { return; }
            e.target.classList.remove('is-invalid');
            if (e.target.classList.contains('f-qty') || e.target.classList.contains('f-name')) { guests(row); }
            refresh();
        });
        table.addEventListener('change', function (e) {
            if (e.target.classList.contains('f-qty')) {
                var row = e.target.closest('tbody.comp-row');
                e.target.value = qtyOf(row);
                guests(row);
                refresh();
            }
        });

        var allPass = document.getElementById('compAllPass');
        if (allPass) {
            allPass.addEventListener('change', function () {
                if (allPass.value === '__keep') { return; }
                rows().forEach(function (row) { row.querySelector('.f-pass').value = allPass.value; });
            });
        }

        // paste a list: Name, Phone, Email, Passes, Copy to – from Excel (tabs) or typed (commas)
        var pasteBox = document.getElementById('compPaste'), pasteToggle = document.getElementById('compPasteToggle');
        pasteToggle.addEventListener('click', function () {
            var open = pasteBox.classList.toggle('d-none') === false;
            pasteToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open) { document.getElementById('compPasteText').focus(); }
        });
        document.getElementById('compPasteAdd').addEventListener('click', function () {
            var text = document.getElementById('compPasteText'), msg = document.getElementById('compPasteMsg');
            var added = 0, skipped = [];
            text.value.split(/\r?\n/).forEach(function (line, i) {
                if (!line.trim()) { return; }
                var cells = line.split(line.indexOf('\t') !== -1 ? '\t' : ',').map(function (c) { return c.trim().replace(/^["']|["']$/g, ''); });
                var email = '', emailAt = -1;
                cells.some(function (c, j) { if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(c)) { email = c; emailAt = j; return true; } return false; });
                if (!email) {
                    if (!/mail/i.test(line)) { skipped.push(i + 1); } // a header row is skipped quietly
                    return;
                }
                var others = cells.filter(function (c, j) { return j !== emailAt; });
                var phone = '', name = '', qty = 1, cc = [];
                others.forEach(function (c) {
                    var digits = c.replace(/[\s\-().+]/g, '');
                    if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(c)) { cc.push(c); }
                    else if (/^\d{10,15}$/.test(digits) && !phone) { phone = c; }
                    else if (/^\d{1,2}$/.test(c)) { qty = Math.max(1, Math.min(MAX, parseInt(c, 10))); }
                    else if (c && !name) { name = c; }
                });
                // fill the first empty row before adding new ones
                var empty = rows().filter(isBlank)[0];
                var row = empty || addRow();
                row.querySelector('.f-name').value = name;
                row.querySelector('.f-phone').value = phone;
                row.querySelector('.f-email').value = email;
                row.querySelector('.f-qty').value = qty;
                row.querySelector('.f-cc').value = cc.join(', ');
                if (allPass && allPass.value !== '__keep') { row.querySelector('.f-pass').value = allPass.value; }
                guests(row);
                added++;
            });
            refresh();
            msg.className = 'small ' + (skipped.length ? 'text-warning' : 'text-success');
            msg.textContent = added + (added === 1 ? ' person' : ' people') + ' added below – check the rows, then issue.'
                + (skipped.length ? ' Skipped line' + (skipped.length > 1 ? 's ' : ' ') + skipped.join(', ') + ' (no email found).' : '');
            if (added) { text.value = ''; }
        });

        notify.addEventListener('change', refresh);

        form.addEventListener('submit', function (e) {
            if (e.submitter && e.submitter.id === 'compTest') { return; }
            var people = 0, passes = 0;
            rows().forEach(function (row) { if (!isBlank(row)) { people++; passes += qtyOf(row); } });
            if (!people) { e.preventDefault(); return; }
            if (passes > 1 || people > 1) {
                var event = @json($event->title);
                var question = 'Issue ' + passes + ' complimentary passes to ' + people + (people === 1 ? ' person' : ' people')
                    + ' for ' + event + (notify.checked ? ' and email them' : ' (no email)') + '?';
                if (!confirm(question)) { e.preventDefault(); return; }
            }
            issueBtn.disabled = true; // no double passes from a double click
            issueBtn.textContent = 'Issuing…';
        });

        rows().forEach(guests);
        refresh();
        if (form.querySelector('.is-invalid, .alert-danger')) {
            document.getElementById('complimentary').scrollIntoView({ block: 'start' });
        }
    })();
</script>
