@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Operations</h1>
            <div class="col-md-12">
                <a href="{{ route('operations.create') }}" class="btn btn-primary">Add operation</a>
            </div>
            <form action="{{ route('operations.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="bill_id">Bill</label>
                            <select name="bill_id" id="bill_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($bills as $bill)
                                <option value="{{ $bill->id }}" @selected(request('bill_id') == $bill->id)>{{ $bill->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_from">Date from</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_to">Date to</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-light">Filter</button>
                    </div
                </div>
            </form>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Bill ID</th>
                        <th>Category ID</th>
                        <th>Currency ID</th>
                        <th>Place ID</th>
                        <th>User</th>
                        <th>Notes</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($operations as $operation)
                    <a href="{{ route('operations.show', $operation->id) }}">
                        <tr onclick="window.location.href = '{{ route('operations.show', $operation->id) }}';" style="cursor: pointer;">
                            <td @class([
                                'text-success' => $operation->amount > 0,
                            ])>{{ $operation->amount }}</td>
                            <td>{{ $operation->bill->name }}</td>
                            <td>{{ $operation->category->name }}</td>
                            <td>{{ $operation->currency->name }}</td>
                            <td>{{ $operation->place->name }}</td>
                            <td>{{ $operation->user->name }}</td>
                            <td>{{ Str::limit($operation->notes, 20, '...') }}</td>
                            <td>{{ $operation->date }}</td>
                        </tr>
                    </a>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $operations->links() }}
    </div>
</div>
@endsection
