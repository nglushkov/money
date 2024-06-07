@php use App\Helpers\MoneyFormatter;use App\Helpers\MoneyHelper; @endphp
@extends('layouts.app')

@section('title', 'Bills')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="bg-light p-3">
                    <a href="{{ route('bills.create') }}" class="btn btn-success">Create</a>
                    <a href="{{ route('bills.index', ['user_id' => Auth::id()]) }}" class="btn btn-light">Show My
                        Bills</a>
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
                        <tr onclick="window.location.href = '{{ route('bills.show', $bill->id) }}';"
                            style="cursor: pointer;">
                            <td>{{ $bill->name }}</td>
                            <td>{{ $bill->owner_name }}</td>
                            @foreach ($bill->getAmounts() as $amount)
                                <td>
                                    {{ MoneyFormatter::getWithoutDecimals($amount->getAmount())}}
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
                                {{ MoneyFormatter::getWithoutDecimals($currency->getSum($bills)) }}
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
                                    <th>Rate</th>
                                    <th>Amount</th>
                                    <th>Total invested</th>
                                    <th>Income</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($bill->getAmountsNotNull() as $amount)
                                    <tr onclick="window.location.href = '{{ route('exchanges.index', ['currency_id' => $amount->getCurrency()->id]) }}';"
                                        style="cursor: pointer;">
                                        <td>{{ $amount->getCurrency()->name }}</a></td>
                                        <td>{{ MoneyFormatter::getWithoutTrailingZeros($amount->getAmount()) }}</td>
                                        <td>1 {{ $amount->getCurrency()->name }} = {{ MoneyFormatter::getWithoutTrailingZeros($amount->getCurrency()->getCurrentInvertedRateAsString()) }} {{ App\Models\Currency::getDefaultCurrencyName(true) }}</td>
                                        <td>{{ MoneyFormatter::getWithCurrencyName($amount->getCurrency()->getAmountByInvertedRate($bill), App\Models\Currency::getDefaultCurrencyName(true)) }}</td>
                                        <td>todo</td>
                                        <td>todo</td>                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="3"></th>
                                    <th>{{ MoneyFormatter::get($amount->getCurrency()->getTotalByInvertedRate($bill)) }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>&nbsp;
                @endforeach
            </div>
        </div>
    </div>
@endsection
