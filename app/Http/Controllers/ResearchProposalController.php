<?php

namespace App\Http\Controllers;

use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Http\Request;

class ResearchProposalController extends Controller
{
    // Researcher: View own proposals
    public function index()
    {
        $leadProposals = auth()->user()->leadProposals()->with(['collaborators', 'milestones'])->latest()->get();
        $collaboratedProposals = auth()->user()->collaboratedProposals()->with(['user', 'collaborators', 'milestones'])->latest()->get();
        
        return view('researcher.my_proposals', compact('leadProposals', 'collaboratedProposals'));
    }

    // Researcher: Show submission form
    public function create()
    {
        $researchers = \App\Models\User::where('role', 'researcher')
            ->where('id', '!=', auth()->id())
            ->get();
        return view('researcher.create_proposal', compact('researchers'));
    }

    // Researcher: Store proposal
    public function store(Request $request)
    {
        $isDraft = $request->action === 'draft';

        $request->validate([
            'title' => 'required',
            'abstract' => 'required',
            'rationale' => 'nullable|string',
            'research_field' => 'nullable|string',
            'document' => $isDraft ? 'nullable|file|mimes:pdf|max:20480' : 'required|file|mimes:pdf|max:20480',
            'collaborators' => 'nullable|array',
            'collaborators.*' => 'exists:users,id',
        ]);

        $filePath = null;

        if ($request->hasFile('document')) {
            $filePath = $request->file('document')->store('documents', config('filesystems.default', 'public'));
        }

        $year = date('Y');
        $latestProposal = ResearchProposal::latest('id')->first();
        $nextId = $latestProposal ? $latestProposal->id + 1 : 1;
        $sequence = str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $proposalCode = "RP-{$year}-{$sequence}";

        $proposal = null;
        \DB::transaction(function () use ($request, $isDraft, $filePath, $proposalCode, &$proposal) {
            $proposal = ResearchProposal::create([
                'user_id' => auth()->id(),
                'proposal_code' => $proposalCode,
                'title' => $request->title,
                'abstract' => $request->abstract,
                'rationale' => $request->rationale ?? null,
                'research_field' => $request->research_field,
                'document_path' => $filePath, // Kept for backwards compatibility
                'status' => $isDraft ? 'draft' : 'pending_coordinator_endorsement',
            ]);

            if ($filePath) {
                $documentTag = "{$proposalCode}-PH1-MANUSCRIPT-V1";
                $proposal->documents()->create([
                    'document_tag' => $documentTag,
                    'document_type' => 'manuscript',
                    'phase' => 1,
                    'version' => 1,
                    'file_path' => $filePath,
                ]);
            }

            if ($request->has('collaborators')) {
                $proposal->collaborators()->sync($request->collaborators);
            }
        });

        if (!$isDraft && $proposal) {
            // Notify Lead Researcher
            $proposal->user->notify(new WorkflowStatusNotification(
                $proposal,
                'Proposal Submitted for Endorsement',
                "Your proposal '{$proposal->title}' ({$proposal->proposal_code}) was submitted successfully and is awaiting College Coordinator endorsement.",
                'bi-send-check',
                'text-primary'
            ));

            // Notify College Coordinator(s)
            $coordinators = User::where('role', 'coordinator')
                ->where('department', auth()->user()->department)
                ->get();
            foreach ($coordinators as $coord) {
                $coord->notify(new WorkflowStatusNotification(
                    $proposal,
                    'New Proposal for Endorsement',
                    "Researcher " . auth()->user()->name . " submitted proposal '{$proposal->title}' for college endorsement.",
                    'bi-file-earmark-plus-fill',
                    'text-info'
                ));
            }
        }

        $msg = $isDraft ? 'Proposal saved as draft!' : 'Proposal submitted successfully!';
        return redirect('/proposal/my')->with('success', $msg);
    }

    // Reviewer: View ALL proposals
    public function reviewerIndex()
    {
        // Reviewers should only see proposals assigned to them, excluding their own (COI safeguard)
        $assignedProposalIds = \DB::table('proposal_assignments')
            ->where('user_id', auth()->id())
            ->pluck('research_proposal_id');

        $proposals = ResearchProposal::with(['user', 'collaborators'])
            ->whereIn('id', $assignedProposalIds)
            ->where('user_id', '!=', auth()->id())
            ->get();

        return view('reviewer.proposals', compact('proposals'));
    }

    // Admin: Assign a reviewer to a proposal
    public function assignReviewer(Request $request, $id)
    {
        $request->validate([
            'reviewer_id' => 'required|exists:users,id',
        ]);

        $reviewer = User::findOrFail($request->reviewer_id);
        if (!$reviewer->hasRole('reviewer')) {
            return redirect()->back()->with('error', 'The selected user is not authorized as an Evaluator/Reviewer.');
        }

        $proposal = ResearchProposal::with(['collaborators'])->findOrFail($id);

        // Conflict of Interest (COI) Protection
        if ($proposal->user_id == $reviewer->id) {
            return redirect()->back()->with('error', 'Conflict of Interest: The lead researcher cannot be assigned to review their own proposal.');
        }

        if ($proposal->collaborators->contains('id', $reviewer->id)) {
            return redirect()->back()->with('error', 'Conflict of Interest: A co-researcher / collaborator cannot be assigned to review this proposal.');
        }

        // Create assignment if not exists
        \DB::table('proposal_assignments')->updateOrInsert(
            ['research_proposal_id' => $id, 'user_id' => $request->reviewer_id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $reviewer->notify(new WorkflowStatusNotification(
            $proposal,
            'Assigned as Peer Reviewer',
            "You have been assigned to evaluate proposal: {$proposal->proposal_code} - {$proposal->title}",
            'bi-person-check-fill',
            'text-info'
        ));

        return redirect()->back()->with('success', 'Reviewer assigned successfully!');
    }

    // Admin: Unassign a reviewer from a proposal
    public function unassignReviewer($id, $reviewer_id)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        \DB::table('proposal_assignments')
            ->where('research_proposal_id', $id)
            ->where('user_id', $reviewer_id)
            ->delete();

        return redirect()->back()->with('success', 'Reviewer removed successfully!');
    }

    // Reviewer: Update proposal status, comments, and suggestions
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,revision_required,approved_with_revisions',
            'review_comments' => 'nullable|string',
            'review_suggestions' => 'nullable|string',
            'evaluation_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $proposal = ResearchProposal::with('collaborators')->findOrFail($id);

        // Conflict of Interest (COI) Protection: Cannot evaluate own proposal
        if ($proposal->user_id === auth()->id() || $proposal->collaborators->contains('id', auth()->id())) {
            abort(403, 'Conflict of Interest: You cannot evaluate or submit feedback on your own research proposal.');
        }

        if (in_array($proposal->status, ['final_approved', 'final_rejected'])) {
            return redirect()->back()->with('error', 'This proposal has already received a final decision and cannot be updated.');
        }

        $proposal->update([
            'status' => $request->status,
            'reviewer_id' => auth()->id(),
            'review_comments' => $request->review_comments,
            'review_suggestions' => $request->review_suggestions,
        ]);

        if ($request->hasFile('evaluation_document')) {
            $filePath = $request->file('evaluation_document')->store('evaluations', config('filesystems.default', 'public'));
            
            $proposal->documents()->create([
                'document_tag' => "{$proposal->proposal_code}-PH{$proposal->current_phase}-EVAL-" . auth()->id() . '-' . time(),
                'document_type' => 'evaluation',
                'phase' => $proposal->current_phase ?? 3,
                'version' => 1,
                'file_path' => $filePath,
            ]);
        }

        // Notify Researcher
        $proposal->user->notify(new \App\Notifications\ProposalFeedbackNotification($proposal));

        return redirect()->back()->with('success', 'Proposal updated and researcher notified successfully!');
    }

    // Admin: View ALL proposals
    public function adminIndex()
    {
        $proposals = ResearchProposal::with(['user', 'assignments'])->latest()->paginate(15, ['*'], 'proposals_page');
        $reviewers = User::withRole('reviewer')->orderBy('name')->get();
        $announcements = \App\Models\Announcement::with('user')->latest()->paginate(15, ['*'], 'announcements_page');
        
        $stats = [
            'total' => ResearchProposal::count(),
            'pending' => ResearchProposal::where('status', 'pending')->count(),
            'approved' => ResearchProposal::whereIn('status', ['approved', 'final_approved'])->count(),
            'announcements' => \App\Models\Announcement::count()
        ];

        return view('admin.proposals', compact('proposals', 'reviewers', 'announcements', 'stats'));
    }

    // Admin: Final decision
    public function updatePhase(Request $request, $id)
    {
        $request->validate([
            'phase' => 'required|integer|min:1|max:5',
        ]);

        $proposal = ResearchProposal::findOrFail($id);
        
        if ($request->phase <= $proposal->current_phase && $request->phase != 1) {
             // Allow resetting if needed, but usually it moves forward
        }

        $proposal->update([
            'current_phase' => $request->phase,
            'phase_updated_at' => now(),
        ]);

        return redirect()->back()->with('success', "Proposal moved to Phase {$request->phase} successfully.");
    }

    public function adminFinalDecision(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:final_approved,final_rejected',
        ]);

        $proposal = ResearchProposal::findOrFail($id);

        if (in_array($proposal->status, ['final_approved', 'final_rejected']) && $request->status == $proposal->status) {
            return redirect()->back()->with('error', 'This proposal already has this final status.');
        }

        $proposal->update([
            'status' => $request->status,
        ]);

        if ($request->status === 'final_approved') {
            $proposal->user->notify(new WorkflowStatusNotification(
                $proposal,
                'Proposal Final Approved',
                "Your proposal '{$proposal->title}' has received final approval from the Research Director.",
                'bi-check-circle-fill',
                'text-success'
            ));
        } else {
            $proposal->user->notify(new WorkflowStatusNotification(
                $proposal,
                'Proposal Decision: Final Rejected',
                "Your proposal '{$proposal->title}' was rejected during administrative final decision.",
                'bi-x-circle-fill',
                'text-danger'
            ));
        }

        return redirect()->back()->with('success', 'Final decision applied successfully!');
    }

    public function show($id)
    {
        $proposal = ResearchProposal::with([
            'user', 'collaborators', 'assignments', 'milestones', 'budgetItems', 'purchaseRequests',
            'researchPresentations', 'researchPublications', 'localForumSubmissions.forum',
            'activities.causer'
        ])->findOrFail($id);

        $openForums = \App\Models\LocalResearchForum::where('status', 'open')->latest()->get();
        $activeRole = auth()->user()->activeRole();
        $isAuthorOrCollaborator = $proposal->user_id === auth()->id() || $proposal->collaborators->contains('id', auth()->id());
        $isAssignedReviewer = $proposal->assignments->contains('id', auth()->id());

        // Reviewers dropdown excludes lead author and collaborators to prevent conflict of interest
        $reviewers = (in_array($activeRole, ['admin', 'super_admin']) || auth()->user()->isSuperAdmin())
            ? User::withRole('reviewer')
                ->where('id', '!=', $proposal->user_id)
                ->whereNotIn('id', $proposal->collaborators->pluck('id'))
                ->orderBy('name')
                ->get()
            : collect();
        
        // Authorization check
        if (!auth()->user()->isSuperAdmin() && !in_array($activeRole, ['admin', 'staff', 'vprei', 'president', 'budget_officer', 'sao_finance'])) {
            if ($activeRole == 'researcher' && !$isAuthorOrCollaborator) {
                abort(403, 'Unauthorized access.');
            } elseif ($activeRole == 'reviewer' && !$isAssignedReviewer && !$isAuthorOrCollaborator) {
                abort(403, 'Unauthorized access. You are not assigned to review this proposal.');
            } elseif ($activeRole == 'dean') {
                if ($proposal->user->department !== auth()->user()->department) {
                    abort(403, 'Unauthorized access. You can only view proposals from your college/department.');
                }
            } elseif ($activeRole == 'coordinator') {
                if ($proposal->user->department !== auth()->user()->department) {
                    abort(403, 'Unauthorized access. You can only view proposals from your college/department.');
                }
            }
        }

        return view('proposals.show', compact('proposal', 'openForums', 'reviewers'));
    }

    // Researcher: Show edit form for revision
    public function edit($id)
    {
        $proposal = ResearchProposal::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!in_array($proposal->status, ['revision_required', 'draft', 'returned_for_revision'])) {
            abort(403, 'You cannot edit this proposal.');
        }

        return view('researcher.edit_proposal', compact('proposal'));
    }

    // Researcher: Update and resubmit proposal
    public function update(Request $request, $id)
    {
        $proposal = ResearchProposal::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!in_array($proposal->status, ['revision_required', 'draft', 'returned_for_revision'])) {
            abort(403, 'You cannot update this proposal.');
        }

        $isDraft = $request->action === 'draft';

        $request->validate([
            'title' => 'required',
            'abstract' => 'required',
            'rationale' => 'nullable|string',
            'research_field' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf|max:20480',
            'collaborators' => 'nullable|array',
            'collaborators.*' => 'exists:users,id',
        ]);

        $filePath = $proposal->document_path;

        if ($request->hasFile('document')) {
            // BUG-08 FIXED: Previous file is no longer deleted to maintain version history
            $filePath = $request->file('document')->store('documents', config('filesystems.default', 'public'));
            
            $latestDoc = $proposal->documents()->where('document_type', 'manuscript')->latest('version')->first();
            $nextVersion = $latestDoc ? $latestDoc->version + 1 : 2;
            $phase = $proposal->current_phase ?? 1;
            $documentTag = "{$proposal->proposal_code}-PH{$phase}-MANUSCRIPT-V{$nextVersion}";

            $proposal->documents()->create([
                'document_tag' => $documentTag,
                'document_type' => 'manuscript',
                'phase' => $phase,
                'version' => $nextVersion,
                'file_path' => $filePath,
            ]);
        }

        $newStatus = 'pending_coordinator_endorsement';
        if ($proposal->status === 'revision_required' || $proposal->status === 'approved_with_revisions') {
            $newStatus = 'accepted_for_in_house_review';
        } elseif ($proposal->status === 'returned_for_revision' && $proposal->returned_by_role === 'staff') {
            // Option A: Fast-track directly back to Receiving Staff
            $newStatus = 'submitted_to_research_unit';
        }

        $updateData = [
            'title' => $request->title,
            'abstract' => $request->abstract,
            'rationale' => $request->rationale ?? $proposal->rationale,
            'research_field' => $request->research_field,
            'document_path' => $filePath, // Update main path for backwards compatibility
            'status' => $isDraft ? 'draft' : $newStatus,
        ];

        if (!$isDraft && $proposal->status === 'returned_for_revision') {
            $updateData['return_reason'] = null;
            $updateData['returned_by_role'] = null;
            $updateData['returned_at'] = null;
        }

        $proposal->update($updateData);

        if ($request->has('collaborators')) {
            $proposal->collaborators()->sync($request->collaborators);
        }

        if (!$isDraft && $newStatus === 'submitted_to_research_unit') {
            $staffUsers = User::where('role', 'staff')->get();
            foreach ($staffUsers as $staff) {
                $staff->notify(new WorkflowStatusNotification(
                    $proposal,
                    'Revised Proposal Resubmitted',
                    "Researcher {$proposal->user->name} resubmitted revised manuscript for '{$proposal->title}' addressing administrative completeness.",
                    'bi-arrow-repeat',
                    'text-info'
                ));
            }
        } elseif (!$isDraft && $newStatus === 'pending_coordinator_endorsement') {
            $coords = User::where('role', 'coordinator')
                ->where('department', $proposal->user->department)
                ->get();
            foreach ($coords as $coord) {
                $coord->notify(new WorkflowStatusNotification(
                    $proposal,
                    'Proposal Resubmitted for Endorsement',
                    "Researcher {$proposal->user->name} resubmitted revised proposal '{$proposal->title}' for your endorsement.",
                    'bi-arrow-repeat',
                    'text-primary'
                ));
            }
        } elseif (!$isDraft && $newStatus === 'accepted_for_in_house_review') {
            $assignments = \DB::table('proposal_assignments')->where('research_proposal_id', $proposal->id)->pluck('user_id');
            $reviewers = User::whereIn('id', $assignments)->get();
            foreach ($reviewers as $rev) {
                $rev->notify(new WorkflowStatusNotification(
                    $proposal,
                    'Revised Manuscript Resubmitted',
                    "Researcher {$proposal->user->name} resubmitted revised manuscript for '{$proposal->title}' addressing reviewer comments.",
                    'bi-arrow-repeat',
                    'text-info'
                ));
            }
        }

        if ($isDraft) {
            $msg = 'Draft updated successfully!';
        } elseif ($newStatus === 'submitted_to_research_unit') {
            $msg = 'Proposal updated and resubmitted directly to the Research Unit receiving queue!';
        } else {
            $msg = 'Proposal submitted successfully!';
        }
        return redirect('/proposal/my')->with('success', $msg);
    }

    public function destroy($id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if ($proposal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized. Only the lead researcher can delete this proposal.');
        }

        // Allowed only as long as the college coordinator has not endorsed it yet
        $deletableStatuses = ['draft', 'pending_coordinator_endorsement', 'returned_for_revision', 'pending'];

        if (!in_array($proposal->status, $deletableStatuses)) {
            return redirect()->back()->with('error', 'Proposals can only be deleted before they are endorsed by the college coordinator.');
        }

        \DB::transaction(function () use ($proposal) {
            // Delete the main manuscript file from storage
            if ($proposal->document_path && \Storage::disk(config('filesystems.default', 'public'))->exists($proposal->document_path)) {
                \Storage::disk(config('filesystems.default', 'public'))->delete($proposal->document_path);
            }

            // Delete terminal report file if any
            if ($proposal->terminal_report_path && \Storage::disk(config('filesystems.default', 'public'))->exists($proposal->terminal_report_path)) {
                \Storage::disk(config('filesystems.default', 'public'))->delete($proposal->terminal_report_path);
            }

            // Delete all versioned document files
            foreach ($proposal->documents as $doc) {
                if ($doc->file_path && \Storage::disk(config('filesystems.default', 'public'))->exists($doc->file_path)) {
                    \Storage::disk(config('filesystems.default', 'public'))->delete($doc->file_path);
                }
            }

            // Detach relations
            $proposal->collaborators()->detach();
            $proposal->assignments()->detach();

            // Clean up related notifications (PostgreSQL safe)
            \Illuminate\Notifications\DatabaseNotification::where('data', 'like', '%"proposal_id":' . $proposal->id . '%')
                ->orWhere('data', 'like', '%"proposal_id":"' . $proposal->id . '"%')
                ->get()
                ->filter(function ($n) use ($proposal) {
                    return isset($n->data['proposal_id']) && (int)$n->data['proposal_id'] === (int)$proposal->id;
                })
                ->each->delete();

            // Delete the proposal record
            $proposal->delete();
        });

        return redirect()->route('proposal.index')->with('success', 'Proposal deleted successfully!');
    }

    public function downloadNoticeOfAcceptance($id)
    {
        $proposal = ResearchProposal::with(['user', 'collaborators', 'assignments'])->findOrFail($id);

        if (auth()->user()->role == 'researcher' && $proposal->user_id !== auth()->id() && !$proposal->collaborators->contains(auth()->id())) {
            abort(403, 'Unauthorized access.');
        } elseif (auth()->user()->role == 'reviewer' && !$proposal->assignments->contains('id', auth()->id())) {
            abort(403, 'Unauthorized access.');
        }

        $allowedStatuses = [
            'accepted_for_in_house_review',
            'under_review',
            'revision_required',
            'returned_for_revision',
            'approved_with_revisions',
            'approved',
            'final_copy_submitted',
            'final_copy_noted_by_dean',
            'pending_budget_certification',
            'funds_certified',
            'endorsed_to_vprei',
            'final_approved',
            'ongoing',
            'completed',
            'archived'
        ];

        if (!in_array($proposal->status, $allowedStatuses)) {
            abort(403, 'This proposal has not been accepted for in-house review yet.');
        }

        $pdf = \PDF::loadView('pdfs.notice', compact('proposal'));
        return $pdf->download('Notice_of_Acceptance_' . $proposal->id . '.pdf');
    }

    public function downloadNoticeToProceed($id)
    {
        $proposal = ResearchProposal::with(['user', 'collaborators', 'assignments'])->findOrFail($id);

        if (auth()->user()->role == 'researcher' && $proposal->user_id !== auth()->id() && !$proposal->collaborators->contains(auth()->id())) {
            abort(403, 'Unauthorized access.');
        } elseif (auth()->user()->role == 'reviewer' && !$proposal->assignments->contains('id', auth()->id())) {
            abort(403, 'Unauthorized access.');
        }

        if (!in_array($proposal->status, ['final_approved', 'ongoing', 'completed', 'archived'])) {
            abort(403, 'This proposal has not received final approval yet.');
        }

        $pdf = \PDF::loadView('pdfs.ntp', compact('proposal'));
        return $pdf->download('Notice_to_Proceed_' . $proposal->id . '.pdf');
    }

    public function downloadCertificate($id)
    {
        $proposal = ResearchProposal::with(['user', 'collaborators', 'assignments'])->findOrFail($id);

        if (auth()->user()->role == 'researcher' && $proposal->user_id !== auth()->id() && !$proposal->collaborators->contains(auth()->id())) {
            abort(403, 'Unauthorized access.');
        } elseif (auth()->user()->role == 'reviewer' && !$proposal->assignments->contains('id', auth()->id())) {
            abort(403, 'Unauthorized access.');
        }

        if ($proposal->current_phase != 5) {
            abort(403, 'This research has not reached completion phase yet.');
        }

        $pdf = \PDF::loadView('pdfs.certificate', compact('proposal'));
        return $pdf->download('Certificate_of_Completion_' . $proposal->id . '.pdf');
    }

    public function submitFinalManuscript(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if ($proposal->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'final_manuscript' => 'required|file|mimes:pdf|max:20480',
        ]);

        $filePath = $request->file('final_manuscript')->store('terminal_reports', config('filesystems.default', 'public'));

        $proposal->documents()->create([
            'document_tag' => "{$proposal->proposal_code}-PH6-FINALMANUSCRIPT",
            'document_type' => 'terminal_report',
            'phase' => 6,
            'version' => 1,
            'file_path' => $filePath,
        ]);

        $proposal->update([
            'current_phase' => 6,
            'terminal_report_path' => $filePath,
            'status' => 'completed'
        ]);

        return redirect()->back()->with('success', 'Final Manuscript submitted successfully.');
    }

    public function acceptForInHouseReview($id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        if ($proposal->status !== 'pending_director_review') {
            return redirect()->back()->with('error', 'Only proposals pending Director review can be accepted for In-House review.');
        }

        $proposal->update([
            'status' => 'accepted_for_in_house_review',
            'current_phase' => 2, // Technical/Ethics Review
            'phase_updated_at' => now(),
        ]);

        // Notify Lead Researcher
        $proposal->user->notify(new WorkflowStatusNotification(
            $proposal,
            'Accepted for In-House Review',
            "Notice of Acceptance issued: Your proposal '{$proposal->title}' was accepted for In-House Review and is moving to Phase 2 (Technical & Ethics Review).",
            'bi-award-fill',
            'text-success'
        ));

        // Notify College Coordinator
        $coordinators = User::where('role', 'coordinator')
            ->where('department', $proposal->user->department)
            ->get();
        foreach ($coordinators as $coord) {
            $coord->notify(new WorkflowStatusNotification(
                $proposal,
                'Proposal Accepted for In-House Review',
                "Proposal '{$proposal->title}' ({$proposal->user->name}) was officially accepted by Research Director for In-House Review.",
                'bi-award-fill',
                'text-primary'
            ));
        }

        return redirect()->back()->with('success', 'Proposal accepted for In-House review. "IN-HOUSE REVIEW ACCEPTANCE FORM" issued.');
    }

    public function endorseToVprei($id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        if ($proposal->status !== 'funds_certified') {
            return redirect()->back()->with('error', 'Only proposals certified by the Budget Officer for availability of funds can be endorsed to the VPREI.');
        }

        $proposal->update([
            'status' => 'endorsed_to_vprei'
        ]);

        // Notify VPREI
        $vpreiUsers = User::where('role', 'vprei')->get();
        foreach ($vpreiUsers as $vprei) {
            $vprei->notify(new WorkflowStatusNotification(
                $proposal,
                'New Proposal Endorsed for Final Approval',
                "Proposal '{$proposal->title}' has been endorsed by Research Director for your final approval and Notice to Proceed (NTP).",
                'bi-send-check-fill',
                'text-primary'
            ));
        }

        // Notify Lead Researcher
        $proposal->user->notify(new WorkflowStatusNotification(
            $proposal,
            'Endorsed to VPREI for Final Approval',
            "Your proposal '{$proposal->title}' has been endorsed by the Research Director to the Office of the VPREI for final approval.",
            'bi-send-check-fill',
            'text-info'
        ));

        return redirect()->back()->with('success', 'Proposal successfully endorsed to the VPREI.');
    }

    public function submitFinalCopy(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if ($proposal->user_id !== auth()->id()) {
            abort(403);
        }

        if ($proposal->status !== 'approved') {
            return redirect()->back()->with('error', 'You can only submit the final copy after the proposal is approved in the In-House review.');
        }

        $request->validate([
            'final_copy' => 'required|file|mimes:pdf|max:20480',
        ]);

        $filePath = $request->file('final_copy')->store('documents', config('filesystems.default', 'public'));

        // Save versioned document
        $latestDoc = $proposal->documents()->where('document_type', 'manuscript')->latest('version')->first();
        $nextVersion = $latestDoc ? $latestDoc->version + 1 : 2;
        $documentTag = "{$proposal->proposal_code}-PH2-FINALCOPY-V{$nextVersion}";

        $proposal->documents()->create([
            'document_tag' => $documentTag,
            'document_type' => 'manuscript',
            'phase' => 2,
            'version' => $nextVersion,
            'file_path' => $filePath,
        ]);

        $proposal->update([
            'document_path' => $filePath,
            'status' => 'final_copy_submitted'
        ]);

        // Notify College Dean
        $deans = User::where('role', 'dean')
            ->where('department', $proposal->user->department)
            ->get();
        foreach ($deans as $dean) {
            $dean->notify(new WorkflowStatusNotification(
                $proposal,
                'Final Copy Submitted for Executive Noting',
                "Researcher {$proposal->user->name} submitted final revised copy of '{$proposal->title}' awaiting your noting.",
                'bi-file-earmark-check-fill',
                'text-primary'
            ));
        }

        // Notify Lead Researcher
        $proposal->user->notify(new WorkflowStatusNotification(
            $proposal,
            'Final Copy Submitted',
            "Your final copy for '{$proposal->title}' was submitted successfully and is awaiting noting by your College Dean.",
            'bi-check2-circle',
            'text-success'
        ));

        return redirect()->back()->with('success', 'Final copy of research proposal submitted successfully.');
    }

    public function archiveProposal($id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $proposal->update([
            'status' => 'archived'
        ]);

        return redirect()->back()->with('success', 'Proposal successfully archived to the repository.');
    }

    public function exportResuFm015($id)
    {
        $proposal = ResearchProposal::with([
            'user', 'collaborators', 'milestones', 'budgetItems'
        ])->findOrFail($id);

        return view('reports.proposal_resu_fm015', compact('proposal'));
    }
}