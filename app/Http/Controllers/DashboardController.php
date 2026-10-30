<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
     * Securely serve files from local public storage or cloud S3 with path traversal protection and authorization.
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

        // Object-level authorization check (prevents IDOR)
        if (!auth()->check() || !$this->authorizeFileAccess(auth()->user(), $path)) {
            abort(403, 'Unauthorized access to this document.');
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
     * Authorize whether the authenticated user has permission to view the requested document.
     */
    protected function authorizeFileAccess(User $user, string $path): bool
    {
        // 1. Super Admins and central executive/administrative roles have full oversight
        $activeRole = $user->activeRole();
        if ($user->isSuperAdmin() || in_array($activeRole, [
            'admin', 'super_admin', 'staff', 'recording_staff', 'vprei', 'president', 
            'budget_officer', 'sao_finance'
        ])) {
            return true;
        }

        // 2. Announcement flyers and media are public to all logged-in portal users
        if (str_starts_with($path, 'announcements/')) {
            return true;
        }

        // 3. External Funding Project documents
        if (str_starts_with($path, 'external_funding/') && Schema::hasTable('external_funding_projects')) {
            $extProject = DB::table('external_funding_projects')
                ->where('moa_mou_file', $path)
                ->orWhere('terminal_report', $path)
                ->first();

            if ($extProject) {
                if ($extProject->user_id === $user->id) {
                    return true;
                }
                if ($activeRole === 'funding_agency' && $extProject->funding_agency_id === $user->id) {
                    return true;
                }
                return false;
            }
        }

        // 4. Resolve the associated ResearchProposal for proposal-related files
        $proposalId = null;

        // Check proposal_documents
        if (Schema::hasTable('proposal_documents')) {
            $doc = DB::table('proposal_documents')->where('file_path', $path)->first();
            if ($doc) {
                $proposalId = $doc->research_proposal_id;
            }
        }

        // Check research_proposals directly
        if (!$proposalId && Schema::hasTable('research_proposals')) {
            $prop = DB::table('research_proposals')
                ->where('document_path', $path)
                ->orWhere('final_copy_path', $path)
                ->first();
            if ($prop) {
                $proposalId = $prop->id;
            }
        }

        // Check research_presentations
        if (!$proposalId && Schema::hasTable('research_presentations')) {
            $pres = DB::table('research_presentations')
                ->where('presentation_file_path', $path)
                ->orWhere('acceptance_letter_file_path', $path)
                ->orWhere('certificate_file_path', $path)
                ->first();
            if ($pres) {
                $proposalId = $pres->research_proposal_id;
            }
        }

        // Check research_publications
        if (!$proposalId && Schema::hasTable('research_publications')) {
            $pub = DB::table('research_publications')
                ->where('ip_registration_file_path', $path)
                ->orWhere('intent_letter_file_path', $path)
                ->orWhere('submission_proof_file_path', $path)
                ->orWhere('published_copy_file_path', $path)
                ->first();
            if ($pub) {
                $proposalId = $pub->research_proposal_id;
            }
        }

        // Check local_forum_submissions
        if (!$proposalId && Schema::hasTable('local_forum_submissions')) {
            $forumSub = DB::table('local_forum_submissions')
                ->where('presentation_file_path', $path)
                ->orWhere('extended_abstract_file_path', $path)
                ->orWhere('notice_file_path', $path)
                ->orWhere('certificate_file_path', $path)
                ->first();
            if ($forumSub) {
                $proposalId = $forumSub->research_proposal_id;
            }
        }

        // Check phase 2 tables
        if (!$proposalId && Schema::hasTable('purchase_requests')) {
            $pr = DB::table('purchase_requests')->where('document_path', $path)->first();
            if ($pr) {
                $proposalId = $pr->research_proposal_id;
            }
        }

        if (!$proposalId && Schema::hasTable('activity_designs')) {
            $ad = DB::table('activity_designs')
                ->where('activity_design_file', $path)
                ->orWhere('budget_requirement_file', $path)
                ->first();
            if ($ad) {
                $proposalId = $ad->research_proposal_id;
            }
        }

        if (!$proposalId && Schema::hasTable('terminal_reports')) {
            $tr = DB::table('terminal_reports')
                ->where('terminal_report_file', $path)
                ->orWhere('full_paper_file', $path)
                ->orWhere('supporting_docs_file', $path)
                ->orWhere('evaluation_form_file', $path)
                ->first();
            if ($tr) {
                $proposalId = $tr->research_proposal_id;
            }
        }

        if (!$proposalId && Schema::hasTable('project_monitoring')) {
            $pm = DB::table('project_monitoring')->where('monitoring_form_path', $path)->first();
            if ($pm) {
                $proposalId = $pm->research_proposal_id;
            }
        }

        if (!$proposalId && Schema::hasTable('research_milestones')) {
            $rm = DB::table('research_milestones')->where('document_path', $path)->first();
            if ($rm) {
                $proposalId = $rm->research_proposal_id;
            }
        }

        // If associated with a proposal, verify user authorization
        if ($proposalId) {
            $proposal = ResearchProposal::with(['collaborators', 'assignments', 'user'])->find($proposalId);
            if ($proposal) {
                // Author or Collaborator
                if ($proposal->user_id === $user->id || $proposal->collaborators->contains('id', $user->id)) {
                    return true;
                }

                // Assigned Reviewer
                if ($activeRole === 'reviewer' && $proposal->assignments->contains('id', $user->id)) {
                    return true;
                }

                // College Dean or Department Coordinator from the same college/department
                if (in_array($activeRole, ['dean', 'coordinator'])) {
                    if ($proposal->user && $proposal->user->department === $user->department) {
                        return true;
                    }
                }
            }

            // Proposal exists but user is not authorized to view it
            return false;
        }

        // Deny by default for unmapped files if the user does not have administrative oversight
        return false;
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
