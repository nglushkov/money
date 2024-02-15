<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class BotController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $telegram->setWebhook(['url' => 'https://money.nglushkov.com/]' . env('TELEGRAM_BOT_TOKEN') . '/webhook']);

        $updates = Telegram::getWebhookUpdate();

        $message = $updates->getMessage();
    
        logger()->info('Message received', ['message' => $message]);
    }
}
