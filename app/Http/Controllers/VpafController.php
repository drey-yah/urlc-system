<?php

namespace App\Http\Controllers;

use App\Models\InternallyFundedEndorsement;
use App\Models\ResearchProposal;
use Illuminate\Http\Request;

class VpafController extends Controller
{
    /**
     * Display the VPAF (Vice President for Academic & Finance) Dashboard.
     */
    public function dashboard()
    {
        // Endorsements awaiting VPAF recommending approval
        $pendingEndorsements = InternallyFundedEndorsement::with(['preparer', 'vpreiNoter', 'vpafNoter'])
            ->whereIn('status', ['pending_recommending', 'pending_president_approval'])
            ->where('vpaf_status', 'pending')
            ->latest()
            ->get();

        $recommendedEndorsements = InternallyFundedEndorsement::with(['preparer', 'presidentApprover'])
            ->where('vpaf_status', 'recommended')
            ->latest()
            ->take(10)
            ->get();

        $allEndorsements = InternallyFundedEndorsement::latest()->get();

        $stats = [
            'pending_review' => $pendingEndorsements->count(),
            'recommended' => $recommendedEndorsements->count(),
            'total_approved' => InternallyFundedEndorsement::where('status', 'approved')->count(),
            'total_budget_recommended' => $recommendedEndorsements->sum('total_budget'),
        ];

        return view('vpaf.dashboard', compact('pendingEndorsements', 'recommendedEndorsements', 'allEndorsements', 'stats'));
    }
}
