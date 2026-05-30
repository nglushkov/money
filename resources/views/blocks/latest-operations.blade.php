@php use App\Helpers\MoneyFormatter; @endphp

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.4rem;">
    <h6 style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--c-muted);margin:0;">Recent operations</h6>
    <a href="{{ route('operations.index', $routeParameters) }}" style="font-size:.8rem;color:var(--c-accent);text-decoration:none;">
        See all <i class="bi bi-arrow-right"></i>
    </a>
</div>

@if($operations->count() > 0)
    <div class="moves-card">
        @foreach($operations as $operation)
            <div class="move-row" onclick="window.location.href='{{ route('operations.show', $operation->id) }}'">
                <div class="move-icon {{ $operation->type === \App\Models\Enum\OperationType::Income->name ? 'mi-income' : 'mi-expense' }}">
                    <i class="bi {{ $operation->type === \App\Models\Enum\OperationType::Income->name ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                </div>
                <div class="move-body">
                    <div class="move-title">{{ $operation->category->name }}</div>
                    <div class="move-subtitle">
                        {{ $operation->bill->name_with_user }}
                        @if($operation->place)
                            · {{ $operation->place->name }}
                        @endif
                        @if($operation->notes)
                            · {{ $operation->notes }}
                        @endif
                    </div>
                </div>
                <div class="move-right">
                    <div class="move-amount-val {{ $operation->type === \App\Models\Enum\OperationType::Income->name ? 'col-income' : 'col-expense' }}">
                        {{ $operation->amount_text }}
                    </div>
                    <div class="move-amount-sub">{{ $operation->amount_in_default_currency_formatted }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top:.75rem;font-size:.875rem;color:var(--c-muted);">
        Total: {{ MoneyFormatter::getWithCurrencyName($operations->sum('amount_in_default_currency'), \App\Models\Currency::getDefaultCurrencyName()) }}
    </div>
    <div style="margin-top:.5rem;">
        {{ $operations->links() }}
    </div>
@else
    <div class="moves-card">
        <div class="move-row" style="justify-content:center;color:var(--c-muted);cursor:default;">
            No operations yet
        </div>
    </div>
@endif
