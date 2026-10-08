<script>
    // Bulk bar: count ticked rows, enable the buttons, confirm before acting.
    document.querySelectorAll('.bulk-bar').forEach(function (bar) {
        var formId = bar.id;
        var checks = function () { return document.querySelectorAll('.bulk-check[form="' + formId + '"]'); };
        var ticked = function () { return Array.prototype.filter.call(checks(), function (c) { return c.checked; }).length; };
        var noun = bar.dataset.noun;
        var clicked = null;

        function refresh() {
            var n = ticked();
            bar.querySelector('.bulk-count').textContent = n + ' selected';
            bar.querySelector('.bulk-count').className = 'bulk-count badge ' + (n ? 'badge-primary' : 'badge-secondary');
            bar.querySelectorAll('.bulk-btn').forEach(function (b) { b.disabled = n === 0; });
        }

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('bulk-all') && e.target.dataset.form === formId) {
                checks().forEach(function (c) { c.checked = e.target.checked; });
            }
            if (e.target.classList.contains('bulk-check') || e.target.classList.contains('bulk-all')) { refresh(); }
        });

        bar.querySelectorAll('.bulk-btn').forEach(function (b) {
            b.addEventListener('click', function () { clicked = b.value; });
        });

        bar.addEventListener('submit', function (e) {
            var n = ticked();
            var year = bar.querySelector('[name=edition_id]');
            var label = year.options[year.selectedIndex] ? year.options[year.selectedIndex].text : '';
            var what = n + ' ' + noun + (n === 1 ? '' : 's');
            if ((clicked === 'move' || clicked === 'copy') && !year.value) {
                e.preventDefault();
                alert('Choose the year first.');
                year.focus();
                return;
            }
            var question = clicked === 'delete'
                ? 'Delete ' + what + '? This cannot be undone.'
                : (clicked === 'copy' ? 'Copy ' : 'Move ') + what + ' to ' + label + '?';
            if (!confirm(question)) { e.preventDefault(); }
        });

        refresh();
    });
</script>
