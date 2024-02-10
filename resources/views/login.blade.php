@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="row justify-content-center mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">
                <form method="POST" action="{{ route('authenticate') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Login</button>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
