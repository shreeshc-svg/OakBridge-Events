{{-- Turns every <textarea class="rich-editor"> on the page into a CKEditor --}}
<script src="{{ asset('public/ckeditor/ckeditor.js') }}"></script>
<script>
    (function() {
        if (!window.CKEDITOR) { return; }
        CKEDITOR.disableAutoInline = true;
        document.querySelectorAll('textarea.rich-editor').forEach(function(el) {
            CKEDITOR.replace(el, {
                height: el.dataset.height || 320,
                allowedContent: true,
                versionCheck: false,
                removeButtons: 'Save,NewPage,Print,Templates,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Flash,Iframe',
                filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
                filebrowserUploadMethod: 'form'
            });
        });
    })();
</script>
