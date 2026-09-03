<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ResearchProposalController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Redirect After Login (Role-Based)
|--------------------------------------------------------------------------
*/

Route::get('/redirect', function () {
    $role = Auth::user()->role;

    if ($role == 'super_admin') {
        return redirect('/superadmin');
    } elseif ($role == 'admin') {
        return redirect('/admin');
    } elseif ($role == 'reviewer') {
        return redirect('/reviewer');
    } elseif ($role == 'coordinator') {
        return redirect('/coordinator');
    } elseif ($role == 'dean') {
        return redirect('/dean');
    } elseif ($role == 'vprei') {
        return redirect('/vprei');
    } elseif ($role == 'president') {
        return redirect('/president');
    } elseif ($role == 'budget_officer') {
        return redirect('/budget');
    } elseif ($role == 'sao_finance') {
        return redirect('/finance');
    } elseif ($role == 'staff') {
        return redirect('/staff');
    } elseif ($role == 'recording_staff') {
        return redirect('/recording-staff/dashboard');
    } else {
        return redirect('/researcher');
    }
})->middleware(['auth', 'approved']);

/*
|--------------------------------------------------------------------------
| Protected Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::get('/admin', function () {
    $stats = [
        'total_proposals' => \App\Models\ResearchProposal::count(),
        'pending_proposals' => \App\Models\ResearchProposal::whereIn('status', [
            'pending', 'submitted', 'pending_director_review', 'accepted_for_in_house_review', 
            'under_review', 'endorsed_to_vprei', 'pending_budget_certification', 
            'funds_certified', 'final_copy_submitted'
        ])->count(),
        'approved_proposals' => \App\Models\ResearchProposal::whereIn('status', ['approved', 'final_approved', 'ongoing', 'completed'])->count(),
        'total_users' => \App\Models\User::count(),
        'active_announcements' => \App\Models\Announcement::count(),
    ];

    $recentProposals = \App\Models\ResearchProposal::with('user')->latest()->take(5)->get();
    $recentUsers = \App\Models\User::latest()->take(5)->get();

    return view('admin.dashboard', compact('stats', 'recentProposals', 'recentUsers'));
})->middleware(['auth', 'role:admin', 'approved'])->name('admin.dashboard');

Route::get('/reviewer', function () {
    $assignedProposalIds = \DB::table('proposal_assignments')
        ->where('user_id', auth()->id())
        ->pluck('research_proposal_id');

    $proposals = \App\Models\ResearchProposal::with(['user', 'milestones'])
        ->whereIn('id', $assignedProposalIds)
        ->latest()
        ->get();

    $stats = [
        'assigned' => $proposals->count(),
        'pending_review' => $proposals->whereIn('status', ['pending', 'submitted', 'under_review', 'accepted_for_in_house_review'])->count(),
        'evaluated' => $proposals->whereIn('status', ['approved', 'rejected', 'revision_required', 'approved_with_revisions', 'final_approved', 'final_rejected'])->count(),
        'completed' => $proposals->where('status', 'completed')->count(),
    ];

    $recentAssigned = $proposals->take(5);

    return view('reviewer.dashboard', compact('stats', 'recentAssigned'));
})->middleware(['auth', 'role:reviewer', 'approved'])->name('reviewer.dashboard');

Route::get('/researcher', function () {
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
    $recentAnnouncements = \App\Models\Announcement::latest()->take(3)->get();

    return view('researcher.dashboard', compact('stats', 'recentProposals', 'recentAnnouncements'));
})->middleware(['auth', 'role:researcher', 'approved'])->name('researcher.dashboard');

Route::get('/coordinator', [\App\Http\Controllers\CoordinatorController::class, 'dashboard'])
    ->middleware(['auth', 'role:coordinator', 'approved'])->name('coordinator.dashboard');

Route::get('/staff', [\App\Http\Controllers\StaffController::class, 'dashboard'])
    ->middleware(['auth', 'role:staff', 'approved'])->name('staff.dashboard');

/*
|--------------------------------------------------------------------------
| Researcher Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:researcher'])->group(function () {
    Route::get('/proposal/create', [ResearchProposalController::class, 'create'])->name('proposal.create');
    Route::post('/proposal/store', [ResearchProposalController::class, 'store'])->name('proposal.store');
    Route::get('/proposal/my', [ResearchProposalController::class, 'index'])->name('proposal.index');

    Route::get('/proposal/{id}/edit', [ResearchProposalController::class, 'edit'])->name('proposal.edit');
    Route::put('/proposal/{id}/update', [ResearchProposalController::class, 'update'])->name('proposal.update');
    Route::delete('/proposal/{id}', [ResearchProposalController::class, 'destroy'])->name('proposal.destroy');
    Route::post('/proposal/{id}/submit-final-copy', [ResearchProposalController::class, 'submitFinalCopy'])->name('proposal.submitFinalCopy');

    // Phase 2 Implementation Routes (Researcher Submissions)
    Route::post('/proposal/{id}/activity-design', [\App\Http\Controllers\ActivityDesignController::class, 'store'])->name('activity_design.store');
    Route::post('/proposal/{id}/purchase-request', [\App\Http\Controllers\PurchaseRequestController::class, 'store'])->name('purchase_request.store');
    Route::post('/proposal/{id}/monitoring', [\App\Http\Controllers\ProjectMonitoringController::class, 'store'])->name('monitoring.store');
    Route::post('/proposal/{id}/terminal-report', [\App\Http\Controllers\TerminalReportController::class, 'store'])->name('terminal_report.store');
});

// Shared Proposal Routes (All authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/proposal/{id}', [ResearchProposalController::class, 'show'])->name('proposal.show');
    Route::get('/proposal/{id}/endorsement-form', [\App\Http\Controllers\CoordinatorController::class, 'generateEndorsementForm'])->name('proposal.endorsement_form');
    Route::get('/proposal/{id}/resu-fm015', [\App\Http\Controllers\ResearchProposalController::class, 'exportResuFm015'])->name('proposal.resu_fm015');
    Route::post('/proposal/{id}/submit-final', [\App\Http\Controllers\ResearchProposalController::class, 'submitFinalManuscript'])->name('proposal.submitFinal');
    
    // Call for Papers (Announcements)
    Route::get('/announcements', [\App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements/{id}/like', [\App\Http\Controllers\AnnouncementInteractionController::class, 'like'])->name('announcements.like');
    Route::post('/announcements/{id}/comment', [\App\Http\Controllers\AnnouncementInteractionController::class, 'comment'])->name('announcements.comment');

    // Messaging Module (Gmail style)
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/{id}/reply', [\App\Http\Controllers\MessageController::class, 'reply'])->name('messages.reply');
    Route::delete('/messages/{id}', [\App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');
    Route::get('/email-templates', function() {
        return redirect()->route('messages.index');
    })->name('email.templates');

    // Repository
    Route::get('/repository', [\App\Http\Controllers\RepositoryController::class, 'index'])->name('repository.index');

    // Notifications
    Route::post('/notifications/mark-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back();
    })->name('notifications.markRead');

    // Milestones
    Route::post('/proposal/{id}/milestones', [\App\Http\Controllers\ResearchMilestoneController::class, 'store'])->name('milestones.store');

    // Line Item Budget
    Route::post('/proposal/{id}/budget-items', [\App\Http\Controllers\ProposalBudgetItemController::class, 'store'])->name('budget_items.store');
    Route::delete('/budget-items/{id}', [\App\Http\Controllers\ProposalBudgetItemController::class, 'destroy'])->name('budget_items.destroy');

    // Phase 3: Presentation & Dissemination Routes
    Route::post('/proposal/{id}/presentation', [\App\Http\Controllers\ResearchPresentationController::class, 'store'])->name('presentation.store');
    Route::post('/presentation/{id}/acceptance', [\App\Http\Controllers\ResearchPresentationController::class, 'uploadAcceptance'])->name('presentation.uploadAcceptance');
    Route::post('/presentation/{id}/file', [\App\Http\Controllers\ResearchPresentationController::class, 'uploadPresentation'])->name('presentation.uploadPresentation');
    Route::post('/presentation/{id}/recommend', [\App\Http\Controllers\ResearchPresentationController::class, 'recommendToPresident'])->name('presentation.recommend');
    Route::post('/presentation/{id}/approve', [\App\Http\Controllers\ResearchPresentationController::class, 'presidentApprove'])->name('presentation.approve');
    Route::post('/presentation/{id}/certificate', [\App\Http\Controllers\ResearchPresentationController::class, 'uploadCertificate'])->name('presentation.uploadCertificate');

    // Phase 4: Publication of Research Outputs Routes (Appendix C)
    Route::post('/proposal/{id}/publication/intent', [\App\Http\Controllers\ResearchPublicationController::class, 'storeIntent'])->name('publication.storeIntent');
    Route::post('/publication/{id}/screen-ip', [\App\Http\Controllers\ResearchPublicationController::class, 'screenIp'])->name('publication.screenIp');
    Route::post('/publication/{id}/ip-proof', [\App\Http\Controllers\ResearchPublicationController::class, 'uploadIpRegistration'])->name('publication.uploadIpRegistration');
    Route::post('/publication/{id}/journal-submission', [\App\Http\Controllers\ResearchPublicationController::class, 'logJournalSubmission'])->name('publication.logJournalSubmission');
    Route::post('/publication/{id}/archive-copy', [\App\Http\Controllers\ResearchPublicationController::class, 'archivePublishedCopy'])->name('publication.archivePublishedCopy');

    // Appendix D: Conduct of Local Research Forum Routes
    Route::post('/local-forum/create', [\App\Http\Controllers\LocalResearchForumController::class, 'createForum'])->name('local_forum.create');
    Route::post('/proposal/{id}/local-forum/submit', [\App\Http\Controllers\LocalResearchForumController::class, 'submitPaper'])->name('local_forum.submit');
    Route::post('/local-forum/{id}/endorse', [\App\Http\Controllers\LocalResearchForumController::class, 'endorseSubmission'])->name('local_forum.endorse');
    Route::post('/local-forum/{id}/accept', [\App\Http\Controllers\LocalResearchForumController::class, 'issueNoticeOfAcceptance'])->name('local_forum.accept');
    Route::post('/local-forum/{id}/certificate', [\App\Http\Controllers\LocalResearchForumController::class, 'uploadCertificate'])->name('local_forum.certificate');

    // Secure File Serving — serves files seamlessly from Supabase S3 or local public storage
    Route::get('/files/serve', function (\Illuminate\Http\Request $request) {
        $path = $request->query('path');

        if (!$path) {
            abort(404, 'File path not provided.');
        }

        // 1. Check local public storage first if file exists locally
        $publicDisk = \Storage::disk('public');
        if ($publicDisk->exists($path)) {
            return response()->file($publicDisk->path($path));
        }

        // 2. Check default cloud storage disk (Supabase S3)
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
    })->name('file.serve');
    
    // PDF Generation
    Route::get('/proposal/{id}/notice-of-acceptance', [\App\Http\Controllers\ResearchProposalController::class, 'downloadNoticeOfAcceptance'])->name('proposal.downloadNotice');
    Route::get('/proposal/{id}/notice-to-proceed', [\App\Http\Controllers\ResearchProposalController::class, 'downloadNoticeToProceed'])->name('proposal.downloadNTP');
    Route::get('/proposal/{id}/certificate-of-completion', [\App\Http\Controllers\ResearchProposalController::class, 'downloadCertificate'])->name('proposal.downloadCertificate');

    // Notifications
    Route::post('/notifications/mark-as-read', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markRead');

    // Profile Routes
    Route::get('/profile/{id?}', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/settings/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/settings/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/settings/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Admin ONLY Announcements CRUD
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/announcements', [\App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{id}', [\App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
});

/*
|--------------------------------------------------------------------------
| Reviewer Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:reviewer'])->group(function () {
    Route::get('/reviewer/proposals', [ResearchProposalController::class, 'reviewerIndex'])->name('reviewer.proposals');
    Route::post('/reviewer/proposals/{id}/update-status', [ResearchProposalController::class, 'updateStatus'])->name('reviewer.proposals.updateStatus');
    Route::post('/phase2/terminal-report/{id}/evaluate', [\App\Http\Controllers\TerminalReportController::class, 'evaluate'])->name('reviewer.phase2.evaluate');
});

/*
|--------------------------------------------------------------------------
| Coordinator Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:coordinator'])->group(function () {
    Route::get('/coordinator/proposals', [\App\Http\Controllers\CoordinatorController::class, 'index'])->name('coordinator.proposals');
    Route::post('/coordinator/proposals/{id}/endorse', [\App\Http\Controllers\CoordinatorController::class, 'endorse'])->name('coordinator.proposals.endorse');
    Route::post('/coordinator/proposals/{id}/submit-to-unit', [\App\Http\Controllers\CoordinatorController::class, 'submitToResearchUnit'])->name('coordinator.proposals.submitToUnit');
    Route::get('/coordinator/endorsement-form', [\App\Http\Controllers\CoordinatorController::class, 'generateBatchEndorsementForm'])->name('coordinator.batch_endorsement_form');
    Route::post('/phase2/monitoring/{id}/coordinator-verify', [\App\Http\Controllers\ProjectMonitoringController::class, 'coordinatorVerify'])->name('coordinator.phase2.verify');
});

/*
|--------------------------------------------------------------------------
| Staff Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/proposals', [\App\Http\Controllers\StaffController::class, 'index'])->name('staff.proposals');
    Route::post('/staff/proposals/{id}/forward', [\App\Http\Controllers\StaffController::class, 'forward'])->name('staff.proposals.forward');
});

Route::middleware(['auth', 'role:recording_staff'])->group(function () {
    Route::get('/recording-staff/dashboard', [\App\Http\Controllers\RecordingStaffController::class, 'dashboard'])->name('recording_staff.dashboard');
});

/*
|--------------------------------------------------------------------------
| Dean Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:dean', 'approved'])->group(function () {
    Route::get('/dean', [\App\Http\Controllers\DeanController::class, 'dashboard'])->name('dean.dashboard');
    Route::post('/dean/proposals/{id}/note-endorsement', [\App\Http\Controllers\DeanController::class, 'noteEndorsement'])->name('dean.noteEndorsement');
    Route::post('/dean/proposals/{id}/note-final', [\App\Http\Controllers\DeanController::class, 'noteFinalCopy'])->name('dean.noteFinal');
});

/*
|--------------------------------------------------------------------------
| VPREI Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:vprei', 'approved'])->group(function () {
    Route::get('/vprei', [\App\Http\Controllers\VpreiController::class, 'dashboard'])->name('vprei.dashboard');
    Route::post('/vprei/proposals/{id}/approve', [\App\Http\Controllers\VpreiController::class, 'approve'])->name('vprei.approve');
    Route::post('/phase2/activity-design/{id}/vprei-approve', [\App\Http\Controllers\ActivityDesignController::class, 'vpreiApprove'])->name('vprei.phase2.approveActivity');
});

/*
|--------------------------------------------------------------------------
| SUC President Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:president', 'approved'])->group(function () {
    Route::get('/president', [\App\Http\Controllers\PresidentController::class, 'dashboard'])->name('president.dashboard');
    Route::post('/president/approve-presentation/{id}', [\App\Http\Controllers\PresidentController::class, 'approvePresentation'])->name('president.approvePresentation');
});

/*
|--------------------------------------------------------------------------
| Budget Officer Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:budget_officer', 'approved'])->group(function () {
    Route::get('/budget', [\App\Http\Controllers\BudgetOfficerController::class, 'dashboard'])->name('budget.dashboard');
    Route::post('/budget/proposals/{id}/certify', [\App\Http\Controllers\BudgetOfficerController::class, 'certify'])->name('budget.certify');
    Route::post('/phase2/activity-design/{id}/budget-note', [\App\Http\Controllers\ActivityDesignController::class, 'budgetNote'])->name('budget.phase2.noteActivity');
});

/*
|--------------------------------------------------------------------------
| Finance Officer Routes (Phase 2 Procurement Approval)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:sao_finance', 'approved'])->group(function () {
    Route::get('/finance', [\App\Http\Controllers\FinanceOfficerController::class, 'dashboard'])->name('finance.dashboard');
    Route::post('/finance/pr/{id}/approve', [\App\Http\Controllers\FinanceOfficerController::class, 'approvePR'])->name('finance.pr.approve');
    Route::post('/finance/pr/{id}/reject', [\App\Http\Controllers\FinanceOfficerController::class, 'rejectPR'])->name('finance.pr.reject');
});

/*
|--------------------------------------------------------------------------
| 🔥 Admin Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/proposals', [ResearchProposalController::class, 'adminIndex'])->name('admin.proposals');
    Route::post('/admin/proposals/{id}/final-decision', [ResearchProposalController::class, 'adminFinalDecision'])->name('admin.proposals.finalDecision');
    Route::patch('/admin/proposals/{id}/phase', [ResearchProposalController::class, 'updatePhase'])->name('admin.proposals.updatePhase');
    Route::post('/admin/proposals/{id}/archive', [ResearchProposalController::class, 'archiveProposal'])->name('admin.proposals.archive');

    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('admin.users.index');
    Route::patch('/admin/users/{id}/role', [\App\Http\Controllers\Admin\UserManagementController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::delete('/admin/users/{id}', [\App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    // Reviewer Assignment
    Route::post('/admin/proposals/{id}/assign', [ResearchProposalController::class, 'assignReviewer'])->name('admin.proposals.assign');

    // Director Review & Endorsement Actions
    Route::post('/admin/proposals/{id}/accept-in-house', [ResearchProposalController::class, 'acceptForInHouseReview'])->name('admin.proposals.acceptInHouse');
    Route::post('/admin/proposals/{id}/endorse-vprei', [ResearchProposalController::class, 'endorseToVprei'])->name('admin.proposals.endorseVprei');

    // Phase 2 Director Approvals
    Route::post('/phase2/activity-design/{id}/director-note', [\App\Http\Controllers\ActivityDesignController::class, 'directorNote'])->name('admin.phase2.noteActivity');
    Route::post('/phase2/purchase-request/{id}/director-countersign', [\App\Http\Controllers\PurchaseRequestController::class, 'directorCountersign'])->name('admin.phase2.countersignPR');
    Route::post('/phase2/terminal-report/{id}/issue-completion', [\App\Http\Controllers\TerminalReportController::class, 'issueCompletion'])->name('admin.phase2.issueCompletion');

    // Milestones Status Update
    Route::patch('/admin/milestones/{id}/status', [\App\Http\Controllers\ResearchMilestoneController::class, 'updateStatus'])->name('admin.milestones.updateStatus');
});

/*
|--------------------------------------------------------------------------
| 👑 Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/superadmin', [\App\Http\Controllers\SuperAdminController::class, 'index'])->name('superadmin.dashboard');
    Route::get('/superadmin/users', [\App\Http\Controllers\SuperAdminController::class, 'users'])->name('superadmin.users');
    Route::post('/superadmin/users/{id}/approve', [\App\Http\Controllers\SuperAdminController::class, 'approveAdmin'])->name('superadmin.users.approve');
    Route::delete('/superadmin/users/{id}', [\App\Http\Controllers\SuperAdminController::class, 'destroyUser'])->name('superadmin.users.destroy');
    Route::get('/superadmin/settings', [\App\Http\Controllers\SuperAdminController::class, 'settings'])->name('superadmin.settings');
    Route::post('/superadmin/settings', [\App\Http\Controllers\SuperAdminController::class, 'updateSettings'])->name('superadmin.settings.update');
    Route::get('/superadmin/logs', [\App\Http\Controllers\SuperAdminController::class, 'activityLogs'])->name('superadmin.logs');
});

/*
|--------------------------------------------------------------------------
| Default Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return redirect('/redirect');
})->middleware(['auth', 'approved'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';