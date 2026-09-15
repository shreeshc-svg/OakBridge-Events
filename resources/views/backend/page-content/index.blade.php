@extends('adminlte::page')

@section('title', 'Page Content')

@section('content_header')
    <h1>Page Content</h1>
@stop

@section('content')
    @include('backend.partials.alerts')

    <p class="text-muted">
        Edit the text on the public pages. Sections you have not edited show the site's original text.
        Other content has its own page:
        <a href="{{ route('hero.edit') }}">Hero Banner</a>,
        <a href="{{ route('strip.edit') }}">Marketing Strip</a>,
        <a href="{{ route('sponsors.index') }}">Sponsors &amp; Exhibitors</a>,
        <a href="{{ route('competitions.index') }}">Legathon</a>,
        <a href="{{ route('menus.index') }}">Menus</a>,
        <a href="{{ route('seo.index') }}">SEO</a>.
    </p>

    <div class="row">
        @foreach ($pages as $page => $sections)
            <div class="col-lg-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ $page }}</h3>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($sections as $key => $def)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="pr-3">
                                    <div class="font-weight-bold">
                                        {{ $def['label'] }}
                                        @if (\App\Support\PageContent::isCustomised($key))
                                            <span class="badge badge-info ml-1">Edited</span>
                                        @endif
                                    </div>
                                    @if (!empty($def['hint']))
                                        <small class="text-muted">{{ $def['hint'] }}</small>
                                    @endif
                                </div>
                                <a href="{{ route('page-content.edit', $key) }}" class="btn btn-sm btn-primary">Edit</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
@stop
