@php use App\Models\Enum\OperationType; @endphp

@extends('operations.create-update')

@section('title', 'Create Operation')

@section('form')

    <div class="col-md-4">
        <form action="{{ route('operations.store') }}" method="POST">
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
                <input type="text" name="amount" id="amount" class="form-control" required autofocus
                       value="{{ old('amount', $plannedExpense->amount ?? '') }}">
            </div>
            <div class="form-group mb-2">
                <label for="type">Type:</label>
                <select name="type" id="type" class="form-control">
                    <option
                        value="{{ OperationType::Expense->name }}" @selected(old('type') == OperationType::Expense->name)>
                        Expense
                    </option>
                    <option
                        value="{{ OperationType::Income->name }}" @selected(old('type') == OperationType::Income->name)>
                        Income
                    </option>
                </select>
            </div>
            <div class="form-group mb-2">
                <label for="bill">Bill:</label>
                <select name="bill_id" id="bill" class="form-control" required>
                    <option value="">Select Bill</option>
                    @foreach($bills as $bill)
                        <option
                            value="{{ $bill->id }}" @selected(old('bill_id', $plannedExpense->bill_id ?? '') == $bill->id)>{{
                                    $bill->name_with_user }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-2">
                <label for="category">Category:</label>
                <select name="category_id" id="category" class="form-control" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option
                            value="{{ $category->id }}" @selected(old('category_id', $plannedExpense->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-2">
                <label for="currency">Currency:</label>
                <select name="currency_id" id="currency" class="form-control">
                    @foreach($currencies as $currency)
                        <option
                            value="{{ $currency->id }}" @selected(old('currency_id', $plannedExpense->currency_id ?? '') == $currency->id)>{{ $currency->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-2">
                <label for="place">Place:</label>
                <select name="place_id" id="place" class="form-control" required>
                    <option value="">Select Place</option>
                    @foreach($places as $place)
                        <option
                            value="{{ $place->id }}" @selected(old('place_id', $plannedExpense->place_id ?? '') == $place->id)>{{
                                    $place->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-2">
                <label for="notes">Notes:</label>
                <input type="text" name="notes" id="notes" class="form-control"
                       value="{{ old('notes', $plannedExpense->notes ?? '') }}">
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" name="date" id="date" class="form-control" required
                       value="{{ old('date', date('Y-m-d')) }}">
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('home') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

@endsection
