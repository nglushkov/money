@extends('layouts.app')

@section('title', 'Coin Details')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Coin Details</h5>
            <hr>
            <p><strong>Name:</strong> {{ $coin->name }}</p>
            <p><strong>Symbol:</strong> {{ $coin->symbol }}</p>
            <p><strong>Notes:</strong> {{ $coin->notes }}</p>
            <p><strong>Is Default:</strong> {{ $coin->is_default ? 'Yes' : 'No' }}</p>
            <div class="card-footer">
                @include('blocks.delete-link', ['model' => $coin, 'routePart' => 'coins'])
            </div>
        </div>
    </div>
@endsection
