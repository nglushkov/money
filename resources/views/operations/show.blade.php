@extends('layouts.app')

@section('title', 'Operation Details')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Operation Details</h5>
        <ul class="list-group list-group-flush">
            @if($operation->is_draft)
                <li class="list-group-item"><span class="badge bg-warning">Draft</span></li>
            @endif
            <li class="list-group-item"><strong>Amount:</strong> <span @class([ 'text-danger'=> $operation->is_expense,
                    'text-success' => $operation->is_income,
                    ])>{{ $operation->amount_text }}</span></li>
            <li class="list-group-item"><strong>Amount in {{ $defaultCurrency->name }}:</strong>
                {{ $operation->amount_in_default_currency_formatted }}
            </li>
            <li class="list-group-item">
                <strong>Currency Rate:</strong> {{ $operation->getCurrencyRate() ? $operation->getCurrencyRate()->rate_human_readable : 'N/A' }}
                <a href="{{ route('currencies.show', $defaultCurrency->id) }}">Rates</a>
            </li>
            <li class="list-group-item"><strong>Type:</strong> {{ $operation->type_name }}</li>
            <li class="list-group-item"><strong>Bill:</strong> <a href="{{ route('bills.show', $operation->bill->id) }}">{{ $operation->bill->name }}</a></li>
            @if($operation->category)
                <li class="list-group-item"><strong>Category:</strong> <a href="{{ route('categories.show', $operation->category->id) }}">{{ $operation->category->name }}</a></li>
            @endif
            <li class="list-group-item"><strong>Currency:</strong> <a href="{{ route('currencies.show', $operation->currency->id) }}">{{ $operation->currency->name }}</a></li>
            @if($operation->place)
                <li class="list-group-item"><strong>Place:</strong> <a href="{{ route('places.show', $operation->place->id) }}">{{ $operation->place->name }}</a></li>
            @endif
            <li class="list-group-item"><strong>User:</strong> {{ $operation->user->name }}</li>
            <li class="list-group-item"><strong>Notes:</strong> {{ $operation->notes }}</li>
            <li class="list-group-item"><strong>Date:</strong> {{ $operation->date_formatted }}</li>
        </ul>
    </div>
    <div class="card-footer">
        @include('blocks.delete-link', ['model' => $operation, 'routePart' => 'operations'])
    </div>
</div>
@endsection
