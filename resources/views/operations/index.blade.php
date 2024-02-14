@extends('layouts.app')

@section('title', 'Operations')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="bg-light p-3">
                @include('operations.filters')
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Amount in {{ $defaultCurrency->name }}</th>
                        <th>Category</th>
                        <th>Place</th>
                        <th>Bill</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($operations as $operation)
                    <a href="{{ route('operations.show', $operation->id) }}">
                        <tr onclick="window.location.href = '{{ route('operations.show', $operation->id) }}';" style="cursor: pointer;">
                            <td>{{ $operation->date_formatted }}</td>
                            <td @class([
                                'text-success' => $operation->type === 1,
                            ])>{{ $operation->amount_text }}</td>
                            <td>{{ $operation->amount_in_default_currency_formatted }}</td>
                            <td><a class="text-body" href="{{ route('categories.show', $operation->category) }}">{{ Str::limit($operation->category->name, 15, '...') }}</a></td>
                            <td><a class="text-body" href="{{ route('places.show', $operation->place) }}">{{ Str::limit($operation->place->name, 15, '...') }}</a></td>
                            <td><a class="text-body" href="{{ route('bills.show', $operation->bill) }}">{{ Str::limit($operation->bill->name, 15, '...') }}</td>
                        </tr>
                    </a>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $operations->links() }}
    </div>
</div>
@endsection
