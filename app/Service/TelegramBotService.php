<?php

namespace App\Service;

class TelegramBotService
{
    public function getUserIds(): array
    {
        $userIds = env('TELEGRAM_USER_IDS', '');
        $userIds = json_decode($userIds, true);

        if (empty($userIds)) {
            throw new \Exception('TELEGRAM_USER_IDS is not set');
        }

        return $userIds;
    }

    public function getUserIdByTelegramUserId($telegramUserId): int
    {
        $userId = array_search($telegramUserId, $this->getUserIds());
        if ($userId === false) {
            throw new \Exception('User not found');
        }
        return $userId;
    }

    public function getTelegramUserIdByUserId($userId): int
    {
        $userIds = $this->getUserIds();
        if (!isset($userIds[$userId])) {
            throw new \Exception('Telegram user not found');
        }
        return $userIds[$userId];
    }
}
