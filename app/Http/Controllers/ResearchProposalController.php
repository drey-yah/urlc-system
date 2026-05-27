<?php

namespace App\Http\Controllers;

use App\Models\ResearchProposal;
use Illuminate\Http\Request;

class ResearchProposalController extends Controller
{
    // Researcher: View own proposals
    public function index()
    {
        $leadProposals = auth()->user()->leadProposals()->with('collaborators')->latest()->get();
        $collaboratedProposals = auth()->user()->collaboratedProposals()->with(['user', 'collaborators'])->latest()->get();
        
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
            'rationale' => 'required',
            'research_field' => 'nullable|string',
            'document' => $isDraft ? 'nullable|file|mimes:pdf|max:20480' : 'required|file|mimes:pdf|max:20480',
            'collaborators' => 'nullable|array',
            'collaborators.*' => 'exists:users,id',
        ]);

        $filePath = null;

        if ($request->hasFile('document')) {
            $filePath = $request->file('document')->store('documents', env('FILESYSTEM_DRIVER', 'public'));
        }

        $department = auth()->user()->department ? strtoupper(auth()->user()->department) : 'UNI';
        $year = date('Y');
        $latestProposal = ResearchProposal::latest('id')->first();
        $nextId = $latestProposal ? $latestProposal->id + 1 : 1;
        $sequence = str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $proposalCode = "{$department}-UA-RP-{$year}-{$sequence}";

        $proposal = ResearchProposal::create([
            'user_id' => auth()->id(),
            'proposal_code' => $proposalCode,
            'title' => $request->title,
            'abstract' => $request->abstract,
            'rationale' => $request->rationale,
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

        $msg = $isDraft ? 'Proposal saved as draft!' : 'Proposal submitted successfully!';
        return redirect('/proposal/my')->with('success', $msg);
    }

    // Reviewer: View ALL proposals
    public function reviewerIndex()
    {
        // BUG FIX: Reviewers should only see proposals assigned to them
        $assignedProposalIds = \DB::table('proposal_assignments')
            ->where('user_id', auth()->id())
            ->pluck('research_proposal_id');

        $proposals = ResearchProposal::with('user')
            ->whereIn('id', $assignedProposalIds)
            ->get();

        return view('reviewer.proposals', compact('proposals'));
    }

    // Admin: Assign a reviewer to a proposal
    public function assignReviewer(Request $request, $id)
    {
        $request->validate([
            'reviewer_id' => 'required|exists:users,id',
        ]);

        $reviewer = \App\Models\User::findOrFail($request->reviewer_id);
        if ($reviewer->role !== 'reviewer') {
            return redirect()->back()->with('error', 'The selected user is not a reviewer.');
        }

        // Create assignment if not exists
        \DB::table('proposal_assignments')->updateOrInsert(
            ['research_proposal_id' => $id, 'user_id' => $request->reviewer_id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        return redirect()->back()->with('success', 'Reviewer assigned successfully!');
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

        $proposal = ResearchProposal::findOrFail($id);

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
            $filePath = $request->file('evaluation_document')->store('evaluations', env('FILESYSTEM_DRIVER', 'public'));
            
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
        $proposals = ResearchProposal::with(['user', 'assignments'])->get();
        $reviewers = \App\Models\User::where('role', 'reviewer')->get();
        $announcements = \App\Models\Announcement::with('user')->latest()->get();
        
        $stats = [
            'total' => $proposals->count(),
            'pending' => $proposals->where('status', 'pending')->count(),
            'approved' => $proposals->whereIn('status', ['approved', 'final_approved'])->count(),
            'announcements' => $announcements->count()
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

        return redirect()->back()->with('success', 'Final decision applied successfully!');
    }

    public function show($id)
    {
        $proposal = ResearchProposal::with(['user', 'collaborators'])->findOrFail($id);
        
        // Authorization check
        if (auth()->user()->role == 'researcher' && $proposal->user_id !== auth()->id()) {
            if (!$proposal->collaborators->contains(auth()->id())) {
                abort(403, 'Unauthorized access.');
            }
        }

        return view('proposals.show', compact('proposal'));
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
            'rationale' => 'required',
            'research_field' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf|max:20480',
            'collaborators' => 'nullable|array',
            'collaborators.*' => 'exists:users,id',
        ]);

        $filePath = $proposal->document_path;

        if ($request->hasFile('document')) {
            // BUG-08 FIXED: Previous file is no longer deleted to maintain version history
            $filePath = $request->file('document')->store('documents', env('FILESYSTEM_DRIVER', 'public'));
            
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

        $proposal->update([
            'title' => $request->title,
            'abstract' => $request->abstract,
            'rationale' => $request->rationale,
            'research_field' => $request->research_field,
            'document_path' => $filePath, // Update main path for backwards compatibility
            'status' => $isDraft ? 'draft' : 'pending_coordinator_endorsement',
        ]);

        if ($request->has('collaborators')) {
            $proposal->collaborators()->sync($request->collaborators);
        }

        $msg = $isDraft ? 'Draft updated successfully!' : 'Proposal submitted successfully!';
        return redirect('/proposal/my')->with('success', $msg);
    }

    public function destroy($id)
    {
        $proposal = ResearchProposal::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Delete the file from storage
        if ($proposal->document_path && \Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->exists($proposal->document_path)) {
            \Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->delete($proposal->document_path);
        }

        // Delete all versioned document files
        foreach ($proposal->documents as $doc) {
            if ($doc->file_path && \Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->exists($doc->file_path)) {
                \Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->delete($doc->file_path);
            }
        }

        $proposal->delete();

        return redirect()->back()->with('success', 'Proposal deleted successfully!');
    }

    public function downloadNoticeOfAcceptance($id)
    {
        $proposal = ResearchProposal::with(['user', 'collaborators'])->findOrFail($id);

        if (auth()->user()->role == 'researcher' && $proposal->user_id !== auth()->id() && !$proposal->collaborators->contains(auth()->id())) {
            abort(403, 'Unauthorized access.');
        }

        if (!in_array($proposal->status, ['approved', 'final_approved', 'ongoing', 'completed', 'archived'])) {
            abort(403, 'This proposal has not been fully approved yet.');
        }

        $pdf = \PDF::loadView('pdfs.notice', compact('proposal'));
        return $pdf->download('Notice_of_Acceptance_' . $proposal->id . '.pdf');
    }

    public function downloadNoticeToProceed($id)
    {
        $proposal = ResearchProposal::with(['user', 'collaborators'])->findOrFail($id);

        if (auth()->user()->role == 'researcher' && $proposal->user_id !== auth()->id() && !$proposal->collaborators->contains(auth()->id())) {
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
        $proposal = ResearchProposal::with(['user', 'collaborators'])->findOrFail($id);

        if (auth()->user()->role == 'researcher' && $proposal->user_id !== auth()->id() && !$proposal->collaborators->contains(auth()->id())) {
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

        $filePath = $request->file('final_manuscript')->store('terminal_reports', env('FILESYSTEM_DRIVER', 'public'));

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
}