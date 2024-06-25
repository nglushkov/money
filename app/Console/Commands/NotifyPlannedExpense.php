<?php

namespace App\Console\Commands;

use App\Models\PlannedExpense;
use App\Service\PlannedExpenseService;
use App\Service\TelegramBotService;
use Illuminate\Console\Command;
use Telegram\Bot\Api;

class NotifyPlannedExpense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-planned-expense';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private Api $telegram;

    private PlannedExpenseService $plannedExpenseService;

    private TelegramBotService $telegramBotService;

    public function __construct(PlannedExpenseService $plannedExpenseService, TelegramBotService $telegramBotService)
    {
        parent::__construct();
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $this->plannedExpenseService = $plannedExpenseService;
        $this->telegramBotService = $telegramBotService;
    }


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $plannedExpenses = $this->plannedExpenseService->getPlannedExpensesForToday();
        foreach ($plannedExpenses as $plannedExpense) {
            $this->telegram->sendMessage([
                'chat_id' => $this->telegramBotService->getTelegramUserIdByUserId($plannedExpense->user_id),
                'text' => sprintf(
                    'You have planned expense today: %s by %s. %s/%s. Notes: %s',
                    $plannedExpense->amount_formatted,
                    $plannedExpense->bill->name,
                    $plannedExpense->category->name,
                    $plannedExpense->place->name,
                    $plannedExpense->notes
                )
            ]);
        }
    }
}
