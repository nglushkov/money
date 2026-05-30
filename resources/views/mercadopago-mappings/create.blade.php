@extends('layouts.app')

@section('title', 'New MP Mapping')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="card-title mb-2">New Mercado Pago Mapping</h5>
            <div class="card-body">
                <form action="{{ route('mercadopago-mappings.store') }}" method="POST">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif
                    @csrf
                    <div class="form-group mb-2">
                        <label for="keyword">Keyword</label>
                        <input type="text" name="keyword" id="keyword" class="form-control"
                            value="{{ old('keyword') }}" placeholder="netflix" autofocus required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="category_id">Category</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">— select —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="place_id">Place</label>
                        <select name="place_id" id="place_id" class="form-control">
                            <option value="">— none —</option>
                            @foreach($places as $place)
                                <option value="{{ $place->id }}" @selected(old('place_id') == $place->id)>
                                    {{ $place->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('mercadopago-mappings.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
