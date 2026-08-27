<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkflowStatusNotification extends Notification
{
    use Queueable;

    public $proposal;
    public $title;
    public $message;
    public $icon;
    public $color;

    public function __construct($proposal, $title, $message, $icon = 'bi-bell-fill', $color = 'text-primary')
    {
        $this->proposal = $proposal;
        $this->title = $title;
        $this->message = $message;
        $this->icon = $icon;
        $this->color = $color;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'proposal_id' => $this->proposal->id ?? null,
            'title' => $this->title,
            'status' => $this->proposal->status ?? 'updated',
            'message' => $this->message,
            'icon' => $this->icon,
            'color' => $this->color
        ];
    }
}
