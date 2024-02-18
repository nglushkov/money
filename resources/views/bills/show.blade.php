@extends('layouts.app')

@section('title', 'Bill Details')

@section('content')
    <div class="card">
        <div class="card-body">
            @error('error')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <h5 class="card-title">Bill Details</h5>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Name:</strong> {{ $bill->name }}</li>
                <li class="list-group-item"><strong>Notes:</strong> {{ $bill->notes }}</li>
                <li class="list-group-item"><strong>User:</strong> {{ $bill->user->name }}</li>
                <li class="list-group-item border-0"><h5>Currencies:</h5></li>
                @foreach ($bill->getAmountNotNull() as $currencyName => $amount)
                    <form id="form-{{ $currencyName }}" action="{{ route('bills.correct', $bill) }}" method="post">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="currency_name" value="{{ $currencyName }}">
                        <input type="hidden" id="amount-{{ $currencyName }}" name="amount" value="0">
                    </form>
                    <li class="list-group-item border-0">
                        <strong>• {{ $currencyName }}</strong>:
                        <span @class(['text-success' => $amount > 0])>{{ App\Helpers\MoneyFormatter::get($amount) }}</span>
                        <button type="submit" class="btn btn-light btn-sm"
                                onclick="event.preventDefault();correctBillAmount('{{ $currencyName }}', {{ $bill->id }})">
                            Correct
                        </button>
                    </li>
                @endforeach
            </ul>
            <div class="card-footer">
                @include('blocks.delete-link', ['model' => $bill, 'routePart' => 'bills'])
            </div>

            <hr>
            @include('blocks.latest-operations', ['operations' => $lastOperations])
        </div>
    </div>

    <script>
        function correctBillAmount(currencyName) {
            let amount = prompt('Enter actual amount for ' + currencyName, '0');
            if (amount !== null) {
                console.log(amount);
                document.getElementById('amount-' + currencyName).value = amount;
                document.getElementById('form-' + currencyName).submit();
            }
        }
    </script>
@endsection
