<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Bill;
use App\Models\Operation;
use App\Models\User;
use App\Service\P2PService;
use Illuminate\Http\Request;

class P2PController extends Controller
{
    public function __construct(private readonly P2PService $p2pService) {}

    public function create(Request $request)
    {
        $operation = $request->filled('from_operation')
            ? Operation::findOrFail($request->integer('from_operation'))
            : null;

        $bybitBill = ($id = (int) AppSetting::get('p2p_bybit_bill_id'))
            ? Bill::find($id)
            : null;

        return view('p2p.create', [
            'operation' => $operation,
            'bybitBill' => $bybitBill,
            'bills'     => Bill::with('user')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'              => 'required|date',
            'usdt_amount'       => 'required|numeric|min:0.01',
            'ars_amount'        => 'required|numeric|min:0.01',
            'bybit_bill_id'     => 'required|integer|exists:bills,id',
            'from_operation_id' => 'nullable|integer|exists:operations,id',
        ]);

        $sourceOperation = isset($data['from_operation_id'])
            ? Operation::findOrFail($data['from_operation_id'])
            : null;

        $this->p2pService->create($data, $sourceOperation);

        return redirect()->route('home')->with('success', 'P2P операция создана');
    }
}
