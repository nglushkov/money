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
                        <label for="place_name">Place name</label>
                        <input type="text" name="place_name" id="place_name" class="form-control"
                            value="{{ old('place_name') }}" placeholder="Netflix">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_default" id="is_default" class="form-check-input"
                            value="1" @checked(old('is_default'))>
                        <label class="form-check-label" for="is_default">Default (fallback)</label>
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
