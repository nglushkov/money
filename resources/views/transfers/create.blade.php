@extends('layouts.app')

@section('title', 'Create Transfer')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="card-title mb-2">Create Transfer</h5>
            <div class="card-body">
                <form action="{{ route('transfers.store') }}" method="POST">
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
                    <div class="form-group mb-2">
                        <label for="from_bill_id">From Bill</label>
                        <select name="from_bill_id" id="from_bill_id" class="form-control" required>
                            <option value="">Select Bill</option>
                            @foreach ($bills as $bill)
                                <option value="{{ $bill->id }}" @selected(old('from_bill_id') == $bill->id)>{{ $bill->name_with_user }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="to_bill_id">To Bill</label>
                        <select name="to_bill_id" id="to_bill_id" class="form-control" required>
                            <option value="">Select Bill</option>
                            @foreach ($bills as $bill)
                                <option value="{{ $bill->id }}" @selected(old('to_bill_id') == $bill->id)>{{ $bill->name_with_user }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="amount">Amount</label>
                        <input type="text" name="amount" id="amount" class="form-control" required value="{{ old('amount') }}">
                    </div>
                    <div class="form-group mb-2">
                        <label for="currency_id">Currency</label>
                        <select name="currency_id" id="currency_id" class="form-control" required>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->id }}" @selected(old('currency_id') == $currency->id)>{{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="notes">Notes</label>
                        <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes') }}">
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('transfers.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection