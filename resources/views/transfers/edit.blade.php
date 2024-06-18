@extends('layouts.app')

@section('title', 'Edit Transfer')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="card-title mb-2">Edit Transfer</h5>
            <div class="card-body">
                <form action="{{ route('transfers.update', $transfer->id) }}" method="POST">
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
                    @method('PUT')
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="from_bill_id">From:</label>
                        </div>
                        <div class="col-auto">
                            <select name="from_bill_id" id="from_bill_id" class="form-control" required>
                                @foreach ($bills as $bill)
                                    <option value="{{ $bill->id }}" @selected(old('from_bill_id') == $bill->id || $transfer->from_bill_id == $bill->id)>{{ $bill->name_with_user }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="to_bill_id">To:</label>
                        </div>
                        <div class="col-auto">
                            <select name="to_bill_id" id="to_bill_id" class="form-control" required>
                                @foreach ($bills as $bill)
                                    <option value="{{ $bill->id }}" @selected(old('to_bill_id') == $bill->id || $transfer->to_bill_id == $bill->id)>{{ $bill->name_with_user }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="amount">Amount:</label>
                        </div>
                        <div class="col-6">
                            <input type="text" name="amount" id="amount" class="form-control" required value="{{ old('amount', $transfer->amount) }}">
                        </div>
                        <div class="col-auto">
                            <select name="currency_id" id="currency_id" class="form-control" required>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}" @selected(old('currency_id') == $currency->id || $transfer->currency_id == $currency->id)>{{ $currency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="date">Date:</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $transfer->date->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-2">
                            <label for="notes">Notes:</label>
                        </div>
                        <div class="col">
                            <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes', $transfer->notes) }}">
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('transfers.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
