@extends('layouts.app')

@section('title', 'Create P2P')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-5">
        <div class="card p-3">
            <h5 class="card-title mb-1">P2P Exchange</h5>
            <p class="text-muted small mb-3">Creates Exchange (USDT → ARS) and Transfer to Mercado Pago</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="card-body p-0">
                <form action="{{ route('p2p.store') }}" method="POST">
                    @csrf

                    @if($operation)
                        <input type="hidden" name="from_operation_id" value="{{ $operation->id }}">
                        <div class="alert alert-info py-2 small">
                            Source operation #{{ $operation->id }} will be deleted after P2P is created
                        </div>
                    @endif

                    <div class="form-group mb-3">
                        <label for="bybit_bill_id">P2P bill (USDT → ARS)</label>
                        <select name="bybit_bill_id" id="bybit_bill_id" class="form-control" required>
                            @foreach($bills as $bill)
                                <option value="{{ $bill->id }}"
                                    @selected(old('bybit_bill_id', $bybitBill?->id) == $bill->id)>
                                    {{ $bill->name }}{{ $bill->user ? ' (' . $bill->user->name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" class="form-control"
                            value="{{ old('date', $operation?->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="usdt_amount">USDT sold</label>
                        <div class="input-group">
                            <input type="number" name="usdt_amount" id="usdt_amount" class="form-control"
                                value="{{ old('usdt_amount') }}" step="0.01" min="0.01" required
                                oninput="calcRate()">
                            <span class="input-group-text">USDT</span>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="ars_amount">ARS received</label>
                        <div class="input-group">
                            <input type="number" name="ars_amount" id="ars_amount" class="form-control"
                                value="{{ old('ars_amount', money_input($operation?->amount)) }}" step="0.01" min="0.01" required
                                oninput="calcRate()">
                            <span class="input-group-text">ARS</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <small class="text-muted">Rate: <strong id="rate-display">—</strong> ARS/USDT</small>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary">Create P2P</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function calcRate() {
    const usdt = parseFloat(document.getElementById('usdt_amount').value);
    const ars  = parseFloat(document.getElementById('ars_amount').value);
    const el   = document.getElementById('rate-display');
    el.textContent = (usdt > 0 && ars > 0) ? (ars / usdt).toFixed(2) : '—';
}
calcRate();
</script>
@endsection
