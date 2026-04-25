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
        $proposals = ResearchProposal::all();
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

    // 🔥 Admin: View ALL proposals
    public function adminIndex()
    {
        $proposals = ResearchProposal::all();
        return view('admin.proposals', compact('proposals'));
    }

    public function show(ResearchProposal $researchProposal)
    {
        //
    }

    public function edit(ResearchProposal $researchProposal)
    {
        //
    }

    public function update(Request $request, ResearchProposal $researchProposal)
    {
        //
    }

    public function destroy(ResearchProposal $researchProposal)
    {
        //
    }
}