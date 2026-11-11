<?php

namespace App\Console\Commands;

use App\Models\ResearchSchedule;
use App\Models\User;
use App\Notifications\ScheduleReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendScheduleRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedules:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan upcoming research schedules and send automated reminders (7 days, 3 days, 1 day, or deadline)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning upcoming research schedules for reminders...');

        $upcomingSchedules = ResearchSchedule::where('status', 'published')
            ->where(function ($q) {
                $q->where('start_date', '>=', now()->startOfDay())
                  ->orWhere(function ($sub) {
                      $sub->whereNotNull('submission_deadline')
                          ->where('submission_deadline', '>=', now()->startOfDay());
                  });
            })
            ->get();

        if ($upcomingSchedules->isEmpty()) {
            $this->info('No active upcoming schedules found.');
            return 0;
        }

        $sentCount = 0;

        foreach ($upcomingSchedules as $schedule) {
            $remindersSent = is_array($schedule->reminders_sent) ? $schedule->reminders_sent : [];
            $daysUntilStart = now()->startOfDay()->diffInDays($schedule->start_date->startOfDay(), false);

            $reminderTypeToSend = null;

            // Check milestone intervals
            if ($daysUntilStart <= 1 && $daysUntilStart >= 0 && empty($remindersSent['1_day'])) {
                $reminderTypeToSend = '1_day';
            } elseif ($daysUntilStart <= 3 && $daysUntilStart > 1 && empty($remindersSent['3_days'])) {
                $reminderTypeToSend = '3_days';
            } elseif ($daysUntilStart <= 7 && $daysUntilStart > 3 && empty($remindersSent['7_days'])) {
                $reminderTypeToSend = '7_days';
            }

            // Check submission deadline if event reminder hasn't triggered today
            if (!$reminderTypeToSend && $schedule->submission_deadline) {
                $daysUntilDeadline = now()->startOfDay()->diffInDays($schedule->submission_deadline->startOfDay(), false);
                if ($daysUntilDeadline <= 2 && $daysUntilDeadline >= 0 && empty($remindersSent['deadline_approaching'])) {
                    $reminderTypeToSend = 'deadline_approaching';
                }
            }

            if ($reminderTypeToSend) {
                $this->info("Dispatching '{$reminderTypeToSend}' reminder for schedule #{$schedule->id}: {$schedule->title}");

                // Determine recipients based on target audience
                $recipients = $this->resolveRecipients($schedule);

                if ($recipients->isNotEmpty()) {
                    try {
                        \Illuminate\Support\Facades\Notification::send($recipients, new ScheduleReminderNotification($schedule, $reminderTypeToSend));
                        $sentCount += $recipients->count();
                    } catch (\Throwable $e) {
                        Log::error("Failed to notify recipients for schedule {$schedule->id}: " . $e->getMessage());
                    }
                }

                $remindersSent[$reminderTypeToSend] = now()->toDateTimeString();
                $schedule->reminders_sent = $remindersSent;
                $schedule->save();
            }
        }

        $this->info("Completed schedule reminders scan. Total notifications dispatched: {$sentCount}");
        return 0;
    }

    /**
     * Resolve users who should receive this schedule's reminder.
     */
    protected function resolveRecipients(ResearchSchedule $schedule)
    {
        $audience = $schedule->target_audience;

        if (empty($audience) || in_array('all', (array)$audience)) {
            return User::where('is_approved', true)->get();
        }

        $roles = (array) $audience;
        return User::where('is_approved', true)
            ->where(function ($query) use ($roles) {
                foreach ($roles as $role) {
                    $query->orWhere(function ($q) use ($role) {
                        $q->where('role', $role);
                        $driver = $q->getConnection()->getDriverName();
                        if ($driver === 'sqlite') {
                            $q->orWhere('additional_roles', 'like', '%"' . $role . '"%');
                        } else {
                            $q->orWhereJsonContains('additional_roles', $role);
                        }
                    });
                }
            })
            ->get();
    }
}
