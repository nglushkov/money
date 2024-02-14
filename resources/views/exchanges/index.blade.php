@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="bg-light p-3">
                    <a href="{{ route('exchanges.create') }}" class="btn btn-success">Create</a>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Bill</th>
                            <th>Exchange</th>
                            <th>Rate</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exchanges as $exchange)
                            <tr onclick="window.location.href = '{{ route('exchanges.show', $exchange->id) }}';" style="cursor: pointer;">
                                <td>{{ $exchange->date_formatted }}</td>
                                <td>{{ $exchange->bill->name }}</td>
                                <td>{{ $exchange->amount_from_formatted }} → {{ $exchange->amount_to_formatted }}</td>
                                <td>{{ $exchange->rate_text }}</td>
                                <td>{{ Str::limit($exchange->notes, 20) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $exchanges->links() }}
            </div>
        </div>
    </div>
@endsection
