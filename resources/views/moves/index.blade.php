@extends('layouts.app')

@section('title', 'Moves')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-light p-3 d-flex flex-wrap align-items-center gap-2">
                        @if(session('success'))
                            <div class="alert alert-success py-1 mb-2 w-100">{{ session('success') }}</div>
                        @endif

                        {{-- Create --}}
                        <div class="btn-group" role="group">
                            <a href="{{ route('operations.create') }}" class="btn btn-success">+ Operation</a>
                            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('transfers.create') }}" class="dropdown-item">+ Transfer</a></li>
                                <li><a href="{{ route('exchanges.create') }}" class="dropdown-item">+ Exchange</a></li>
                                <li><a href="{{ route('p2p.create') }}" class="dropdown-item">+ P2P</a></li>
                            </ul>
                        </div>

                        {{-- Filters --}}
                        <div class="btn-group" role="group">
                            @php $noFilter = !$mpOnly && !$draftOnly && !$activeType; @endphp
                            <a href="{{ route('home') }}" class="btn {{ $noFilter ? 'btn-secondary' : 'btn-outline-secondary' }}">All</a>
                            <a href="{{ route('home', ['type' => \App\Models\Enum\MoveType::Operation->name]) }}"
                               class="btn {{ $activeType === \App\Models\Enum\MoveType::Operation->name ? 'btn-secondary' : 'btn-outline-secondary' }}">Operations</a>
                            <a href="{{ route('home', ['type' => \App\Models\Enum\MoveType::Transfer->name]) }}"
                               class="btn {{ $activeType === \App\Models\Enum\MoveType::Transfer->name ? 'btn-secondary' : 'btn-outline-secondary' }}">Transfers</a>
                            <a href="{{ route('home', ['type' => \App\Models\Enum\MoveType::Exchange->name]) }}"
                               class="btn {{ $activeType === \App\Models\Enum\MoveType::Exchange->name ? 'btn-secondary' : 'btn-outline-secondary' }}">Exchanges</a>
                            <a href="{{ route('home', ['mp' => 1]) }}"
                               class="btn {{ $mpOnly ? 'btn-primary' : 'btn-outline-primary' }}">MP</a>
                            <a href="{{ route('home', ['draft' => 1]) }}"
                               class="btn {{ $draftOnly ? 'btn-warning' : 'btn-outline-warning' }}">Drafts</a>
                        </div>

                        {{-- Actions --}}
                        <form action="{{ route('mp-sync') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">↻ Sync MP</button>
                        </form>

                        @if (count($plannedExpenses) > 0)
                            <button type="button" class="btn btn-sm btn-light"
                                    onclick="document.getElementById('planned-expense-dismiss-all-form').submit();">Dismiss all planned
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
            @foreach($moves as $date => $moveGroup)
                <span class="fw-light badge bg-secondary text-wrap">{{ $date }}</span>
                <div class="card mb-3">
                    <ul class="list-group list-group-flush">
                        @include('blocks.moves', ['moves' => $moveGroup])
                    </ul>
                </div>
            @endforeach
            @endif
            {{ $paginator->links() }}
        </div>
    </div>
</div>
@endsection
