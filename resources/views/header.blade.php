<nav class="navbar navbar-expand-md navbar-money mb-0">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-wallet2"></i> Money
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain"
                aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list text-white" style="font-size:1.5rem;"></i>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}"><i class="bi bi-house me-1"></i>Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('bills.index') }}"><i class="bi bi-credit-card me-1"></i>Bills</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('crypto.index') }}"><i class="bi bi-graph-up me-1"></i>Crypto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('exchanger.create') }}">Exchanger</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('p2p.create') }}">P2P</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Reports
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('reports.total-by-categories') }}">
                            <i class="bi bi-bar-chart"></i> Total by Categories
                        </a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        More
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('operations.index') }}">
                            <i class="bi bi-list-ul"></i> All Operations
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('transfers.index') }}">
                            <i class="bi bi-arrow-left-right"></i> Transfers
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('exchanges.index') }}">
                            <i class="bi bi-currency-exchange"></i> Exchanges
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('planned-expenses.index') }}">
                            <i class="bi bi-calendar-check"></i> Planned Expenses
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('currencies.index') }}">
                            <i class="bi bi-cash-coin"></i> Currencies
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('categories.index') }}">
                            <i class="bi bi-tags"></i> Categories
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('places.index') }}">
                            <i class="bi bi-geo-alt"></i> Places
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('external-rates.index') }}">
                            <i class="bi bi-graph-up-arrow"></i> External Rates
                        </a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-gear"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('settings.app') }}">
                            <i class="bi bi-sliders"></i> General
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('users.index') }}">
                            <i class="bi bi-people"></i> Users
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('mercadopago-mappings.index') }}">
                            <i class="bi bi-bank"></i> MP Mappings
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
            @if (Auth::check())
                <span class="nav-link user-chip">
                    <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                </span>
            @endif
        </div>
    </div>
</nav>
