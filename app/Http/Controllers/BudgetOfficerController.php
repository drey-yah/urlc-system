<?php

namespace App\Http\Controllers;

use App\Models\ResearchProposal;
use Illuminate\Http\Request;

class BudgetOfficerController extends Controller
{
    public function dashboard()
    {
        // Proposals waiting for certification
        $pendingCertification = ResearchProposal::with(['user', 'documents'])
            ->where('status', 'final_copy_noted_by_dean')
            ->latest()
            ->get();

        // Proposals already certified
        $certifiedProposals = ResearchProposal::with(['user', 'documents'])
            ->whereIn('status', ['funds_certified', 'endorsed_to_vprei', 'final_approved', 'ongoing', 'completed', 'archived'])
            ->latest()
            ->get();

        $allProposals = ResearchProposal::with(['user'])->latest()->get();

        $stats = [
            'total_processed' => $certifiedProposals->count() + $pendingCertification->count(),
            'pending_certification' => $pendingCertification->count(),
            'certified_funds' => $certifiedProposals->count(),
            'active_funded' => $allProposals->whereIn('status', ['final_approved', 'completed'])->count(),
        ];

        return view('budget.dashboard', compact('pendingCertification', 'certifiedProposals', 'allProposals', 'stats'));
    }

    public function certify(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if ($proposal->status !== 'final_copy_noted_by_dean') {
            return redirect()->back()->with('error', 'Proposal is not awaiting budget certification.');
        }

        // Certify proposal for availability of funds
        $proposal->update([
            'status' => 'funds_certified'
        ]);

        return redirect()->back()->with('success', 'Research proposal certified for availability of funds successfully.');
    }
}
