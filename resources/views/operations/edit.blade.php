@php use App\Models\Enum\OperationType; @endphp

@extends('operations.create-update')

@section('title', 'Edit Operation')

@section('form')

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Edit Operation @if($operation->is_draft)
                        <span class="badge bg-warning">Draft</span>
                    @endif</h5>
                <form action="{{ route('operations.update', $operation) }}" method="POST" enctype="multipart/form-data">
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
                            <option
                                value="{{ OperationType::Expense->name }}" @selected(old('type', $operation->type) == OperationType::Expense->name)>
                                Expense
                            </option>
                            <option
                                value="{{ OperationType::Income->name }}" @selected(old('type', $operation->type) == OperationType::Income->name)>
                                Income
                            </option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="bill">Bill:</label>
                        <select name="bill_id" id="bill" class="form-control">
                            @foreach($bills as $bill)
                                <option
                                    value="{{ $bill->id }}" @selected(old('bill_id', $operation->bill_id) == $bill->id)>{{
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
                                    value="{{ $category->id }}" @selected(old('category_id', $operation->category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="currency">Currency:</label>
                        <select name="currency_id" id="currency" class="form-control">
                            @foreach($currencies as $currency)
                                <option
                                    value="{{ $currency->id }}" @selected(old('currency_id', $operation->currency_id) == $currency->id)>{{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="place">Place:</label>
                        <select name="place_id" id="place" class="form-control" required>
                            <option value="">Select Place</option>
                            @foreach($places as $place)
                                <option
                                    value="{{ $place->id }}" @selected(old('place_id', $operation->place_id) == $place->id)>{{
                        $place->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="notes">Notes:</label>
                        <input type="text" name="notes" id="notes" class="form-control"
                               value="{{ old('notes', $operation->notes) }}">
                    </div>
                    <div class="form-group mb-2">
                        <label for="date">Date:</label>
                        <input type="date" name="date" id="date" class="form-control" required
                               value="{{ $operation->date->format('Y-m-d') }}">
                    </div>
                    <div class="form-group mb-2">
                        <label for="time">Attachment:</label>
                        <input type="file" name="attachment" id="attachment" class="form-control">
                    </div>
                    @if ($operation->attachment)
                        <div class="form-group">
                            📎 File attachment:<br><a href="{{ route('operations.get-attachment', $operation) }}" target="_blank">{{ $operation->attachment }}</a>
                            <br>
                            <p class="mt-2"><a href="#" class="text-danger"
                               onclick="event.preventDefault(); if (confirm('Are you sure you want to delete?')) { document.getElementById('delete-attachment').submit(); }">Delete</a>
                            </p>
                        </div>
                    @endif
                    <hr>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('home') }}" class="btn btn-secondary">Cancel</a>
                </form>
                <form action="{{ route('operations.delete-attachment', $operation) }}" id="delete-attachment" method="post">
                    @method('DELETE')
                    @csrf
                    <input type="hidden" name="attachment" value="{{ $operation->attachment }}">
                </form>
            </div>
        </div>
    </div>

@endsection
