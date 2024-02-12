@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <a href="{{ route('currencies.create') }}" class="btn btn-success">Create</a>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currencies as $currency)
                            <tr onclick="window.location.href = '{{ route('currencies.show', $currency) }}';" style="cursor: pointer;">
                                <td>{{ $currency->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
