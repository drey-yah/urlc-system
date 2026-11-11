<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'event_type',
        'academic_year',
        'description',
        'start_date',
        'end_date',
        'submission_deadline',
        'venue',
        'meeting_link',
        'target_audience',
        'attachment_path',
        'attachment_name',
        'status',
        'announcement_id',
        'reminders_sent',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'submission_deadline' => 'datetime',
        'target_audience' => 'array',
        'reminders_sent' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

    public function attendees()
    {
        return $this->hasMany(ScheduleAttendee::class);
    }

    public function confirmedAttendees()
    {
        return $this->hasMany(ScheduleAttendee::class)->where('status', 'confirmed');
    }

    public function getUserRsvp($userId)
    {
        return $this->attendees->firstWhere('user_id', $userId);
    }

    public function isAttending($userId): bool
    {
        $rsvp = $this->getUserRsvp($userId);
        return $rsvp && $rsvp->status === 'confirmed';
    }

    public function eventTypeLabel(): string
    {
        return match ($this->event_type) {
            'call_for_paper' => 'Call for Papers / Proposals',
            'in_house_review' => 'In-House Review Schedule',
            'inception_meeting' => 'Inception Meeting Schedule',
            default => 'Research Schedule',
        };
    }

    public function eventTypeBadgeClass(): string
    {
        return match ($this->event_type) {
            'call_for_paper' => 'badge-gold',
            'in_house_review' => 'badge-maroon',
            'inception_meeting' => 'badge-success',
            default => 'badge-secondary',
        };
    }

    public function eventTypeIcon(): string
    {
        return match ($this->event_type) {
            'call_for_paper' => 'bi-megaphone-fill',
            'in_house_review' => 'bi-journal-check',
            'inception_meeting' => 'bi-rocket-takeoff-fill',
            default => 'bi-calendar-event-fill',
        };
    }

    public function isUpcoming(): bool
    {
        return now()->lt($this->start_date);
    }

    public function isOngoing(): bool
    {
        $now = now();
        $end = $this->end_date ?? $this->start_date->copy()->endOfDay();
        return $now->gte($this->start_date) && $now->lte($end);
    }

    public function isPast(): bool
    {
        $end = $this->end_date ?? $this->start_date->copy()->endOfDay();
        return now()->gt($end);
    }

    /**
     * Generate 1-click Google Calendar Event link.
     */
    public function googleCalendarUrl(): string
    {
        $start = $this->start_date ? $this->start_date->copy()->setTimezone('UTC')->format('Ymd\THis\Z') : '';
        $end = $this->end_date
            ? $this->end_date->copy()->setTimezone('UTC')->format('Ymd\THis\Z')
            : ($this->start_date ? $this->start_date->copy()->addHours(2)->setTimezone('UTC')->format('Ymd\THis\Z') : '');

        $details = strip_tags($this->description ?? '');
        if ($this->submission_deadline) {
            $details .= "\n\nSubmission Deadline: " . $this->submission_deadline->format('M d, Y h:i A');
        }
        if ($this->meeting_link) {
            $details .= "\n\nVirtual Meeting Link: " . $this->meeting_link;
        }

        $location = $this->venue ?: ($this->meeting_link ?: 'University Research and Development Center');

        return 'https://calendar.google.com/calendar/render?action=TEMPLATE'
            . '&text=' . urlencode($this->title)
            . '&dates=' . $start . '/' . $end
            . '&details=' . urlencode($details)
            . '&location=' . urlencode($location);
    }

    /**
     * Generate RFC 5545 .ics iCalendar format for Outlook, Apple, and Mobile devices.
     */
    public function generateIcsContent(): string
    {
        $dtStamp = now()->setTimezone('UTC')->format('Ymd\THis\Z');
        $dtStart = $this->start_date ? $this->start_date->copy()->setTimezone('UTC')->format('Ymd\THis\Z') : $dtStamp;
        $dtEnd = $this->end_date
            ? $this->end_date->copy()->setTimezone('UTC')->format('Ymd\THis\Z')
            : ($this->start_date ? $this->start_date->copy()->addHours(2)->setTimezone('UTC')->format('Ymd\THis\Z') : $dtStamp);

        $uid = 'urlc-schedule-' . $this->id . '-' . md5($this->created_at ?? now()) . '@antiquespride.edu.ph';
        $summary = preg_replace('/([\,;])/', '\\\$1', $this->title);
        $location = preg_replace('/([\,;])/', '\\\$1', $this->venue ?: ($this->meeting_link ?: 'URLC Campus'));
        
        $desc = strip_tags($this->description ?? '');
        if ($this->meeting_link) {
            $desc .= "\\nVirtual Link: " . $this->meeting_link;
        }
        if ($this->submission_deadline) {
            $desc .= "\\nSubmission Deadline: " . $this->submission_deadline->format('M d, Y h:i A');
        }
        $desc = preg_replace('/([\,;])/', '\\\$1', str_replace(["\r\n", "\n", "\r"], "\\n", $desc));

        return "BEGIN:VCALENDAR\r\n"
            . "VERSION:2.0\r\n"
            . "PRODID:-//URLC Research Management System//Research Schedules//EN\r\n"
            . "CALSCALE:GREGORIAN\r\n"
            . "METHOD:PUBLISH\r\n"
            . "BEGIN:VEVENT\r\n"
            . "UID:{$uid}\r\n"
            . "DTSTAMP:{$dtStamp}\r\n"
            . "DTSTART:{$dtStart}\r\n"
            . "DTEND:{$dtEnd}\r\n"
            . "SUMMARY:{$summary}\r\n"
            . "DESCRIPTION:{$desc}\r\n"
            . "LOCATION:{$location}\r\n"
            . "STATUS:CONFIRMED\r\n"
            . "END:VEVENT\r\n"
            . "END:VCALENDAR\r\n";
    }
}
