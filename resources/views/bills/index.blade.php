@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <caption>Bills count: {{ $bills->count() }}</caption>
                <thead>
                    <tr>
                        <th>Name</th>
                        @foreach ($currencies as $currency)
                            <th>{{ $currency->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @foreach ($bills as $bill)
                        <tr onclick="window.location.href = '{{ route('bills.show', $operation->id) }}';" style="cursor: pointer;">
                            <td>{{ $bill->name }}</td>
                            @foreach ($currencies as $currency)
                            @php
                                $amount = $currency->bills->find($bill->id)->pivot->amount ?? 0;
                            @endphp
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
                        @foreach ($currencies as $currency)
                            <th>
                                {{ \App\Helpers\MoneyFormatter::getWithoutDecimals($currency->bills->sum('pivot.amount')) }}
                            </th>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
