@extends('layouts.app')

@section('title', 'Mercado Pago Mappings')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="bg-light p-3 mb-2">
                <a href="{{ route('mercadopago-mappings.create') }}" class="btn btn-success">Add mapping</a>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Category</th>
                        <th>Place</th>
                        <th>Default</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mappings as $mapping)
                        <tr>
                            <td><code>{{ $mapping->keyword }}</code></td>
                            <td>{{ $mapping->category?->name }}</td>
                            <td>{{ $mapping->place?->name }}</td>
                            <td>{{ $mapping->is_default ? '✓' : '' }}</td>
                            <td>
                                <a href="{{ route('mercadopago-mappings.edit', $mapping) }}" class="btn btn-sm btn-light">Edit</a>
                                <form action="{{ route('mercadopago-mappings.destroy', $mapping) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
