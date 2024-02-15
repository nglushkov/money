<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BotController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $telegram->setWebhook(['url' => 'https://money.nglushkov.com/]' . env('TELEGRAM_BOT_TOKEN') . '/webhook']);

        $updates = Telegram::getWebhookUpdate();

        $text = $updates->getMessage()->getText();
    
        logger()->info('Message received', ['message' => $text]);
    }
}
