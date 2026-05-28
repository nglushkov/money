<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MercadoPagoMapping;
use App\Models\Place;
use Illuminate\Http\Request;

class MercadoPagoMappingController extends Controller
{
    public function index()
    {
        $mappings = MercadoPagoMapping::with(['category', 'place'])->orderBy('is_default')->orderBy('keyword')->get();

        return view('mercadopago-mappings.index', compact('mappings'));
    }

    public function create()
    {
        return view('mercadopago-mappings.create', [
            'categories' => Category::orderBy('name')->get(),
            'places'     => Place::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'keyword'     => 'required|string|unique:mercado_pago_mappings,keyword',
            'category_id' => 'required|exists:categories,id',
            'place_id'    => 'nullable|exists:places,id',
            'is_default'  => 'boolean',
        ]);

        $data['is_default'] = $request->boolean('is_default');

        MercadoPagoMapping::create($data);

        return redirect()->route('mercadopago-mappings.index');
    }

    public function edit(MercadoPagoMapping $mercadopagoMapping)
    {
        return view('mercadopago-mappings.edit', [
            'mapping'    => $mercadopagoMapping,
            'categories' => Category::orderBy('name')->get(),
            'places'     => Place::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MercadoPagoMapping $mercadopagoMapping)
    {
        $data = $request->validate([
            'keyword'     => 'required|string|unique:mercado_pago_mappings,keyword,' . $mercadopagoMapping->id,
            'category_id' => 'required|exists:categories,id',
            'place_id'    => 'nullable|exists:places,id',
            'is_default'  => 'boolean',
        ]);

        $data['is_default'] = $request->boolean('is_default');

        $mercadopagoMapping->update($data);

        return redirect()->route('mercadopago-mappings.index');
    }

    public function destroy(MercadoPagoMapping $mercadopagoMapping)
    {
        $mercadopagoMapping->delete();

        return redirect()->route('mercadopago-mappings.index');
    }
}
