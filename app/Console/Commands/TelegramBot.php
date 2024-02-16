<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;

class TelegramBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bot-set-webhook';

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
        $telegram->setWebhook(['url' => env('BOT_DOMAIN') . '/6579986722:AAHGHHcKjOIFROkXNPeQcTEffL9bN-3La04/webhook']);
    }
}
