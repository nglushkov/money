@extends('layouts.app')

@section('title', 'Currencies')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>From Currency</th>
                            <th>To Currency</th>
                            <th>Buy</th>
                            <th>Sell</th>
                            <th>Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($externalRates as $externalRate)
                            <tr>
                                <td>{{ $externalRate->date }}</td>
                                <td>{{ $externalRate->fromCurrency->name }}</td>
                                <td>{{ $externalRate->toCurrency->name }}</td>
                                <td>{{ $externalRate->buy }}</td>
                                <td>{{ $externalRate->sell }}</td>
                                <td>{{ $externalRate->rate }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection