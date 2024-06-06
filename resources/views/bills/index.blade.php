@extends('layouts.app')

@section('title', 'Bills')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="bg-light p-3">
                <a href="{{ route('bills.create') }}" class="btn btn-success">Create</a>
                <a href="{{ route('bills.index', ['user_id' => Auth::id()]) }}" class="btn btn-light">Show My Bills</a>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Bill Name</th>
                        <th>User</th>
                        @foreach ($currencies as $currency)
                            <th class="table-info">{{ $currency->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @foreach ($bills as $bill)
                        <tr onclick="window.location.href = '{{ route('bills.show', $bill->id) }}';" style="cursor: pointer;">
                            <td>{{ $bill->name }}</td>
                            <td>{{ $bill->owner_name }}</td>
                            @foreach ($bill->getAmounts() as $amount)
                                <td>
                                    {{ \App\Helpers\MoneyFormatter::getWithoutDecimals($amount)}}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th></th>
                        @foreach ($currencies as $currency)
                            <th>
                                {{ \App\Helpers\MoneyFormatter::getWithoutDecimals($currency->getSum($bills)) }}
                            </th>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
            @foreach ($cryptoBills as $bill)
            <div class="card">
                <div class="card-body">
                    <h4><a href="{{ route('bills.show', $bill) }}">{{ $bill->name }}</a></h4>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Coin</th>
                                <th>Amount</th>
                                <th>Rate (USDT)</th>
                                <th>Amount (USDT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bill->getCryptoAmounts() as $currencyName => $amount)
                                <tr>
                                    <td>{{ $currencyName }}</td>
                                    <td>{{ \App\Helpers\MoneyFormatter::getWithoutDecimals($amount) }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>&nbsp;
            @endforeach
        </div>
    </div>
</div>
@endsection
