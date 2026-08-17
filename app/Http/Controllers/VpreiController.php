<?php

namespace App\Http\Controllers;

use App\Models\ResearchProposal;
use Illuminate\Http\Request;

class VpreiController extends Controller
{
    public function dashboard()
    {
        // VPREI sees all proposals endorsed to them globally
        $proposals = ResearchProposal::with(['user', 'documents'])
            ->where('status', 'endorsed_to_vprei')
            ->latest()
            ->get();

        // Proposals given final approval by VPREI
        $approvedProposals = ResearchProposal::with(['user', 'documents'])
            ->whereIn('status', ['final_approved', 'completed'])
            ->latest()
            ->get();

        $allProposals = ResearchProposal::with(['user'])->latest()->get();

        $stats = [
            'total_reviews' => $allProposals->count(),
            'pending_approval' => $proposals->count(),
            'final_approved' => $approvedProposals->count(),
            'active_ongoing' => $allProposals->where('current_phase', '>=', 4)->count(),
        ];

        return view('vprei.dashboard', compact('proposals', 'approvedProposals', 'allProposals', 'stats'));
    }

    public function approve(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if ($proposal->status !== 'endorsed_to_vprei') {
            return redirect()->back()->with('error', 'Proposal has not been endorsed to the VPREI.');
        }

        // VPREI gives final approval and moves it to Phase 4 (Ongoing)
        $proposal->update([
            'status' => 'final_approved',
            'current_phase' => 4,
            'phase_updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Proposal has been approved by VPREI. Notice to Proceed (NTP) has been issued, and the project is now in Phase 4 (Ongoing).');
    }
}
