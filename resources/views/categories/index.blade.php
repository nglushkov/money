@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div x-data="{ q: '' }">

    <div class="page-toolbar">
        <div class="toolbar-left">
            <div style="position:relative;">
                <i class="bi bi-search" style="position:absolute;left:.6rem;top:50%;transform:translateY(-50%);color:var(--c-muted);font-size:.875rem;pointer-events:none;"></i>
                <input
                    type="text"
                    x-model="q"
                    placeholder="Search categories..."
                    class="form-control form-control-sm search-input"
                    style="min-width:200px;"
                    autocomplete="off"
                >
            </div>
        </div>
        <div class="toolbar-right">
            <a href="{{ route('categories.create') }}" class="btn btn-success btn-sm" style="font-weight:600;">
                <i class="bi bi-plus-lg me-1"></i>New
            </a>
        </div>
    </div>

    <div style="font-size:.72rem;font-weight:700;color:var(--c-muted);text-transform:uppercase;letter-spacing:.06em;margin:.75rem 0 .4rem .25rem;">
        {{ count($categories) }} categories
    </div>

    <div class="moves-card">
        @forelse($categories as $category)
            <div class="move-row"
                onclick="window.location.href='{{ route('categories.show', $category) }}'"
                x-show="!q || '{{ strtolower($category->name . ' ' . ($category->notes ?? '')) }}'.includes(q.toLowerCase())"
            >
                <div class="move-icon" style="background:#f1f5f9;color:var(--c-muted);">
                    <i class="bi bi-tag"></i>
                </div>
                <div class="move-body">
                    <div class="move-title">{{ $category->name }}</div>
                    @if($category->notes)
                        <div class="move-subtitle">{{ $category->notes }}</div>
                    @endif
                </div>
                <div class="move-right" style="display:flex;align-items:center;gap:.5rem;">
                    @if($category->operations_count > 0)
                        <span class="badge rounded-pill" style="background:#dcfce7;color:#166534;font-size:.7rem;font-weight:600;">{{ $category->operations_count }} ops</span>
                    @else
                        <span class="badge rounded-pill" style="background:#f1f5f9;color:var(--c-muted);font-size:.7rem;font-weight:600;">0 ops</span>
                    @endif
                    <i class="bi bi-chevron-right" style="color:var(--c-muted);font-size:.75rem;"></i>
                </div>
            </div>
        @empty
            <div class="move-row" style="justify-content:center;color:var(--c-muted);cursor:default;">
                No categories yet
            </div>
        @endforelse
    </div>

</div>
@endsection
