@extends('layouts.app')

@section('title', 'Create Coin')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="card-title mb-2">Create Coin</h5>
            <div class="card-body">
                <form action="{{ route('coins.store') }}" method="POST">
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
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" autofocus
                            required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="symbol">Symbol</label>
                        <input type="text" name="symbol" id="symbol" class="form-control" value="{{ old('symbol') }}"
                            required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="notes">Notes</label>
                        <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes') }}">
                    </div>
                    <div class="form-group">
                        <label for="is_default">Is Default</label>
                        <input type="checkbox" name="is_default" id="is_default" class="form-check-input" value="1"
                            {{ old('is_default') ? 'checked' : '' }}>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('coins.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
