@extends('layouts.app')

@section('title', 'Operation')

@section('content')

<div class="detail-card mx-auto" style="max-width: 640px;">

    {{-- Header: amount + actions --}}
    <div class="detail-header">
        <div>
            @if($operation->is_draft)
                <span class="badge-draft mb-2 d-inline-block">Draft</span>
            @endif
            <div class="detail-amount {{ $operation->is_income ? 'col-income' : 'col-expense' }}">
                {{ $operation->amount_text }}
            </div>
            <div class="detail-meta">
                <span class="me-2">
                    <i class="bi {{ $operation->is_income ? 'bi-arrow-up' : 'bi-arrow-down' }} me-1"></i>{{ $operation->type_name }}
                </span>
                · {{ $operation->date_formatted }}
                · {{ $operation->user->name }}
            </div>
        </div>
        <div class="detail-actions">
            <a href="{{ route('operations.edit', $operation) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            @if($operation instanceof App\Models\Interfaces\Copyable)
                <a href="{{ route('operations.copy', $operation) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-copy me-1"></i>Copy
                </a>
            @endif
            <button class="btn btn-outline-danger btn-sm"
                    onclick="if(confirm('Delete this operation?')) document.getElementById('delete-form').submit()">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>

    {{-- Details --}}
    <div class="detail-body">

        @if($operation->amount_in_default_currency_formatted)
        <div class="detail-row">
            <span class="detail-label">In {{ $defaultCurrency->name }}</span>
            <span class="detail-value">{{ $operation->amount_in_default_currency_formatted }}</span>
        </div>
        @endif

        <div class="detail-row">
            <span class="detail-label">Rate</span>
            <span class="detail-value">
                {{ $operation->getCurrencyRate() ? $operation->getCurrencyRate()->rate_human_readable : '—' }}
                @if($defaultCurrency->id !== $operation->currency->id)
                    <a href="{{ route('currencies.show', ['currency' => $defaultCurrency->id, 'rate_currency_id' => $operation->currency->id]) }}" class="ms-1">
                        <i class="bi bi-graph-up-arrow" style="font-size:.8rem;"></i>
                    </a>
                @endif
            </span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Bill</span>
            <span class="detail-value">
                <a href="{{ route('bills.show', $operation->bill->id) }}">{{ $operation->bill->name }}</a>
            </span>
        </div>

        @if($operation->category)
        <div class="detail-row">
            <span class="detail-label">Category</span>
            <span class="detail-value">
                <a href="{{ route('categories.show', $operation->category->id) }}">{{ $operation->category->name }}</a>
            </span>
        </div>
        @endif

        <div class="detail-row">
            <span class="detail-label">Currency</span>
            <span class="detail-value">
                <a href="{{ route('currencies.show', $operation->currency->id) }}">{{ $operation->currency->name }}</a>
            </span>
        </div>

        @if($operation->place)
        <div class="detail-row">
            <span class="detail-label">Place</span>
            <span class="detail-value">
                <a href="{{ route('places.show', $operation->place->id) }}">{{ $operation->place->name }}</a>
            </span>
        </div>
        @endif

        @if($operation->notes)
        <div class="detail-row">
            <span class="detail-label">Notes</span>
            <span class="detail-value">{{ $operation->notes }}</span>
        </div>
        @endif

        @if($operation->attachment)
        <div class="detail-row">
            <span class="detail-label">Attachment</span>
            <span class="detail-value">
                <a href="{{ route('operations.get-attachment', $operation) }}" target="_blank">
                    <i class="bi bi-paperclip me-1"></i>{{ $operation->attachment }}
                </a>
            </span>
        </div>
        @endif

    </div>

</div>

<div class="mt-3">
    <a href="{{ route('home') }}" class="btn btn-link btn-sm text-muted ps-0">
        <i class="bi bi-arrow-left me-1"></i>Back to Home
    </a>
    <a href="{{ route('operations.index') }}" class="btn btn-link btn-sm text-muted">All Operations</a>
    <a href="{{ route('operations.create') }}" class="btn btn-link btn-sm text-muted">New Operation</a>
</div>

<form autocomplete="off" id="delete-form" action="{{ route('operations.destroy', $operation) }}" method="POST" class="d-none">
    @csrf @method('DELETE')
</form>

@endsection
