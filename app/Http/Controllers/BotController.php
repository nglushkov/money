<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BotController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $telegram = new Api('6579986722:AAHGHHcKjOIFROkXNPeQcTEffL9bN-3La04');
        $telegram->setWebhook(['url' => 'https://money.nglushkov.com/6579986722:AAHGHHcKjOIFROkXNPeQcTEffL9bN-3La04/webhook']);

        $update = $telegram->commandsHandler(true);

        $chatId = $update->getMessage()->getChat()->getId();
        $text = $update->getMessage()->getText();

        logger($chatId, $text);
    }
}
