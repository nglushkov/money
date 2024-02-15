<h5>Last Operations <small><a href="{{ route('operations.index') }}">all</a></small></h5>
<table class="table">
    <thead>
        <tr>
            <th scope="col">Date</th>
            <th scope="col">Amount</th>
            <th scope="col">Category</th>
            <th scope="col">Place</th>
            <th scope="col">Bill</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($lastOperations as $operation)
        <tr onclick="window.location.href = '{{ route('operations.show', $operation->id) }}';" style="cursor: pointer;">
            <td>{{ $operation->date_formatted }}</td>
            <td>{{ $operation->amount_text }}</td>
            <td><a href="{{ route('categories.show', $operation->category) }}">{{ $operation->category->name }}</a></td>
            <td><a href="{{ route('places.show', $operation->place) }}">{{ $operation->place->name }}</a></td>
            <td><a href="{{ route('bills.show', $operation->bill) }}">{{ $operation->bill->name }}</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $lastOperations->links() }}