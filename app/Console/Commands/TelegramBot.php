<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Api;

class TelegramBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:telegram-bot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $telegram = new Api('6579986722:AAHGHHcKjOIFROkXNPeQcTEffL9bN-3La04');
        $telegram->setWebhook(['url' => 'https://money.nglushkov.com/6579986722:AAHGHHcKjOIFROkXNPeQcTEffL9bN-3La04/webhook']);

        $updates = $telegram->getUpdates();

        foreach ($updates as $update) {
            $message = $update->getMessage();
            if ($message !== null) {
                $chat = $message->getChat();
                // Дальнейшая обработка чата...
            }
        }

        logger($chatId, $text, $message, $chat, $updates);
    }
}
