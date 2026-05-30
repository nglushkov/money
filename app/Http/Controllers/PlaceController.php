<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaceRequest;
use App\Http\Requests\UpdatePlaceRequest;
use App\Models\Currency;
use App\Models\Place;

class PlaceController extends Controller
{
    public function index()
    {
        $places = Place::orderBy('name')->withCount('operations')->get();
        $defaultCurrency = Currency::getDefaultCurrency();

        return view('places.index', compact('places', 'defaultCurrency'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('places.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlaceRequest $request)
    {
        Place::create($request->validated());

        return redirect()->route('places.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Place $place)
    {
        $lastOperations = $place->operations()->with([
            'bill',
            'category',
            'currency',
            'place',
        ])->latestDate()->paginate(20);

        $totalSpent = $place->operations()->with('currency')->get()->sum('amount_in_default_currency');
        $operationsCount = $place->operations()->count();
        $lastOperationDate = $place->operations()->max('date');
        $defaultCurrency = Currency::getDefaultCurrency();

        return view('places.show', [
            'place' => $place,
            'lastOperations' => $lastOperations,
            'totalSpent' => $totalSpent,
            'operationsCount' => $operationsCount,
            'lastOperationDate' => $lastOperationDate,
            'defaultCurrency' => $defaultCurrency,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Place $place)
    {
        return view('places.edit', compact('place'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlaceRequest $request, Place $place)
    {
        $place->update($request->validated());

        return redirect()->route('places.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Place $place)
    {
        if ($place->operations()->count() > 0) {
            return redirect()->route('places.show', $place)->withErrors(['error' => 'This place is used in operations and cannot be deleted.']);
        }
        $place->delete();

        return redirect()->route('places.index');
    }
}
