<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAnnouncementNotification extends Notification
{
    use Queueable;

    protected $announcement;

    /**
     * Create a new notification instance.
     */
    public function __construct($announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Call for Papers: ' . $this->announcement->title)
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('A new announcement has been posted on the URLC Research Portal.')
                    ->line('Title: ' . $this->announcement->title)
                    ->action('Read Announcement', url('/announcements'))
                    ->line('Stay updated with the latest research opportunities!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'message' => 'New announcement: ' . $this->announcement->title,
            'icon' => 'bi-bell-fill',
            'color' => 'text-warning'
        ];
    }
}
