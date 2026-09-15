{{-- On/off switch that saves straight away. Needs: $action, $id, $visible, $name. Script: backend.partials.visibility-switch-js --}}
<form action="{{ $action }}" method="post" class="visibility-switch mb-0">
    @csrf
    <input type="hidden" name="visible" value="{{ $visible ? 0 : 1 }}">
    <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="{{ $id }}" @checked($visible)>
        <label class="custom-control-label" for="{{ $id }}">
            <span class="vs-state">{{ $visible ? 'Shown' : 'Hidden' }}</span><span class="sr-only"> – {{ $name }}</span>
        </label>
    </div>
    <noscript><button type="submit" class="btn btn-xs btn-outline-secondary">{{ $visible ? 'Hide' : 'Show' }}</button></noscript>
</form>
