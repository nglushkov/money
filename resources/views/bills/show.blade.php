@php use App\Helpers\MoneyFormatter; @endphp
@extends('layouts.app')

@section('title', $bill->name)

@section('content')

<div style="margin-bottom:.75rem;">
    <a href="{{ route('bills.index') }}" style="color:var(--c-muted);font-size:.875rem;text-decoration:none;">
        <i class="bi bi-arrow-left me-1"></i>All bills
    </a>
</div>

@error('error')
    <div class="alert alert-danger mb-3">{{ $message }}</div>
@enderror
@if ($errors->any())
    <div class="alert alert-danger mb-3">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

{{-- Header card --}}
<div class="form-card mb-3">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
        <div>
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.25rem;">
                <div class="move-icon" style="background:#f1f5f9;color:var(--c-muted);">
                    <i class="bi {{ $bill->is_crypto ? 'bi-currency-bitcoin' : 'bi-credit-card' }}"></i>
                </div>
                <h2 style="font-size:1.25rem;font-weight:700;margin:0;color:var(--c-text);">{{ $bill->name }}</h2>
                @if($bill->is_crypto)
                    <span class="badge bg-secondary" style="font-size:.65rem;">Crypto</span>
                @endif
            </div>
            <div style="font-size:.8rem;color:var(--c-muted);">
                <i class="bi bi-person me-1"></i>{{ $bill->owner_name }}
                @if($bill->notes)
                    · {{ $bill->notes }}
                @endif
            </div>
        </div>
        <div style="display:flex;gap:.5rem;flex-shrink:0;">
            <a href="{{ route('bills.edit', $bill) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <form autocomplete="off" action="{{ route('bills.destroy', $bill) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm"
                    onclick="return confirm('Delete this bill?')">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Balances --}}
    @php $amounts = $bill->getAmountsNotNull(); @endphp
    @if(count($amounts) > 0)
        <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--c-border);">
            <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--c-muted);margin-bottom:.6rem;">Balance</div>
            @foreach($amounts as $amount)
                <div x-data="{ editing: false, val: '{{ $amount->getAmount() }}' }"
                     style="display:flex;align-items:center;gap:.75rem;padding:.4rem 0;border-bottom:1px solid var(--c-border);">
                    <span style="font-size:.8rem;font-weight:600;color:var(--c-muted);width:3rem;">{{ $amount->getCurrency()->name }}</span>

                    <span x-show="!editing"
                          style="font-size:1rem;font-weight:700;color:{{ $amount->getAmount() > 0 ? 'var(--c-accent)' : 'var(--c-muted)' }};">
                        {{ MoneyFormatter::getWithoutTrailingZeros($amount->getAmount()) }}
                    </span>

                    <form x-show="editing" x-ref="correctForm"
                          autocomplete="off"
                          action="{{ route('bills.correct', $bill) }}" method="POST"
                          style="display:flex;align-items:center;gap:.4rem;">
                        @csrf @method('PUT')
                        <input type="hidden" name="currency_name" value="{{ $amount->getCurrency()->name }}">
                        <input type="number" name="amount" x-model="val" step="any"
                               style="width:9rem;font-size:.9rem;padding:.25rem .5rem;border:1px solid var(--c-border);border-radius:var(--radius-sm);"
                               @keydown.escape="editing=false"
                               @keydown.enter.prevent="$refs.correctForm.submit()">
                        <button type="submit" class="btn btn-success btn-sm" style="padding:.2rem .6rem;">Save</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" style="padding:.2rem .6rem;" @click="editing=false">Cancel</button>
                    </form>

                    <button x-show="!editing" type="button"
                            class="btn btn-outline-secondary btn-sm" style="margin-left:auto;padding:.2rem .6rem;font-size:.75rem;"
                            @click="editing=true; $nextTick(() => $refs.correctForm.querySelector('input[name=amount]').focus())">
                        <i class="bi bi-pencil me-1"></i>Correct
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Currency filter pills --}}
<div class="mb-3" style="background:var(--c-surface);border-radius:var(--radius-sm);padding:.5rem .75rem;box-shadow:var(--shadow-sm);display:flex;gap:.25rem;overflow-x:auto;flex-wrap:nowrap;scrollbar-width:none;">
    <a href="{{ route('bills.show', $bill) }}"
       class="filter-pill {{ !request('currency_id') ? 'pill-active' : '' }}" style="white-space:nowrap;">All</a>
    @foreach ($currencies as $currency)
        <a href="{{ route('bills.show', ['bill' => $bill, 'currency_id' => $currency->id]) }}"
           class="filter-pill {{ $currency->id == request('currency_id') ? 'pill-active' : '' }}" style="white-space:nowrap;">
            {{ $currency->name }}
        </a>
    @endforeach
</div>

@if(count($moves) > 0)
    @foreach($moves as $date => $moveGroup)
        <div class="moves-date-header">{{ $date }}</div>
        <div class="moves-card">
            @include('blocks.moves', ['moves' => $moveGroup])
        </div>
    @endforeach
    <div class="mt-3">{{ $paginator->links() }}</div>
@else
    <div class="moves-card">
        <div class="move-row" style="justify-content:center;color:var(--c-muted);cursor:default;">
            No activity yet
        </div>
    </div>
@endif

@endsection
