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

        return redirect('/researcher')->with('success', 'Proposal submitted successfully!');
    }

    // Reviewer: View ALL proposals
    public function reviewerIndex()
    {
        $proposals = ResearchProposal::with('user')->get();
        return view('reviewer.proposals', compact('proposals'));
    }

    // Reviewer: Update proposal status, comments, and suggestions
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
            'review_comments' => 'nullable|string',
            'review_suggestions' => 'nullable|string',
        ]);

        $proposal = ResearchProposal::findOrFail($id);

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
        $proposals = ResearchProposal::with('user')->get();
        return view('admin.proposals', compact('proposals'));
    }

    // Admin: Final decision
    public function adminFinalDecision(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:final_approved,final_rejected',
        ]);

        $proposal = ResearchProposal::findOrFail($id);

        $proposal->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Final decision applied successfully!');
    }

    public function show(ResearchProposal $researchProposal)
    {
        //
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

    public function destroy(ResearchProposal $researchProposal)
    {
        //
    }
}