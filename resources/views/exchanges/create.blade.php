@extends('layouts.app')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="card-title mb-2">Create Exchange</h5>
            <div class="card-body">
                <form action="{{ route('exchanges.store', request()->query()) }}" method="POST">
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
                        <div class="col-6">
                            <input type="text" name="amount_from" id="amount_from" class="form-control" required value="{{ old('amount_from', \App\Helpers\MoneyFormatter::getWithoutTrailingZeros($copyExchange->amount_from ?? '')) }}">
                        </div>
                        <div class="col-auto">
                            <select name="from_currency_id" id="from_currency_id" class="form-control" required>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}" @selected(old('from_currency_id') == $currency->id || $currency->id == $defaultCurrency->id || $copyExchange->from_currency_id == $currency->id)>{{ $currency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="amount_to">To:</label>
                        </div>
                        <div class="col-6">
                            <input type="text" name="amount_to" id="amount_to" class="form-control" required value="{{ old('amount_to', \App\Helpers\MoneyFormatter::getWithoutTrailingZeros($copyExchange->amount_to ?? '')) }}">
                        </div>
                        <div class="col-auto">
                            <select name="to_currency_id" id="to_currency_id" class="form-control" required>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}" @selected(old('to_currency_id') == $currency->id || $copyExchange->to_currency_id == $currency->id)>{{ $currency->name }}</option>
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
                                    <option value="{{ $bill->id }}" @selected(old('bill_id') == $bill->id || $copyExchange->bill_id == $bill->id)>{{ $bill->name_with_user }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if (!Request::has('is_crypto'))
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="place_id">Place:</label>
                        </div>
                        <div class="col">
                            <select name="place_id" id="place_id" class="form-control">
                                <option value="">Select Place</option>
                                @foreach ($places as $place)
                                    <option value="{{ $place->id }}" @selected(old('place_id') == $place->id || $copyExchange->place_id == $place->id)>{{ $place->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="place_name">New Place:</label>
                        </div>
                        <div class="col">
                            <input type="text" name="place_name" id="notes" class="form-control" value="{{ old('place_name') }}">
                        </div>
                    </div>
                    @endif
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="date">Date:</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $copyExchange->date ? $copyExchange->date->format('Y-m-d') : date('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="notes">Notes:</label>
                        </div>
                        <div class="col">
                            <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes', $copyExchange->notes) }}">
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="create_currency_rate" id="create_currency_rate" value="1" @checked(old('create_currency_rate'))>
                        <label class="form-check-label" for="create_currency_rate">
                            Create Currency Rate
                        </label>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('exchanges.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
