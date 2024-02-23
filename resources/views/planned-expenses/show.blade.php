@extends('layouts.app')

@section('title', 'Planned Expense Details')

@section('content')
    <div class="card">
        <div class="card-body">
            @error('error')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <h5 class="card-title">Planned Expense Details</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Amount:</strong> {{ $plannedExpense->amount }}</li>
                <li class="list-group-item"><strong>Day:</strong> {{ $plannedExpense->day }}</li>
                <li class="list-group-item"><strong>Month:</strong> {{ $plannedExpense->month }}</li>
                <li class="list-group-item"><strong>Frequency:</strong> {{ $plannedExpense->frequency }}</li>
                <li class="list-group-item"><strong>Currency:</strong> {{ $plannedExpense->currency->name }}</li>
                <li class="list-group-item"><strong>Category:</strong> {{ $plannedExpense->category->name }}</li>
                <li class="list-group-item"><strong>Place:</strong> {{ $plannedExpense->place->name }}</li>
                <li class="list-group-item"><strong>Reminder Days:</strong> {{ $plannedExpense->reminder_days }}</li>
                <li class="list-group-item"><strong>Bill:</strong> {{ $plannedExpense->bill->name ?? '' }}</li>
                <li class="list-group-item"><strong>Notes:</strong> {{ $plannedExpense->notes }}</li>
            </ul>
            <div class="card-footer">
                @include('blocks.delete-link', ['model' => $plannedExpense, 'routePart' => 'planned-expenses'])
            </div>
        </div>
    </div>
@endsection
