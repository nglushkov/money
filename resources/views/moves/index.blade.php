@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div x-data="{ selectMode: false, selected: [] }" @keydown.escape.window="selectMode = false; selected = []">

{{-- Bulk delete form (hidden, submitted via Alpine) --}}
<form id="bulk-delete-form" action="{{ route('moves.bulk-delete') }}" method="POST" style="display:none">
    @csrf
    <template x-for="item in selected" :key="item">
        <input type="hidden" name="selected[]" :value="item">
    </template>
</form>

{{-- Toolbar --}}
<div class="page-toolbar">
    <div class="toolbar-left">
        {{-- New operation button --}}
        <div class="btn-group" x-show="!selectMode">
            <a href="{{ route('operations.create') }}" class="btn btn-success btn-sm fw-600" style="font-weight:600;">
                <i class="bi bi-plus-lg me-1"></i>New
            </a>
            <button type="button" class="btn btn-success btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
            <ul class="dropdown-menu shadow-sm">
                <li><h6 class="dropdown-header" style="font-size:.7rem;">Create</h6></li>
                <li><a href="{{ route('operations.create') }}" class="dropdown-item">
                    <i class="bi bi-lightning-charge text-success"></i> Operation
                </a></li>
                <li><a href="{{ route('transfers.create') }}" class="dropdown-item">
                    <i class="bi bi-arrow-left-right text-primary"></i> Transfer
                </a></li>
                <li><a href="{{ route('exchanges.create') }}" class="dropdown-item">
                    <i class="bi bi-currency-exchange text-purple" style="color:var(--c-exchange)"></i> Exchange
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="{{ route('p2p.create') }}" class="dropdown-item">
                    <i class="bi bi-people"></i> P2P exchange
                </a></li>
            </ul>
        </div>

        {{-- Select mode: cancel + delete --}}
        <template x-if="selectMode">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm"
                        @click="selectMode = false; selected = []">
                    Cancel
                </button>
                <button type="button" class="btn btn-danger btn-sm"
                        :disabled="selected.length === 0"
                        @click="if(selected.length && confirm('Delete ' + selected.length + ' item(s)?')) document.getElementById('bulk-delete-form').submit()">
                    <i class="bi bi-trash me-1"></i>Delete
                    <span x-text="selected.length > 0 ? '(' + selected.length + ')' : ''"></span>
                </button>
            </div>
        </template>

        {{-- Filter pills --}}
        @php $noFilter = !$mpOnly && !$draftOnly && !$activeType; @endphp
        <div class="d-flex gap-1 filter-pills-scroll" x-show="!selectMode">
            <a href="{{ route('home') }}"
               class="filter-pill {{ $noFilter ? 'pill-active' : '' }}">All</a>
            <a href="{{ route('home', ['type' => \App\Models\Enum\MoveType::Operation->name]) }}"
               class="filter-pill {{ $activeType === \App\Models\Enum\MoveType::Operation->name ? 'pill-active' : '' }}">Operations</a>
            <a href="{{ route('home', ['type' => \App\Models\Enum\MoveType::Transfer->name]) }}"
               class="filter-pill {{ $activeType === \App\Models\Enum\MoveType::Transfer->name ? 'pill-active' : '' }}">Transfers</a>
            <a href="{{ route('home', ['type' => \App\Models\Enum\MoveType::Exchange->name]) }}"
               class="filter-pill {{ $activeType === \App\Models\Enum\MoveType::Exchange->name ? 'pill-active' : '' }}">Exchanges</a>
            <a href="{{ route('home', ['mp' => 1]) }}"
               class="filter-pill {{ $mpOnly ? 'pill-active-mp' : '' }}">MP</a>
            <a href="{{ route('home', ['draft' => 1]) }}"
               class="filter-pill {{ $draftOnly ? 'pill-active-draft' : '' }}">Drafts</a>
        </div>
    </div>

    <div class="toolbar-right">
        <button type="button" class="btn btn-outline-secondary btn-sm"
                x-show="!selectMode"
                @click="selectMode = true">
            <i class="bi bi-check2-square me-1"></i>Select
        </button>

        <form autocomplete="off" action="{{ route('mp-sync') }}" method="POST" x-show="!selectMode">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-repeat me-1"></i>Sync MP
            </button>
        </form>

        @if (count($plannedExpenses) > 0)
            <button type="button" class="btn btn-sm btn-outline-warning" x-show="!selectMode"
                    onclick="document.getElementById('dismiss-all-form').submit();">
                <i class="bi bi-bell-slash me-1"></i>Dismiss planned
            </button>
            <form autocomplete="off" action="{{ route('planned-expenses.dismiss-all') }}" method="post" id="dismiss-all-form">
                @csrf
            </form>
        @endif
    </div>
</div>

{{-- Success / Error alerts --}}
@if(session('success'))
    <div class="alert alert-success py-2 mb-3">
        <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
    </div>
@endif
@if (Session::has('error'))
    <div class="alert alert-danger py-2 mb-3">
        <i class="bi bi-exclamation-triangle me-1"></i>{{ Session::get('error') }}
    </div>
@endif

{{-- Planned expenses --}}
@foreach($plannedExpenses as $pe)
    <div class="planned-row">
        <i class="bi bi-calendar-event text-warning"></i>
        <a href="{{ route('planned-expenses.show', $pe) }}" class="fw-500">Planned Expense</a>
        <span class="text-muted">{{ $pe->next_payment_date_formatted }}</span>
        <span class="text-muted">({{ $pe->next_payment_date_humans }})</span>
        <strong>{{ $pe->amount_formatted }}</strong>
        @if($pe->category)<span>{{ $pe->category->name }}</span>@endif
        @if($pe->place)<span class="text-muted">@ {{ $pe->place->name }}</span>@endif
        <div class="ms-auto">
            <button type="button" class="btn-close btn-close-sm"
                onclick="document.getElementById('pe-dismiss-{{ $pe->id }}').submit();"></button>
            <form autocomplete="off" action="{{ route('planned-expenses.dismiss', $pe) }}" method="post" id="pe-dismiss-{{ $pe->id }}" class="d-inline">
                @csrf @method('PUT')
                <input type="hidden" name="dismiss" value="{{ $pe->id }}">
            </form>
        </div>
    </div>
@endforeach

{{-- Moves list --}}
@if (count($moves) == 0)
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox" style="font-size:2.5rem;display:block;margin-bottom:.75rem;opacity:.4;"></i>
        No moves found
    </div>
@else
    @foreach($moves as $date => $moveGroup)
        <div class="moves-date-header">{{ $date }}</div>
        <div class="moves-card">
            @include('blocks.moves', ['moves' => $moveGroup])
        </div>
    @endforeach
@endif

<div class="mt-3">
    {{ $paginator->links() }}
</div>

</div>{{-- end x-data --}}

@endsection
