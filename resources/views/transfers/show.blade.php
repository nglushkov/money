@extends('layouts.app')

@section('title', 'Transfer Details')

@section('content')
<div class="card">
    <div class="card-body">
    @error('error')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
        <h5 class="card-title">Transfer Details</h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>From:</strong> {{ $transfer->from->name }}</li>
            <li class="list-group-item"><strong>To:</strong> {{ $transfer->to->name }}</li>
            <li class="list-group-item"><strong>Amount:</strong> {{ $transfer->amount_text_with_currency }}</li>
            <li class="list-group-item"><strong>Date:</strong> {{ $transfer->date_formatted }}</li>
            <li class="list-group-item"><strong>Notes:</strong> {{ $transfer->notes }}</li>
            <li class="list-group-item"><strong>User ID:</strong> {{ $transfer->user->name }}</li>
        </ul>
        <div class="card-footer">
            @include('blocks.delete-link', ['model' => $transfer, 'routePart' => 'transfers'])
        </div>
    </div>
</div>
@endsection