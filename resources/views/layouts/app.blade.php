<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
</head>

<body>

    <div class="container-fluid">
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
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2/dist/js/tom-select.complete.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('select:not([x-model])').forEach(function (el) {
                new TomSelect(el, { allowEmptyOption: true });
            });
        });
    </script>
    <style>
        body {
            background-color: cornsilk;
        }
    </style>
</body>

</html>
