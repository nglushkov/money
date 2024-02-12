@extends('layouts.app')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <h5 class="card-title mb-2">Edit Bill</h5>
        <div class="card">
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
                        <input type="text" name="name" id="name" class="form-control" value="{{ $bill->name }}"
                            required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="notes">Notes</label>
                        <input type="text" name="notes" id="notes" class="form-control" value="{{ $bill->notes }}">
                    </div>
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
                </form>
            </div>
        </div>
    </div>
</div>
@endsection