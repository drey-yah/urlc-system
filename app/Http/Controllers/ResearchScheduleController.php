<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ResearchSchedule;
use App\Models\ScheduleAttendee;
use App\Models\User;
use App\Notifications\NewScheduleNotification;
use App\Notifications\ScheduleReminderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ResearchScheduleController extends Controller
{
    /**
     * Display a listing of research schedules.
     */
    public function index(Request $request)
    {
        $query = ResearchSchedule::with(['creator', 'attendees.user', 'confirmedAttendees'])
            ->latest('start_date');

        // Type filter
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('event_type', $request->type);
        }

        // Status / timeframe filter
        $timeframe = $request->get('timeframe', 'all');
        if ($timeframe === 'upcoming') {
            $query->where('start_date', '>=', now());
        } elseif ($timeframe === 'past') {
            $query->where(function ($q) {
                $q->whereNotNull('end_date')->where('end_date', '<', now())
                  ->orWhere(function ($sub) {
                      $sub->whereNull('end_date')->where('start_date', '<', now()->startOfDay());
                  });
            });
        }

        $schedules = $query->paginate(10)->withQueryString();

        // Summary metrics
        $stats = [
            'total_active' => ResearchSchedule::where('status', 'published')->where('start_date', '>=', now())->count(),
            'call_for_papers' => ResearchSchedule::where('event_type', 'call_for_paper')->where('status', 'published')->where('start_date', '>=', now())->count(),
            'in_house_reviews' => ResearchSchedule::where('event_type', 'in_house_review')->where('status', 'published')->where('start_date', '>=', now())->count(),
            'inception_meetings' => ResearchSchedule::where('event_type', 'inception_meeting')->where('status', 'published')->where('start_date', '>=', now())->count(),
        ];

        $user = Auth::user();
        $isDirector = $user ? ($user->isDirector() || $user->isAdmin()) : false;

        return view('schedules.index', compact('schedules', 'stats', 'isDirector', 'timeframe'));
    }

    /**
     * Store a newly created research schedule.
     */
    public function store(Request $request)
    {
        $this->authorizeDirector();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_type' => 'required|string|in:call_for_paper,in_house_review,inception_meeting,other',
            'academic_year' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'submission_deadline' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|string|max:255',
            'target_audience' => 'nullable|array',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,png,jpg,jpeg',
            'cross_post_announcement' => 'nullable|boolean',
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('schedules', config('filesystems.default', 'public'));
        }

        // Normalize target audience
        $targetAudience = $request->input('target_audience', ['all']);
        if (empty($targetAudience)) {
            $targetAudience = ['all'];
        }

        $schedule = ResearchSchedule::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'event_type' => $validated['event_type'],
            'academic_year' => $validated['academic_year'] ?? null,
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'submission_deadline' => $validated['submission_deadline'] ?? null,
            'venue' => $validated['venue'] ?? null,
            'meeting_link' => $validated['meeting_link'] ?? null,
            'target_audience' => $targetAudience,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'status' => 'published',
        ]);

        // Cross-post to Announcements feed if requested
        if ($request->boolean('cross_post_announcement', true)) {
            $announcementContent = $schedule->description ? $schedule->description . "\n\n" : '';
            $announcementContent .= "📅 **Schedule:** " . $schedule->start_date->format('l, F j, Y \a\t g:i A');
            if ($schedule->venue) {
                $announcementContent .= "\n📍 **Venue:** " . $schedule->venue;
            }
            if ($schedule->meeting_link) {
                $announcementContent .= "\n🔗 **Link:** " . $schedule->meeting_link;
            }
            if ($schedule->submission_deadline) {
                $announcementContent .= "\n⏰ **Submission Deadline:** " . $schedule->submission_deadline->format('l, F j, Y \a\t g:i A');
            }

            $announcement = Announcement::create([
                'user_id' => Auth::id(),
                'title' => "[{$schedule->eventTypeLabel()}] " . $schedule->title,
                'content' => $announcementContent,
                'image_path' => null,
            ]);

            $schedule->update(['announcement_id' => $announcement->id]);
        }

        // Send notifications & emails to targeted users
        $recipients = $this->resolveRecipients($schedule);
        if ($recipients->isNotEmpty()) {
            try {
                \Illuminate\Support\Facades\Notification::send($recipients, new NewScheduleNotification($schedule));
            } catch (\Throwable $e) {
                Log::warning("Could not send schedule notification: " . $e->getMessage());
            }
        }

        return redirect()->route('schedules.show', $schedule->id)
            ->with('success', 'Research schedule published and notifications sent successfully to ' . $recipients->count() . ' user(s)!');
    }

    /**
     * Display the specified schedule.
     */
    public function show($id)
    {
        $schedule = ResearchSchedule::with(['creator', 'attendees.user', 'confirmedAttendees.user'])
            ->findOrFail($id);

        $user = Auth::user();
        $isDirector = $user ? ($user->isDirector() || $user->isAdmin()) : false;
        $userRsvp = $user ? $schedule->getUserRsvp($user->id) : null;

        return view('schedules.show', compact('schedule', 'isDirector', 'userRsvp'));
    }

    /**
     * Update the specified research schedule.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeDirector();

        $schedule = ResearchSchedule::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_type' => 'required|string|in:call_for_paper,in_house_review,inception_meeting,other',
            'academic_year' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'submission_deadline' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|string|max:255',
            'status' => 'required|string|in:published,completed,cancelled',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,png,jpg,jpeg',
        ]);

        if ($request->hasFile('attachment')) {
            if ($schedule->attachment_path && Storage::disk(config('filesystems.default', 'public'))->exists($schedule->attachment_path)) {
                Storage::disk(config('filesystems.default', 'public'))->delete($schedule->attachment_path);
            }
            $file = $request->file('attachment');
            $validated['attachment_name'] = $file->getClientOriginalName();
            $validated['attachment_path'] = $file->store('schedules', config('filesystems.default', 'public'));
        }

        $schedule->update($validated);

        return redirect()->route('schedules.show', $schedule->id)
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Remove the specified schedule.
     */
    public function destroy($id)
    {
        $this->authorizeDirector();

        $schedule = ResearchSchedule::findOrFail($id);

        if ($schedule->attachment_path && Storage::disk(config('filesystems.default', 'public'))->exists($schedule->attachment_path)) {
            Storage::disk(config('filesystems.default', 'public'))->delete($schedule->attachment_path);
        }

        if ($schedule->announcement_id) {
            Announcement::where('id', $schedule->announcement_id)->delete();
        }

        $schedule->delete();

        return redirect()->route('schedules.index')
            ->with('success', 'Research schedule deleted successfully.');
    }

    /**
     * Download the attachment for the schedule.
     */
    public function downloadAttachment($id)
    {
        $schedule = ResearchSchedule::findOrFail($id);

        if (!$schedule->attachment_path || !Storage::disk(config('filesystems.default', 'public'))->exists($schedule->attachment_path)) {
            return redirect()->back()->with('error', 'Attachment file not found.');
        }

        return Storage::disk(config('filesystems.default', 'public'))->download(
            $schedule->attachment_path,
            $schedule->attachment_name ?: 'schedule-attachment'
        );
    }

    /**
     * Download .ics iCalendar file for calendar apps.
     */
    public function downloadIcs($id)
    {
        $schedule = ResearchSchedule::findOrFail($id);
        $icsContent = $schedule->generateIcsContent();
        $filename = 'urlc-schedule-' . $schedule->id . '.ics';

        return response($icsContent, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * RSVP / attendance confirmation.
     */
    public function rsvp(Request $request, $id)
    {
        $schedule = ResearchSchedule::findOrFail($id);

        $request->validate([
            'status' => 'required|in:confirmed,declined,tentative',
            'remarks' => 'nullable|string|max:500',
        ]);

        ScheduleAttendee::updateOrCreate(
            [
                'research_schedule_id' => $schedule->id,
                'user_id' => Auth::id(),
            ],
            [
                'status' => $request->status,
                'remarks' => $request->remarks,
            ]
        );

        $statusText = match($request->status) {
            'confirmed' => 'confirmed your attendance',
            'declined' => 'indicated you cannot attend',
            default => 'marked your status as tentative',
        };

        return redirect()->back()->with('success', "You have {$statusText} for this schedule.");
    }

    /**
     * Director action: Send immediate reminder blast to target audience.
     */
    public function sendImmediateReminder(Request $request, $id)
    {
        $this->authorizeDirector();

        $schedule = ResearchSchedule::findOrFail($id);
        $customNote = $request->input('custom_note');

        $recipients = $this->resolveRecipients($schedule);
        $count = $recipients->count();

        if ($recipients->isNotEmpty()) {
            try {
                \Illuminate\Support\Facades\Notification::send($recipients, new ScheduleReminderNotification($schedule, 'immediate', $customNote));
            } catch (\Throwable $e) {
                Log::warning("Could not dispatch immediate reminder: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "Immediate reminder sent successfully to {$count} user(s) via system notification and email!");
    }

    /**
     * Authorize that the current user has director or admin privileges.
     */
    protected function authorizeDirector(): void
    {
        $user = Auth::user();
        if (!$user || (!$user->isDirector() && !$user->isAdmin())) {
            abort(403, 'Unauthorized action. Only the Research Director or System Administrator can perform this action.');
        }
    }

    /**
     * Resolve audience recipients for a given schedule.
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
