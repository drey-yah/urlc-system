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
    } elseif ($role == 'budget_officer') {
        return redirect('/budget');
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
    return view('admin.dashboard');
})->middleware(['auth', 'role:admin', 'approved']);

Route::get('/reviewer', function () {
    return view('reviewer.dashboard');
})->middleware(['auth', 'role:reviewer', 'approved']);

Route::get('/researcher', function () {
    return view('researcher.dashboard');
})->middleware(['auth', 'role:researcher', 'approved']);

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
});

// Shared Proposal Routes (All authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/proposal/{id}', [ResearchProposalController::class, 'show'])->name('proposal.show');
    Route::post('/proposal/{id}/submit-final', [\App\Http\Controllers\ResearchProposalController::class, 'submitFinalManuscript'])->name('proposal.submitFinal');
    
    // Call for Papers (Announcements)
    Route::get('/announcements', [\App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements/{id}/like', [\App\Http\Controllers\AnnouncementInteractionController::class, 'like'])->name('announcements.like');
    Route::post('/announcements/{id}/comment', [\App\Http\Controllers\AnnouncementInteractionController::class, 'comment'])->name('announcements.comment');

    // Email Templates Preview
    Route::get('/email-templates', function() {
        return view('emails.templates');
    })->name('email.templates');

    // Repository
    Route::get('/repository', [\App\Http\Controllers\RepositoryController::class, 'index'])->name('repository.index');

    // Milestones
    Route::post('/proposal/{id}/milestones', [\App\Http\Controllers\ResearchMilestoneController::class, 'store'])->name('milestones.store');

    // Secure File Serving — generates a signed S3/Supabase URL for private bucket files
    Route::get('/files/serve', function (\Illuminate\Http\Request $request) {
        $path = $request->query('path');

        if (!$path) {
            abort(404, 'File path not provided.');
        }

        $disk = \Storage::disk(config('filesystems.default', 'public'));

        try {
            // For S3/Supabase: generate a 60-minute temporary signed URL
            $url = $disk->temporaryUrl($path, now()->addMinutes(60));
        } catch (\Exception $e) {
            // Fallback for local disk (does not support temporaryUrl)
            $url = $disk->url($path);
        }

        return redirect($url);
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
});

/*
|--------------------------------------------------------------------------
| Budget Officer Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:budget_officer', 'approved'])->group(function () {
    Route::get('/budget', [\App\Http\Controllers\BudgetOfficerController::class, 'dashboard'])->name('budget.dashboard');
    Route::post('/budget/proposals/{id}/certify', [\App\Http\Controllers\BudgetOfficerController::class, 'certify'])->name('budget.certify');
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