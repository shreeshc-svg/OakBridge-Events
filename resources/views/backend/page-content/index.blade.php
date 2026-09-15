@extends('adminlte::page')

@section('title', 'Page Content')

@section('content_header')
    <h1>Page Content</h1>
@stop

@section('content')
    @include('backend.partials.alerts')

    <p class="text-muted">
        Edit the text on the public pages, and use the <strong>Shown / Hidden</strong> switches to take a section off the
        site without losing its content (switches save straight away). Sections you have not edited show the site's
        original text. Other content has its own page:
        <a href="{{ route('hero.edit') }}">Hero Banner</a>,
        <a href="{{ route('strip.edit') }}">Marketing Strip</a>,
        <a href="{{ route('sponsors.index') }}">Sponsors &amp; Exhibitors</a>,
        <a href="{{ route('competitions.index') }}">Legathon</a>,
        <a href="{{ route('seo.index') }}">SEO</a>.
        To hide a tab in the header menu, use the switches in <a href="{{ route('menus.index') }}">Menus</a>.
    </p>

    @php
        // page order: pages with switches first (Homepage, About), then the rest
        $pageNames = array_values(array_unique(array_merge(array_keys($toggles), array_keys($pages))));
    @endphp
    <div class="row">
        @foreach ($pageNames as $page)
            @php
                $sections = $pages[$page] ?? [];
                $pageToggles = $toggles[$page] ?? [];
                $keys = array_values(array_unique(array_merge(array_keys($pageToggles), array_keys($sections))));
            @endphp
            <div class="col-lg-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ $page }}</h3>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($keys as $key)
                            @php
                                $def = $sections[$key] ?? null;
                                $toggle = $pageToggles[$key] ?? null;
                                $visible = $toggle ? \App\Support\SiteSections::isVisible($key) : true;
                                $hints = array_filter([$def['hint'] ?? null, $toggle['hint'] ?? null]);
                                $editUrl = $def ? route('page-content.edit', $key) : (isset($toggle['route']) ? route($toggle['route']) : null);
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center {{ $visible ? '' : 'is-hidden' }}"
                                data-visibility-row>
                                <div class="pr-3 vs-dim">
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
                                <div class="d-flex align-items-center flex-shrink-0">
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
@stop
