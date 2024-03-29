@extends('layouts.app')

@section('title', 'Places')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="bg-light p-3">
                    <a href="{{ route('places.create') }}" class="btn btn-success">Create</a>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($places as $place)
                            <tr onclick="window.location.href = '{{ route('places.show', $place) }}';" style="cursor: pointer;">
                                <td>{{ $place->name }}</td>
                                <td>{{ $place->notes }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $places->links() }}
            </div>
        </div>
    </div>
@endsection
