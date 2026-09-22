{{-- Drag (or arrow) the homepage sections into order. Saves on every move. --}}
<style>
    #homeSectionOrder [data-order-row] { cursor: grab; }
    #homeSectionOrder [data-order-row].is-dragging { opacity: .45; }
    #homeSectionOrder [data-order-row].is-over { box-shadow: inset 0 3px 0 0 #007bff; }
    #homeSectionOrder [data-order-row].just-moved { background-color: #eaf3ff; transition: background-color .9s ease-out; }
    #homeSectionOrder .ob-grip { color: #adb5bd; font-size: 15px; letter-spacing: -3px; cursor: grab; user-select: none; }
    #homeSectionOrder [data-order-row]:first-child [data-move="up"],
    #homeSectionOrder [data-order-row]:last-child [data-move="down"] { opacity: .35; pointer-events: none; }
    /* a dragged row must not start a drag from the switch or the Edit button */
    #homeSectionOrder .ob-move, #homeSectionOrder .visibility-switch, #homeSectionOrder .btn-primary { cursor: default; }
</style>
<script>
    (function () {
        var list = document.getElementById('homeSectionOrder');
        if (!list) return;

        var URL = @json($orderUrl);
        var TOKEN = document.querySelector('meta[name=csrf-token]')
            ? document.querySelector('meta[name=csrf-token]').getAttribute('content')
            : (document.querySelector('input[name=_token]') || {}).value;
        var resetBtn = document.getElementById('resetOrder');
        var status = document.getElementById('visibilityStatus');
        var timer;
        var dragged = null;

        function say(text, isError) {
            if (!status) return;
            status.textContent = text;
            status.className = 'alert shadow py-2 px-3 mb-0 ' + (isError ? 'alert-danger' : 'alert-dark');
            status.style.display = 'block';
            clearTimeout(timer);
            timer = setTimeout(function () { status.style.display = 'none'; }, isError ? 6000 : 2200);
        }

        function keys() {
            return Array.prototype.map.call(list.querySelectorAll('[data-order-row]'), function (row) {
                return row.dataset.key;
            });
        }

        function save(list_) {
            var data = new FormData();
            data.append('_token', TOKEN);
            (list_ || keys()).forEach(function (key) { data.append('keys[]', key); });

            return fetch(URL, {
                method: 'POST',
                body: data,
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (response) {
                if (!response.ok) { throw new Error(response.status); }
                return response.json();
            }).then(function (result) {
                if (resetBtn) resetBtn.hidden = result.is_default;
                say(result.message, false);
                return result;
            }).catch(function (error) {
                say(String(error.message) === '419'
                    ? 'Your session expired - reload the page and try again.'
                    : 'Could not save the new order - reload the page and try again.', true);
                throw error;
            });
        }

        function flash(row) {
            row.classList.add('just-moved');
            setTimeout(function () { row.classList.remove('just-moved'); }, 900);
        }

        // --- arrows (also the accessible and touch-friendly path) ---
        list.addEventListener('click', function (event) {
            var button = event.target.closest('[data-move]');
            if (!button) return;

            var row = button.closest('[data-order-row]');
            var sibling = button.dataset.move === 'up'
                ? row.previousElementSibling
                : row.nextElementSibling;
            if (!sibling) return;

            if (button.dataset.move === 'up') {
                list.insertBefore(row, sibling);
            } else {
                list.insertBefore(sibling, row);
            }

            flash(row);
            button.focus();
            save();
        });

        // --- dragging ---
        list.addEventListener('dragstart', function (event) {
            var row = event.target.closest('[data-order-row]');
            if (!row) return;
            dragged = row;
            row.classList.add('is-dragging');
            event.dataTransfer.effectAllowed = 'move';
            // Firefox will not start a drag without something on the clipboard
            try { event.dataTransfer.setData('text/plain', row.dataset.key); } catch (e) { /* ignore */ }
        });

        list.addEventListener('dragover', function (event) {
            if (!dragged) return;
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';

            var over = event.target.closest('[data-order-row]');
            if (!over || over === dragged) return;

            list.querySelectorAll('.is-over').forEach(function (el) { el.classList.remove('is-over'); });
            over.classList.add('is-over');

            var box = over.getBoundingClientRect();
            var below = event.clientY > box.top + box.height / 2;
            list.insertBefore(dragged, below ? over.nextElementSibling : over);
        });

        list.addEventListener('dragend', function () {
            if (!dragged) return;
            dragged.classList.remove('is-dragging');
            list.querySelectorAll('.is-over').forEach(function (el) { el.classList.remove('is-over'); });
            flash(dragged);
            dragged = null;
            save();
        });

        list.addEventListener('drop', function (event) { event.preventDefault(); });

        // --- back to how it shipped ---
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                var data = new FormData();
                data.append('_token', TOKEN);

                fetch(URL, {
                    method: 'POST',
                    body: data,
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                }).then(function (response) {
                    if (!response.ok) { throw new Error(response.status); }
                    return response.json();
                }).then(function (result) {
                    // put the rows back in the order the server just confirmed
                    result.order.forEach(function (key) {
                        var row = list.querySelector('[data-order-row][data-key="' + key + '"]');
                        if (row) list.appendChild(row);
                    });
                    resetBtn.hidden = true;
                    say(result.message, false);
                }).catch(function () {
                    say('Could not reset the order - reload the page and try again.', true);
                });
            });
        }
    })();
</script>
