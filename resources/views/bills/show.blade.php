@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
    @error('error')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
        <h5 class="card-title">Bill Details</h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Name:</strong> {{ $bill->name }}</li>
            <li class="list-group-item"><strong>Notes:</strong> {{ $bill->notes }}</li>
            <li class="list-group-item"><strong>User:</strong> {{ $bill->user->name }}</li>
            <li class="list-group-item">Currencies:
                <ul>
                    @foreach ($bill->currencies as $currency)
                        <li><strong>{{ $currency->name }}</strong>: {{ App\Helpers\MoneyFormatter::get($currency->pivot->amount, $currency->name) }}</li>
                    @endforeach
                </ul>
            </li>
        </ul>
    </div>
    <div class="card-footer">
        @include('blocks.delete-link', ['model' => $bill, 'routePart' => 'bills'])
    </div>
</div>
@endsection