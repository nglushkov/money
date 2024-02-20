@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card p-3">
                <h5 class="card-title mb-2 text-center">@yield('title')</h5>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="text-end pe-4">
                                <h5>Top 10 usable categories</h5>
                                <a href="{{ route('categories.create') }}" class="btn btn-outline-primary btn-sm">Create new category</a>
                                <ul class="list-group">
                                    @foreach($topCategories as $category)
                                        <li class="list-group">
                                            <a href="{{ route('operations.index', ['category_id' => $category->id]) }}" onclick="event.preventDefault(); document.getElementById('category').value = {{ $category->id }}; document.getElementById('form').submit();">{{ $category->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="mb-3"></div>
                                <h5>Top 5 usable bills</h5>
                                <ul class="list-group">
                                    @foreach($topBills as $bill)
                                        <li class="list-group">
                                            <a href="{{ route('operations.index', ['bill_id' => $bill->id]) }}" onclick="event.preventDefault(); document.getElementById('bill').value = {{ $bill->id }}; document.getElementById('form').submit();">{{ $bill->name_with_user }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        @yield('form')

                        <div class="col-md-4">
                            <div class="ps-3">
                                <h5>Top 15 usable places</h5>
                                <a href="{{ route('places.create') }}" class="btn btn-outline-primary btn-sm">Create new place</a>
                                <ul class="list-group">
                                    @foreach($topPlaces as $place)
                                        <li class="list-group">
                                            <a href="{{ route('operations.index', ['place_id' => $place->id]) }}" onclick="event.preventDefault(); document.getElementById('place').value = {{ $place->id }}; document.getElementById('form').submit();">{{ $place->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
