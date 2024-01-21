<?php

namespace App\Helpers\Managers;

use App\Models\Notification;
use App\Models\User;

class NotificationManager
{
    /**
     * Create a notification for the given user.
     *
     * @param User $user
     * @param int $type
     * @param array $payload
     * @return void
     */
    public static function Notify(User $user, int $type, array $payload): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'payload' => json_encode($payload)
        ]);
    }
}
