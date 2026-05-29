@extends('layouts.app')

@section('title', 'App Settings')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="card-title mb-3">General Settings</h5>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('settings.app.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="text-muted mb-3">Mercado Pago</h6>

                    <div class="form-group mb-3">
                        <label for="mp_review_threshold">Review threshold (ARS)</label>
                        <input type="number" name="mp_review_threshold" id="mp_review_threshold"
                            class="form-control" value="{{ old('mp_review_threshold', $reviewThreshold) }}" min="0" required>
                        <small class="text-muted">MP operations above this amount are flagged for review</small>
                    </div>

                    <h6 class="text-muted mb-3 mt-4">P2P</h6>

                    <div class="form-group mb-4">
                        <label for="p2p_bybit_bill_name">Default P2P bill (bill name)</label>
                        <input type="text" name="p2p_bybit_bill_name" id="p2p_bybit_bill_name"
                            class="form-control" value="{{ old('p2p_bybit_bill_name', $p2pBybitBillName) }}" required>
                        <small class="text-muted">Bill where USDT → ARS exchange is created</small>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
