<div id="visibilityStatus" class="alert alert-dark shadow py-2 px-3 mb-0" role="status" aria-live="polite"
    style="position: fixed; right: 1rem; bottom: 1rem; z-index: 1080; display: none;"></div>
<style>
    .visibility-switch .custom-control-label { cursor: pointer; white-space: nowrap; }
    .visibility-switch .vs-state { display: inline-block; min-width: 3.6em; }
    .visibility-switch input:not(:checked) ~ .custom-control-label .vs-state { color: #b02a37; font-weight: 600; }
    [data-visibility-row].is-hidden .vs-dim { opacity: .55; }
</style>
<script>
    (function() {
        var status = document.getElementById('visibilityStatus');
        var timer;

        function say(text, isError) {
            status.textContent = text;
            status.className = 'alert shadow py-2 px-3 mb-0 ' + (isError ? 'alert-danger' : 'alert-dark');
            status.style.display = 'block';
            clearTimeout(timer);
            timer = setTimeout(function() { status.style.display = 'none'; }, isError ? 6000 : 2500);
        }

        document.querySelectorAll('.visibility-switch input[type=checkbox]').forEach(function(box) {
            box.addEventListener('change', function() {
                var form = box.closest('form');
                var visible = box.checked;
                var data = new FormData(form);
                data.set('visible', visible ? 1 : 0);
                box.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    body: data,
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                }).then(function(response) {
                    if (!response.ok) { throw new Error(response.status); }
                    return response.json();
                }).then(function(result) {
                    box.checked = result.visible;
                    form.querySelector('input[name=visible]').value = result.visible ? 0 : 1;
                    form.querySelector('.vs-state').textContent = result.visible ? 'Shown' : 'Hidden';
                    var row = form.closest('[data-visibility-row]');
                    if (row) { row.classList.toggle('is-hidden', !result.visible); }
                    say(result.message, false);
                }).catch(function(error) {
                    box.checked = !visible;
                    say(String(error.message) === '419'
                        ? 'Your session expired – reload the page and try again.'
                        : 'Could not save that change – reload the page and try again.', true);
                }).finally(function() {
                    box.disabled = false;
                });
            });
        });
    })();
</script>
