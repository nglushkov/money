<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Operation;
use Illuminate\Http\Request;
use Telegram\Bot\Api;

class BotController extends Controller
{
    const USER_IDS = [
        1 => 106809815,
        2 => 61089668,
    ];

    private $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    public function handleWebhook()
    {
        $updates = $this->telegram->getWebhookUpdate();
        $message = $updates->getMessage();

        $userId = $message->from->id;
        $text = $message->text;

        logger()->info('Message received', [
            'message' => $text,
            'user_id' => $userId,
        ]);

        logger()->info('test', [
            'user_ids' => self::USER_IDS,
            'user_id' => $userId,
            'in_array' => in_array($userId, self::USER_IDS),
        ]);

        if (!in_array($userId, self::USER_IDS)) {
            $this->telegram->sendMessage([
                'chat_id' => $userId,
                'text' => 'You are not allowed to use this bot',
            ]);

            logger()->warning('User not allowed', [
                'user_id' => $userId,
                'text' => $text,
            ]);
            return;
        }

        $this->createExpense($text, $userId);
    }

    private function createExpense(string $text, int $userId)
    {
        try {
            $text = explode(' ', $text);
            $amount = $text[0];
            if (!is_numeric($amount)) {
                $this->telegram->sendMessage([
                    'chat_id' => $userId,
                    'text' => 'Invalid amount',
                ]);

                logger()->warning('Invalid amount', [
                    'user_id' => $userId,
                    'text' => $text,
                ]);
                return;
            }
            $categoryName = $text[1] ?? '';

            $category = null;
            if ($categoryName) {
                $category = Category::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($categoryName) . '%'])->first();
            }
            $bill = Bill::default()->firstOrFail();
            $currency = Currency::active()->firstOrFail();

            $operation = new Operation();
            $operation->amount = $amount;

            if ($category) {
                $operation->category_id = $category->id;
            } else {
                $operation->notes = $categoryName;
            }

            $operation->bill_id = $bill->id;
            $operation->currency_id = $currency->id;
            $operation->date = date('Y-m-d');
            $operation->type = 1;

            $operation->user_id = array_search($userId, self::USER_IDS);
            if ($operation->user_id === false) {
                $operation->user_id = 1;
            }

            $operation->is_draft = true;

            $operation->save();

            if ($category) {
                $this->telegram->sendMessage([
                    'chat_id' => $userId,
                    'text' => sprintf('Expense of %s %s for %s created', $amount, $currency->name, $category->name)
                ]);
            } else if (strlen($operation->notes) > 0) {
                $this->telegram->sendMessage([
                    'chat_id' => $userId,
                    'text' => sprintf('Expense of %s %s with notes %s created', $amount, $currency->name, $operation->notes)
                ]);
            } else {
                $this->telegram->sendMessage([
                    'chat_id' => $userId,
                    'text' => sprintf('Expense of %s %s created', $amount, $currency->name)
                ]);
            }
        } catch (\Exception $e) {
            $this->telegram->sendMessage([
                'chat_id' => $userId,
                'text' => 'Error creating expense: ' . $e->getMessage(),
            ]);

            logger()->error('Error creating expense', [
                'user_id' => $userId,
                'text' => $text,
                'exception' => $e,
            ]);
        }
    }
}
