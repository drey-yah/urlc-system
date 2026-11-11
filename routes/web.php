<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ResearchProposalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProposalAnnotationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Session Keep-Alive & CSRF Token Refresh endpoint
Route::get('/ping-session', function () {
    return response()->json([
        'status' => 'alive',
        'csrf_token' => csrf_token(),
    ]);
})->middleware(['web']);

// Download Official University Research Proposal Template (.docx)
Route::get('/templates/download/proposal', function () {
    $filePath = public_path('templates/UA-RESEARCH-PROPOSAL-TEMPLATE-RESU-FM-015.docx');
    if (!file_exists($filePath)) {
        abort(404, 'Official template document not found.');
    }
    return response()->streamDownload(function () use ($filePath) {
        $stream = fopen($filePath, 'rb');
        fpassthru($stream);
        fclose($stream);
    }, '15-RESEARCH-PROPOSAL-TEMPLATE-REVISED (RESU-FM-015).docx', [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'Content-Length' => filesize($filePath),
    ]);
})->middleware(['auth', 'approved', 'verified'])->name('template.download.proposal');

/*
|--------------------------------------------------------------------------
| Redirect After Login (Role-Based)
|--------------------------------------------------------------------------
*/

Route::get('/redirect', [DashboardController::class, 'redirectByRole'])
    ->middleware(['auth', 'approved', 'verified'])
    ->name('redirect');

Route::post('/switch-role', [DashboardController::class, 'switchRole'])
    ->middleware(['auth', 'approved', 'verified'])
    ->name('role.switch');

/*
|--------------------------------------------------------------------------
| Protected Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::get('/admin', [DashboardController::class, 'adminDashboard'])
    ->middleware(['auth', 'role:admin', 'approved', 'verified'])
    ->name('admin.dashboard');

Route::get('/reviewer', [DashboardController::class, 'reviewerDashboard'])
    ->middleware(['auth', 'role:reviewer', 'approved', 'verified'])
    ->name('reviewer.dashboard');

Route::get('/researcher', [DashboardController::class, 'researcherDashboard'])
    ->middleware(['auth', 'role:researcher', 'approved', 'verified'])
    ->name('researcher.dashboard');

Route::get('/coordinator', [\App\Http\Controllers\CoordinatorController::class, 'dashboard'])
    ->middleware(['auth', 'role:coordinator', 'approved', 'verified'])->name('coordinator.dashboard');

Route::get('/staff', [\App\Http\Controllers\StaffController::class, 'dashboard'])
    ->middleware(['auth', 'role:staff', 'approved', 'verified'])->name('staff.dashboard');

/*
|--------------------------------------------------------------------------
| Researcher Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:researcher', 'approved', 'verified'])->group(function () {
    Route::get('/proposal/create', [ResearchProposalController::class, 'create'])->name('proposal.create');
    Route::post('/proposal/store', [ResearchProposalController::class, 'store'])->name('proposal.store');
    Route::post('/proposal/extract-pdf', [ResearchProposalController::class, 'extractPdfMetadata'])->name('proposal.extractPdf');
    Route::get('/proposal/my', [ResearchProposalController::class, 'index'])->name('proposal.index');

    Route::get('/proposal/{id}/edit', [ResearchProposalController::class, 'edit'])->name('proposal.edit');
    Route::put('/proposal/{id}/update', [ResearchProposalController::class, 'update'])->name('proposal.update');
    Route::delete('/proposal/{id}', [ResearchProposalController::class, 'destroy'])->name('proposal.destroy');
    Route::post('/proposal/{id}/submit-final-copy', [ResearchProposalController::class, 'submitFinalCopy'])->name('proposal.submitFinalCopy');
    Route::post('/proposal/{id}/collaboration/{action}', [ResearchProposalController::class, 'respondToCollaboration'])
        ->name('collaboration.respond')
        ->where('action', 'accept|decline');

    // Phase 2 Implementation Routes (Researcher Submissions)
    Route::post('/proposal/{id}/activity-design', [\App\Http\Controllers\ActivityDesignController::class, 'store'])->name('activity_design.store');
    Route::post('/proposal/{id}/purchase-request', [\App\Http\Controllers\PurchaseRequestController::class, 'store'])->name('purchase_request.store');
    Route::post('/proposal/{id}/monitoring', [\App\Http\Controllers\ProjectMonitoringController::class, 'store'])->name('monitoring.store');
    Route::post('/proposal/{id}/terminal-report', [\App\Http\Controllers\TerminalReportController::class, 'store'])->name('terminal_report.store');

    Route::post('/external-funding', [\App\Http\Controllers\ExternalFundingController::class, 'store'])->name('external-funding.store');
    Route::post('/external-funding/{id}/resubmit', [\App\Http\Controllers\ExternalFundingController::class, 'resubmit'])->name('external-funding.resubmit');
    Route::post('/external-funding/{id}/terminal-report', [\App\Http\Controllers\ExternalFundingController::class, 'submitTerminalReport'])->name('external-funding.terminal-report');
});

// Shared Proposal Routes (All authenticated users)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/external-funding', [\App\Http\Controllers\ExternalFundingController::class, 'index'])->middleware('approved')->name('external-funding.index');
    Route::get('/proposal/{id}', [ResearchProposalController::class, 'show'])->name('proposal.show');
    Route::get('/proposal/{id}/review-workspace', [ProposalAnnotationController::class, 'workspace'])->name('proposal.reviewWorkspace');
    Route::post('/proposal/{id}/annotations', [ProposalAnnotationController::class, 'store'])->name('proposal.annotations.store');
    Route::patch('/annotations/{id}/toggle-resolve', [ProposalAnnotationController::class, 'toggleResolve'])->name('proposal.annotations.toggleResolve');
    Route::delete('/annotations/{id}', [ProposalAnnotationController::class, 'destroy'])->name('proposal.annotations.destroy');
    Route::get('/proposal/{id}/review-summary', [ProposalAnnotationController::class, 'exportSummary'])->name('proposal.reviewSummary');
    Route::get('/proposal/{id}/endorsement-form', [\App\Http\Controllers\CoordinatorController::class, 'generateEndorsementForm'])->name('proposal.endorsement_form');
    Route::get('/proposal/{id}/resu-fm015', [\App\Http\Controllers\ResearchProposalController::class, 'exportResuFm015'])->name('proposal.resu_fm015');
    Route::get('/proposal/{id}/notice', [ResearchProposalController::class, 'downloadNoticeOfAcceptance'])->name('proposal.downloadNotice');
    Route::get('/proposal/{id}/ntp', [ResearchProposalController::class, 'downloadNoticeToProceed'])->name('proposal.downloadNTP');
    Route::get('/proposal/{id}/certificate', [ResearchProposalController::class, 'downloadCertificate'])->name('proposal.downloadCertificate');
    Route::post('/proposal/{id}/submit-final', [\App\Http\Controllers\ResearchProposalController::class, 'submitFinalManuscript'])->name('proposal.submitFinal');
    
    // Call for Papers (Announcements)
    Route::get('/announcements', [\App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements/{id}/like', [\App\Http\Controllers\AnnouncementInteractionController::class, 'like'])->name('announcements.like');
    Route::post('/announcements/{id}/comment', [\App\Http\Controllers\AnnouncementInteractionController::class, 'comment'])->name('announcements.comment');

    // Research Schedules & Calendar
    Route::get('/schedules', [\App\Http\Controllers\ResearchScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [\App\Http\Controllers\ResearchScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/schedules/{id}', [\App\Http\Controllers\ResearchScheduleController::class, 'show'])->name('schedules.show');
    Route::put('/schedules/{id}', [\App\Http\Controllers\ResearchScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{id}', [\App\Http\Controllers\ResearchScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::get('/schedules/{id}/ics', [\App\Http\Controllers\ResearchScheduleController::class, 'downloadIcs'])->name('schedules.ics');
    Route::get('/schedules/{id}/attachment', [\App\Http\Controllers\ResearchScheduleController::class, 'downloadAttachment'])->name('schedules.downloadAttachment');
    Route::post('/schedules/{id}/rsvp', [\App\Http\Controllers\ResearchScheduleController::class, 'rsvp'])->name('schedules.rsvp');
    Route::post('/schedules/{id}/reminder', [\App\Http\Controllers\ResearchScheduleController::class, 'sendImmediateReminder'])->name('schedules.reminder');

    // Messaging Module (Gmail style)
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/{id}/reply', [\App\Http\Controllers\MessageController::class, 'reply'])->name('messages.reply');
    Route::delete('/messages/{id}', [\App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');
    // Email Templates
    Route::redirect('/email-templates', '/messages')->name('email.templates');

    // Repository
    Route::get('/repository', [\App\Http\Controllers\RepositoryController::class, 'index'])->name('repository.index');

    // Notifications
    Route::post('/notifications/mark-read', [DashboardController::class, 'markNotificationsAsRead'])->name('notifications.markRead');
    Route::get('/notifications/{id}/read', [DashboardController::class, 'markSingleNotificationAsRead'])->name('notifications.readSingle');
    Route::get('/notifications/poll', [DashboardController::class, 'pollNotifications'])->name('notifications.poll');

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

    // Secure File Serving — serves files seamlessly with path traversal protection
    Route::get('/files/serve', [DashboardController::class, 'serveFile'])->name('file.serve');

    // List of Approved Researches (Institutional Dossier: Prepared by Director, Noted by VPREI)
    Route::get('/approved-researches', [\App\Http\Controllers\ApprovedResearchListController::class, 'index'])->name('approved_researches.index');
    Route::get('/approved-researches/create', [\App\Http\Controllers\ApprovedResearchListController::class, 'create'])->name('approved_researches.create');
    Route::post('/approved-researches', [\App\Http\Controllers\ApprovedResearchListController::class, 'store'])->name('approved_researches.store');
    Route::get('/approved-researches/{id}', [\App\Http\Controllers\ApprovedResearchListController::class, 'show'])->name('approved_researches.show');
    Route::post('/approved-researches/{id}/note', [\App\Http\Controllers\ApprovedResearchListController::class, 'note'])->name('approved_researches.note');
    Route::get('/approved-researches/{id}/pdf', [\App\Http\Controllers\ApprovedResearchListController::class, 'exportPdf'])->name('approved_researches.pdf');
    Route::delete('/approved-researches/{id}', [\App\Http\Controllers\ApprovedResearchListController::class, 'destroy'])->name('approved_researches.destroy');

    // In-house Reviewed Research Proposals (Institutional Dossier: Prepared by Director, Noted by VPREI)
    Route::get('/in-house-reviewed-proposals', [\App\Http\Controllers\InHouseReviewListController::class, 'index'])->name('in_house_reviews.index');
    Route::get('/in-house-reviewed-proposals/create', [\App\Http\Controllers\InHouseReviewListController::class, 'create'])->name('in_house_reviews.create');
    Route::post('/in-house-reviewed-proposals', [\App\Http\Controllers\InHouseReviewListController::class, 'store'])->name('in_house_reviews.store');
    Route::get('/in-house-reviewed-proposals/{id}', [\App\Http\Controllers\InHouseReviewListController::class, 'show'])->name('in_house_reviews.show');
    Route::post('/in-house-reviewed-proposals/{id}/note', [\App\Http\Controllers\InHouseReviewListController::class, 'note'])->name('in_house_reviews.note');
    Route::get('/in-house-reviewed-proposals/{id}/pdf', [\App\Http\Controllers\InHouseReviewListController::class, 'exportPdf'])->name('in_house_reviews.pdf');
    Route::delete('/in-house-reviewed-proposals/{id}', [\App\Http\Controllers\InHouseReviewListController::class, 'destroy'])->name('in_house_reviews.destroy');

    // List of On-going Researches (Institutional Dossier: Prepared by Director, Noted by VPREI)
    Route::get('/ongoing-researches', [\App\Http\Controllers\OngoingResearchListController::class, 'index'])->name('ongoing_researches.index');
    Route::get('/ongoing-researches/create', [\App\Http\Controllers\OngoingResearchListController::class, 'create'])->name('ongoing_researches.create');
    Route::post('/ongoing-researches', [\App\Http\Controllers\OngoingResearchListController::class, 'store'])->name('ongoing_researches.store');
    Route::get('/ongoing-researches/{id}', [\App\Http\Controllers\OngoingResearchListController::class, 'show'])->name('ongoing_researches.show');
    Route::post('/ongoing-researches/{id}/note', [\App\Http\Controllers\OngoingResearchListController::class, 'note'])->name('ongoing_researches.note');
    Route::get('/ongoing-researches/{id}/pdf', [\App\Http\Controllers\OngoingResearchListController::class, 'exportPdf'])->name('ongoing_researches.pdf');
    Route::delete('/ongoing-researches/{id}', [\App\Http\Controllers\OngoingResearchListController::class, 'destroy'])->name('ongoing_researches.destroy');

    // List of Published Researches (Institutional Dossier: Prepared by Director, Noted by VPREI)
    Route::get('/published-researches-dossiers', [\App\Http\Controllers\PublishedResearchListController::class, 'index'])->name('published_researches_dossiers.index');
    Route::get('/published-researches-dossiers/create', [\App\Http\Controllers\PublishedResearchListController::class, 'create'])->name('published_researches_dossiers.create');
    Route::post('/published-researches-dossiers', [\App\Http\Controllers\PublishedResearchListController::class, 'store'])->name('published_researches_dossiers.store');
    Route::get('/published-researches-dossiers/{id}', [\App\Http\Controllers\PublishedResearchListController::class, 'show'])->name('published_researches_dossiers.show');
    Route::post('/published-researches-dossiers/{id}/note', [\App\Http\Controllers\PublishedResearchListController::class, 'note'])->name('published_researches_dossiers.note');
    Route::get('/published-researches-dossiers/{id}/pdf', [\App\Http\Controllers\PublishedResearchListController::class, 'exportPdf'])->name('published_researches_dossiers.pdf');
    Route::delete('/published-researches-dossiers/{id}', [\App\Http\Controllers\PublishedResearchListController::class, 'destroy'])->name('published_researches_dossiers.destroy');

    // List of Presented Researches (Institutional Dossier: Prepared by Receiving Staff, Noted by Director)
    Route::get('/presented-researches', [\App\Http\Controllers\PresentedResearchListController::class, 'index'])->name('presented_researches.index');
    Route::get('/presented-researches/create', [\App\Http\Controllers\PresentedResearchListController::class, 'create'])->name('presented_researches.create');
    Route::post('/presented-researches', [\App\Http\Controllers\PresentedResearchListController::class, 'store'])->name('presented_researches.store');
    Route::get('/presented-researches/{id}', [\App\Http\Controllers\PresentedResearchListController::class, 'show'])->name('presented_researches.show');
    Route::post('/presented-researches/{id}/note', [\App\Http\Controllers\PresentedResearchListController::class, 'note'])->name('presented_researches.note');
    Route::get('/presented-researches/{id}/pdf', [\App\Http\Controllers\PresentedResearchListController::class, 'exportPdf'])->name('presented_researches.pdf');
    Route::delete('/presented-researches/{id}', [\App\Http\Controllers\PresentedResearchListController::class, 'destroy'])->name('presented_researches.destroy');

    // List of Research for Inception (Institutional Dossier: Prepared by Recording Staff, Noted by Director)
    Route::get('/inception-researches', [\App\Http\Controllers\InceptionResearchListController::class, 'index'])->name('inception_researches.index');
    Route::get('/inception-researches/create', [\App\Http\Controllers\InceptionResearchListController::class, 'create'])->name('inception_researches.create');
    Route::post('/inception-researches', [\App\Http\Controllers\InceptionResearchListController::class, 'store'])->name('inception_researches.store');
    Route::get('/inception-researches/{id}', [\App\Http\Controllers\InceptionResearchListController::class, 'show'])->name('inception_researches.show');
    Route::post('/inception-researches/{id}/note', [\App\Http\Controllers\InceptionResearchListController::class, 'note'])->name('inception_researches.note');
    Route::get('/inception-researches/{id}/pdf', [\App\Http\Controllers\InceptionResearchListController::class, 'exportPdf'])->name('inception_researches.pdf');
    Route::delete('/inception-researches/{id}', [\App\Http\Controllers\InceptionResearchListController::class, 'destroy'])->name('inception_researches.destroy');

    // List of Researchers Qualified to Avail Service Credits (Institutional Dossier: Prepared by Director, Approved by VPREI)
    Route::get('/service-credits', [\App\Http\Controllers\ServiceCreditListController::class, 'index'])->name('service_credits.index');
    Route::get('/service-credits/create', [\App\Http\Controllers\ServiceCreditListController::class, 'create'])->name('service_credits.create');
    Route::post('/service-credits', [\App\Http\Controllers\ServiceCreditListController::class, 'store'])->name('service_credits.store');
    Route::get('/service-credits/{id}', [\App\Http\Controllers\ServiceCreditListController::class, 'show'])->name('service_credits.show');
    Route::post('/service-credits/{id}/approve', [\App\Http\Controllers\ServiceCreditListController::class, 'approve'])->name('service_credits.approve');
    Route::get('/service-credits/{id}/pdf', [\App\Http\Controllers\ServiceCreditListController::class, 'exportPdf'])->name('service_credits.pdf');
    Route::delete('/service-credits/{id}', [\App\Http\Controllers\ServiceCreditListController::class, 'destroy'])->name('service_credits.destroy');

    // Completed Researches Summary Form (Institutional Dossier: Prepared by College Coordinator, Noted by College Dean)
    Route::get('/completed-researches-summary', [\App\Http\Controllers\CompletedResearchSummaryController::class, 'index'])->name('completed_researches_summary.index');
    Route::get('/completed-researches-summary/create', [\App\Http\Controllers\CompletedResearchSummaryController::class, 'create'])->name('completed_researches_summary.create');
    Route::post('/completed-researches-summary', [\App\Http\Controllers\CompletedResearchSummaryController::class, 'store'])->name('completed_researches_summary.store');
    Route::get('/completed-researches-summary/{id}', [\App\Http\Controllers\CompletedResearchSummaryController::class, 'show'])->name('completed_researches_summary.show');
    Route::post('/completed-researches-summary/{id}/note', [\App\Http\Controllers\CompletedResearchSummaryController::class, 'note'])->name('completed_researches_summary.note');
    Route::get('/completed-researches-summary/{id}/pdf', [\App\Http\Controllers\CompletedResearchSummaryController::class, 'exportPdf'])->name('completed_researches_summary.pdf');
    Route::delete('/completed-researches-summary/{id}', [\App\Http\Controllers\CompletedResearchSummaryController::class, 'destroy'])->name('completed_researches_summary.destroy');

    // Research Accomplishment Report (RESU-FM-004: Prepared by Coordinator, Noted & Submitted by Dean, Received by Director)
    Route::get('/accomplishment-reports', [\App\Http\Controllers\ResearchAccomplishmentReportController::class, 'index'])->name('accomplishment_reports.index');
    Route::get('/accomplishment-reports/create', [\App\Http\Controllers\ResearchAccomplishmentReportController::class, 'create'])->name('accomplishment_reports.create');
    Route::post('/accomplishment-reports', [\App\Http\Controllers\ResearchAccomplishmentReportController::class, 'store'])->name('accomplishment_reports.store');
    Route::get('/accomplishment-reports/{id}', [\App\Http\Controllers\ResearchAccomplishmentReportController::class, 'show'])->name('accomplishment_reports.show');
    Route::post('/accomplishment-reports/{id}/note-and-submit', [\App\Http\Controllers\ResearchAccomplishmentReportController::class, 'noteAndSubmit'])->name('accomplishment_reports.note_and_submit');
    Route::post('/accomplishment-reports/{id}/acknowledge', [\App\Http\Controllers\ResearchAccomplishmentReportController::class, 'acknowledge'])->name('accomplishment_reports.acknowledge');
    Route::get('/accomplishment-reports/{id}/pdf', [\App\Http\Controllers\ResearchAccomplishmentReportController::class, 'exportPdf'])->name('accomplishment_reports.pdf');
    Route::delete('/accomplishment-reports/{id}', [\App\Http\Controllers\ResearchAccomplishmentReportController::class, 'destroy'])->name('accomplishment_reports.destroy');

    // Internally-Funded Research Proposal Endorsement Form (Prepared by Director, Recommending: VPREI & VPAF, Approved by President)
    Route::get('/internally-funded-endorsements', [\App\Http\Controllers\InternallyFundedEndorsementController::class, 'index'])->name('internally_funded_endorsements.index');
    Route::get('/internally-funded-endorsements/create', [\App\Http\Controllers\InternallyFundedEndorsementController::class, 'create'])->name('internally_funded_endorsements.create');
    Route::post('/internally-funded-endorsements', [\App\Http\Controllers\InternallyFundedEndorsementController::class, 'store'])->name('internally_funded_endorsements.store');
    Route::get('/internally-funded-endorsements/{id}', [\App\Http\Controllers\InternallyFundedEndorsementController::class, 'show'])->name('internally_funded_endorsements.show');
    Route::post('/internally-funded-endorsements/{id}/recommend-vprei', [\App\Http\Controllers\InternallyFundedEndorsementController::class, 'recommendVprei'])->name('internally_funded_endorsements.recommend_vprei');
    Route::post('/internally-funded-endorsements/{id}/recommend-vpaf', [\App\Http\Controllers\InternallyFundedEndorsementController::class, 'recommendVpaf'])->name('internally_funded_endorsements.recommend_vpaf');
    Route::post('/internally-funded-endorsements/{id}/approve-president', [\App\Http\Controllers\InternallyFundedEndorsementController::class, 'approvePresident'])->name('internally_funded_endorsements.approve_president');
    Route::post('/internally-funded-endorsements/{id}/return', [\App\Http\Controllers\InternallyFundedEndorsementController::class, 'returnDossier'])->name('internally_funded_endorsements.return');
    Route::get('/internally-funded-endorsements/{id}/pdf', [\App\Http\Controllers\InternallyFundedEndorsementController::class, 'exportPdf'])->name('internally_funded_endorsements.pdf');
    Route::delete('/internally-funded-endorsements/{id}', [\App\Http\Controllers\InternallyFundedEndorsementController::class, 'destroy'])->name('internally_funded_endorsements.destroy');

    // Profile Routes
    Route::get('/profile/{id?}', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/settings/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/settings/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/settings/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Admin ONLY Announcements CRUD
Route::middleware(['auth', 'role:admin', 'verified'])->group(function () {
    Route::post('/announcements', [\App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{id}', [\App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
});

/*
|--------------------------------------------------------------------------
| Reviewer Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:reviewer', 'verified'])->group(function () {
    Route::get('/reviewer/proposals', [ResearchProposalController::class, 'reviewerIndex'])->name('reviewer.proposals');
    Route::post('/reviewer/proposals/{id}/update-status', [ResearchProposalController::class, 'updateStatus'])->name('reviewer.proposals.updateStatus');
    Route::post('/phase2/terminal-report/{id}/evaluate', [\App\Http\Controllers\TerminalReportController::class, 'evaluate'])->name('reviewer.phase2.evaluate');
});

/*
|--------------------------------------------------------------------------
| Coordinator Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:coordinator', 'verified'])->group(function () {
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

Route::middleware(['auth', 'role:staff', 'verified'])->group(function () {
    Route::get('/staff/proposals', [\App\Http\Controllers\StaffController::class, 'index'])->name('staff.proposals');
    Route::post('/staff/proposals/{id}/forward', [\App\Http\Controllers\StaffController::class, 'forward'])->name('staff.proposals.forward');
});

Route::middleware(['auth', 'role:recording_staff', 'verified'])->group(function () {
    Route::get('/recording-staff/dashboard', [\App\Http\Controllers\RecordingStaffController::class, 'dashboard'])->name('recording_staff.dashboard');
});

Route::middleware(['auth', 'role:recording_staff,admin,budget_officer', 'verified'])->group(function () {
    Route::get('/recording-staff/budget-allocations', [\App\Http\Controllers\RecordingStaffBudgetAllocationController::class, 'index'])->name('recording_staff.budget_allocations.index');
    Route::post('/recording-staff/budget-allocations', [\App\Http\Controllers\RecordingStaffBudgetAllocationController::class, 'store'])->name('recording_staff.budget_allocations.store');
    Route::put('/recording-staff/budget-allocations/{id}', [\App\Http\Controllers\RecordingStaffBudgetAllocationController::class, 'update'])->name('recording_staff.budget_allocations.update');
    Route::post('/recording-staff/budget-allocations/update-college', [\App\Http\Controllers\RecordingStaffBudgetAllocationController::class, 'updateCollege'])->name('recording_staff.budget_allocations.update_college');
    Route::post('/recording-staff/budget-allocations/save-allotment', [\App\Http\Controllers\RecordingStaffBudgetAllocationController::class, 'saveCollegeAllotment'])->name('recording_staff.budget_allocations.save_allotment');
    Route::delete('/recording-staff/budget-allocations/delete-allotment', [\App\Http\Controllers\RecordingStaffBudgetAllocationController::class, 'deleteCollegeAllotment'])->name('recording_staff.budget_allocations.delete_allotment');
    Route::delete('/recording-staff/budget-allocations/{id}', [\App\Http\Controllers\RecordingStaffBudgetAllocationController::class, 'destroy'])->name('recording_staff.budget_allocations.destroy');
    Route::get('/recording-staff/budget-allocations/print', [\App\Http\Controllers\RecordingStaffBudgetAllocationController::class, 'print'])->name('recording_staff.budget_allocations.print');
});

/*
|--------------------------------------------------------------------------
| Dean Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:dean', 'approved', 'verified'])->group(function () {
    Route::get('/dean', [\App\Http\Controllers\DeanController::class, 'dashboard'])->name('dean.dashboard');
    Route::post('/dean/proposals/{id}/note-endorsement', [\App\Http\Controllers\DeanController::class, 'noteEndorsement'])->name('dean.noteEndorsement');
    Route::post('/dean/proposals/{id}/note-final', [\App\Http\Controllers\DeanController::class, 'noteFinalCopy'])->name('dean.noteFinal');
});

/*
|--------------------------------------------------------------------------
| VPREI Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:vprei', 'approved', 'verified'])->group(function () {
    Route::get('/vprei', [\App\Http\Controllers\VpreiController::class, 'dashboard'])->name('vprei.dashboard');
    Route::post('/vprei/proposals/{id}/approve', [\App\Http\Controllers\VpreiController::class, 'approve'])->name('vprei.approve');
    Route::post('/phase2/activity-design/{id}/vprei-approve', [\App\Http\Controllers\ActivityDesignController::class, 'vpreiApprove'])->name('vprei.phase2.approveActivity');
});

/*
|--------------------------------------------------------------------------
| VPAF (VP for Academic & Finance) Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:vpaf', 'approved', 'verified'])->group(function () {
    Route::get('/vpaf', [\App\Http\Controllers\VpafController::class, 'dashboard'])->name('vpaf.dashboard');
});

/*
|--------------------------------------------------------------------------
| SUC President Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:president', 'approved', 'verified'])->group(function () {
    Route::get('/president', [\App\Http\Controllers\PresidentController::class, 'dashboard'])->name('president.dashboard');
    Route::post('/president/approve-presentation/{id}', [\App\Http\Controllers\PresidentController::class, 'approvePresentation'])->name('president.approvePresentation');
    Route::post('/external-funding/{id}/endorse-agency', [\App\Http\Controllers\ExternalFundingController::class, 'endorseToAgency'])->name('external-funding.endorse-agency');
    Route::post('/external-funding/{id}/agreement', [\App\Http\Controllers\ExternalFundingController::class, 'executeAgreement'])->name('external-funding.agreement');
});

/*
|--------------------------------------------------------------------------
| Budget Officer Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:budget_officer', 'approved', 'verified'])->group(function () {
    Route::get('/budget', [\App\Http\Controllers\BudgetOfficerController::class, 'dashboard'])->name('budget.dashboard');
    Route::post('/budget/proposals/{id}/certify', [\App\Http\Controllers\BudgetOfficerController::class, 'certify'])->name('budget.certify');
    Route::post('/phase2/activity-design/{id}/budget-note', [\App\Http\Controllers\ActivityDesignController::class, 'budgetNote'])->name('budget.phase2.noteActivity');
});

/*
|--------------------------------------------------------------------------
| Finance Officer Routes (Phase 2 Procurement Approval)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:sao_finance', 'approved', 'verified'])->group(function () {
    Route::get('/finance', [\App\Http\Controllers\FinanceOfficerController::class, 'dashboard'])->name('finance.dashboard');
    Route::post('/finance/pr/{id}/approve', [\App\Http\Controllers\FinanceOfficerController::class, 'approvePR'])->name('finance.pr.approve');
    Route::post('/finance/pr/{id}/reject', [\App\Http\Controllers\FinanceOfficerController::class, 'rejectPR'])->name('finance.pr.reject');
});

/*
|--------------------------------------------------------------------------
| 🔥 Admin Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin', 'verified'])->group(function () {
    Route::get('/admin/proposals', [ResearchProposalController::class, 'adminIndex'])->name('admin.proposals');
    Route::post('/admin/proposals/{id}/final-decision', [ResearchProposalController::class, 'adminFinalDecision'])->name('admin.proposals.finalDecision');
    Route::patch('/admin/proposals/{id}/phase', [ResearchProposalController::class, 'updatePhase'])->name('admin.proposals.updatePhase');
    Route::post('/admin/proposals/{id}/archive', [ResearchProposalController::class, 'archiveProposal'])->name('admin.proposals.archive');

    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('admin.users.index');
    Route::patch('/admin/users/{id}/role', [\App\Http\Controllers\Admin\UserManagementController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::post('/admin/users/{id}/roles', [\App\Http\Controllers\Admin\UserManagementController::class, 'updateRoles'])->name('admin.users.updateRoles');
    Route::delete('/admin/users/{id}', [\App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    // Reviewer Assignment
    Route::post('/admin/proposals/{id}/assign', [ResearchProposalController::class, 'assignReviewer'])->name('admin.proposals.assign');
    Route::delete('/admin/proposals/{id}/unassign/{reviewer_id}', [ResearchProposalController::class, 'unassignReviewer'])->name('admin.proposals.unassign');

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

Route::middleware(['auth', 'approved', 'verified'])->group(function () {
    Route::post('/external-funding/{id}/endorse-university', [\App\Http\Controllers\ExternalFundingController::class, 'endorseUniversity'])->name('external-funding.endorse-university');
    Route::post('/external-funding/{id}/agency-decision', [\App\Http\Controllers\ExternalFundingController::class, 'recordAgencyDecision'])->name('external-funding.agency-decision');
    Route::post('/external-funding/{id}/monitor', [\App\Http\Controllers\ExternalFundingController::class, 'markMonitoring'])->name('external-funding.monitor');
    Route::post('/external-funding/{id}/close', [\App\Http\Controllers\ExternalFundingController::class, 'closeGrant'])->name('external-funding.close');
});

/*
|--------------------------------------------------------------------------
| 👑 Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'superadmin', 'verified'])->group(function () {
    Route::get('/superadmin', [\App\Http\Controllers\SuperAdminController::class, 'index'])->name('superadmin.dashboard');
    Route::get('/superadmin/users', [\App\Http\Controllers\SuperAdminController::class, 'users'])->name('superadmin.users');
    Route::post('/superadmin/users/{id}/approve', [\App\Http\Controllers\SuperAdminController::class, 'approveAdmin'])->name('superadmin.users.approve');
    Route::post('/superadmin/users/{id}/roles', [\App\Http\Controllers\SuperAdminController::class, 'updateUserRoles'])->name('superadmin.users.updateRoles');
    Route::delete('/superadmin/users/{id}', [\App\Http\Controllers\SuperAdminController::class, 'destroyUser'])->name('superadmin.users.destroy');
    Route::get('/superadmin/settings', [\App\Http\Controllers\SuperAdminController::class, 'settings'])->name('superadmin.settings');
    Route::post('/superadmin/settings', [\App\Http\Controllers\SuperAdminController::class, 'updateSettings'])->name('superadmin.settings.update');
    Route::get('/superadmin/logs', [\App\Http\Controllers\SuperAdminController::class, 'activityLogs'])->name('superadmin.logs');
    Route::get('/superadmin/logs/export', [\App\Http\Controllers\SuperAdminController::class, 'exportActivityLogs'])->name('superadmin.logs.export');
});

/*
|--------------------------------------------------------------------------
| Default Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'redirectByRole'])
    ->middleware(['auth', 'approved', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
