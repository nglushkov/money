@extends('layouts.app')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="card-title mb-2">Update Exchange</h5>
            <div class="card-body">
                <form action="{{ route('exchanges.update', $exchange->id) }}" method="POST">
                    @method('PUT')
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{!! $error !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @csrf
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="amount_from">From:</label>
                        </div>
                        <div class="col-4">
                            <input type="text" name="amount_from" id="amount_from" class="form-control" required value="{{ old('amount_from', \App\Helpers\MoneyFormatter::getWithoutTrailingZeros($exchange->amount_from)) }}">
                        </div>
                        <div class="col-auto">
                            <select name="from_currency_id" id="from_currency_id" class="form-control" required>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}" @selected($exchange->from_currency_id == $currency->id || old('from_currency_id') == $currency->id)>{{ $currency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="amount_to">To:</label>
                        </div>
                        <div class="col-4">
                            <input type="text" name="amount_to" id="amount_to" class="form-control" required value="{{ old('amount_to', \App\Helpers\MoneyFormatter::getWithoutTrailingZeros($exchange->amount_to)) }}">
                        </div>
                        <div class="col-auto">
                            <select name="to_currency_id" id="to_currency_id" class="form-control" required>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}" @selected($exchange->to_currency_id == $currency->id || old('to_currency_id') == $currency->id)>{{ $currency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="bill_id">Bill:</label>
                        </div>
                        <div class="col">
                            <select name="bill_id" id="bill_id" class="form-control" required>
                                @foreach ($bills as $bill)
                                    <option value="{{ $bill->id }}" @selected($exchange->bill_id == $bill->id || old('bill_id') == $bill->id)>{{ $bill->name_with_user }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="place_id">Place:</label>
                        </div>
                        <div class="col">
                            <select name="place_id" id="place_id" class="form-control">
                                <option value="">Select Place</option>
                                @foreach ($places as $place)
                                    <option value="{{ $place->id }}" @selected($exchange->place_id == $place->id || old('place_id') == $place->id)>{{ $place->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="place_name">New Place:</label>
                        </div>
                        <div class="col">
                            <input type="text" name="place_name" id="notes" class="form-control" value="{{ old('place_name', $exchange->place_name) }}">
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="date">Date:</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $exchange->date->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="notes">Notes:</label>
                        </div>
                        <div class="col">
                            <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes', $exchange->notes) }}">
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('exchanges.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
