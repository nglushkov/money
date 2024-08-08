@php use App\Helpers\MoneyFormatter;use App\Helpers\MoneyHelper; @endphp
@extends('layouts.app')

@section('title', 'Crypto Bills')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="bg-light p-3 mb-3">
                    <h3>
                        Crypto Bills
                        <a href="{{ route('bills.create') }}" class="btn btn-sm btn-success">Add bill</a>
                        <a href="{{ route('currencies.show', \App\Models\Currency::getDefaultCurrency(true)) }}" class="btn btn-sm btn-success">Add rate</a>
                        <a href="{{ route('exchanges.create', ['is_crypto' => 1]) }}" class="btn btn-sm btn-success">Add exchange</a>&nbsp;
                    </h3>
                    <small class="text-muted">Rates last updated at: {{ $ratesUpdatedAt }}&nbsp;<a href="{{ route('rates.refresh-crypto') }}" class="text-muted">Refresh</a></small>
                    <div class="mt-2"></div>
                    @foreach($bills as $bill)
                        <a href="#bill-{{ $bill->id }}" class="">{{ $bill->name }}</a>
                    @endforeach
                </div>
                @foreach ($bills as $bill)
                    <div class="card" id="bill-{{ $bill->id }}">
                        <div class="card-body">
                            <h4>
                                <a href="{{ route('bills.show', $bill) }}">{{ $bill->name }}</a>
                            </h4>
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Coin</th>
                                    <th>Amount</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                    <th>Total invested</th>
                                    <th>Revenue</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($bill->getAmountsNotNull() as $amount)
                                    <tr>
                                        <td>
                                            <a href="{{ route('exchanges.index', ['currency_id' => $amount->getCurrency()->id]) }}">{{ $amount->getCurrency()->name }}</a>
                                        </td>
                                        <td>{{ MoneyFormatter::getWithoutTrailingZeros($amount->getAmount()) }}</td>
                                        <td>
                                            @if (!$amount->getCurrency()->is_default)
                                                1 {{ $amount->getCurrency()->name }} = {{ MoneyFormatter::getWithoutTrailingZeros($amount->getCurrency()->getCurrentInvertedRateAsString()) }} {{ App\Models\Currency::getDefaultCurrencyName(true) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (!$amount->getCurrency()->is_default)
                                                {{ MoneyFormatter::getWithCurrencyName($amount->getCurrency()->getAmountByInvertedRate($bill), App\Models\Currency::getDefaultCurrencyName(true)) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (!$amount->getCurrency()->is_default)
                                            <form id="form-{{ $bill->id }}-{{ $amount->getCurrency()->id }}" action="{{ route('crypto.set-total-invested-amount', $bill) }}" method="post">
                                                @csrf
                                                @method('PUT')
                                                {{ MoneyFormatter::getWithCurrencyName($bill->getCryptoInvestedByCurrency($amount->getCurrency()), App\Models\Currency::getDefaultCurrencyName(true)) }}
                                                <input type="hidden" id="amount-{{ $bill->id }}-{{ $amount->getCurrency()->id }}" name="amount">
                                                <input type="hidden" name="currency_id" value="{{ $amount->getCurrency()->id }}">
                                                <button type="submit" class="btn btn-light btn-sm"
                                                        onclick="event.preventDefault();setInvestedAmount({{ $amount->getCurrency()->id }}, {{ $bill->id }})">
                                                    Set
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                        @php
                                            $revenue = MoneyHelper::subtract($amount->getCurrency()->getAmountByInvertedRate($bill), $bill->getCryptoInvestedByCurrency($amount->getCurrency()));
                                        @endphp
                                        <td>
                                            @if (!$amount->getCurrency()->is_default)
                                                <span @class(['text-danger' => $revenue < 0, 'text-success' => $revenue > 0])>
                                                    {{ MoneyFormatter::getWithCurrencyName($revenue, App\Models\Currency::getDefaultCurrencyName(true)) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="3"></th>
                                    <th>{{ MoneyFormatter::getWithCurrencyName($amount->getCurrency()->getTotalByInvertedRate($bill), App\Models\Currency::getDefaultCurrencyName(true)) }}</th>
                                    <th>{{ MoneyFormatter::getWithCurrencyName($amount->getCurrency()->getTotalCryptoInvested($bill), App\Models\Currency::getDefaultCurrencyName(true)) }}</th>
                                    <th>{{ MoneyFormatter::getWithCurrencyName($amount->getCurrency()->getTotalRevenue($bill), App\Models\Currency::getDefaultCurrencyName(true)) }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>&nbsp;
                @endforeach
            </div>
        </div>
    </div>

    <script>
        function setInvestedAmount(currencyId, billId) {
            const amount = parseFloat(prompt('Enter the amount invested', '0'));
            if (amount !== null) {
                document.getElementById('amount-' + billId + '-' + currencyId).value = amount;
                document.getElementById('form-' + billId + '-' + currencyId).submit();
            } else {
                alert('Invalid amount');
            }
        }
    </script>
@endsection
