{{-- Tick-to-select plus a guarded "Delete selected" button. --}}
<script>
    function confirmBulkDelete(form, noun) {
        var picked = document.querySelectorAll('input.bulk-pick:checked').length;
        if (!picked) {
            alert('Tick the ' + noun + 's you want to delete first.');
            return false;
        }
        return confirm('Delete ' + picked + ' ' + noun + (picked > 1 ? 's' : '') +
            '? This cannot be undone.');
    }

    (function() {
        var boxes = Array.prototype.slice.call(document.querySelectorAll('input.bulk-pick'));
        var all = document.querySelector('[id^=checkAll]');
        // a list can offer more than one action on the same ticked rows
        var buttons = Array.prototype.slice.call(document.querySelectorAll('[id$=Btn][form]'));
        var count = document.querySelector('[id$=Count]');

        function refresh() {
            var picked = boxes.filter(function(b) { return b.checked; }).length;
            buttons.forEach(function(button) { button.disabled = picked === 0; });
            if (count) {
                count.textContent = picked ? picked + ' selected' : 'Nothing selected';
            }
            if (all) {
                all.checked = picked > 0 && picked === boxes.length;
                all.indeterminate = picked > 0 && picked < boxes.length;
            }
        }

        boxes.forEach(function(box) { box.addEventListener('change', refresh); });
        if (all) {
            all.addEventListener('change', function() {
                boxes.forEach(function(box) { box.checked = all.checked; });
                refresh();
            });
        }
        refresh();
    })();
</script>
