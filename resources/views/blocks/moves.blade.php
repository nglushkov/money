@foreach($moves as $move)
    @if ($move instanceof App\Models\Operation)
        @php $href = route($move->is_draft ? 'operations.edit' : 'operations.show', $move); @endphp
        @php $moveKey = 'operation:' . $move->id; @endphp
        <div class="move-row"
             :class="selectMode && selected.includes('{{ $moveKey }}') ? 'move-row-selected' : ''"
             @click="if(selectMode) { selected.includes('{{ $moveKey }}') ? selected = selected.filter(x => x !== '{{ $moveKey }}') : selected.push('{{ $moveKey }}') } else { window.location.href='{{ $href }}' }">
            <div class="move-select" x-show="selectMode" @click.stop style="display:none;align-items:center;padding-right:.5rem;">
                <input type="checkbox"
                       :checked="selected.includes('{{ $moveKey }}')"
                       @change="$event.target.checked ? selected.push('{{ $moveKey }}') : selected = selected.filter(x => x !== '{{ $moveKey }}')"
                       style="width:1.1rem;height:1.1rem;cursor:pointer;">
            </div>
            <div class="move-icon {{ $move->is_income ? 'mi-income' : 'mi-expense' }}">
                <i class="bi {{ $move->is_income ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
            </div>
            <div class="move-body">
                <div class="move-title">
                    {{ $move->category?->name ?? '—' }}
                    @if($move->place)<span class="text-muted fw-normal"> @ {{ $move->place->name }}</span>@endif
                </div>
                <div class="move-subtitle">
                    <a href="{{ route('bills.show', $move->bill) }}" class="text-muted" onclick="event.stopPropagation()">{{ $move->bill->name_with_user }}</a>
                    @if($move->notes) · {{ Str::limit($move->notes, 35) }}@endif
                    @if($move->attachment) · <i class="bi bi-paperclip"></i>@endif
                </div>
            </div>
            <div class="move-right" onclick="event.stopPropagation()">
                <div class="move-amount-val {{ $move->is_income ? 'col-income' : 'col-expense' }}">
                    {{ $move->amount_text }}
                </div>
                @if ($move->currency->id !== $defaultCurrency->id && $move->amount_in_default_currency != 0)
                    <div class="move-amount-sub">{{ $move->amount_in_default_currency_formatted }}</div>
                @endif
                @if($move->mp_review_status === 'pending')
                    <div class="mt-1">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-sm btn-warning dropdown-toggle"
                                    data-bs-toggle="dropdown" style="font-size:.7rem;padding:.2rem .5rem;">
                                Review
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('p2p.create', ['from_operation' => $move->id]) }}">
                                    <i class="bi bi-people"></i> Create P2P
                                </a></li>
                                <li>
                                    <form autocomplete="off" action="{{ route('operations.keep', $move->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-check2"></i> Keep as operation
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form autocomplete="off" action="{{ route('operations.destroy', $move) }}" method="POST"
                                          onsubmit="return confirm('Delete this operation?')">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="back_route" value="{{ url()->current() }}">
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @elseif($move->is_draft)
                    <div class="d-flex align-items-center gap-1 mt-1 justify-content-end">
                        <span class="badge-draft">Draft</span>
                        <form autocomplete="off" action="{{ route('operations.destroy', $move) }}" method="post" class="d-inline">
                            @csrf @method('DELETE')
                            <input type="hidden" name="back_route" value="{{ route('home') }}">
                            <button type="submit" class="btn btn-link btn-sm p-0 text-danger" style="font-size:.8rem;line-height:1;">×</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

    @elseif ($move instanceof App\Models\Transfer)
        @php $moveKey = 'transfer:' . $move->id; @endphp
        <div class="move-row"
             :class="selectMode && selected.includes('{{ $moveKey }}') ? 'move-row-selected' : ''"
             @click="if(selectMode) { selected.includes('{{ $moveKey }}') ? selected = selected.filter(x => x !== '{{ $moveKey }}') : selected.push('{{ $moveKey }}') } else { window.location.href='{{ route('transfers.show', $move) }}' }">
            <div class="move-select" x-show="selectMode" @click.stop style="display:none;align-items:center;padding-right:.5rem;">
                <input type="checkbox"
                       :checked="selected.includes('{{ $moveKey }}')"
                       @change="$event.target.checked ? selected.push('{{ $moveKey }}') : selected = selected.filter(x => x !== '{{ $moveKey }}')"
                       style="width:1.1rem;height:1.1rem;cursor:pointer;">
            </div>
            <div class="move-icon mi-transfer">
                <i class="bi bi-arrow-left-right"></i>
            </div>
            <div class="move-body">
                <div class="move-title">
                    <a href="{{ route('bills.show', $move->from) }}" class="text-body" onclick="event.stopPropagation()">{{ $move->from->name_with_user }}</a>
                    <span class="text-muted fw-normal"> → </span>
                    <a href="{{ route('bills.show', $move->to) }}" class="text-body" onclick="event.stopPropagation()">{{ $move->to->name_with_user }}</a>
                </div>
                <div class="move-subtitle">
                    Transfer · {{ $move->user->name }}
                    @if($move->notes) · {{ Str::limit($move->notes, 35) }}@endif
                </div>
            </div>
            <div class="move-right">
                <div class="move-amount-val col-neutral">{{ $move->amount_text_with_currency }}</div>
            </div>
        </div>

    @elseif ($move instanceof App\Models\Exchange)
        @php $moveKey = 'exchange:' . $move->id; @endphp
        <div class="move-row"
             :class="selectMode && selected.includes('{{ $moveKey }}') ? 'move-row-selected' : ''"
             @click="if(selectMode) { selected.includes('{{ $moveKey }}') ? selected = selected.filter(x => x !== '{{ $moveKey }}') : selected.push('{{ $moveKey }}') } else { window.location.href='{{ route('exchanges.show', $move) }}' }">
            <div class="move-select" x-show="selectMode" @click.stop style="display:none;align-items:center;padding-right:.5rem;">
                <input type="checkbox"
                       :checked="selected.includes('{{ $moveKey }}')"
                       @change="$event.target.checked ? selected.push('{{ $moveKey }}') : selected = selected.filter(x => x !== '{{ $moveKey }}')"
                       style="width:1.1rem;height:1.1rem;cursor:pointer;">
            </div>
            <div class="move-icon mi-exchange">
                <i class="bi bi-currency-exchange"></i>
            </div>
            <div class="move-body">
                <div class="move-title">
                    {{ $move->amount_from_formatted }}
                    <span class="text-muted fw-normal"> → </span>
                    {{ $move->amount_to_formatted }}
                </div>
                <div class="move-subtitle">
                    <a href="{{ route('bills.show', $move->bill) }}" class="text-muted" onclick="event.stopPropagation()">{{ $move->bill->name_with_user }}</a>
                    @if($move->place) · {{ $move->place->name }}@endif
                    · {{ $move->user->name }}
                </div>
            </div>
            <div class="move-right">
                <div class="move-amount-val col-neutral" style="font-size:.8rem;">{{ $move->rate_text }}</div>
                @if($move->notes)<div class="move-amount-sub">{{ Str::limit($move->notes, 20) }}</div>@endif
            </div>
        </div>
    @endif
@endforeach
