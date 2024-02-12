@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <a href="{{ route('transfers.create') }}" class="btn btn-success">Create</a>
                <table class="table">
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
                                <td>{{ $transfer->from->name }}</td>
                                <td>{{ $transfer->to->name }}</td>
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
