<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Bill;
use Illuminate\Http\Request;

class AppSettingsController extends Controller
{
    public function index()
    {
        return view('settings.app', [
            'bills'              => Bill::orderBy('name')->get(),
            'reviewThreshold'    => AppSetting::get('mp_review_threshold', 300000),
            'p2pBybitBillName'   => AppSetting::get('p2p_bybit_bill_name', 'Bybit'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'mp_review_threshold' => 'required|numeric|min:0',
            'p2p_bybit_bill_name' => 'required|string|max:255',
        ]);

        foreach ($data as $key => $value) {
            AppSetting::set($key, $value);
        }

        return redirect()->route('settings.app')->with('success', 'Настройки сохранены');
    }
}
