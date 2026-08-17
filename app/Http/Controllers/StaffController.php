<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResearchProposal;

class StaffController extends Controller
{
    public function dashboard()
    {
        return redirect()->route('staff.proposals');
    }

    public function index()
    {
        // Staff sees all proposals submitted to the research unit
        $proposals = ResearchProposal::with(['user', 'documents'])
            ->where('status', 'submitted_to_research_unit')
            ->latest()
            ->get();

        // Proposals already routed / processed
        $routedProposals = ResearchProposal::with(['user', 'documents'])
            ->whereIn('status', ['pending_director_review', 'accepted_for_in_house_review', 'under_review', 'approved', 'final_approved'])
            ->latest()
            ->take(10)
            ->get();

        $allProposals = ResearchProposal::with(['user'])->latest()->get();

        $stats = [
            'pending_receiving' => $proposals->count(),
            'routed_to_director' => $allProposals->whereIn('status', ['pending_director_review', 'accepted_for_in_house_review'])->count(),
            'under_review' => $allProposals->where('status', 'under_review')->count(),
            'total_received' => $allProposals->whereNotIn('status', ['draft', 'pending_coordinator_endorsement', 'pending_dean_noting', 'submitted_to_research_unit'])->count(),
        ];
            
        return view('staff.dashboard', compact('proposals', 'routedProposals', 'allProposals', 'stats'));
    }

    public function forward(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        // Change status to pending_director_review
        $proposal->update([
            'status' => 'pending_director_review'
        ]);

        return redirect()->back()->with('success', 'Manuscript received and forwarded to Research Director successfully.');
    }
}
