<a href="{{ route($routePart . '.create', $model) }}" class="card-link text-success">Create New</a>
<a href="{{ route($routePart . '.edit', $model) }}" class="card-link">Edit</a>
@if ($model instanceof App\Models\Exchange)
    <a href="{{ route($routePart . '.create', ['copy_id' => $model]) }}" class="card-link">Copy</a>
@endif
<a href="{{ route($routePart . '.index') }}" class="card-link">Show All</a>
<a href="#" class="card-link link-danger"
    onclick="event.preventDefault(); if (confirm('Are you sure you want to delete?')) { document.getElementById('delete-form').submit(); }">Delete</a>
<form id="delete-form" action="{{ route($routePart . '.destroy', $model) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
