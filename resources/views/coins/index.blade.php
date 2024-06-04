@extends('layouts.app')

@section('title', 'Coins')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="bg-light p-3">
                <a href="{{ route('coins.create') }}" class="btn btn-success">Create</a>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Symbol</th>
                        <th>Is Default</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @foreach ($coins as $coin)
                        <tr onclick="window.location.href = '{{ route('coins.show', $coin->id) }}';" style="cursor: pointer;">
                            <td>{{ $coin->name }}</td>
                            <td>{{ $coin->symbol }}</td>
                            <td>{{ $coin->is_default ? 'Yes' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
