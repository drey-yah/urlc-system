<?php

namespace App\Http\Controllers;

use App\Models\ResearchMilestone;
use App\Models\ResearchProposal;
use Illuminate\Http\Request;

class ResearchMilestoneController extends Controller
{
    public function store(Request $request, $proposal_id)
    {
        $proposal = ResearchProposal::findOrFail($proposal_id);

        // Only researcher can add milestones
        if ($proposal->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $filePath = null;
        if ($request->hasFile('document')) {
            $filePath = $request->file('document')->store('milestones', env('FILESYSTEM_DRIVER', 'public'));
        }

        ResearchMilestone::create([
            'research_proposal_id' => $proposal->id,
            'title' => $request->title,
            'description' => $request->description,
            'document_path' => $filePath,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Implementation milestone submitted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $milestone = ResearchMilestone::findOrFail($id);
        
        // Only admins or reviewers can approve/reject milestones
        if (!in_array(auth()->user()->role, ['admin', 'super_admin', 'reviewer'])) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $milestone->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Milestone status updated successfully.');
    }
}
