<?php

namespace App\Http\Controllers;

use App\Enum\CacheKey;
use App\Http\Requests\StoreBillRequest;
use App\Http\Requests\UpdateBillRequest;
use App\Models\Bill;
use App\Models\BillCurrencyInitial;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bills = Bill::isNotCrypto();
        if ($request->get('user_id')) {
            $bills->where(function ($query) use ($request) {
                $query->where('user_id', $request->user_id)->orWhere('user_id', null);
            });
        }

        return view('bills.index', [
            'bills' => $bills->orderBy('name')->get(),
            'currencies' => Currency::isNotCrypto()->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bills.create', [
            'currencies' => Currency::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    /**
     * Скорректировать счет
     * @todo: Refactor
     *
     * @param Request $request
     * @param Bill $bill
     * @return \Illuminate\Http\RedirectResponse
     */
    public function correct(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|max:99999999',
            'currency_name' => 'required|exists:currencies,name'
        ]);
        if (!$validated) {
            return redirect()->route('bills.show', $bill)->withErrors($request->errors());
        }

        $bill->correctAmount($bill, $request->input('currency_name'), $request->input('amount'));
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBillRequest $request)
    {
        if (!$request->validated()) {
            return redirect()->route('bills.create')->withErrors($request->errors());
        }

        DB::transaction(function () use ($request) {
            $bill = new Bill($request->validated());
            $bill->save();

            foreach ($request->input('amount') as $currencyId => $amount) {
                if (is_null($amount) || $amount == 0) {
                    continue;
                }
                $billCurrency = new BillCurrencyInitial([
                    'bill_id' => $bill->id,
                    'currency_id' => $currencyId,
                    'amount' => $amount
                ]);
                $billCurrency->save();
            }
        });

        return redirect()->route('bills.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Bill $bill)
    {
        // todo: refactor this
        $currencyId = $request->get('currency_id');

        $operations = $bill->operations()->with([
            'bill',
            'category',
            'currency',
            'place',
            'user'
        ]);

        if ($currencyId !== null) {
            $operations->where('currency_id', $currencyId);
        }
        $operations = $operations->get();

        $transfers = Transfer::with([
            'from',
            'to',
            'currency',
            'user',
        ])->where(function ($query) use ($bill) {
            $query->orWhere('from_bill_id', $bill->id)
                ->orWhere('to_bill_id', $bill->id);
        });

        if ($currencyId !== null) {
            $transfers->where('currency_id', $currencyId);
        }
        $transfers = $transfers->get();

        $exchanges = Exchange::with([
            'from',
            'to',
            'bill',
            'user',
            'place'
        ])->where('bill_id', $bill->id);

        if ($currencyId !== null) {
            $exchanges->where(function ($query) use ($currencyId) {
                $query->orWhere('from_currency_id', $currencyId)
                    ->orWhere('to_currency_id', $currencyId);
            });;
        }
        $exchanges = $exchanges->get();

        $moves = $operations->concat($transfers)->concat($exchanges)->sortByDesc(function ($move) {
            return $move->date->format('U') . $move->created_at->format('U');
        });

        $paginator = $this->paginate($moves, 100);
        $moves = $paginator->items();
        $defaultCurrency = Currency::getDefaultCurrency();

        $currencies = Currency::orderBy('is_crypto')->orderBy('name')->get();

        return view('bills.show', [
            'bill' => $bill,
            'moves' => $moves,
            'paginator' => $paginator,
            'defaultCurrency' => $defaultCurrency,
            'currencies' => $currencies,
        ]);
    }

    // todo: refactor this, remove doubles
    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bill $bill)
    {
        return view('bills.edit', [
            'bill' => $bill,
            'currencies' => Currency::orderBy('name')->get(),
            'users' => User::orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBillRequest $request, Bill $bill)
    {
        DB::transaction(function () use ($request, $bill) {
            $bill->fill($request->validated());
            $bill->is_crypto = $request->has('is_crypto');
            $bill->save();
            $bill->currenciesInitial()->detach();

            foreach ($request->input('amount') as $currencyId => $amount) {
                if (is_null($amount) || $amount == 0) {
                    continue;
                }
                $billCurrency = new BillCurrencyInitial([
                    'bill_id' => $bill->id,
                    'currency_id' => $currencyId,
                    'amount' => $amount
                ]);
                $billCurrency->save();
                $bill->clearAmountCache();
            }
        });

        return redirect()->route('bills.show', $bill);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bill $bill)
    {
        if ($bill->operations()->count() > 0) {
            return redirect()->route('place.show', $bill)->withErrors(['error' => 'This bill is used in operations and cannot be deleted.']);
        }

        try {
            $bill->delete();
        } catch (\Exception $e) {
            return redirect()->route('bills.show', $bill)->with('error', 'Error deleting bill.');
        }

        return redirect()->route('bills.index');
    }
}
