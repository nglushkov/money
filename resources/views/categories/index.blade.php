@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <a href="{{ route('categories.create') }}" class="btn btn-success">Create</a>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr onclick="window.location.href = '{{ route('categories.show', $category->id) }}';" style="cursor: pointer;">
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->notes }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $categories->links() }}
            </div>
        </div>
    </div>
@endsection
