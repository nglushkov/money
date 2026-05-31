@extends('layouts.app')

@section('title', 'Total by Categories')

@section('content')
@php
    $grandTotalRaw = $totalByCategories->sum('total_raw');
    $hasFilter     = !empty($filterCategoryIds);
    $activeYear    = request('year', date('Y'));
    $activeMonth   = (int) request('month', date('n'));
@endphp

{{-- Toolbar --}}
<div class="page-toolbar mb-3">
    <div class="toolbar-left">
        <span class="report-total-label">Expenses</span>
        <strong class="ms-1 col-expense">{{ $total }}</strong>
        @if($hasFilter)
            <span class="text-muted ms-2" style="font-size:.75rem;">(excl. {{ count($filterCategoryIds) }} {{ count($filterCategoryIds) === 1 ? 'category' : 'categories' }})</span>
        @endif
    </div>
    <div class="toolbar-right">
        <select class="form-select form-select-sm" style="width:auto;" onchange="redirectToBill(this)">
            <option value="">All bills</option>
            @foreach($bills as $bill)
                <option value="{{ $bill->id }}" {{ request('bill_id') == $bill->id ? 'selected' : '' }}>
                    {{ $bill->name_with_user }}
                </option>
            @endforeach
        </select>
        @if($hasFilter)
            <a href="{{ route('reports.total-by-categories', request()->except('filter_category_ids')) }}"
               class="btn btn-sm btn-outline-secondary">Clear filter</a>
        @else
            <a href="{{ route('reports.total-by-categories', array_merge(request()->all(), ['filter_category_ids' => $categoryIds])) }}"
               class="btn btn-sm btn-outline-secondary">Exclude all</a>
        @endif
        <a href="{{ route('reports.total-by-categories', ['year' => date('Y'), 'month' => date('n')]) }}"
           class="btn btn-sm btn-outline-secondary">Current</a>
    </div>
</div>

{{-- Year filter --}}
<div class="d-flex gap-1 filter-pills-scroll mb-2">
    @foreach($years as $year)
        <a href="{{ route('reports.total-by-categories', array_merge(request()->all(), ['year' => $year])) }}"
           class="filter-pill {{ $year == $activeYear ? 'pill-active' : '' }}">{{ $year }}</a>
    @endforeach
</div>

{{-- Month filter --}}
<div class="d-flex gap-1 filter-pills-scroll mb-3">
    @foreach($months as $number => $name)
        <a href="{{ route('reports.total-by-categories', array_merge(request()->all(), ['month' => $number])) }}"
           class="filter-pill {{ $number === $activeMonth ? 'pill-active' : '' }}">{{ $name }}</a>
    @endforeach
</div>

{{-- Category list --}}
<div class="moves-card">
    @forelse($totalByCategories as $item)
        @php
            $isFiltered = in_array($item['categoryId'], $filterCategoryIds);
            $pct        = $grandTotalRaw > 0 ? round($item['total_raw'] / $grandTotalRaw * 100, 1) : 0;
            $newFilter  = $isFiltered
                ? array_values(array_diff($filterCategoryIds, [$item['categoryId']]))
                : array_merge($filterCategoryIds, [$item['categoryId']]);
        @endphp
        <div class="category-row {{ $isFiltered ? 'category-row--excluded' : '' }}">
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-icon-sm {{ $isFiltered ? 'btn-outline-danger' : 'btn-outline-secondary' }} flex-shrink-0"
                   href="{{ route('reports.total-by-categories', array_merge(request()->all(), ['filter_category_ids' => $newFilter])) }}"
                   title="{{ $isFiltered ? 'Include back' : 'Exclude from total' }}"
                   onclick="event.stopPropagation()">
                    <i class="bi {{ $isFiltered ? 'bi-plus' : 'bi-dash' }}"></i>
                </a>

                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-baseline justify-content-between gap-2">
                        <a class="category-row__name text-decoration-none text-body"
                           href="{{ route('operations.index', [
                               'category_id' => $item['categoryId'],
                               'show_filter' => 1,
                               'bill_id'     => request('bill_id', ''),
                               'date_from'   => \Carbon\Carbon::create($activeYear, $activeMonth, 1)->startOfMonth()->format('Y-m-d'),
                               'date_to'     => \Carbon\Carbon::create($activeYear, $activeMonth, 1)->endOfMonth()->format('Y-m-d'),
                           ]) }}">
                            {{ $item['categoryName'] }}
                        </a>
                        <div class="text-end flex-shrink-0">
                            <span class="col-expense fw-semibold" style="font-size:.875rem;">{{ $item['total'] }}</span>
                            <span class="text-muted ms-1" style="font-size:.75rem;">{{ $pct }}%</span>
                        </div>
                    </div>
                    <div class="progress mt-1" style="height:3px;border-radius:2px;">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:var(--c-expense);"></div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted text-center py-4 mb-0">No expenses for this period.</p>
    @endforelse
</div>

<script>
    function redirectToBill(el) {
        const url = new URL(window.location.href);
        el.value ? url.searchParams.set('bill_id', el.value) : url.searchParams.delete('bill_id');
        window.location.href = url.toString();
    }
</script>

<style>
    .report-total-label {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--c-muted);
    }
    .category-row {
        padding: .65rem 1rem;
        border-bottom: 1px solid var(--c-border);
        background: var(--c-surface);
        transition: background .1s;
    }
    .category-row:last-child { border-bottom: none; }
    .category-row:hover      { background: #f8fafc; }
    .category-row--excluded  { opacity: .4; }
    .category-row__name {
        font-size: .875rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
    }
    .btn-icon-sm {
        width: 1.75rem;
        height: 1.75rem;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .8rem;
        border-radius: var(--radius-sm);
    }
    .min-w-0 { min-width: 0; }
</style>
@endsection
