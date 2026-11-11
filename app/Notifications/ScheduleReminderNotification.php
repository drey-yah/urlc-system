<?php

namespace App\Notifications;

use App\Models\ResearchSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ResearchSchedule $schedule;
    public string $reminderType;
    public ?string $customNote;

    /**
     * Create a new notification instance.
     */
    public function __construct(ResearchSchedule $schedule, string $reminderType = 'reminder', ?string $customNote = null)
    {
        $this->schedule = $schedule;
        $this->reminderType = $reminderType;
        $this->customNote = $customNote;
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

            $fp = @fsockopen($host, $port, $errno, $errstr, 1.5);
            if ($fp) {
                fclose($fp);
                return self::$mailReachable = true;
            }

            \Illuminate\Support\Facades\Log::info("SMTP server {$host}:{$port} is currently unreachable. System bell reminder delivered; email skipped.");
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
     * Get the reminder headline text.
     */
    public function getHeadline(): string
    {
        return match ($this->reminderType) {
            '7_days' => 'Upcoming Event Reminder (7 Days Ahead)',
            '3_days' => 'Approaching Schedule Alert (3 Days Left)',
            '1_day' => 'Urgent: Schedule is Tomorrow!',
            'deadline_approaching' => 'Upcoming Submission Cut-Off Reminder',
            'immediate' => 'Director Priority Reminder',
            default => 'Schedule Reminder',
        };
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $headline = $this->getHeadline();
        $eventType = $this->schedule->eventTypeLabel();
        $dateStr = $this->schedule->start_date ? $this->schedule->start_date->format('l, F j, Y \a\t g:i A') : 'TBA';

        $mail = (new MailMessage)
            ->subject("[{$headline}] {$this->schedule->title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("This is an official reminder regarding the upcoming **{$eventType}**.")
            ->line("**Title:** {$this->schedule->title}")
            ->line("**Event Schedule:** {$dateStr}");

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

        if ($this->customNote) {
            $mail->line("**Director's Note:** {$this->customNote}");
        }

        return $mail
            ->action('Open Schedule in Portal', route('schedules.show', $this->schedule->id))
            ->line('Please make sure you have submitted all requisite manuscripts or requirements.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $headline = $this->getHeadline();
        $dateStr = $this->schedule->start_date ? $this->schedule->start_date->format('M d, Y') : 'TBA';

        return [
            'schedule_id' => $this->schedule->id,
            'title' => "Reminder: {$this->schedule->title}",
            'message' => "{$headline}: Scheduled for {$dateStr}. Don't forget to prepare and confirm attendance.",
            'icon' => 'bi-alarm-fill',
            'color' => 'text-warning',
            'type' => 'research_schedule',
            'target_url' => route('schedules.show', $this->schedule->id),
        ];
    }
}
