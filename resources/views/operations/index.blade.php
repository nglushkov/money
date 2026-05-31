@extends('layouts.app')

@section('title', 'Operations')

@section('content')
<div class="container">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
        <h1 style="font-size:1.125rem;font-weight:700;color:var(--c-text);margin:0;">Operations</h1>
        <a href="{{ route('operations.create') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-lg"></i> New
        </a>
    </div>

    @include('operations.filters')

    @if($operations->total() > 0)
        <div style="display:flex;align-items:center;justify-content:space-between;margin:.875rem 0 .5rem;font-size:.8rem;color:var(--c-muted);">
            <span>{{ number_format($operations->total()) }} operations</span>
            <span>{{ $operations->currentPage() > 1 ? 'Page ' . $operations->currentPage() . ' · ' : '' }}{{ $operations->count() }} shown</span>
        </div>
    @endif

    <div class="moves-card">
        @forelse($operations as $operation)
            @php
                $isIncome = $operation->type === \App\Models\Enum\OperationType::Income->name;
            @endphp
            <div class="move-row"
                 @if(!$operation->is_correction)
                     onclick="window.location.href='{{ route('operations.show', $operation->id) }}';"
                     style="cursor:pointer;"
                 @endif
            >
                <div class="move-icon {{ $isIncome ? 'mi-income' : 'mi-expense' }}">
                    <i class="bi {{ $isIncome ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                </div>

                <div class="move-body">
                    <div class="move-title">
                        @if($operation->is_correction)
                            Correction
                        @else
                            {{ $operation->category->name }}
                        @endif
                        @if($operation->mp_review_status === 'pending')
                            <span class="badge bg-warning text-dark" style="font-size:.62rem;padding:.15em .4em;vertical-align:middle;margin-left:.25rem;">Review</span>
                        @endif
                        @if($operation->external_source === 'mercadopago')
                            <span style="font-size:.65rem;font-weight:600;color:#1d4ed8;vertical-align:middle;margin-left:.2rem;">MP</span>
                        @endif
                    </div>
                    <div class="move-subtitle">
                        {{ $operation->date_formatted }}
                        · {{ $operation->bill->name }}
                        @if($operation->place)
                            · {{ $operation->place->name }}
                        @endif
                        @if($operation->notes)
                            · {{ Str::limit($operation->notes, 40) }}
                        @endif
                    </div>
                </div>

                <div class="move-right" onclick="event.stopPropagation()">
                    <div class="move-amount-val {{ $isIncome ? 'col-income' : 'col-expense' }}">
                        {{ $operation->amount_text }}
                    </div>
                    @if($operation->amount_in_default_currency_formatted)
                        <div class="move-amount-sub">{{ $operation->amount_in_default_currency_formatted }}</div>
                    @endif

                    @if($operation->mp_review_status === 'pending')
                        <div class="dropdown" style="margin-top:.35rem;">
                            <button type="button"
                                    class="btn btn-warning dropdown-toggle"
                                    style="font-size:.72rem;padding:.2rem .5rem;line-height:1.4;"
                                    data-bs-toggle="dropdown">
                                Review
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('p2p.create', ['from_operation' => $operation->id]) }}">
                                        <i class="bi bi-arrow-left-right me-1"></i> Create P2P
                                    </a>
                                </li>
                                <li>
                                    <form autocomplete="off" action="{{ route('operations.keep', $operation->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-check-lg me-1"></i> Keep as operation
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endif

                    @if($operation->is_correction)
                        <button type="button"
                                class="btn btn-outline-danger"
                                style="font-size:.72rem;padding:.2rem .45rem;line-height:1.4;margin-top:.35rem;"
                                onclick="if(confirm('Delete this correction?')) document.getElementById('del-op-{{ $operation->id }}').submit()">
                            <i class="bi bi-trash"></i>
                        </button>
                        <form id="del-op-{{ $operation->id }}"
                              action="{{ route('operations.destroy', $operation) }}"
                              method="POST" style="display:none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="move-row" style="justify-content:center;color:var(--c-muted);cursor:default;padding:2rem;">
                No operations found
            </div>
        @endforelse
    </div>

    <div style="margin-top:1rem;">
        {{ $operations->withQueryString()->links() }}
    </div>

</div>
@endsection
