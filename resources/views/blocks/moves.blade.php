@foreach($moves as $move)
    @if ($move instanceof App\Models\Operation)
        <li class="list-group-item" onclick="window.location.href = '{{ route($move->is_draft ? 'operations.edit' : 'operations.show', $move) }}';" style="cursor: pointer;">
            <table style="width:100%">
                <tr>
                    <td style="width: 30%">
                        <span @class(['text-success' => $move->is_income])>💰 {{ $move->amount_text }}</span>
                        @if ($move->currency->id !== $defaultCurrency->id)
                            <small class="text-body-secondary">{{ $move->amount_in_default_currency_formatted }}</small>
                        @endif
                    </td>
                    <td style="width: 50%">
                        @if($move->category)
                            <span><a href="{{ route('categories.show', $move->category) }}" class="text-body">{{ $move->category->name }}</a></span>&nbsp;<small class="text-secondary">in</small>
                        @endif
                        @if($move->place)
                            <span class=""><a href="{{ route('places.show', $move->place) }}" class="text-body">{{ $move->place->name }}</a></span>
                        @endif
                        <small class="text-secondary">{{ $move->is_expense ? 'by' : 'to' }}</small>&nbsp;<span><a href="{{ route('bills.show', $move->bill) }}" class="text-body">{{ $move->bill->name_with_user }}</a></span>
                    </td>
                    <td style="width: 20%">
                        <span class="text-body-secondary fw-light"><small>{{ $move->user->name }}</small></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div><small class="text-body-secondary fw-light">{{ $move->date_formatted }}</small></div>
                    </td>
                    <td>
                        <small>
                            @if ($move->attachment)
                                📎
                            @endif
                        </small>
                        <small class="text-body-secondary">{{ $move->notes ? Str::limit($move->notes, 40) : '' }}</small>
                    </td>
                    <td>
                        @if($move->is_draft)
                            <form action="{{ route('operations.destroy', $move) }}" method="post">
                                <span class="badge bg-warning">Draft</span>
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="back_route" value="{{ route('home') }}">
                                <button type="submit" class="btn btn-light btn-sm">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            </table>
        </li>

    @elseif ($move instanceof App\Models\Transfer)
        <li class="list-group-item" onclick="window.location.href = '{{ route('transfers.show', $move) }}';" style="cursor: pointer;">
            <table style="width:100%">
                <tr>
                    <td style="width: 30%">
                        <span>➡️ {{ $move->amount_text_with_currency }}</span>
                    </td>
                    <td style="width: 50%">
                        <span><a href="{{ route('bills.show', $move->from) }}" class="text-body">{{ $move->from->name_with_user }}</a></span>&nbsp;
                        <small class="text-secondary">→</small>&nbsp;
                        <span><a href="{{ route('bills.show', $move->to) }}" class="text-body">{{ $move->to->name_with_user }}</a></span>
                    </td>
                    <td style="width: 20%">
                        <span class="text-body-secondary fw-light"><small>{{ $move->user->name }}</small></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div><small class="text-body-secondary fw-light">{{ $move->date_formatted }}</small></div>
                    </td>
                    <td>
                        <small class="text-body-secondary">{{ $move->notes ? Str::limit($move->notes, 40) : '' }}</small>
                    </td>
                    <td></td>
                </tr>
            </table>
        </li>

    @elseif ($move instanceof App\Models\Exchange)
        <li class="list-group-item" onclick="window.location.href = '{{ route('exchanges.show', $move) }}';" style="cursor: pointer;">
            <table style="width:100%">
                <tr>
                    <td style="width: 30%">
                        <span>🔁 {{ $move->amount_from_formatted }} → {{ $move->amount_to_formatted }}</span>
                    </td>
                    <td style="width: 50%">
                        <span>{{ $move->rate_text }}</span>
                    </td>
                    <td style="width: 20%">
                        <span class="text-body-secondary fw-light"><small>{{ $move->user->name }}</small></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div><small class="text-body-secondary fw-light">{{ $move->date_formatted }}</small></div>
                    </td>
                    <td>
                        <span>
                            <a href="{{ route('bills.show', $move->bill) }}" class="text-body">{{ $move->bill->name_with_user }}</a>&nbsp;
                            <small class="text-body-secondary">{{ $move->place ? $move->place->name : '' }}</small>
                        </span>
                    </td>
                    <td><small class="text-body-secondary">{{ $move->notes ? Str::limit($move->notes, 40) : '' }}</small></td>
                </tr>
            </table>
        </li>
    @endif
@endforeach
