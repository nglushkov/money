@extends('layouts.app')

@section('title', 'Planned Expenses')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="bg-light p-3">
                    <a href="{{ route('planned-expenses.create') }}" class="btn btn-success">Create</a>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Next Payment</th>
                            <th>Amount</th>
                            <th>Frequency</th>
                            <th>Category</th>
                            <th>Place</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plannedExpenses as $plannedExpense)
                            <tr onclick="window.location.href = '{{ route('planned-expenses.show', $plannedExpense) }}';" style="cursor: pointer;">
                                <td>{{ $plannedExpense->next_payment_date_formatted }} ({{ $plannedExpense->next_payment_date_humans }})</td>
                                <td>{{ $plannedExpense->amount_formatted }}</td>
                                <td>
                                    {{ $plannedExpense->frequency_text }}
                                </td>
                                <td><a href="{{ route('categories.show', $plannedExpense->category) }}">{{ $plannedExpense->category->name }}</a></td>
                                <td><a href="{{ route('places.show', $plannedExpense->place) }}">{{ $plannedExpense->place->name }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $paginator->links() }}
            </div>
        </div>
    </div>
@endsection
