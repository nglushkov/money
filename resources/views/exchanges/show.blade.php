@extends('layouts.app')

@section('title', 'Exchange Details')

@section('content')
<div class="card">
    <div class="card-body">
    @error('error')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
        <h5 class="card-title">Exchange Details</h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Date:</strong> {{ $exchange->date_formatted }}</li>
            <li class="list-group-item"><strong>From:</strong> {{ $exchange->amount_from_formatted }}</li>
            <li class="list-group-item"><strong>To:</strong> {{ $exchange->amount_to_formatted }}</li>
            <li class="list-group-item"><strong>Rate:</strong> {{ $exchange->rate_formatted . ' (' . ($exchange->rate_text) . ')' }}
                <a href="{{ route('currencies.show', ['currency' => $defaultCurrency->id, 'rate_currency_id' => $exchange->to->id]) }}">Rates</a>
            </li>
            <li class="list-group-item"><strong>Bill:</strong> <a href="{{ route('bills.show', $exchange->bill->id) }}">{{ $exchange->bill->name }}</a></li>
            @if ($exchange->place)
                <li class="list-group-item"><strong>Place:</strong> {{ $exchange->place->name }}</li>
            @endif
            @if ($exchange->notes)
                <li class="list-group-item"><strong>Notes:</strong> {{ $exchange->notes }}</li>
            @endif
            <li class="list-group-item"><strong>User:</strong> {{ $exchange->user->name }}</li>
        </ul>
        <div class="card-footer">
            @include('blocks.delete-link', ['model' => $exchange, 'routePart' => 'exchanges'])
        </div>
    </div>
</div>
@endsection
