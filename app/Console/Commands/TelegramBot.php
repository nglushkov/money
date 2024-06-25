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
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $url = env('TELEGRAM_BOT_DOMAIN') . '/' . env('TELEGRAM_BOT_TOKEN') . '/webhook';
        logger()->info('Setting webhook', ['url' => $url]);
        $telegram->setWebhook(['url' => $url]);
    }
}
