<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CollaborationInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $proposal;
    public $inviter;

    public function __construct($proposal, $inviter)
    {
        $this->proposal = $proposal;
        $this->inviter = $inviter;
    }

    /**
     * Delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (config('mail.default') && !in_array(config('mail.default'), ['null', 'log']) && !empty($notifiable->email)) {
            if (filter_var($notifiable->email, FILTER_VALIDATE_EMAIL) && !str_ends_with($notifiable->email, '@example.com')) {
                $channels[] = 'mail';
            }
        }

        return $channels;
    }

    /**
     * Mail representation.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $title = $this->proposal ? ($this->proposal->title ?? 'Research Proposal') : 'Research Proposal';
        $inviterName = $this->inviter->name ?? 'A faculty researcher';

        return (new MailMessage)
            ->subject('Collaboration Invitation: "' . Str::limit($title, 50) . '" — URLC Portal')
            ->greeting('Hello ' . ($notifiable->name ?? 'Researcher') . '!')
            ->line($inviterName . ' has tagged and invited you as a co-proponent/collaborator on the research proposal:')
            ->line('"' . $title . '"')
            ->line('You may review the proposal details and choose to Accept or Decline this collaboration.')
            ->action('Review & Respond to Invitation', url('/proposal/' . $this->proposal->id))
            ->line('Thank you for participating in University Research at the University of Antique.');
    }

    public function failed(\Throwable $exception)
    {
        Log::warning('Collaboration invitation email failed: ' . $exception->getMessage());
    }

    /**
     * Database representation.
     */
    public function toArray($notifiable)
    {
        $inviterName = $this->inviter->name ?? 'A researcher';
        $title = $this->proposal ? ($this->proposal->title ?? 'Research Proposal') : 'Research Proposal';

        return [
            'proposal_id' => $this->proposal->id ?? null,
            'type' => 'collaboration_invitation',
            'title' => 'Collaboration Invitation',
            'message' => $inviterName . ' tagged and invited you to collaborate on "' . Str::limit($title, 60) . '".',
            'inviter_name' => $inviterName,
            'icon' => 'bi-person-plus-fill',
            'color' => 'text-primary',
            'action_url' => url('/proposal/' . ($this->proposal->id ?? '')),
        ];
    }
}
