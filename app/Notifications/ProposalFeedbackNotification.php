<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProposalFeedbackNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $proposal;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($proposal, $type = 'feedback')
    {
        $this->proposal = $proposal;
        $this->type = $type;
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
                    ->subject('Feedback Received: ' . $this->proposal->title)
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Your research proposal has received new feedback from a reviewer.')
                    ->line('Status: ' . strtoupper($this->proposal->status))
                    ->action('View Proposal Details', url('/proposal/' . $this->proposal->id))
                    ->line('Thank you for using our research portal!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'proposal_id' => $this->proposal->id,
            'title' => $this->proposal->title,
            'status' => $this->proposal->status,
            'message' => 'Your proposal has received new feedback and suggestions.',
            'icon' => 'bi-chat-left-text-fill',
            'color' => 'text-primary'
        ];
    }
}
