@extends('layouts.app')

@section('content')
<div class="row g-3">

    {{-- Left: Quick categories + bills --}}
    <div class="col-md-3 order-2 order-md-1">
        <div class="quickpick-card">
            <div class="quickpick-label">
                Top Categories
                <a href="{{ route('categories.create') }}" class="text-muted" style="font-size:.75rem;">+ New</a>
            </div>
            @foreach($topCategories as $category)
                <a class="quickpick-item"
                   href="#"
                   onclick="event.preventDefault(); document.getElementById('category').tomselect.setValue({{ $category->id }});">
                    {{ $category->name }}
                </a>
            @endforeach

            <div class="quickpick-label mt-3">
                Top Bills
            </div>
            @foreach($topBills as $bill)
                <a class="quickpick-item"
                   href="#"
                   onclick="event.preventDefault(); document.getElementById('bill').tomselect.setValue({{ $bill->id }});">
                    {{ $bill->name_with_user }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Center: Form --}}
    <div class="col-md-6 order-1 order-md-2">
        <div class="form-card">
            <h5 class="fw-700 mb-4" style="font-weight:700;">@yield('title')</h5>
            @yield('form')
        </div>
    </div>

    {{-- Right: Quick places --}}
    <div class="col-md-3 order-3">
        <div class="quickpick-card">
            <div class="quickpick-label">
                Top Places
                <a href="{{ route('places.create') }}" class="text-muted" style="font-size:.75rem;">+ New</a>
            </div>
            @foreach($topPlaces as $place)
                <a class="quickpick-item"
                   href="#"
                   onclick="event.preventDefault(); document.getElementById('place').tomselect.setValue({{ $place->id }});">
                    {{ $place->name }}
                </a>
            @endforeach
        </div>
    </div>

</div>
@endsection
