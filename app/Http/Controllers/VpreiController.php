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

        return view('vprei.dashboard', compact('proposals'));
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
