@extends('layouts.app')

@section('title', 'Moves')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-light p-3">
                        <div class="btn-group" role="group">
                            <a href="{{ route('operations.create') }}" class="btn btn-success">New Operation</a>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Create
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('transfers.create') }}" class="dropdown-item">Transfer</a></li>
                                    <li><a href="{{ route('exchanges.create') }}" class="dropdown-item">Exchange</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="btn-group" role="group">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Show
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('home') }}" class="dropdown-item">
                                            All
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('home', ['date' => \Carbon\Carbon::today()->format('Y-m-d')]) }}" class="dropdown-item">
                                            Today
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('home', ['date' => \Carbon\Carbon::yesterday()->format('Y-m-d')]) }}" class="dropdown-item">
                                            Yesterday
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @if (count($plannedExpenses) > 0)
                            <button type="button" class="btn btn-sm btn-light" id="planned-expense-dismiss-all"
                                    onclick="document.getElementById('planned-expense-dismiss-all-form').submit();">Dismiss all
                            </button>
                            <form action="{{ route('planned-expenses.dismiss-all') }}" method="post" id="planned-expense-dismiss-all-form">
                                @csrf
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @if (Session::has('error'))
            <div class="alert alert-danger mt-3">
                <ul>
                    <li>{{ Session::get('error') }}</li>
                </ul>
            </div>
            @endif
            <table class="table">
                <tbody>
                    @foreach($plannedExpenses as $plannedExpense)
                        <tr>
                            <td><a href="{{ route('planned-expenses.show', $plannedExpense) }}">Planned Expense</a></td>
                            <td>{{ $plannedExpense->next_payment_date_formatted }} ({{ $plannedExpense->next_payment_date_humans }})</td>
                            <td>{{ $plannedExpense->amount_formatted }}</td>
                            <td>{{ $plannedExpense->category->name }}</td>
                            <td>{{ $plannedExpense->place->name }}</td>
                            <td>
                                <button type="button" class="btn-close" id="planned-expense-dismiss"
                                onclick="document.getElementById('planned-expense-dismiss-{{ $plannedExpense->id }}').submit();"></button>
                                <form action="{{ route('planned-expenses.dismiss', $plannedExpense) }}" method="post" id="planned-expense-dismiss-{{ $plannedExpense->id }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="dismiss" value="{{ $plannedExpense->id }}">
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if (count($moves) == 0)
                <div class="alert alert-info mt-3">
                    No moves found
                </div>
            @else
            <div class="card mb-3">
                <ul class="list-group list-group-flush">
                    @include('blocks.moves', ['moves' => $moves])
                </ul>
            </div>
            @endif
            {{ $paginator->links() }}
        </div>
    </div>
</div>
@endsection
