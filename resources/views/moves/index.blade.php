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
                        <a href="{{ route('currencies.show', \App\Models\Currency::getDefaultCurrencyId()) }}" class="btn btn-light">{{ \App\Models\Currency::getDefaultCurrencyName() }} rates</a>
                        <div class="float-end">
                            <form action="{{ route('operations.create-draft') }}" method="POST">
                                {{ csrf_field() }}
                                <input type="text" class="form-control" style="width: 20rem" id="search" placeholder="Create: Amount Category Place..." name="raw_text" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }">
                            </form>
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
            <div class="card mb-3">
                <ul class="list-group list-group-flush">
                    @include('blocks.moves', ['moves' => $moves])
                </ul>
            </div>
            {{ $paginator->links() }}
        </div>
    </div>
</div>
@endsection
