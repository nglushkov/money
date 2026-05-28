<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MercadoPagoMapping;
use Illuminate\Http\Request;

class MercadoPagoMappingController extends Controller
{
    public function index()
    {
        $mappings = MercadoPagoMapping::with('category')->orderBy('is_default')->orderBy('keyword')->get();

        return view('mercadopago-mappings.index', compact('mappings'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('mercadopago-mappings.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'keyword'     => 'required|string|unique:mercado_pago_mappings,keyword',
            'category_id' => 'required|exists:categories,id',
            'place_name'  => 'nullable|string|max:255',
            'is_default'  => 'boolean',
        ]);

        $data['is_default'] = $request->boolean('is_default');

        MercadoPagoMapping::create($data);

        return redirect()->route('mercadopago-mappings.index');
    }

    public function edit(MercadoPagoMapping $mercadopagoMapping)
    {
        $categories = Category::orderBy('name')->get();

        return view('mercadopago-mappings.edit', [
            'mapping'    => $mercadopagoMapping,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, MercadoPagoMapping $mercadopagoMapping)
    {
        $data = $request->validate([
            'keyword'     => 'required|string|unique:mercado_pago_mappings,keyword,' . $mercadopagoMapping->id,
            'category_id' => 'required|exists:categories,id',
            'place_name'  => 'nullable|string|max:255',
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
