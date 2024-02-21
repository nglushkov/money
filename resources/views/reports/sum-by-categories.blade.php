@extends('layouts.app')

@section('title', 'Transfers')

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-title p-3 pb-0 mb-0">
                <h4>Sum by Categories:</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">

                    <div class="col-3">
                        <div class="mb-2">
                            <a href="{{ route('reports.sum-by-categories', ['year' => date('Y'), 'month' => date('n')]) }}"
                                @class(['btn' => true, 'btn-light' => true])
                            >Current</a>
                        </div>

                        <div class="btn-group-vertical align-top">
                            @foreach($years as $year)
                                <a href="{{ route('reports.sum-by-categories', ['year' => $year, 'month' => request('month', date('n'))]) }}"
                                    @class(['active' => $year == request('year', date('Y')), 'btn' => true, 'btn-secondary' => true])
                                >
                                    {{ $year }}
                                </a>
                            @endforeach
                        </div>

                        <div class="btn-group-vertical">
                            @foreach($months as $number => $name)
                                <a href="{{ route('reports.sum-by-categories', ['month' => $number, 'year' => request('year', date('Y'))]) }}"
                                    @class(['active' => $number == request('month', date('n')), 'btn' => true, 'btn-secondary' => true, 'position-relative' => true])
                                >
                                    {{ $name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-5">
                        <h5>Total: <strong class="text-success">{{ $total }}</strong></h5>
                        <table class="table">
                            <tbody>
                            @foreach($result as $categoryName => $currency)
                                <tr>
                                    <td><strong>{{ $categoryName }}</strong>
                                        <small class="text-body-secondary">{{ $totalByCategories->get($categoryName) }}</small>
                                    </td>
                                </tr>
                                @foreach($currency as $currencyName => $amounts)
                                    <tr>
                                        <td>
                                            <span>{{ $amounts->get('amount') }}</span>
                                            @if($amounts->get('operation_currency') !== $defaultCurrencyName && count($currency) > 1)
                                                <small class="text-body-secondary">{{ $amounts->get('amount_in_default_currency') }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="col-4">
                        <h5>Total by Categories</h5>
                        <table class="table">
                            <tbody>
                            @foreach($totalByCategories as $item)
                                <tr>
                                    <td><a class="text-body" href="{{ route('categories.show', $item['categoryId']) }}">{{ $item['categoryName'] }}</a></td>
                                    <td>{{ $item['total'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th><strong>Total</strong></th>
                                <th>{{ $total }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
