<?php

namespace App\Service;

use App\Models\AppSetting;
use App\Models\Bill;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Enum\OperationType;
use App\Models\Operation;
use App\Models\Place;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class OperationService
{
    /**
     * Create draft
     * todo: Refactor this
     * @param string $rawText
     * @param int $userId
     * @return void
     */
    public function createDraft(string $rawText, int $userId): void
    {
        $data = explode(' ', $rawText);

        $amount = $data[0];
        if (!is_numeric($amount)) {
            throw new \InvalidArgumentException('Invalid amount');
        }

        $noteData = [];
        $categoryText = $data[1] ?? null;
        if (!is_null($categoryText)) {
            $category = Category::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($categoryText) . '%'])->first();
        }
        if (!isset($category) && $categoryText !== null) {
            $noteData[] = $categoryText;
        }

        $placeText = $data[2] ?? null;
        if (!is_null($placeText)) {
            $place = Place::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($placeText) . '%'])->first();
        }
        if (!isset($place) && $placeText !== null) {
            $noteData[] = $placeText;
        }

        $note = implode(' ', $noteData);
        $bill = Bill::default()->firstOrFail();
        $currency = Currency::active()->firstOrFail();

        $operation = new Operation([
            'amount' => $amount,
            'type' => OperationType::Expense->name,
            'bill_id' => $bill->id,
            'currency_id' => $currency->id,
            'category_id' => $category->id ?? null,
            'place_id' => $place->id ?? null,
            'notes' => $note ?? null,
            'date' => Carbon::now(),
            'is_draft' => true,
        ]);
        $operation->user_id = $userId;
        $operation->save();
    }

    public function createFromExternal(array $data): ?Operation
    {
        if (Operation::where('external_id', $data['external_id'])->exists()) {
            return null;
        }

        $threshold = (float) AppSetting::get('mp_review_threshold', 300000);
        $reviewStatus = ($data['external_source'] === 'mercadopago' && (float) $data['amount'] >= $threshold)
            ? 'pending'
            : null;

        $operation = new Operation([
            'amount'            => $data['amount'],
            'type'              => $data['type'],
            'bill_id'           => $data['bill_id'],
            'currency_id'       => $data['currency_id'],
            'category_id'       => $data['category_id'],
            'place_id'          => $data['place_id'] ?? null,
            'date'              => $data['date'],
            'notes'             => $data['notes'] ?? null,
            'is_draft'          => false,
            'external_id'       => $data['external_id'],
            'external_source'   => $data['external_source'],
            'mp_review_status'  => $reviewStatus,
        ]);
        $operation->user_id = $data['user_id'];
        $operation->save();

        Log::info('MP operation created', ['external_id' => $data['external_id'], 'amount' => $data['amount']]);

        return $operation;
    }
}
