@php
    use App\Models\Enum\OperationType;

    $hasAdvanced = request()->hasAny(['bill_id','type','user_id','place_id','notes','amount_from','amount_to','currency_id','external_source']);
    $isMyOps = request('user_id') == Auth::id();

    $myParams = request()->all();
    if ($isMyOps) {
        unset($myParams['user_id']);
    } else {
        $myParams['user_id'] = Auth::id();
    }
@endphp

<form autocomplete="off" action="{{ route('operations.index') }}" method="GET">
    <div class="page-toolbar">
        <div class="toolbar-left filter-pills-scroll">
            <a href="{{ route('operations.index') }}"
               class="filter-pill {{ !request()->hasAny(['category_id','bill_id','date_from','date_to','type','user_id','place_id','notes','amount_from','amount_to','currency_id','external_source']) ? 'pill-active' : '' }}">
                All
            </a>
            <a href="{{ route('operations.index', $myParams) }}"
               class="filter-pill {{ $isMyOps ? 'pill-active' : '' }}">
                My
            </a>

            <input type="date" name="date_from"
                   class="form-control form-control-sm"
                   style="width:auto;border-radius:var(--radius-sm);border-color:var(--c-border);font-size:.8rem;"
                   value="{{ request('date_from') }}">

            <span style="color:var(--c-muted);flex-shrink:0;">—</span>

            <input type="date" name="date_to"
                   class="form-control form-control-sm"
                   style="width:auto;border-radius:var(--radius-sm);border-color:var(--c-border);font-size:.8rem;"
                   value="{{ request('date_to') }}">

            <select name="category_id"
                    class="form-select form-select-sm"
                    style="width:auto;max-width:150px;border-radius:var(--radius-sm);border-color:var(--c-border);font-size:.8rem;">
                <option value="">Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>

            <button type="button"
                    class="filter-pill {{ $hasAdvanced ? 'pill-active' : '' }}"
                    onclick="document.getElementById('ops-adv-filters').classList.toggle('d-none')">
                <i class="bi bi-sliders"></i> Filters
                @if($hasAdvanced)
                    <span style="display:inline-block;width:.4rem;height:.4rem;background:currentColor;border-radius:50%;margin-left:.2rem;opacity:.8;vertical-align:middle;"></span>
                @endif
            </button>
        </div>

        <div class="toolbar-right">
            <button type="submit" class="btn btn-sm btn-success">
                <i class="bi bi-search"></i> Apply
            </button>
            <a href="{{ route('operations.index') }}" class="btn btn-sm btn-outline-secondary">
                Clear
            </a>
        </div>
    </div>

    <div id="ops-adv-filters"
         class="{{ $hasAdvanced || request('show_filter') ? '' : 'd-none' }}"
         style="margin-top:.75rem;padding:.875rem;background:var(--c-bg);border-radius:var(--radius-sm);border:1px solid var(--c-border);">
        <div class="row g-2">
            <div class="col-6 col-md-3">
                <label class="form-label">Bill</label>
                <select name="bill_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($bills as $bill)
                        <option value="{{ $bill->id }}" @selected(request('bill_id') == $bill->id)>{{ $bill->name_with_user }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach(OperationType::cases() as $t)
                        <option value="{{ $t->name }}" @selected(request('type') === $t->name)>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Place</label>
                <select name="place_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($places as $place)
                        <option value="{{ $place->id }}" @selected(request('place_id') == $place->id)>{{ $place->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Currency</label>
                <select name="currency_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" @selected(request('currency_id') == $currency->id)>{{ $currency->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Amount</label>
                <div class="d-flex gap-1">
                    <input type="number" autocomplete="off" name="amount_from" class="form-control form-control-sm"
                           placeholder="From" value="{{ request('amount_from') }}">
                    <input type="number" autocomplete="off" name="amount_to" class="form-control form-control-sm"
                           placeholder="To" value="{{ request('amount_to') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Notes</label>
                <input type="text" autocomplete="off" name="notes" class="form-control form-control-sm"
                       value="{{ request('notes') }}">
            </div>
            <div class="col-6 col-md-3 d-flex align-items-end pb-1">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                           name="external_source" value="mercadopago"
                           id="mp_filter"
                           @checked(request('external_source') === 'mercadopago')>
                    <label class="form-check-label" for="mp_filter" style="font-size:.8rem;">
                        Mercado Pago only
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
