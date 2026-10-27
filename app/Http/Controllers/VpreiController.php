<?php

namespace App\Http\Controllers;

use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
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
        $proposal = ResearchProposal::with(['user', 'collaborators'])->findOrFail($id);

        if ($proposal->status !== 'endorsed_to_vprei') {
            return redirect()->back()->with('error', 'Proposal has not been endorsed to the VPREI.');
        }

        // VPREI gives final approval and moves it to Phase 4 (Ongoing)
        $proposal->update([
            'status' => 'final_approved',
            'current_phase' => 4,
            'phase_updated_at' => now(),
        ]);

        // Notify Lead Researcher
        $proposal->user->notify(new WorkflowStatusNotification(
            $proposal,
            '🎉 Final Approval Granted & NTP Issued!',
            "Congratulations! The VPREI has approved your proposal '{$proposal->title}'. Notice to Proceed (NTP) has been issued and Phase 4 is now active.",
            'bi-award-fill',
            'text-success'
        ));

        // Notify Collaborators
        foreach ($proposal->collaborators as $collaborator) {
            $collaborator->notify(new WorkflowStatusNotification(
                $proposal,
                '🎉 Final Approval Granted & NTP Issued!',
                "The VPREI has approved proposal '{$proposal->title}'. Notice to Proceed (NTP) has been issued.",
                'bi-award-fill',
                'text-success'
            ));
        }

        // Notify College Coordinator
        $coordinators = User::where('role', 'coordinator')
            ->where('department', $proposal->user->department)
            ->get();
        foreach ($coordinators as $coord) {
            $coord->notify(new WorkflowStatusNotification(
                $proposal,
                'Proposal Final Approved by VPREI',
                "Proposal '{$proposal->title}' ({$proposal->user->name}) received final approval from VPREI and is now active in Phase 4.",
                'bi-check-all',
                'text-success'
            ));
        }

        // Notify Research Director / Admins
        $directors = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($directors as $dir) {
            $dir->notify(new WorkflowStatusNotification(
                $proposal,
                'VPREI Approved Proposal',
                "VPREI approved proposal '{$proposal->title}' ({$proposal->user->name}). Notice to Proceed issued.",
                'bi-check-all',
                'text-info'
            ));
        }

        return redirect()->back()->with('success', 'Proposal has been approved by VPREI. Notice to Proceed (NTP) has been issued, and the project is now in Phase 4 (Ongoing).');
    }
}
