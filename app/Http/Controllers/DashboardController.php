<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Models\Announcement;

class DashboardController extends Controller
{
    /**
     * Redirect authenticated users to their role-specific dashboard.
     */
    public function redirectByRole()
    {
        $role = Auth::user()->activeRole();

        switch ($role) {
            case 'super_admin':
                return redirect('/superadmin');
            case 'admin':
                return redirect('/admin');
            case 'reviewer':
                return redirect('/reviewer');
            case 'coordinator':
                return redirect('/coordinator');
            case 'dean':
                return redirect('/dean');
            case 'vprei':
                return redirect('/vprei');
            case 'president':
                return redirect('/president');
            case 'budget_officer':
                return redirect('/budget');
            case 'sao_finance':
                return redirect('/finance');
            case 'staff':
                return redirect('/staff');
            case 'recording_staff':
                return redirect('/recording-staff/dashboard');
            case 'funding_agency':
                return redirect('/external-funding');
            default:
                return redirect('/researcher');
        }
    }

    /**
     * Switch active role for users with multiple authorized roles.
     */
    public function switchRole(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $targetRole = $request->role;
        $user = auth()->user();

        if (!$user->hasRole($targetRole) && !$user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'You are not authorized to switch to that role.');
        }

        session(['active_role' => $targetRole]);

        $roleLabels = [
            'researcher' => 'Researcher',
            'reviewer' => 'Evaluator / Reviewer',
            'coordinator' => 'College Coordinator',
            'dean' => 'College Dean',
            'vprei' => 'VPREI',
            'president' => 'President',
            'budget_officer' => 'Budget Officer',
            'sao_finance' => 'Finance Officer',
            'staff' => 'Receiving Staff',
            'recording_staff' => 'Recording Staff',
            'admin' => 'Research Director / Admin',
            'super_admin' => 'Super Admin',
        ];

        $label = $roleLabels[$targetRole] ?? ucfirst(str_replace('_', ' ', $targetRole));

        return $this->redirectByRole()->with('success', "Switched view to {$label} portal.");
    }

    /**
     * Display the Admin Dashboard.
     */
    public function adminDashboard()
    {
        $stats = [
            'total_proposals' => ResearchProposal::count(),
            'pending_proposals' => ResearchProposal::whereIn('status', [
                'pending', 'submitted', 'pending_director_review', 'accepted_for_in_house_review', 
                'under_review', 'endorsed_to_vprei', 'pending_budget_certification', 
                'funds_certified', 'final_copy_submitted'
            ])->count(),
            'approved_proposals' => ResearchProposal::whereIn('status', ['approved', 'final_approved', 'ongoing', 'completed'])->count(),
            'total_users' => User::count(),
            'active_announcements' => Announcement::count(),
        ];

        $recentProposals = ResearchProposal::with('user')->latest()->take(5)->get();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentProposals', 'recentUsers'));
    }

    /**
     * Display the Reviewer Dashboard.
     */
    public function reviewerDashboard()
    {
        $assignedProposalIds = DB::table('proposal_assignments')
            ->where('user_id', auth()->id())
            ->pluck('research_proposal_id');

        $proposals = ResearchProposal::with(['user', 'milestones'])
            ->whereIn('id', $assignedProposalIds)
            ->latest()
            ->get();

        $stats = [
            'assigned' => $proposals->count(),
            'pending_review' => $proposals->whereIn('status', ['pending', 'submitted', 'under_review', 'accepted_for_in_house_review'])->count(),
            'evaluated' => $proposals->whereIn('status', ['approved', 'rejected', 'revision_required', 'approved_with_revisions', 'final_approved', 'final_rejected'])->count(),
            'completed' => $proposals->count() > 0 ? $proposals->where('status', 'completed')->count() : 0,
        ];

        $recentAssigned = $proposals->take(5);

        return view('reviewer.dashboard', compact('stats', 'recentAssigned'));
    }

    /**
     * Display the Researcher Dashboard.
     */
    public function researcherDashboard()
    {
        $leadProposals = auth()->user()->leadProposals()->with(['collaborators'])->latest()->get();
        $collaboratedProposals = auth()->user()->collaboratedProposals()->with(['user', 'collaborators'])->latest()->get();
        $allProposals = $leadProposals->merge($collaboratedProposals)->sortByDesc('created_at');

        $stats = [
            'total' => $allProposals->count(),
            'under_review' => $allProposals->whereIn('status', [
                'pending', 'submitted', 'pending_director_review', 'accepted_for_in_house_review', 
                'under_review', 'endorsed_to_vprei', 'pending_budget_certification', 
                'funds_certified', 'final_copy_submitted'
            ])->count(),
            'action_required' => $allProposals->whereIn('status', ['revision_required', 'returned_for_revision', 'approved_with_revisions'])->count(),
            'approved' => $allProposals->whereIn('status', ['approved', 'final_approved', 'ongoing', 'completed'])->count(),
            'drafts' => $allProposals->where('status', 'draft')->count(),
        ];

        $recentProposals = $allProposals->take(5);
        $recentAnnouncements = Announcement::latest()->take(3)->get();

        return view('researcher.dashboard', compact('stats', 'recentProposals', 'recentAnnouncements'));
    }

    /**
     * Securely serve files from local public storage or cloud S3 with path traversal protection.
     */
    public function serveFile(Request $request)
    {
        $path = $request->query('path');

        if (!$path) {
            abort(404, 'File path not provided.');
        }

        // Strict path traversal defense
        if (str_contains($path, '..') || str_starts_with($path, '/') || str_starts_with($path, '\\')) {
            abort(403, 'Invalid file path.');
        }

        // 1. Check local public storage first if file exists locally
        $publicDisk = \Storage::disk('public');
        if ($publicDisk->exists($path)) {
            return response()->file($publicDisk->path($path));
        }

        // 2. Check cloud storage disk (Supabase S3)
        $defaultDiskName = config('filesystems.default', 'public');
        if ($defaultDiskName !== 'public') {
            $defaultDisk = \Storage::disk($defaultDiskName);
            if ($defaultDisk->exists($path)) {
                try {
                    return redirect($defaultDisk->temporaryUrl($path, now()->addMinutes(60)));
                } catch (\Exception $e) {
                    return redirect($defaultDisk->url($path));
                }
            }
        }

        abort(404, 'Uploaded file not found on storage server.');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markNotificationsAsRead()
    {
        if (auth()->check()) {
            auth()->user()->unreadNotifications->markAsRead();
        }
        return redirect()->back();
    }

    /**
     * Mark a single notification as read and redirect to target URL.
     */
    public function markSingleNotificationAsRead($id)
    {
        if (auth()->check()) {
            $notification = auth()->user()->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();

                if (!empty($notification->data['proposal_id'])) {
                    return redirect()->route('proposal.show', $notification->data['proposal_id']);
                } elseif (!empty($notification->data['announcement_id'])) {
                    return redirect()->route('announcements.index');
                }
            }
        }
        return redirect()->back();
    }

    /**
     * Poll latest notifications and unread count for real-time live updates.
     */
    public function pollNotifications()
    {
        if (!auth()->check()) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $user = auth()->user();
        $unreadCount = $user->unreadNotifications()->count();
        $notifications = $user->notifications()->take(25)->get()->map(function ($notif) {
            $titleLower = strtolower($notif->data['title'] ?? '');
            
            if (str_contains($titleLower, 'return')) {
                $badgeBg = 'rgba(239, 68, 68, 0.12)';
                $badgeColor = '#dc2626';
                $itemIcon = 'bi-arrow-return-left';
            } elseif (str_contains($titleLower, 'endorse') || str_contains($titleLower, 'noted') || str_contains($titleLower, 'accept') || str_contains($titleLower, 'approve')) {
                $badgeBg = 'rgba(16, 185, 129, 0.12)';
                $badgeColor = '#059669';
                $itemIcon = 'bi-check-circle-fill';
            } elseif (str_contains($titleLower, 'announcement')) {
                $badgeBg = 'rgba(245, 158, 11, 0.12)';
                $badgeColor = '#d97706';
                $itemIcon = 'bi-megaphone-fill';
            } else {
                $badgeBg = 'rgba(59, 130, 246, 0.12)';
                $badgeColor = '#2563eb';
                $itemIcon = $notif->data['icon'] ?? 'bi-bell-fill';
            }

            return [
                'id' => $notif->id,
                'title' => $notif->data['title'] ?? 'Notification',
                'message' => $notif->data['message'] ?? '',
                'icon' => $itemIcon,
                'badge_bg' => $badgeBg,
                'badge_color' => $badgeColor,
                'is_unread' => is_null($notif->read_at),
                'created_at_human' => $notif->created_at->diffForHumans(),
                'read_url' => route('notifications.readSingle', $notif->id),
            ];
        });

        return response()->json([
            'unread_count' => $unreadCount,
            'total_count' => $user->notifications()->count(),
            'notifications' => $notifications,
        ]);
    }
}
