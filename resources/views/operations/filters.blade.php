@php use App\Models\Enum\OperationType; @endphp

<form action="{{ route('operations.index') }}" method="GET">
    <a href="{{ route('operations.create') }}" class="btn btn-success">Create</a>
    <a class="btn btn-light"
       href="{{ route('operations.index', array_merge(request()->all(), ['show_filter' => 1])) }}"
       role="button">
        Show filters
    </a>
    <a href="{{ route('operations.index', ['user_id' => Auth::id()]) }}" class="btn btn-light">Show My Operations</a>
    <a href="{{ route('operations.index') }}" class="btn btn-light float-end">Clear</a>
    <div class="collapse {{ request('show_filter') == '1' ? 'show' : '' }}" id="collapseFilter">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="bill_id">Bill</label>
                    <select name="bill_id" id="bill_id" class="form-control">
                        <option value="">All</option>
                        @foreach ($bills as $bill)
                            <option value="{{ $bill->id }}" @selected(request('bill_id') == $bill->id)>{{ $bill->name_with_user }}
                            </option>
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
                            <option
                                value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="date_from">Date from</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                           value="{{ request('date_from') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="date_to">Date to</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                           value="{{ request('date_to') }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="type">Type</label>
                    <select name="type" id="type" class="form-control">
                        <option value="">All</option>
                        @foreach(OperationType::cases() as $operationType)
                            <option value="{{ $operationType->name }}" @selected(request('type') === $operationType->name)>{{ $operationType->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="user_id">User</label>
                    <select name="user_id" id="user_id" class="form-control">
                        <option value="">All</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @if(request('user_id')==$user->id) selected @endif>{{
                            $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="place_id">Place</label>
                    <select name="place_id" id="place_id" class="form-control">
                        <option value="">All</option>
                        @foreach ($places as $place)
                            <option value="{{ $place->id }}" @if(request('place_id')==$place->id) selected @endif>{{
                            $place->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <input type="text" name="notes" id="notes" class="form-control" value="{{ request('notes') }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="amount_from">Amount from</label>
                    <input type="number" name="amount_from" id="amount_from" class="form-control"
                           value="{{ request('amount_from') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="amount_to">Amount to</label>
                    <input type="number" name="amount_to" id="amount_to" class="form-control"
                           value="{{ request('amount_to') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="currency_id">Currency</label>
                    <select name="currency_id" id="currency_id" class="form-control">
                        <option value="">All</option>
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->id }}" @if(request('currency_id')==$currency->id) selected @endif>{{
                            $currency->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <input type="hidden" name="show_filter" value="1">
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary align-bottom">Filter</button>
            </div>
        </div>
    </div>
</form>
