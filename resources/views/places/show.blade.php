@extends('layouts.app')

@section('title', 'Place Details')

@section('content')
<div class="card">
    <div class="card-body">
    @error('error')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
        <h5 class="card-title">Place Details</h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Name:</strong> {{ $place->name }}</li>
            <li class="list-group-item"><strong>Notes:</strong> {{ $place->notes }}</li>
        </ul>
        <div class="card-footer">
            @include('blocks.delete-link', ['model' => $place, 'routePart' => 'places'])
        </div>
    </div>
</div>
@endsection