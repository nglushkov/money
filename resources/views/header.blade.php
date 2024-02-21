<div class="col-12">
    <nav class="navbar navbar-expand-lg bg-success bg-gradient" data-bs-theme="dark">
        <div class="container-fluid">
{{--            <a class="navbar-brand" href="{{ route('home') }}">💵 Money</a>--}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('home') }}">🏠 Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('operations.index') }}">💰 Operations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('bills.index') }}">💳 Bills</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('categories.index') }}">📒 Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('places.index') }}">🏢 Places</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            More
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('planned-expenses.index') }}">📅 Planned Expenses</a></li>
                            <li><a class="dropdown-item" href="{{ route('transfers.index') }}">🔁 Transfers</a></li>
                            <li><a class="dropdown-item" href="{{ route('exchanges.index') }}">🔁 Exchanges</a></li>
                            <li><a class="dropdown-item" href="{{ route('external-rates.index') }}">💹 External Rates</a></li>
                            <li><a class="dropdown-item" href="{{ route('currencies.show', \App\Models\Currency::default()->first()) }}">⛩️ Default Currency</a></li>
                            <li><a class="dropdown-item" href="{{ route('currencies.index') }}">💱 Currencies</a></li>
                            <!-- <li>
                                <hr class="dropdown-divider">
                            </li> -->
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Reports
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('reports.sum-by-categories') }}">📊 Sum by Categories</a></li>
                        </ul>
                    </li>
                    @if (Auth::check())
                    <li class="nav-item">
                        <a href="#" class="nav-link disabled">{{ Auth::user()->name }}</a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}">🚪 Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
