@extends('adminlte::page')

@section('title', 'Menus')

@section('content_header')
    <h1>Menus</h1>
@stop

@section('content')
    @include('backend.partials.alerts')

    @php $activeTab = session('tab', old('location', 'header')); @endphp

    <p class="text-muted">
        Links can point to a page on this site (e.g. <code>/speakers</code>, <code>/#exhibitors</code>,
        <code>/event/your-event-slug</code>) or a full address (<code>https://...</code>). Use <code>#</code> for a
        header item that only opens a dropdown. Lower order numbers show first. Use the <strong>On site</strong> switch to
        hide a tab without deleting it – it saves straight away (hiding a dropdown also hides its sub-links). The footer title and text are in
        <a href="{{ route('page-content.edit', 'footer') }}">Page Content › Footer</a>.
    </p>

    <ul class="nav nav-tabs" role="tablist">
        @foreach (\App\Support\SiteMenu::LOCATIONS as $location => $label)
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === $location ? 'active' : '' }}" data-toggle="tab" href="#tab-{{ $location }}"
                    role="tab">{{ $label }}</a>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        @foreach ($menus as $location => $items)
            <div class="tab-pane fade {{ $activeTab === $location ? 'show active' : '' }}" id="tab-{{ $location }}" role="tabpanel">
                <div class="card card-primary card-outline" style="border-top-left-radius: 0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 menu-table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 160px">Label</th>
                                        <th style="min-width: 260px">Link</th>
                                        @if ($location === 'header')
                                            <th style="min-width: 150px">Inside dropdown of</th>
                                        @endif
                                        <th style="width: 80px">Order</th>
                                        <th class="text-center">New tab</th>
                                        <th style="width: 120px">On site</th>
                                        <th style="width: 150px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $item)
                                        @include('backend.menus.row', ['item' => $item, 'location' => $location, 'parents' => $items, 'child' => false, 'parentHidden' => false])
                                        @foreach ($item->children as $childItem)
                                            @include('backend.menus.row', ['item' => $childItem, 'location' => $location, 'parents' => $items, 'child' => true, 'parentHidden' => ! $item->is_active])
                                        @endforeach
                                    @empty
                                        <tr><td colspan="7" class="text-muted text-center py-3">No links yet.</td></tr>
                                    @endforelse

                                    {{-- add --}}
                                    <tr class="table-success">
                                        <td>
                                            <input form="add-{{ $location }}" type="text" name="label" class="form-control form-control-sm"
                                                placeholder="New link label" maxlength="60" required>
                                        </td>
                                        <td>
                                            <input form="add-{{ $location }}" type="text" name="url" class="form-control form-control-sm"
                                                placeholder="/page or https://..." maxlength="500">
                                        </td>
                                        @if ($location === 'header')
                                            <td>
                                                <select form="add-{{ $location }}" name="parent_id" class="form-control form-control-sm">
                                                    <option value="">— Top level —</option>
                                                    @foreach ($items as $parent)
                                                        <option value="{{ $parent->id }}">{{ $parent->label }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        @endif
                                        <td>
                                            <input form="add-{{ $location }}" type="number" name="sort_order" min="0" max="9999"
                                                class="form-control form-control-sm" placeholder="auto">
                                        </td>
                                        <td class="text-center align-middle">
                                            <input form="add-{{ $location }}" type="checkbox" name="new_tab" value="1">
                                        </td>
                                        <td class="text-center align-middle">
                                            <input form="add-{{ $location }}" type="checkbox" name="is_active" value="1" checked>
                                        </td>
                                        <td>
                                            <form id="add-{{ $location }}" action="{{ route('menus.store') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="location" value="{{ $location }}">
                                                <button type="submit" class="btn btn-sm btn-success btn-block">+ Add link</button>
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@stop

@section('js')
    @include('backend.partials.visibility-switch-js')
@stop

@section('css')
    <style>
        .menu-table td { vertical-align: middle; }
        .menu-table tr.is-child td:first-child { padding-left: 2rem; }
        .menu-table tr.is-child td:first-child::before { content: "\21B3"; color: #999; margin-left: -1.1rem; margin-right: .3rem; }
        .menu-table tr.is-child td:first-child input { display: inline-block; width: calc(100% - 1rem); }
        .menu-table tr.is-hidden input[type=text] { color: #999; background-color: #f4f4f4; }
    </style>
@stop
