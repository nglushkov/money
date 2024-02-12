@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
    @error('error')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
        <h5 class="card-title">Currency Details</h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Name:</strong> {{ $currency->name }}</li>
            <li class="list-group-item"><strong>Active:</strong> {{ $currency->active ? 'Да' : 'Нет' }}</li>
            <li class="list-group-item"><strong>По умолчанию:</strong> {{ $currency->is_default ? 'Да' : 'Нет' }}</li>
        </ul>
        <div class="card-footer">
            @include('blocks.delete-link', ['model' => $currency, 'routePart' => 'currencies'])
        </div>
    </div>
</div>
@endsection