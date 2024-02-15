<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

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
        $response = $telegram->setWebhook(['url' => 'https://money.nglushkov.com/6579986722:AAHGHHcKjOIFROkXNPeQcTEffL9bN-3La04/webhook']);
        dd($response);
    }
}
