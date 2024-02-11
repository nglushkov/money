@extends('layouts.app')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <h5 class="card-title mb-2">Create Bill</h5>
        <div class="card">
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
                    @foreach($currencies as $currency)
                    <div class="form-group mb-2">
                        <label for="amount_{{ $currency->id }}">{{ $currency->name }}</label>
                        <input type="text" name="amount[{{ $currency->id }}]" id="amount_{{ $currency->id }}"
                            class="form-control" value="{{ old('amount.' . $currency->id, 0) }}" required>
                    </div>
                    @endforeach
                    <hr>
                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection