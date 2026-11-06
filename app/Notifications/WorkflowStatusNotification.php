<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WorkflowStatusNotification extends Notification implements ShouldQueue
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

    /**
     * Determine delivery channels (Database notification + Email).
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Deliver email if mail driver is configured and not null/log, and recipient has valid email
        if (config('mail.default') && !in_array(config('mail.default'), ['null', 'log']) && !empty($notifiable->email)) {
            // Avoid attempting dummy test domains if offline
            if (filter_var($notifiable->email, FILTER_VALIDATE_EMAIL) && !str_ends_with($notifiable->email, '@example.com')) {
                $channels[] = 'mail';
            }
        }

        return $channels;
    }

    /**
     * Email representation of the workflow update.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $proposalTitle = $this->proposal ? ($this->proposal->title ?? 'Research Proposal') : 'Research Update';
        $proposalCode = $this->proposal ? ($this->proposal->proposal_code ?? '') : '';

        $subject = 'URLC Portal: ' . $this->title;
        if ($proposalCode) {
            $subject .= ' [' . $proposalCode . ']';
        }

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . ($notifiable->name ?? 'Researcher') . '!')
            ->line($this->message);

        if ($this->proposal && isset($this->proposal->id)) {
            $mail->line('Research Title: "' . \Illuminate\Support\Str::limit($proposalTitle, 80) . '"')
                 ->action('View Proposal in Portal', url('/proposal/' . $this->proposal->id));
        } else {
            $mail->action('Go to URLC Dashboard', url('/dashboard'));
        }

        $mail->line('Thank you for using the University of Antique Research Lifecycle Platform.');

        return $mail;
    }

    /**
     * Catch mail delivery failures safely so in-app workflow is never blocked.
     */
    public function failed(\Throwable $exception)
    {
        Log::warning('Workflow email notification delivery failed: ' . $exception->getMessage());
    }

    /**
     * In-app database representation.
     */
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
