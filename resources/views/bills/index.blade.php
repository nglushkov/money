@php use App\Helpers\MoneyFormatter; @endphp
@extends('layouts.app')

@section('title', 'Bills')

@section('content')

<div x-data="{ hideZero: true }">

<div class="page-toolbar">
    <div class="toolbar-left">
        <a href="{{ route('bills.create') }}" class="btn btn-success btn-sm" style="font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i>New Bill
        </a>
        <a href="{{ route('bills.index') }}"
           class="filter-pill {{ !$showAll ? 'pill-active' : '' }}">Mine</a>
        <a href="{{ route('bills.index', ['all' => 1]) }}"
           class="filter-pill {{ $showAll ? 'pill-active' : '' }}">All users</a>
        <button type="button"
                class="filter-pill"
                :class="hideZero ? 'pill-active' : ''"
                @click="hideZero = !hideZero">
            Hide zero
        </button>
    </div>
</div>

@if($bills->isEmpty())
    <div class="moves-card">
        <div class="move-row" style="justify-content:center;color:var(--c-muted);cursor:default;">
            <i class="bi bi-credit-card me-2"></i>No bills yet
        </div>
    </div>
@else
    <div class="card" style="border-radius:var(--radius);box-shadow:var(--shadow);">
        <div style="overflow-x: auto;">
            <table class="bills-table">
                <thead>
                    <tr>
                        <th>Bill</th>
                        @foreach ($currencies as $currency)
                            <th class="col-amount">{{ $currency->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bills as $bill)
                        @php
                            $amounts = $bill->getAmounts();
                            $isZero  = collect($amounts)->every(fn($a) => floatval($a->getAmount()) == 0);
                        @endphp
                        <tr onclick="window.location.href='{{ route('bills.show', $bill->id) }}'"
                            x-show="!hideZero || !{{ $isZero ? 'true' : 'false' }}">
                            <td>
                                <div style="display:flex;align-items:center;gap:.5rem;">
                                    <i class="bi bi-credit-card" style="color:var(--c-muted);font-size:.85rem;"></i>
                                    <span class="bill-name">{{ $bill->name }}</span>
                                    @if($bill->default)
                                        <span style="font-size:.6rem;background:#dcfce7;color:#166534;border-radius:4px;padding:.1rem .3rem;font-weight:600;letter-spacing:.03em;">DEFAULT</span>
                                    @endif
                                    @if($showAll && $bill->user)
                                        <span class="bill-owner">{{ $bill->owner_name }}</span>
                                    @endif
                                </div>
                            </td>
                            @foreach ($amounts as $amount)
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
                        <td class="text-muted" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;">Total</td>
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
@endif

</div>{{-- end x-data --}}

@endsection
