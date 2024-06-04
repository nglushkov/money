@extends('layouts.app')

@section('title', 'Wallets')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="bg-light p-3">
                <a href="{{ route('wallets.create') }}" class="btn btn-success">Create</a>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @foreach ($wallets as $wallet)
                    <tr onclick="window.location.href = '{{ route('wallets.show', $wallet->id) }}';" style="cursor: pointer;">
                        <td>
                            {{ $wallet->name }}
                        </td>
                        <td>
                            {{ $wallet->notes }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
