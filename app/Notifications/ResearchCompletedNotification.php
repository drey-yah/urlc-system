<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResearchCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $proposal;
    protected $milestone;

    /**
     * Create a new notification instance.
     */
    public function __construct($proposal, $milestone)
    {
        $this->proposal = $proposal;
        $this->milestone = $milestone;
    }

    /**
     * Get the notification's delivery channels.
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
            ->subject('🎉 Implementation Report Approved — Research Completed!')
            ->greeting('Congratulations, ' . $notifiable->name . '!')
            ->line('Your implementation progress report has been approved by the admin.')
            ->line('Research Title: ' . $this->proposal->title)
            ->line('Approved Milestone: ' . $this->milestone->title)
            ->line('Your research is now marked as completed. You may proceed to submit your final manuscript.')
            ->action('View Research Details', url('/proposal/' . $this->proposal->id))
            ->line('Thank you for your dedication to this research!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'proposal_id'    => $this->proposal->id,
            'title'          => $this->proposal->title,
            'milestone'      => $this->milestone->title,
            'message'        => 'Your implementation report has been approved. Your research is now completed!',
            'icon'           => 'bi-trophy-fill',
            'color'          => 'text-success',
        ];
    }
}
