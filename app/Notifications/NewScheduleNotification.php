<?php

namespace App\Notifications;

use App\Models\ResearchSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewScheduleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ResearchSchedule $schedule;

    /**
     * Create a new notification instance.
     */
    public function __construct(ResearchSchedule $schedule)
    {
        $this->schedule = $schedule;
    }

    protected static ?bool $mailReachable = null;

    /**
     * Determine if the configured mail transport is currently online and reachable.
     */
    public static function isMailReachable(): bool
    {
        if (self::$mailReachable !== null) {
            return self::$mailReachable;
        }

        $mailer = config('mail.default');
        if (!$mailer || $mailer === 'null') {
            return self::$mailReachable = false;
        }

        if ($mailer === 'log') {
            return self::$mailReachable = true;
        }

        if ($mailer === 'smtp') {
            $host = config('mail.mailers.smtp.host');
            $port = (int) config('mail.mailers.smtp.port', 587);

            if (empty($host)) {
                return self::$mailReachable = false;
            }

            // Quick 1.5s socket probe to prevent SwiftMailer from freezing and timing out the HTTP request
            $fp = @fsockopen($host, $port, $errno, $errstr, 1.5);
            if ($fp) {
                fclose($fp);
                return self::$mailReachable = true;
            }

            \Illuminate\Support\Facades\Log::info("SMTP server {$host}:{$port} is currently unreachable. System bell notification will be delivered; email skipped to avoid timeout.");
            return self::$mailReachable = false;
        }

        return self::$mailReachable = true;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (self::isMailReachable()) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $eventType = $this->schedule->eventTypeLabel();
        $dateStr = $this->schedule->start_date ? $this->schedule->start_date->format('l, F j, Y \a\t g:i A') : 'TBA';

        $mail = (new MailMessage)
            ->subject("[URLC Schedule] {$eventType}: {$this->schedule->title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("The Research Director has officially posted a new institutional schedule in the URLC Portal.")
            ->line("**Title:** {$this->schedule->title}")
            ->line("**Category:** {$eventType}")
            ->line("**Schedule Date:** {$dateStr}");

        if ($this->schedule->venue) {
            $mail->line("**Venue:** {$this->schedule->venue}");
        }

        if ($this->schedule->meeting_link) {
            $mail->line("**Virtual Link:** {$this->schedule->meeting_link}");
        }

        if ($this->schedule->submission_deadline) {
            $deadlineStr = $this->schedule->submission_deadline->format('l, F j, Y \a\t g:i A');
            $mail->line("**Submission Deadline:** {$deadlineStr}");
        }

        return $mail
            ->action('View Schedule & Confirm Attendance', route('schedules.show', $this->schedule->id))
            ->line('Please review the guidelines and prepare required documents ahead of time.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $dateStr = $this->schedule->start_date ? $this->schedule->start_date->format('M d, Y h:i A') : 'TBA';

        return [
            'schedule_id' => $this->schedule->id,
            'title' => "New {$this->schedule->eventTypeLabel()}",
            'message' => "The Director announced '{$this->schedule->title}' set for {$dateStr}.",
            'icon' => $this->schedule->eventTypeIcon(),
            'color' => 'text-primary',
            'type' => 'research_schedule',
            'target_url' => route('schedules.show', $this->schedule->id),
        ];
    }
}
