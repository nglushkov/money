@extends('layouts.app')

@section('title', 'Places')

@section('content')
<div x-data="{ q: '' }">

    <div class="page-toolbar">
        <div class="toolbar-left">
            <div style="position:relative;">
                <i class="bi bi-search" style="position:absolute;left:.6rem;top:50%;transform:translateY(-50%);color:var(--c-muted);font-size:.875rem;pointer-events:none;"></i>
                <input
                    type="text"
                    x-model="q"
                    placeholder="Search places..."
                    class="form-control form-control-sm search-input"
                    style="min-width:200px;"
                    autocomplete="off"
                >
            </div>
        </div>
        <div class="toolbar-right">
            <a href="{{ route('places.create') }}" class="btn btn-success btn-sm" style="font-weight:600;">
                <i class="bi bi-plus-lg me-1"></i>New
            </a>
        </div>
    </div>

    <div style="font-size:.72rem;font-weight:700;color:var(--c-muted);text-transform:uppercase;letter-spacing:.06em;margin:.75rem 0 .4rem .25rem;">
        {{ count($places) }} places
    </div>

    <div class="moves-card">
        @forelse($places as $place)
            <div class="move-row"
                onclick="window.location.href='{{ route('places.show', $place) }}'"
                x-show="!q || '{{ strtolower($place->name . ' ' . ($place->notes ?? '')) }}'.includes(q.toLowerCase())"
            >
                <div class="move-icon" style="background:#f1f5f9;color:var(--c-muted);">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <div class="move-body">
                    <div class="move-title">{{ $place->name }}</div>
                    @if($place->notes)
                        <div class="move-subtitle">{{ $place->notes }}</div>
                    @endif
                </div>
                <div class="move-right" style="display:flex;align-items:center;gap:.5rem;">
                    @if($place->operations_count > 0)
                        <span class="badge rounded-pill" style="background:#dcfce7;color:#166534;font-size:.7rem;font-weight:600;">{{ $place->operations_count }} ops</span>
                    @else
                        <span class="badge rounded-pill" style="background:#f1f5f9;color:var(--c-muted);font-size:.7rem;font-weight:600;">0 ops</span>
                    @endif
                    <i class="bi bi-chevron-right" style="color:var(--c-muted);font-size:.75rem;"></i>
                </div>
            </div>
        @empty
            <div class="move-row" style="justify-content:center;color:var(--c-muted);cursor:default;">
                No places yet
            </div>
        @endforelse
    </div>

</div>
@endsection
