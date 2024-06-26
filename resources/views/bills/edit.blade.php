@extends('layouts.app')

@section('title', 'Edit Bill')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="card-title mb-2">Edit Bill</h5>
            <div class="card-body">
                <form action="{{ route('bills.update', $bill->id) }}" method="POST">
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
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $bill->name) }}"
                            required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="notes">Notes</label>
                        <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes', $bill->notes) }}">
                    </div>
                    <div class="form-group mb-2">
                        <label for="user_id">User</label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">Bill is Common</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @if(old('user_id', $bill->user_id) == $user->id) selected @endif>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="is_crypto" id="is_crypto" class="form-check-input"
                               {{ old('is_crypto') ?? $bill->is_crypto ? 'checked' : '' }} value="1">&nbsp;
                        <label for="is_crypto">Is Crypto</label>
                    <hr>
                    <h5>Начальный остаток:</h5>
                    @foreach($currencies as $currency)
                        <div class="form-group mb-2">
                            <label for="amount_{{ $currency->id }}">{{ $currency->name }}</label>
                            <input type="text" name="amount[{{ $currency->id }}]" id="amount_{{ $currency->id }}"
                                class="form-control" value="{{ old('amount.' . $currency->id, $bill->currenciesInitial->find($currency->id)->pivot->amount ?? 0) }}"
                                required>
                        </div>
                    @endforeach
                    <hr>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('bills.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
