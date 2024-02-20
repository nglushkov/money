@extends('layouts.app')

@section('title', 'Moves')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="bg-light p-3">
                <a href="{{ route('operations.create') }}" class="btn btn-success">New Operation</a>
                <a href="{{ route('transfers.create') }}" class="btn btn-success">New Transfer</a>
                <a href="{{ route('exchanges.create') }}" class="btn btn-success">New Exchange</a>&nbsp;
                <a href="{{ route('currencies.show', \App\Models\Currency::default()->first()) }}" class="btn btn-light">{{ \App\Models\Currency::default()->first()->name }} rates</a>
            </div>
            @if($plannedExpenses->count() > 0)
                @foreach($plannedExpenses as $plannedExpense)
                    <div class="alert alert-info alert-dismissible mt-3" role="alert">
                            <p class="m-0">У вас есть <a href="{{ route('planned-expenses.index', ['id' => $plannedExpense]) }}" class="alert-link">
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

                    @foreach($moves as $move)
                        @if ($move instanceof App\Models\Operation)
                        <li class="list-group-item" onclick="window.location.href = '{{ route($move->is_draft ? 'operations.edit' : 'operations.show', $move) }}';" style="cursor: pointer;">
                            <table style="width:100%">
                                <tr>
                                    <td style="width: 30%">
                                        <span @class(['text-success' => $move->is_income])>💰 {{ $move->amount_text }}</span>
                                        @if ($move->currency->id !== $defaultCurrency->id)
                                            <small class="text-body-secondary">{{ $move->amount_in_default_currency_formatted }}</small>
                                        @endif
                                    </td>
                                    <td style="width: 50%">
                                        @if($move->category)
                                            <span><a href="{{ route('categories.show', $move->category) }}" class="text-body">{{ $move->category->name }}</a></span>&nbsp;<small class="text-secondary">in</small>
                                        @endif
                                        @if($move->place)
                                            <span class=""><a href="{{ route('places.show', $move->place) }}" class="text-body">{{ $move->place->name }}</a></span></span>
                                        @endif
                                        <small class="text-secondary">by</small>&nbsp;<span><a href="{{ route('bills.show', $move->bill) }}" class="text-body">{{ $move->bill->name }}</a></span>
                                    </td>
                                    <td style="width: 20%">
                                        <span class="text-body-secondary fw-light"><small>{{ $move->user->name }}</small></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div><small class="text-body-secondary fw-light">{{ $move->date_formatted }}</small></div>
                                    </td>
                                    <td><small class="text-body-secondary">{{ Str::limit($move->notes, 40) }}</small></td>
                                    <td>
                                        @if($move->is_draft)
                                            <form action="{{ route('operations.destroy', $move) }}" method="post">
                                                <span class="badge bg-warning">Draft</span>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light btn-sm">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </li>

                        @elseif ($move instanceof App\Models\Transfer)
                        <li class="list-group-item" onclick="window.location.href = '{{ route('transfers.show', $move) }}';" style="cursor: pointer;">
                            <table style="width:100%">
                                <tr>
                                    <td style="width: 30%">
                                        <span>📤 {{ $move->amount_text_with_currency }}</span>
                                    </td>
                                    <td style="width: 50%">
                                        <span><a href="{{ route('bills.show', $move->from) }}" class="text-body">{{ $move->from->name }}</a></span>&nbsp;
                                            <small class="text-secondary">→</small>&nbsp;
                                        <span><a href="{{ route('bills.show', $move->to) }}" class="text-body">{{ $move->to->name }}</a></span>
                                    </td>
                                    <td style="width: 20%">
                                        <span class="text-body-secondary fw-light"><small>{{ $move->user->name }}</small></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div><small class="text-body-secondary fw-light">{{ $move->date_humans }}</small></div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table
                        </li>

                        @elseif ($move instanceof App\Models\Exchange)
                        <li class="list-group-item" onclick="window.location.href = '{{ route('exchanges.show', $move) }}';" style="cursor: pointer;">
                            <table style="width:100%">
                                <tr>
                                    <td style="width: 30%">
                                        <span>🔁 {{ $move->amount_from_formatted }} → {{ $move->amount_to_formatted }}</span>
                                    </td>
                                    <td style="width: 50%">
                                        <span>{{ $move->rate_text }}</span>
                                    </td>
                                    <td style="width: 20%">
                                        <span class="text-body-secondary fw-light"><small>{{ $move->user->name }}</small></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div><small class="text-body-secondary fw-light">{{ $move->date_humans }}</small></div>
                                    </td>
                                    <td>
                                        <span><a href="{{ route('bills.show', $move->bill) }}" class="text-body">{{ $move->bill->name }}</a></span>
                                    </td>
                                    <td><small class="text-body-secondary">{{ Str::limit($move->notes, 20) }}</small></td>
                                </tr>
                            </table
                        </li>
                        @endif
                    @endforeach
                </ul>
            </div>
            {{ $paginator->links() }}
        </div>
    </div>
</div>
@endsection
