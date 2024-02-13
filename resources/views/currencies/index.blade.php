@extends('layouts.app')

@section('title', 'Currencies')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <a href="{{ route('currencies.create') }}" class="btn btn-success">Create</a>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Is Default</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currencies as $currency)
                            <tr onclick="window.location.href = '{{ route('currencies.show', $currency) }}';" style="cursor: pointer;">
                                <td>{{ $currency->name }}</td>
                                <td>{{ $currency->is_default ? '✅' : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
