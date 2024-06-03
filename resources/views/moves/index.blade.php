@extends('layouts.app')

@section('title', 'Moves')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-light p-3">
                        <a href="{{ route('operations.create') }}" class="btn btn-success">New Operation</a>
                        <a href="{{ route('transfers.create') }}" class="btn btn-success">New Transfer</a>
                        <a href="{{ route('exchanges.create') }}" class="btn btn-success">New Exchange</a>&nbsp;
                        <div class="btn-group" role="group">
                            <a href="{{ route('home', ['date' => \Carbon\Carbon::today()->format('Y-m-d')]) }}" @class(['btn', 'btn-sm', 'btn-secondary', 'active' => request('date') == \Carbon\Carbon::today()->format('Y-m-d')])>
                                Today
                            </a>
                            <a href="{{ route('home', ['date' => \Carbon\Carbon::yesterday()->format('Y-m-d')]) }}" @class(['btn', 'btn-sm', 'btn-secondary', 'active' => request('date') == \Carbon\Carbon::yesterday()->format('Y-m-d')])>
                                Yesterday
                            </a>
                        </div>
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
            @if($plannedExpenses->count() > 0)
                @foreach($plannedExpenses as $plannedExpense)
                    <div class="alert alert-info alert-dismissible mt-3" role="alert">
                            <p class="m-0">У вас есть <a href="{{ route('planned-expenses.show', $plannedExpense) }}" class="alert-link">
                                    запланированные расходы</a> на {{ $plannedExpense->next_payment_date_formatted }} ({{ $plannedExpense->next_payment_date_humans }})
                                    на {{ $plannedExpense->amount_formatted }} в категории {{ $plannedExpense->category->name }} ({{ $plannedExpense->place->name }})
                            </p>

                            <button type="button" class="btn-close" id="planned-expense-dismiss"
                            onclick="document.getElementById('planned-expense-dismiss-{{ $plannedExpense->id }}').submit();"></button>

                            <form action="{{ route('planned-expenses.dismiss', $plannedExpense) }}" method="post" id="planned-expense-dismiss-{{ $plannedExpense->id }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="dismiss" value="{{ $plannedExpense->id }}">
                            </form>
                    </div>
                @endforeach
            @endif
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
