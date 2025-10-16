<?php

namespace App\Support;

use Illuminate\Notifications\Notifiable;

class SystemNotifier
{
    use Notifiable;

    public function routeNotificationForMail(): string
    {
        return env('NOTIFICATION_MAIL', 'teste@example.com');
    }
}
