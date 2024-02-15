<?php

namespace App\Http\Controllers;

use App\Models\ExternalRate;
use Illuminate\Http\Request;

class ExternalRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('external-rates.index', [
            'externalRates' => ExternalRate::orderBy('date', 'desc')
                ->orderBy('from_currency_id')
                ->orderBy('to_currency_id')
                ->paginate(50),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ExternalRate $externalRate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExternalRate $externalRate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExternalRate $externalRate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExternalRate $externalRate)
    {
        //
    }
}
