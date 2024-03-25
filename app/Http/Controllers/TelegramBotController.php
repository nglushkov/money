<?php

namespace App\Http\Controllers;

use App\Helpers\MoneyFormatter;
use App\Models\Currency;
use App\Service\OperationService;
use App\Service\ReportService;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramBotController extends Controller
{
    const COMMAND_REPORT = '/report';
    const COMMANDS = [
        self::COMMAND_REPORT,
    ];

    private $telegram;
    private ReportService $reportService;

    private OperationService $operationService;

    public function __construct(Api $telegram, ReportService $reportService, OperationService $operationService)
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $this->reportService = $reportService;
        $this->operationService = $operationService;
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

        if (!in_array($userId, $this->getUserIds())) {
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

        if ($text === self::COMMAND_REPORT) {
            $this->handleReportCommand($userId);
        } else {
            $this->createExpense($text, $userId);
        }
    }

    /**
     * @param string $text
     * @param int $telegramUserId
     * @return void
     * @throws TelegramSDKException
     */
    private function createExpense(string $text, int $telegramUserId)
    {
        $userId = $this->getUserIdByTelegramUserId($telegramUserId);

        try {
            $this->operationService->createDraft($text, $userId);

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

        $this->telegram->sendMessage([
            'chat_id' => $telegramUserId,
            'text' => 'Expense created',
        ]);
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

    public function getUserIds(): array
    {
        $userIds = env('TELEGRAM_USER_IDS', '');
        $userIds = json_decode($userIds, true);

        if (empty($userIds)) {
            throw new \Exception('TELEGRAM_USER_IDS is not set');
        }

        return $userIds;
    }

    private function getUserIdByTelegramUserId($telegramUserId): int
    {
        $userId = array_search($telegramUserId, $this->getUserIds());
        if ($userId === false) {
            return 1;
        }
        return $userId;
    }
}
