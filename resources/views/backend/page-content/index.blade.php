@extends('adminlte::page')

@section('title', 'Page Content')

@section('content_header')
    <h1>Page Content</h1>
@stop

@section('content')
    @include('backend.partials.alerts')

    <p class="text-muted">
        Edit the text on the public pages, and use the <strong>Shown / Hidden</strong> switches to take a section off the
        site without losing its content (switches save straight away). On the homepage you can also <strong>drag a
        section</strong>, or use the arrows, to change the order it appears in. Sections you have not edited show the
        site's original text. Other content has its own page:
        <a href="{{ route('hero.edit') }}">Hero Banner</a>,
        <a href="{{ route('strip.edit') }}">Marketing Strip</a>,
        <a href="{{ route('promo.edit') }}">Marketing Popup</a>,
        <a href="{{ route('sponsors.index') }}">Sponsors &amp; Exhibitors</a>,
        <a href="{{ route('competitions.index') }}">Legathon</a>,
        <a href="{{ route('seo.index') }}">SEO</a>.
        To hide a tab in the header menu, use the switches in <a href="{{ route('menus.index') }}">Menus</a>.
    </p>

    @php
        // page order: pages with switches first (Homepage, About), then the rest
        $pageNames = array_values(array_unique(array_merge(array_keys($toggles), array_keys($pages))));
        $orderIsDefault = \App\Support\SiteSections::homeOrderIsDefault();
    @endphp
    <div class="row">
        @foreach ($pageNames as $page)
            @php
                $sections = $pages[$page] ?? [];
                $pageToggles = $toggles[$page] ?? [];
                $sortable = $page === 'Homepage';
                $keys = $sortable
                    ? \App\Support\SiteSections::homeOrder()
                    : array_values(array_unique(array_merge(array_keys($pageToggles), array_keys($sections))));
            @endphp
            <div class="col-lg-6">
                <div class="card card-primary card-outline">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">{{ $page }}</h3>
                        @if ($sortable)
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="resetOrder"
                                {{ $orderIsDefault ? 'hidden' : '' }}>
                                Put back in the original order
                            </button>
                        @endif
                    </div>
                    @if ($sortable)
                        <div class="px-3 pt-2">
                            <small class="text-muted">Top to bottom is how the homepage reads. Saves as you move things.</small>
                        </div>
                    @endif
                    <ul class="list-group list-group-flush" @if ($sortable) id="homeSectionOrder" @endif>
                        @foreach ($keys as $key)
                            @php
                                $def = $sections[$key] ?? null;
                                $toggle = $pageToggles[$key] ?? null;
                                $visible = $toggle ? \App\Support\SiteSections::isVisible($key) : true;
                                $hints = array_filter([$def['hint'] ?? null, $toggle['hint'] ?? null]);
                                $editUrl = $def ? route('page-content.edit', $key) : (isset($toggle['route']) ? route($toggle['route']) : null);
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center {{ $visible ? '' : 'is-hidden' }}"
                                data-visibility-row
                                @if ($sortable) data-order-row data-key="{{ $key }}" draggable="true" @endif>
                                <div class="d-flex align-items-center pr-3 vs-dim" style="min-width: 0">
                                    @if ($sortable)
                                        <span class="ob-grip mr-2" aria-hidden="true" title="Drag to move">&#x22EE;&#x22EE;</span>
                                    @endif
                                    <div style="min-width: 0">
                                        <div class="font-weight-bold">
                                            {{ $toggle['label'] ?? $def['label'] }}
                                            @if ($def && \App\Support\PageContent::isCustomised($key))
                                                <span class="badge badge-info ml-1">Edited</span>
                                            @endif
                                        </div>
                                        @foreach ($hints as $hint)
                                            <small class="text-muted d-block">{{ $hint }}</small>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="d-flex align-items-center flex-shrink-0">
                                    @if ($sortable)
                                        <div class="btn-group btn-group-sm mr-2 ob-move">
                                            <button type="button" class="btn btn-outline-secondary" data-move="up"
                                                aria-label="Move {{ $toggle['label'] ?? $def['label'] }} up">&uarr;</button>
                                            <button type="button" class="btn btn-outline-secondary" data-move="down"
                                                aria-label="Move {{ $toggle['label'] ?? $def['label'] }} down">&darr;</button>
                                        </div>
                                    @endif
                                    @if ($toggle)
                                        <div class="mr-3">
                                            @include('backend.partials.visibility-switch', [
                                                'action' => route('page-content.visibility', $key),
                                                'id' => 'vis-' . str_replace('.', '-', $key),
                                                'visible' => $visible,
                                                'name' => $toggle['label'] ?? $def['label'],
                                            ])
                                        </div>
                                    @endif
                                    @if ($editUrl)
                                        <a href="{{ $editUrl }}" class="btn btn-sm btn-primary">Edit</a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
@stop

@section('js')
    @include('backend.partials.visibility-switch-js')
    @include('backend.partials.section-order-js', ['orderUrl' => route('page-content.order')])
@stop
