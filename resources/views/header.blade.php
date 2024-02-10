<div class="col-12">
    <nav class="navbar navbar-expand-lg bg-body-tertiary mb-2">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">Money</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('bills.index') }}">Bills</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            More
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('categories.index') }}">Categories</a></li>
                            <li><a class="dropdown-item" href="{{ route('places.index') }}">Places</a></li>
                            <li><a class="dropdown-item" href="{{ route('bills.index') }}">Bills</a></li>
                            <li><a class="dropdown-item" href="{{ route('currencies.index') }}">Currencies</a></li>
                            <li><a class="dropdown-item" href="{{ route('exchanges.index') }}">Exchanges</a></li>
                            <li><a class="dropdown-item" href="{{ route('transfers.index') }}">Transfers</a></li>
                            <!-- <li>
                                <hr class="dropdown-divider">
                            </li> -->
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link disabled">|</a>
                    </li>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link disabled">{{ Auth::user()->name }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>