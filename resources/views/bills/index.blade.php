@extends('layouts.app')

@

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('bills.create') }}" class="btn btn-success">Create</a>
            <a href="{{ route('bills.index', ['user_id' => Auth::id()]) }}" class="btn btn-light">Show My Bills</a>

            <table class="table table-striped table-hover">
                <caption>Bills count: {{ $bills->count() }}</caption>
                <thead>
                    <tr>
                        <th>Счёт</th>
                        <th>User</th>
                        @foreach ($currencies as $currency)
                            <th class="table-info">{{ $currency->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @foreach ($bills as $bill)
                        <tr onclick="window.location.href = '{{ route('bills.show', $bill->id) }}';" style="cursor: pointer;">
                            <td>{{ $bill->name }}</td>
                            <td>{{ $bill->user->name }}</td>
                            @foreach ($bill->getAmounts() as $amount)
                                <td>
                                    {{ \App\Helpers\MoneyFormatter::getWithoutDecimals($amount)}}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th></th>
                        @foreach ($currencies as $currency)
                            <th>
                                {{ \App\Helpers\MoneyFormatter::getWithoutDecimals($currency->getSum($bills)) }}
                            </th>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
