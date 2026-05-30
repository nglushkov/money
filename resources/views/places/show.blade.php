@php use App\Helpers\MoneyFormatter; @endphp
@extends('layouts.app')

@section('title', 'Place: ' . $place->name)

@section('content')

    <div style="margin-bottom:.75rem;">
        <a href="{{ route('places.index') }}" style="color:var(--c-muted);font-size:.875rem;text-decoration:none;">
            <i class="bi bi-arrow-left me-1"></i>All places
        </a>
    </div>

    @error('error')
        <div class="alert alert-danger mb-3">{{ $message }}</div>
    @enderror

    <div class="form-card mb-3">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
            <div>
                <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.25rem;">
                    <div class="move-icon" style="background:#f1f5f9;color:var(--c-muted);">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <h2 style="font-size:1.25rem;font-weight:700;margin:0;color:var(--c-text);">{{ $place->name }}</h2>
                </div>
                @if($place->notes)
                    <p style="color:var(--c-muted);margin:.5rem 0 0 0;font-size:.875rem;">{{ $place->notes }}</p>
                @endif
            </div>
            <div style="display:flex;gap:.5rem;flex-shrink:0;">
                <a href="{{ route('places.edit', $place) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <form autocomplete="off" action="{{ route('places.destroy', $place) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm"
                        onclick="return confirm('Delete this place?')">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--c-border);">
            <div style="text-align:center;">
                <div style="font-size:1.5rem;font-weight:700;color:var(--c-text);">{{ $operationsCount }}</div>
                <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;color:var(--c-muted);">Operations</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.1rem;font-weight:700;color:var(--c-text);">
                    {{ MoneyFormatter::getWithCurrencyName($totalSpent, $defaultCurrency->name) }}
                </div>
                <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;color:var(--c-muted);">Total spent</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.1rem;font-weight:700;color:var(--c-text);">
                    {{ $lastOperationDate ? \Carbon\Carbon::parse($lastOperationDate)->format('d.m.Y') : '—' }}
                </div>
                <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;color:var(--c-muted);">Last op</div>
            </div>
        </div>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.4rem;">
        <h6 style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--c-muted);margin:0;">Recent operations</h6>
        <a href="{{ route('operations.index', ['place_id' => $place->id]) }}" style="font-size:.8rem;color:var(--c-accent);text-decoration:none;">
            See all <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    @if($lastOperations->count() > 0)
        <div class="moves-card">
            @foreach($lastOperations as $operation)
                <div class="move-row" onclick="window.location.href='{{ route('operations.show', $operation->id) }}'">
                    <div class="move-icon {{ $operation->type === \App\Models\Enum\OperationType::Income->name ? 'mi-income' : 'mi-expense' }}">
                        <i class="bi {{ $operation->type === \App\Models\Enum\OperationType::Income->name ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                    </div>
                    <div class="move-body">
                        <div class="move-title">{{ $operation->category->name }}</div>
                        <div class="move-subtitle">
                            {{ $operation->bill->name_with_user }}{{ $operation->notes ? ' · ' . $operation->notes : '' }}
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
            Total: {{ MoneyFormatter::getWithCurrencyName($lastOperations->sum('amount_in_default_currency'), $defaultCurrency->name) }}
        </div>
        <div style="margin-top:.5rem;">
            {{ $lastOperations->links() }}
        </div>
    @else
        <div class="moves-card">
            <div class="move-row" style="justify-content:center;color:var(--c-muted);cursor:default;">
                No operations yet
            </div>
        </div>
    @endif

@endsection
