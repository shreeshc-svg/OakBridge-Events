@extends('adminlte::page')

@section('title', 'Legathon')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>Legathon competitions</h1>
        <div>
            <a href="{{ route('page-content.edit', 'legathon') }}" class="btn btn-outline-secondary btn-sm">Page title</a>
            <a href="{{ route('competitions.create') }}" class="btn btn-primary btn-sm">+ Add competition</a>
        </div>
    </div>
@stop

@section('content')
    @include('backend.partials.alerts')

    <div class="card card-primary card-outline">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px">Order</th>
                            <th style="width: 110px">Image</th>
                            <th>Competition</th>
                            <th style="width: 90px">Status</th>
                            <th style="width: 150px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($competitions as $competition)
                            <tr>
                                <td>{{ $competition->sort_order }}</td>
                                <td>
                                    @if ($competition->image)
                                        <img src="{{ \App\Support\Uploads::url($competition->image) }}" alt="" class="img-thumbnail" style="max-height: 60px">
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $competition->title }}</strong><br>
                                    <small class="text-muted">{{ $competition->subtitle }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $competition->is_active ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $competition->is_active ? 'Shown' : 'Hidden' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('competitions.edit', $competition) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('competitions.destroy', $competition) }}" method="post" class="d-inline"
                                        onsubmit="return confirm('Delete this competition?');">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted text-center py-4">No competitions yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <p class="text-muted small">Shown on <a href="{{ route('legathan') }}" target="_blank">/legathon</a> from the lowest order number to the highest.</p>
@stop
