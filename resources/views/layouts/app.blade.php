<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — Money</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body>

    @if (Auth::check())
        @include('header')
    @endif

    <div class="page-content">
        @yield('content')
    </div>

    @if (Auth::check())
    <nav class="mobile-bottom-nav">
        <a href="{{ route('home') }}" class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="bi bi-house-fill"></i>
            Home
        </a>
        <a href="{{ route('bills.index') }}" class="mobile-nav-link {{ request()->routeIs('bills.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card-fill"></i>
            Bills
        </a>
        <a href="{{ route('operations.create') }}" class="mobile-nav-link mobile-nav-fab">
            <i class="bi bi-plus-lg"></i>
        </a>
        <a href="{{ route('reports.total-by-categories') }}" class="mobile-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-fill"></i>
            Reports
        </a>
        <a href="{{ route('exchanger.create') }}" class="mobile-nav-link {{ request()->routeIs('exchanger.*') ? 'active' : '' }}">
            <i class="bi bi-currency-exchange"></i>
            Exchanger
        </a>
    </nav>
    @endif

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2/dist/js/tom-select.complete.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('select:not([x-model])').forEach(function (el) {
                new TomSelect(el, { allowEmptyOption: true });
            });
        });
    </script>
</body>
</html>
