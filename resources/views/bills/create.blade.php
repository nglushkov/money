@extends('layouts.app')

@section('title', 'Create Bill')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="card-title mb-2">Create Bill</h5>
            <div class="card-body">
                <form action="{{ route('bills.store') }}" method="POST">
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
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                            required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="notes">Notes</label>
                        <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes') }}">
                    </div>
                    <div class="form-group mb-2">
                        <label for="user_id">User</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">Bill is Common</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" @if(old('user_id') == $user->id) selected @endif>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr>
                    <h5>Начальный остаток:</h5>
                    @foreach($currencies as $currency)
                    <div class="form-group mb-2">
                        <label for="amount_{{ $currency->id }}">{{ $currency->name }}</label>
                        <input type="text" name="amount[{{ $currency->id }}]" id="amount_{{ $currency->id }}"
                            class="form-control" value="{{ old('amount.' . $currency->id, 0) }}" required>
                    </div>
                    @endforeach
                    <hr>
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('bills.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
