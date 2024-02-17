@extends('layouts.app')

@section('title', 'Create Planned Expense')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="card-title mb-2">New Planned Expense</h5>
            <div class="card-body">
                <form action="{{ route('planned-expense.store') }}" method="POST">
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
                        <input type="text" name="name" id="name" class="form-control" autofocus value="{{ old('name') }}" required >
                    </div>
                    <div class="form-group mb-2">
                        <label for="notes">Notes</label>
                        <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes') }}">
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('planned-expense.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
