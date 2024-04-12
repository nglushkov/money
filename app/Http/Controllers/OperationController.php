<?php

namespace App\Http\Controllers;

use App\Enum\StorageFilePath;
use App\Http\Requests\StoreOperationRequest;
use App\Http\Requests\UpdateOperationRequest;
use App\Models\Enum\OperationType;
use App\Models\Operation;
use App\Models\Bill;
use App\Models\Category;
use App\Models\PlannedExpense;
use App\Models\Scopes\IsNotCorrectionScope;
use App\Service\OperationService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Place;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $operations = Operation::orderBy('date', 'desc')->withoutGlobalScope(IsNotCorrectionScope::class)->latest();

        if ($request->get('bill_id')) {
            $operations->where('bill_id', $request->bill_id);
        }

        if ($request->get('category_id')) {
            $operations->where('category_id', $request->category_id);
        }

        if ($request->get('currency_id')) {
            $operations->where('currency_id', $request->currency_id);
        }

        if ($request->get('date_from')) {
            $operations->where('date', '>=', $request->date_from);
        }

        if ($request->get('date_to')) {
            $operations->where('date', '<=', $request->date_to);
        }

        if (in_array($request->get('type'), OperationType::names())) {
            $operations->where('type', $request->type);
        }

        if ($request->get('user_id')) {
            $operations->where('user_id', $request->user_id);
        }

        if ($request->get('place_id')) {
            $operations->where('place_id', $request->place_id);
        }

        if ($request->get('notes')) {
            $operations->where('notes', 'like', '%' . $request->notes . '%');
        }

        if ($request->get('amount_from')) {
            $operations->where('amount', '>=', $request->amount_from);
        }

        if ($request->get('amount_to')) {
            $operations->where('amount', '<=', $request->amount_to);
        }

        return view('operations.index', [
            'operations' => $operations->isNotDraft()->with(['bill', 'category', 'user', 'place', 'currency'])->paginate(50),
            'bills' => Bill::orderBy('name', 'asc')->get(),
            'categories' => Category::orderBy('name', 'asc')->get(),
            'users' => User::orderBy('name', 'asc')->get(),
            'places' => Place::orderBy('name', 'asc')->get(),
            'currencies' => Currency::orderBy('name', 'asc')->get(),
            'defaultCurrency' => Currency::getDefaultCurrency(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->get('planned_expense_id')) {
            $plannedExpense = PlannedExpense::findOrFail($request->planned_expense_id);
        }

        return view('operations.create', [
            'bills' => Bill::orderBy('name', 'asc')->get(),
            'categories' => Category::orderBy('name', 'asc')->get(),
            'users' => User::orderBy('name', 'asc')->get(),
            'places' => Place::orderBy('name', 'asc')->get(),
            'currencies' => Currency::orderBy('name', 'asc')->get(),
            'topCategories' => $this->getTopCategories(),
            'topPlaces' => $this->getTopPlaces(),
            'topBills' => $this->getTopBills(),
            'plannedExpense' => $plannedExpense ?? null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOperationRequest $request)
    {
        $operation = new Operation();
        $operation->fill($request->validated());

        DB::transaction(function () use ($request, $operation) {
            $operation->user_id = auth()->id();
            $operation->save();

            if ($request->has('attachment')) {
                $originalName = $request->file('attachment')->getClientOriginalName();
                $request->file('attachment')->storeAs(
                    StorageFilePath::OperationAttachments->value, $this->getAttachmentFileNameEncrypted($operation->id, $originalName)
                );

                $operation->attachment = basename($originalName);
            }
            $operation->save();
        });

        return redirect()->route('home');
    }

    private function getAttachmentFileNameEncrypted(int $operationId, string $fileName): string
    {
        return md5($operationId . $fileName);
    }

    /**
     * Display the specified resource.
     */
    public function show(Operation $operation)
    {
        return view('operations.show', [
            'operation' => $operation,
            'defaultCurrency' => Currency::getDefaultCurrency(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Operation $operation)
    {
        Session::put('index_url', $request->headers->get('referer'));

        return view('operations.edit', [
            'operation' => $operation,
            'bills' => Bill::orderBy('name', 'asc')->get(),
            'categories' => Category::orderBy('name', 'asc')->get(),
            'users' => User::orderBy('name', 'asc')->get(),
            'places' => Place::orderBy('name', 'asc')->get(),
            'currencies' => Currency::orderBy('name', 'asc')->get(),
            'topCategories' => $this->getTopCategories(),
            'topPlaces' => $this->getTopPlaces(),
            'topBills' => $this->getTopBills(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOperationRequest $request, Operation $operation)
    {
        DB::transaction(function () use ($request, $operation) {
            $operation->fill($request->validated());
            $operation->date = $request->date;
            $operation->is_draft = false;
            $operation->save();

            if ($request->has('attachment')) {
                $originalName = $request->file('attachment')->getClientOriginalName();
                $encryptedFileName = $this->getAttachmentFileNameEncrypted($operation->id, $originalName);

                if ($operation->attachment) {
                    Storage::delete(StorageFilePath::OperationAttachments->value . '/' . $encryptedFileName);
                }
                $request->file('attachment')->storeAs(StorageFilePath::OperationAttachments->value, $encryptedFileName);

                $operation->attachment = $originalName;
                $operation->save();
            }
        });

        return Session::has('index_url') ? redirect(Session::get('index_url')) : back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Operation $operation)
    {
        DB::transaction(function () use ($request, $operation) {
            $operation->delete();

            Storage::delete(StorageFilePath::OperationAttachments->value . '/' . $this->getAttachmentFileNameEncrypted($operation->id, $operation->attachment));
        });

        if ($request->has('back_route')) {
            return redirect($request->back_route);
        }
        return redirect()->route('operations.index');
    }

    public function getAttachment(Operation $operation)
    {
        $response = response()->file(
            Storage::path(
                StorageFilePath::OperationAttachments->value . '/' . $this->getAttachmentFileNameEncrypted($operation->id, $operation->attachment)
            )
        );
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');

        return $response;
    }

    public function deleteAttachment(Operation $operation)
    {
        Storage::delete(StorageFilePath::OperationAttachments->value . '/' . $this->getAttachmentFileNameEncrypted($operation->id, $operation->attachment));
        $operation->attachment = null;
        $operation->save();

        return redirect()->back();
    }

    public function createDraft(Request $request, OperationService $operationService)
    {
        try {
            $operationService->createDraft($request->get('raw_text'), auth()->id());
        } catch (\InvalidArgumentException $e) {
            Session::flash('error', $e->getMessage());
        }

        return redirect()->route('home');
    }

    private function getTopCategories()
    {
        return Category::select('categories.*', DB::raw('COUNT(operations.category_id) as count'))
            ->leftJoin('operations', 'categories.id', '=', 'operations.category_id')
            ->groupBy('categories.id')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();
    }

    private function getTopPlaces()
    {
        return Place::select('places.*', DB::raw('COUNT(operations.place_id) as count'))
            ->leftJoin('operations', 'places.id', '=', 'operations.place_id')
            ->groupBy('places.id')
            ->orderBy('count', 'desc')
            ->take(15)
            ->get();
    }

    private function getTopBills()
    {
        return Bill::select('bills.*', DB::raw('COUNT(operations.bill_id) as count'))
            ->leftJoin('operations', 'bills.id', '=', 'operations.bill_id')
            ->groupBy('bills.id')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();
    }
}
