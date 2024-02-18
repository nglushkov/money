@extends('layouts.app')

@section('title', 'Transfers')

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-title p-3 pb-0 mb-0">
                <h4>Report:</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">

                    <div class="col-2">
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
                                </button>
                                <a href="{{ route('reports.sum-by-categories', ['month' => $number, 'year' => request('year', date('Y'))]) }}"
                                    @class(['active' => $number == request('month', date('n')), 'btn' => true, 'btn-secondary' => true, 'position-relative' => true])
                                >
                                    {{ $name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-10">
                        <p>Total: <strong>{{ $total }}</strong></p>
                        <table class="table">
                            <tbody>
                            @foreach($result as $categoryName => $currency)
                                <tr>
                                    <td><strong>{{ $categoryName }}</strong></td>
                                </tr>
                                @foreach($currency as $currencyName => $amount)
                                    <tr>
                                        <td>{{ $currencyName }}: {{ $amount }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
