@extends('layouts.app')

@section('title', 'Edit Operation')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
        <div class="card-body">
            <h5 class="card-title">Edit Operation</h5>
            <form action="{{ route('operations.update', $operation) }}" method="POST">
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
                    <label for="amount">Amount:</label>
                    <input type="text" name="amount" id="amount" class="form-control" required autofocus
                        value="{{ old('amount', $operation->amount) }}">
                </div>
                <div class="form-group mb-2">
                    <label for="type">Type:</label>
                    <select name="type" id="type" class="form-control">
                        <option value="0" @selected(old('type', $operation->type) == 0)>Expense</option>
                        <option value="1" @selected(old('type', $operation->type) == 1)>Income</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label for="bill">Bill:</label>
                    <select name="bill_id" id="bill" class="form-control">
                        @foreach($bills as $bill)
                        <option value="{{ $bill->id }}" @selected(old('bill_id', $operation->bill_id) == $bill->id)>{{
                            $bill->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label for="category">Category:</label>
                    <select name="category_id" id="category" class="form-control">
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $operation->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label for="currency">Currency:</label>
                    <select name="currency_id" id="currency" class="form-control">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" @selected(old('currency_id', $operation->currency_id) == $currency->id)>{{ $currency->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label for="place">Place:</label>
                    <select name="place_id" id="place" class="form-control">
                        @foreach($places as $place)
                        <option value="{{ $place->id }}" @selected(old('place_id', $operation->place_id) == $place->id)>{{
                            $place->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label for="notes">Notes:</label>
                    <textarea rows=5 name="notes" id="notes" class="form-control">{{ old('notes', $operation->notes) }}</textarea>
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" name="date" id="date" class="form-control" required value="{{ $operation->date->format('Y-m-d') }}">
                </div>
                <hr>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>

        <div class="card-footer">
            <a href="#" class="card-link link-danger"
                onclick="event.preventDefault(); if (confirm('Are you sure you want to delete?')) { document.getElementById('delete-form').submit(); }">Delete</a>
            <form id="delete-form" action="{{ route('operations.destroy', $operation) }}" method="POST"
                style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

@endsection