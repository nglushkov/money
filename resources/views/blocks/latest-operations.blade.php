<h5>Last Operations <small><a href="{{ route('operations.index', $routeParameters) }}">all</a></small></h5>
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
            <td>
                {{ $operation->amount_text }}
                <small class="text-body-secondary">{{ $operation->amount_in_default_currency_formatted }}</small>
            </td>
            <td><a href="{{ route('categories.show', $operation->category) }}">{{ $operation->category->name }}</a></td>
            <td><a href="{{ route('places.show', $operation->place) }}">{{ $operation->place->name }}</a></td>
            <td><a href="{{ route('bills.show', $operation->bill) }}">{{ $operation->bill->name }}</a></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th>Total</th>
        <th>
            {{ \App\Helpers\MoneyFormatter::getWithCurrencyName($operations->sum('amount_in_default_currency'), \App\Models\Currency::getDefaultCurrencyName()) }}
        </th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    </tfoot>
</table>
{{ $lastOperations->links() }}
