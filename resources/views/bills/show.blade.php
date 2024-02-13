@extends('layouts.app')

@section('title', 'Bill Details')

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
                    @foreach ($bill->getAmountNotNull() as $currencyName => $amount)
                        <li><strong>{{ $currencyName }}</strong>: <span @class(['text-success' => $amount > 0])>{{ App\Helpers\MoneyFormatter::get($amount) }}</span></li>
                    @endforeach
                </ul>
            </li>
        </ul>
        <div class="card-footer">
            @include('blocks.delete-link', ['model' => $bill, 'routePart' => 'bills'])
        </div>
        @if ($lastOperations->count() > 0)
        <hr>
        <h5>Last Operations <small><a href="{{ route('operations.index') }}">all</a></small></h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Category</th>
                        <th scope="col">Place</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lastOperations as $operation)
                    <tr onclick="window.location.href = '{{ route('operations.show', $operation->id) }}';" style="cursor: pointer;">
                        <td>{{ $operation->date_formatted }}</td>
                        <td>{{ $operation->amount_text }}</td>
                        <td>{{ $operation->category->name }}</td>
                        <td>{{ $operation->place->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection