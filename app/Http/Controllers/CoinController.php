<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCoinRequest;
use App\Http\Requests\UpdateCoinRequest;

class CoinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('coins.index', [
            'coins' => Coin::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('coins.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCoinRequest $request)
    {
        Coin::create($request->validated());

        return redirect()->route('coins.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coin $coin)
    {
        return view('coins.show', [
            'coin' => $coin,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coin $coin)
    {
        return view('coins.edit', [
            'coin' => $coin,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCoinRequest $request, Coin $coin)
    {
        if ($request->has('is_default')) {
            Coin::where('id', '!=', $coin->id)->update(['is_default' => false]);
        }
        $coin->update($request->validated());
        return redirect()->route('coins.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coin $coin)
    {
        $coin->delete();

        return redirect()->route('coins.index');
    }
}
