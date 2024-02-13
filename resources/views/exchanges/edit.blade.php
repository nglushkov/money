@extends('layouts.app')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <h5 class="card-title mb-2">Edit Exchange</h5>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('exchanges.update', $exchange->id) }}" method="POST">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-2">
                        <label for="amount_from">Amount From</label>
                        <input type="number" name="amount_from" id="amount_from" class="form-control" value="{{ old('amount_from', $exchange->amount_from) }}"
                            required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="from_currency_id">From Currency</label>
                        <select name="from_currency_id" id="from_currency_id" class="form-control" required>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->id }}" @selected(old('from_currency_id') == $currency->id or $exchange->from_currency_id == $currency->id)>
                                    {{ $currency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="amount_to">Amount To</label>
                        <input type="number" name="amount_to" id="amount_to" class="form-control" value="{{ old('amount_to', $exchange->amount_to) }}"
                            required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="to_currency_id">To Currency</label>
                        <select name="to_currency_id" id="to_currency_id" class="form-control" required>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->id }}" @selected(old('to_currency_id') == $currency->id or $exchange->to_currency_id == $currency->id)>
                                    {{ $currency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="bill_id">Bill</label>
                        <select name="bill_id" id="bill_id" class="form-control" required>
                            @foreach ($bills as $bill)
                                <option value="{{ $bill->id }}" @selected(old('bill_id') == $bill->id or $exchange->bill_id == $bill->id)>
                                    {{ $bill->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $exchange->date->format('Y-m-d')) }}"
                            required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="notes">Notes</label>
                        <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes', $exchange->notes) }}">
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection