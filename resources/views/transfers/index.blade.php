@extends('layouts.app')

@section('title', 'Transfers')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="bg-light p-3">
                    <a href="{{ route('transfers.create') }}" class="btn btn-success">Create</a>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                            <tr onclick="window.location.href = '{{ route('transfers.show', $transfer->id) }}';" style="cursor: pointer;">
                                <td>{{ $transfer->date_formatted }}</td>
                                <td>{{ $transfer->amount_text_with_currency }}</td>
                                <td>{{ $transfer->from->name_with_user }}</td>
                                <td>{{ $transfer->to->name_with_user }}</td>
                                <td>{{ Str::limit($transfer->notes, 20) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $transfers->links() }}
            </div>
        </div>
    </div>
@endsection
