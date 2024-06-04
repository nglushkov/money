@extends('layouts.app')

@section('title', 'Wallet Details')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Wallet Details</h5>
            <hr>
            <p><strong>Name:</strong> {{ $wallet->name }}</p>
            <p><strong>Notes:</strong> {{ $wallet->notes }}</p>
            <div class="card-footer">
                @include('blocks.delete-link', ['model' => $wallet, 'routePart' => 'wallets'])
            </div>
        </div>
    </div>
@endsection
