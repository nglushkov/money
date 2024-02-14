@extends('layouts.app')

@section('title', 'Create Operation')

@section('content')
<div class="row">
    <div class="col">
        <div class="card p-3">
            <h5 class="card-title mb-2">Create Operation</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
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
                                    value="{{ old('amount') }}">
                            </div>
                            <div class="form-group mb-2">
                                <label for="type">Type:</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="0" @selected(old('type') == 0)>Expense</option>
                                    <option value="1" @selected(old('type') == 1)>Income</option>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="bill">Bill:</label>
                                <select name="bill_id" id="bill" class="form-control" required>
                                    <option value="">Select Bill</option>
                                    @foreach($bills as $bill)
                                    <option value="{{ $bill->id }}" @selected(old('bill_id') == $bill->id)>{{
                                        $bill->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="category">Category:</label>
                                <select name="category_id" id="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="currency">Currency:</label>
                                <select name="currency_id" id="currency" class="form-control">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}" @selected(old('currency_id') == $currency->id)>{{ $currency->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="place">Place:</label>
                                <select name="place_id" id="place" class="form-control" required>
                                    <option value="">Select Place</option>
                                    @foreach($places as $place)
                                    <option value="{{ $place->id }}" @selected(old('place_id') == $place->id)>{{
                                        $place->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="notes">Notes:</label>
                                <textarea rows=5 name="notes" id="notes" class="form-control">{{ old('notes') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="date">Date:</label>
                                <input type="date" name="date" id="date" class="form-control" required value="{{ old('date', date('Y-m-d')) }}">
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{ route('home') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <h5>Top 10 usable categories</h5>
                        <a href="{{ route('categories.create') }}" class="btn btn-outline-primary btn-sm">Create new category</a>
                        <ul class="list-group">
                            @foreach($topCategories as $category)
                            <li class="list-group">
                                <a href="{{ route('operations.index', ['category_id' => $category->id]) }}" onclick="event.preventDefault(); document.getElementById('category').value = {{ $category->id }}; document.getElementById('form').submit();">{{ $category->name }}</a>
                            </li>
                            @endforeach
                        </ul>
                        <hr>

                        <h5>Top 15 usable places</h5>
                        <a href="{{ route('places.create') }}" class="btn btn-outline-primary btn-sm">Create new place</a>
                        <ul class="list-group">
                            @foreach($topPlaces as $place)
                            <li class="list-group">
                                <a href="{{ route('operations.index', ['place_id' => $place->id]) }}" onclick="event.preventDefault(); document.getElementById('place').value = {{ $place->id }}; document.getElementById('form').submit();">{{ $place->name }}</a>
                            </li>
                            @endforeach
                        </ul>
                        <hr>

                        <h5>Top 5 usable bills</h5>
                        <ul class="list-group">
                            @foreach($topBills as $bill)
                            <li class="list-group">
                                <a href="{{ route('operations.index', ['bill_id' => $bill->id]) }}" onclick="event.preventDefault(); document.getElementById('bill').value = {{ $bill->id }}; document.getElementById('form').submit();">{{ $bill->name }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection