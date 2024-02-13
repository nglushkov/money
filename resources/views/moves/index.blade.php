@extends('layouts.app')

@section('title', 'Moves')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('operations.create') }}" class="btn btn-outline-primary">New Operation</a>
            <a href="{{ route('transfers.create') }}" class="btn btn-outline-primary">New Transfer</a>
            <a href="{{ route('exchanges.create') }}" class="btn btn-outline-primary">New Exchange</a>
            <div class="card mt-3 mb-3">
                <ul class="list-group list-group-flush">
                    @foreach($moves as $move)
                        @if ($move instanceof App\Models\Operation)
                        <li class="list-group-item" onclick="window.location.href = '{{ route('operations.show', $move) }}';" style="cursor: pointer;">
                            <table style="width:100%">
                                <tr>
                                    <td style="width: 8rem">
                                        <span @class(['text-success' => $move->is_income])>💰 {{ $move->amount_text }}</span>
                                    </td>
                                    <td style="width: 8rem">
                                        <span><a href="{{ route('categories.show', $move->category) }}" class="text-body">{{ $move->category->name }}</a></span>&nbsp;
                                        <span class=""><a href="{{ route('places.show', $move->place) }}" class="text-body">{{ $move->place->name }}</a></span></span>
                                    </td>
                                    <td style="width: 8rem">
                                        <span class="text-body-secondary fw-light"><small>{{ Auth::user()->name }}</small></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div><small class="text-body-secondary fw-light">{{ $move->date_formatted }}</small></div>
                                    </td>
                                    <td>
                                        <span><a href="{{ route('bills.show', $move->bill) }}" class="text-body">{{ $move->bill->name }}</a></span>
                                    </td>
                                    <td><small class="text-body-secondary">{{ Str::limit($move->notes, 20) }}</small></td>
                                </tr>
                            </table>
                        @elseif ($move instanceof App\Models\Transfer)
                        <li class="list-group-item" onclick="window.location.href = '{{ route('transfers.show', $move) }}';" style="cursor: pointer;">
                            <table style="width:100%">
                                <tr>
                                    <td style="width: 8rem;">
                                        <span>📤 {{ $move->amount_text_with_currency }}</span>
                                    </td>
                                    <td style="width: 8rem">
                                        <span>{{ $move->from->name }} → {{ $move->to->name }}</span>
                                    </td>
                                    <td style="width: 8rem">
                                        <span class="text-body-secondary fw-light"><small>{{ Auth::user()->name }}</small></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div><small class="text-body-secondary fw-light">{{ $move->date_formatted }}</small></div>
                                    </td>
                                    <td></td>
                                    <td><small class="text-body-secondary">{{ Str::limit($move->notes, 20) }}</small></td>
                                </tr>
                            </table
                        </li>    
                        @elseif ($move instanceof App\Models\Exchange)
                        <li class="list-group-item" onclick="window.location.href = '{{ route('exchanges.show', $move) }}';" style="cursor: pointer;">
                            <table style="width:100%">
                                <tr>
                                    <td style="width: 8rem;">
                                        <span>🔁 {{ $move->amount_from_formatted }} → {{ $move->amount_to_formatted }}</span>
                                    </td>
                                    <td style="width: 8rem">
                                        <span>{{ $move->rate_text }}</span>
                                    </td>
                                    <td style="width: 8rem">
                                        <span class="text-body-secondary fw-light"><small>{{ Auth::user()->name }}</small></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div><small class="text-body-secondary fw-light">{{ $move->date_formatted }}</small></div>
                                    </td>
                                    <td>
                                        <span><a href="{{ route('bills.show', $move->bill) }}" class="text-body">{{ $move->bill->name }}</a></span>
                                    </td>
                                    <td><small class="text-body-secondary">{{ Str::limit($move->notes, 20) }}</small></td>
                                </tr>
                            </table
                        </li>    
                        @endif
                    @endforeach
                </ul>
            </div>
            {{ $paginator->links() }}
        </div>
    </div>
</div>
@endsection