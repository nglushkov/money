@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @include('operations.filters')
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Category</th>
                        <th>Place</th>
                        <th>Bill</th>
                        <!-- <th>Notes</th> -->
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($operations as $operation)
                    <a href="{{ route('operations.show', $operation->id) }}">
                        <tr onclick="window.location.href = '{{ route('operations.show', $operation->id) }}';" style="cursor: pointer;">
                            <td @class([
                                'text-success' => $operation->type === 1,
                            ])>{{ $operation->amount_text_with_currency }}</td>
                            <td>{{ Str::limit($operation->category->name, 10, '...') }}</td>
                            <td>{{ Str::limit($operation->place->name, 10, '...') }}</td>
                            <td>{{ Str::limit($operation->bill->name, 20, '...') }}</td>
                            <!-- <td>{{ Str::limit($operation->notes, 20, '...') }}</td> -->
                            <td>{{ $operation->date_formatted }}</td>
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
