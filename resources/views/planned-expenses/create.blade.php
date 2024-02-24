@extends('layouts.app')

@section('title', 'Create Planned Expense')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="card-title mb-2">New Planned Expense</h5>
            <div class="card-body">
                <form action="{{ route('planned-expenses.store') }}" method="POST">
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
                        <label for="amount">Amount:</label>
                        <input type="text" class="form-control" id="amount" name="amount" value="{{ old('amount') }}">
                    </div>
                    <div class="form-group mb-2">
                        <label for="day">Day Number:</label>
                        <input type="number" class="form-control" id="day" name="day" value="{{ old('day') }}">
                    </div>
                    <div class="form-group mb-2">
                        <label for="month">Month Number:</label>
                        <input type="number" class="form-control" id="month" name="month" value="{{ old('month') }}">
                    </div>
                    <div class="form-group mb-2">
                        <label for="reminder_days">Reminder Days:</label>
                        <input type="number" class="form-control" id="reminder_days" name="reminder_days" value="{{ old('reminder_days') }}">
                    </div>
                    <div class="form-group mb-2">
                        <label for="frequency">Frequency:</label>
{{--                        todo: refactor this--}}
                        <select class="form-control" id="frequency" name="frequency">
                            <option value="monthly" @selected(old('frequency') === 'monthly')>Monthly</option>
                            <option value="annually" @selected(old('frequency') === 'annually')>Annually</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="currency_id">Currency:</label>
                        <select class="form-control" id="currency_id" name="currency_id">
                            <option value="">Select Currency</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" @selected(old('currency_id') == $currency->id)>{{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="category_id">Category:</label>
                        <select class="form-control" id="category_id" name="category_id">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="place_id">Place:</label>
                        <select class="form-control" id="place_id" name="place_id">
                            <option value="">Select Place</option>
                            @foreach($places as $place)
                                <option value="{{ $place->id }}" @selected(old('place_id') == $place->id)>{{ $place->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="bill_id">Bill:</label>
                    <select class="form-control" id="bill_id" name="bill_id">
                        <option value="">Select Bill</option>
                        @foreach($bills as $bill)
                            <option value="{{ $bill->id }}" @selected(old('bill_id') == $bill->id)>{{ $bill->name }}</option>
                        @endforeach
                    </select>
                    <div class="form-group mb-2">
                        <label for="notes">Notes:</label>
                        <input class="form-control" type="text" id="notes" name="notes" value="{{ old('notes') }}">
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
