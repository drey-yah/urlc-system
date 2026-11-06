<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ProposalAnnotation;

class ProposalAnnotationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $annotation;
    protected $actorName;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProposalAnnotation $annotation, string $actorName)
    {
        $this->annotation = $annotation;
        $this->actorName = $actorName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Deliver to database, and attempt mail if configured
        $channels = ['database'];
        if (config('mail.default') && config('mail.default') !== 'null') {
            $channels[] = 'mail';
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $proposal = $this->annotation->proposal;
        $textExcerpt = $this->annotation->highlighted_text ? \Illuminate\Support\Str::limit($this->annotation->highlighted_text, 120) : null;

        $mail = (new MailMessage)
            ->subject('New Document Revision Note: ' . ($proposal->proposal_code ?? 'Proposal') . ' - ' . \Illuminate\Support\Str::limit($proposal->title, 40))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->actorName . ' added a document review note on ' . $this->annotation->chapter_section . '.');

        if ($textExcerpt) {
            $mail->line('Referenced Passage: "' . $textExcerpt . '"');
        }

        $mail->line('Suggestion: "' . $this->annotation->comment . '"')
             ->action('Open Review Workspace', url('/proposal/' . $proposal->id . '/review-workspace'))
             ->line('Thank you for your active collaboration in University of Antique Research!');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $proposal = $this->annotation->proposal;
        $isTagged = $this->annotation->tagged_user_id === $notifiable->id;

        return [
            'proposal_id' => $proposal->id,
            'title' => $proposal->title,
            'status' => $proposal->status,
            'message' => $isTagged 
                ? "{$this->actorName} tagged you in a review note on {$this->annotation->chapter_section}."
                : "{$this->actorName} added a review note on {$this->annotation->chapter_section}.",
            'annotation_id' => $this->annotation->id,
            'url' => route('proposal.reviewWorkspace', $proposal->id),
            'icon' => 'bi-chat-quote-fill',
            'color' => 'text-warning'
        ];
    }
}
