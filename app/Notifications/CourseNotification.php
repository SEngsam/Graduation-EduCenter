<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $type;
    public $params;
    public $course;
    public $url;

    public function __construct($type, $params, $course, $url)
    {
        $this->type = $type;
        $this->params = $params;
        $this->course = $course;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        if ($notifiable->email && $notifiable->wantsEmail($this->type)) {
            return ['database', 'mail'];
        }

        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'params' => $this->params,
            'course' => $this->course,
            'url' => $this->url,
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('notify.subject_' . $this->type, $this->params))
            ->view('emails.notification', [
                'name' => $notifiable->name,
                'type' => $this->type,
                'params' => $this->params,
                'course' => $this->course,
                'url' => $this->url,
            ]);
    }
}
