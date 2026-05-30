@extends('layouts.app')

@section('title', 'New Category')

@section('content')

<div class="page-toolbar">
    <div class="toolbar-left">
        <a href="{{ route('categories.index') }}" style="color:var(--c-muted);font-size:.875rem;text-decoration:none;">
            <i class="bi bi-arrow-left me-1"></i>All categories
        </a>
    </div>
</div>

<div class="row justify-content-md-center mt-3">
    <div class="col-md-6">
        <div class="form-card">
            <h5 style="font-size:1rem;font-weight:700;margin:0 0 1.25rem;">New Category</h5>

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form autocomplete="off" action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;color:var(--c-muted);">Name</label>
                    <input type="text" autocomplete="off" name="name" id="name" class="form-control" autofocus value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;color:var(--c-muted);">Notes</label>
                    <input type="text" autocomplete="off" name="notes" id="notes" class="form-control" value="{{ old('notes') }}">
                </div>
                <div style="display:flex;gap:.5rem;margin-top:1.5rem;">
                    <button type="submit" class="btn btn-success">Create</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
