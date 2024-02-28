@extends('layouts.app')

@section('title', 'External Rates')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Direction</th>
                            <th>Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($externalRates as $externalRate)
                            <tr>
                                <td>{{ $externalRate->date }}</td>
                                <td>{{ $externalRate->fromCurrency->name }} → {{ $externalRate->toCurrency->name }}</td>
                                <td>{{ $externalRate->rate ?: $externalRate->sell }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
