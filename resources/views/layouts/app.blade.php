<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @if (env('APP_ENV') === 'production')
    <div style="position: absolute; bottom: 0; left: 0; z-index: -1;">
        <a href="/get-cat-url"><img src="{{ url('/get-cat-url') }}" alt="Cat Image" style="width: 200px;"></a>
    </div>
    @endif

    <div class="container">
        <div class="row">
            @if (Auth::check())
                @include('header')
            @endif
            <div class="col-12">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: cornsilk;
        }
    </style>
</body>

</html>
