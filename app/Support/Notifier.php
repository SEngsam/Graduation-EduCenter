<?php

namespace App\Support;

use Illuminate\Support\Facades\Notification;
use Throwable;

class Notifier
{
    public static function send($users, $notification)
    {
        try {
            Notification::send($users, $notification);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
