@php use App\Helpers\MoneyFormatter; @endphp
@extends('layouts.app')

@section('title', 'Bills')

@section('content')

<div class="page-toolbar">
    <div class="toolbar-left">
        <a href="{{ route('bills.create') }}" class="btn btn-success btn-sm" style="font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i>New Bill
        </a>
        @if(request('user_id'))
            <a href="{{ route('bills.index') }}" class="filter-pill pill-active">My Bills</a>
        @else
            <a href="{{ route('bills.index', ['user_id' => Auth::id()]) }}" class="filter-pill">My Bills</a>
        @endif
    </div>
</div>

<div class="card">
    <div style="overflow-x: auto;">
        <table class="bills-table">
            <thead>
                <tr>
                    <th>Bill</th>
                    <th>Owner</th>
                    @foreach ($currencies as $currency)
                        <th class="col-amount">{{ $currency->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($bills as $bill)
                    <tr onclick="window.location.href='{{ route('bills.show', $bill->id) }}'">
                        <td>
                            <span class="bill-name">{{ $bill->name }}</span>
                            @if($bill->is_crypto)
                                <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Crypto</span>
                            @endif
                        </td>
                        <td class="bill-owner">{{ $bill->owner_name }}</td>
                        @foreach ($bill->getAmounts() as $amount)
                            @php $val = MoneyFormatter::getWithoutDecimals($amount->getAmount()); @endphp
                            <td class="col-amount {{ $val == '0' ? 'amount-zero' : '' }}">
                                {{ $val }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-muted" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;">Total</td>
                    @foreach ($currencies as $currency)
                        <td class="col-amount">
                            {{ MoneyFormatter::getWithoutDecimals($currency->getSum($bills)) }}
                        </td>
                    @endforeach
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection
