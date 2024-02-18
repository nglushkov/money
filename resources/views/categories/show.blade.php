@extends('layouts.app')

@section('title', 'Category Details')

@section('content')
<div class="card">
    <div class="card-body">
    @error('error')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
        <h5 class="card-title">Category Details</h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Name:</strong> {{ $category->name }}</li>
            <li class="list-group-item"><strong>Notes:</strong> {{ $category->notes }}</li>
        </ul>
        <div class="card-footer">
            @include('blocks.delete-link', ['model' => $category, 'routePart' => 'categories'])
        </div>

        <hr>
        @include('blocks.latest-operations', ['operations' => $lastOperations, 'routeParameters' => ['category_id' => $category->id]])
    </div>
</div>
@endsection
