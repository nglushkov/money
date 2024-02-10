<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-12">
            <nav class="navbar navbar-light bg-light">
                <a class="navbar-brand" href="{{ route('home') }}">Home</a>
                <a class="navbar-brand" href="{{ route('operations.index') }}">Operations</a>
                <a class="navbar-brand" href="{{ route('categories.index') }}">Categories</a>
                <a class="navbar-brand" href="{{ route('places.index') }}">Places</a>
                <a class="navbar-brand" href="{{ route('currencies.index') }}">Currencies</a>
                <a class="navbar-brand" href="{{ route('bills.index') }}">Bills</a>
            </nav>
        <div class="col-12">
            @yield('content')
        </div>
    </div>
</div>

<script src="/assets/js/bootstrap.min.js"></script>
</body>
</html>
