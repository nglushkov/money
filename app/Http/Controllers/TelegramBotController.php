<?php

namespace App\Http\Controllers;

use App\Helpers\MoneyFormatter;
use App\Models\Bill;
use App\Models\Currency;
use App\Service\OperationService;
use App\Service\ReportService;
use App\Service\TelegramBotService;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramBotController extends Controller
{
    const COMMAND_REPORT = '/report';
    const COMMAND_BALANCE = '/balance';

    private Api $telegram;
    private ReportService $reportService;

    private OperationService $operationService;

    private TelegramBotService $telegramBotService;

    public function __construct(ReportService $reportService, OperationService $operationService, TelegramBotService $telegramBotService)
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $this->reportService = $reportService;
        $this->operationService = $operationService;
        $this->telegramBotService = $telegramBotService;
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

        if (!in_array($userId, $this->telegramBotService->getUserIds())) {
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

        switch ($text) {
            case self::COMMAND_REPORT:
                $this->handleReportCommand($userId);
                break;
            case self::COMMAND_BALANCE:
                $this->handleBalanceCommand($userId);
                break;
            default:
                $this->createExpense($text, $userId);
        }
    }

    /**
     * @param string $text
     * @param int $telegramUserId
     * @return void
     * @throws TelegramSDKException
     * @throws \Exception
     */
    private function createExpense(string $text, int $telegramUserId)
    {
        $userId = $this->telegramBotService->getUserIdByTelegramUserId($telegramUserId);

        try {
            $this->operationService->createDraft($text, $userId);

            $this->telegram->sendMessage([
                'chat_id' => $telegramUserId,
                'text' => 'Expense created',
            ]);

        } catch (\InvalidArgumentException $e) {
            $this->telegram->sendMessage([
                'chat_id' => $telegramUserId,
                'text' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            $this->telegram->sendMessage([
                'chat_id' => $telegramUserId,
                'text' => 'Error creating expense: ' . $e->getMessage(),
            ]);
        }
    }

    private function handleReportCommand(int $userId): void
    {
        $month = date('n');
        $year = date('Y');

        // @todo: move together with \App\Service\ReportService::getOperations
        $operations = $this->reportService->getOperations($month, $year);

        $total = $operations->map(function ($operation) {
            return $operation->amount_in_default_currency;
        })->sum();
        $total = MoneyFormatter::getWithCurrencyName($total, Currency::getDefaultCurrencyName());

        $data = $this->reportService->getTotalByCategories(
            $operations,
            Currency::getDefaultCurrencyName()
        );
        $text = 'Report for ' . date('F Y') . ':' . PHP_EOL;
        $text .= 'Total: ' . $total . PHP_EOL . PHP_EOL;
        foreach ($data as $value) {
            $text .= $value['categoryName'] . ': ' . $value['total'] . PHP_EOL;
        }

        $this->telegram->sendMessage([
            'chat_id' => $userId,
            'text' => $text,
        ]);
    }

    /**
     * @throws TelegramSDKException
     * @throws \Exception
     */
    public function handleBalanceCommand(int $telegramUserId): void
    {
        $userId = $this->telegramBotService->getUserIdByTelegramUserId($telegramUserId);
        /** @var Bill[] $bills */
        $bills = Bill::userIdOrNull($userId)->orderBy('name')->get();

        $messageText = '';
        foreach ($bills as $bill) {
            $amounts = $bill->getAmountsNotNull();
            $billName = $bill->default ? '💰 ' . $bill->name : $bill->name;
            if (count($amounts) === 0) {
                continue;
            } else if (count($amounts) > 1) {
                $messageText .= $billName . ": \n";
            }
            foreach ($amounts as $amount) {
                if (count($amounts) === 1) {
                    $messageText .= $billName . ': <b>' . MoneyFormatter::get($amount->getAmount()) . ' ' . $amount->getCurrency()->name . '</b>';
                } else {
                    $messageText .= MoneyFormatter::get($amount->getAmount()) . ' ' . $amount->getCurrency()->name . "\n";
                }
            }
            $messageText .= "\n";
            if (count($amounts) === 1) {
                $messageText .= "\n";
            }
        }

        $this->telegram->sendMessage([
            'chat_id' => $telegramUserId,
            'text' => $messageText,
            'parse_mode' => 'html',
        ]);
    }

    public function getUserIds(): array
    {
        $userIds = env('TELEGRAM_USER_IDS', '');
        $userIds = json_decode($userIds, true);

        if (empty($userIds)) {
            throw new \Exception('TELEGRAM_USER_IDS is not set');
        }

        return $userIds;
    }
}
