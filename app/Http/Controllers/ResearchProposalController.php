<?php

namespace App\Http\Controllers;

use App\Models\ResearchProposal;
use Illuminate\Http\Request;

class ResearchProposalController extends Controller
{
    // Researcher: View own proposals
    public function index()
    {
        $proposals = ResearchProposal::where('user_id', auth()->id())->get();
        return view('researcher.my_proposals', compact('proposals'));
    }

    // Researcher: Show submission form
    public function create()
    {
        return view('researcher.create_proposal');
    }

    // Researcher: Store proposal
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'abstract' => 'required',
            'research_field' => 'nullable|string',
            'budget_requested' => 'required|numeric',
            'document' => 'required|file|mimes:pdf|max:20480',
        ]);

        $filePath = null;

        if ($request->hasFile('document')) {
            $filePath = $request->file('document')->store('documents', 'public');
        }

        ResearchProposal::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'abstract' => $request->abstract,
            'research_field' => $request->research_field,
            'budget_requested' => $request->budget_requested,
            'document_path' => $filePath,
            'status' => 'pending',
        ]);

        return redirect('/proposal/my')->with('success', 'Proposal submitted successfully!');
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
            'status' => 'required|in:pending,approved,rejected,revision_required',
            'review_comments' => 'nullable|string',
            'review_suggestions' => 'nullable|string',
        ]);

        $proposal = ResearchProposal::findOrFail($id);

        if (in_array($proposal->status, ['final_approved', 'final_rejected'])) {
            return redirect()->back()->with('error', 'This proposal has already received a final decision and cannot be updated.');
        }

        $proposal->update([
            'status' => $request->status,
            'review_comments' => $request->review_comments,
            'review_suggestions' => $request->review_suggestions,
        ]);

        return redirect()->back()->with('success', 'Proposal updated successfully!');
    }

    // Admin: View ALL proposals
    public function adminIndex()
    {
        $proposals = ResearchProposal::with(['user', 'assignments'])->get();
        $reviewers = \App\Models\User::where('role', 'reviewer')->get();
        return view('admin.proposals', compact('proposals', 'reviewers'));
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
        $proposal = ResearchProposal::with('user')->findOrFail($id);
        
        // Authorization check
        if (auth()->user()->role == 'researcher' && $proposal->user_id !== auth()->id()) {
            abort(403);
        }

        return view('proposals.show', compact('proposal'));
    }

    // Researcher: Show edit form for revision
    public function edit($id)
    {
        $proposal = ResearchProposal::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($proposal->status !== 'revision_required') {
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

        if ($proposal->status !== 'revision_required') {
            abort(403, 'You cannot update this proposal.');
        }

        $request->validate([
            'title' => 'required',
            'abstract' => 'required',
            'research_field' => 'nullable|string',
            'budget_requested' => 'required|numeric',
            'document' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $filePath = $proposal->document_path;

        if ($request->hasFile('document')) {
            // BUG-08: Delete old file if it exists
            if ($proposal->document_path && \Storage::disk('public')->exists($proposal->document_path)) {
                \Storage::disk('public')->delete($proposal->document_path);
            }
            $filePath = $request->file('document')->store('documents', 'public');
        }

        $proposal->update([
            'title' => $request->title,
            'abstract' => $request->abstract,
            'research_field' => $request->research_field,
            'budget_requested' => $request->budget_requested,
            'document_path' => $filePath,
            'status' => 'pending',
        ]);

        return redirect('/proposal/my')->with('success', 'Proposal resubmitted successfully!');
    }

    public function destroy($id)
    {
        $proposal = ResearchProposal::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Delete the file from storage
        if ($proposal->document_path && \Storage::disk('public')->exists($proposal->document_path)) {
            \Storage::disk('public')->delete($proposal->document_path);
        }

        $proposal->delete();

        return redirect()->back()->with('success', 'Proposal deleted successfully!');
    }
}