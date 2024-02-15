@extends('layouts.app')

@section('title', 'Currency Details')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Currency Details</h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Name:</strong> {{ $currency->name }}</li>
            <li class="list-group-item"><strong>По умолчанию:</strong> {{ $currency->is_default ? 'Да' : 'Нет' }}</li>
        </ul>
        <div class="card-footer">
            @include('blocks.delete-link', ['model' => $currency, 'routePart' => 'currencies'])
        </div>
    </div>
</div>

<!-- Add rates -->
@if ($currency->is_default)
<div class="row">
    <div class="col-md-4">
        <div class="card mt-3">
            <div class="card-body">
                @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                @endif
                <form action="{{ route('rates.store') }}" method="POST">
                    @csrf

                    <h5>Add Rate</h5>
                    
                    <div class="form-group mb-3">
                        <label for="rate">1 {{ $currency->name }} =</label>
                        <input type="text" name="rate" id="rate" class="form-control" value="{{ old('rate') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="from_currency_id">Currency:</label>
                        <select name="from_currency_id" id="from_currency_id" class="form-control" required>
                            <option value="">Select Currency</option>
                            @foreach ($currencies as $currencyOption)
                                <option value="{{ $currencyOption->id }}" @selected(old('from_currency_id') == $currencyOption->id)>{{ $currencyOption->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="date">Date:</label>
                        <input type="date" name="date" id="date" class="form-control" required
                            value="{{ old('date', date('Y-m-d')) }}">
                    </div>

                    <input type="hidden" name="to_currency_id" value="{{ $currency->id }}">

                    <button type="submit" class="btn btn-primary">Add Rate</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Rates -->
    <div class="col-md-8">
        <div class="card mt-3 mb-3">
            <div class="card-body">
                <h5>Rates</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Rate</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($currencyRates as $rate)
                        <tr>
                            <td>{{ $rate->date_formatted }}</td>
                            <td>1 {{ $currency->name . ' = ' . $rate->rate . ' ' . $rate->currencyTo->name }}</td>
                            <td>
                                <form action="{{ route('rates.destroy', $rate) }}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            <td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $currencyRates->links() }}
    </div>
</div>
@endif
@endsection