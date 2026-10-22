{{-- Text above the registration form (Admin > Page Content > Registration page).
     $withTitle: also show the event title and subtitle (default: yes). --}}
@php $regText = \App\Support\PageContent::get('register'); @endphp
<div class="ob-reg-head">
    @if (($withTitle ?? true) && $regText['title'])
        <{{ $titleTag ?? 'p' }} class="ob-reg-head__title">{{ $regText['title'] }}</{{ $titleTag ?? 'p' }}>
        @if ($regText['subtitle'])
            <p class="ob-reg-head__sub">{{ $regText['subtitle'] }}</p>
        @endif
    @endif
    @if ($regText['form_heading'])
        <{{ $tag ?? 'h2' }} class="ob-reg-head__heading" @isset($headingId) id="{{ $headingId }}" @endisset>{{ $regText['form_heading'] }}</{{ $tag ?? 'h2' }}>
    @endif
    @if ($regText['note'])
        <p class="ob-reg-head__note">{!! nl2br(e($regText['note'])) !!}</p>
    @endif
</div>
