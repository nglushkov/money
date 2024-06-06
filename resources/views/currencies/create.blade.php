@extends('layouts.app')

@section('title', 'New Currency')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="card-title mb-2">Create Currency</h5>
            <div class="card-body">
                <form action="{{ route('currencies.store') }}" method="POST">
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
                    <div class="form-group">
                        <input type="checkbox" name="is_crypto" id="is_crypto" class="form-check-input"
                            {{ old('is_crypto') ? 'checked' : '' }} value="1">&nbsp;
                        <label for="is_crypto">Is Crypto</label>
                    <hr>
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('currencies.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
