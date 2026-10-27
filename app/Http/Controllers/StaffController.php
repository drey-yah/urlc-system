<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;

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

        if ($request->action === 'return') {
            $request->validate([
                'return_reason' => 'required|string|max:3000',
            ], [
                'return_reason.required' => 'Please provide the remarks or describe the missing requirements for returning this proposal.',
            ]);

            $failedChecks = [];
            if (!$request->boolean('check_technical')) {
                $failedChecks[] = 'Technical Checking (Incomplete)';
            }
            if (!$request->boolean('check_format')) {
                $failedChecks[] = 'Format & Template Compliance (Non-compliant)';
            }
            if (!$request->boolean('check_similarity')) {
                $failedChecks[] = 'Similarity / Plagiarism Check (Not verified / Exceeded limit)';
            }

            $formattedReason = $request->return_reason;
            if (!empty($failedChecks)) {
                $formattedReason = "Administrative Deficiencies:\n• " . implode("\n• ", $failedChecks) . "\n\nRemarks: " . $request->return_reason;
            }

            $proposal->update([
                'status' => 'returned_for_revision',
                'return_reason' => $formattedReason,
                'returned_by_role' => 'staff',
                'returned_at' => now(),
            ]);

            // Notify Researcher directly
            $proposal->user->notify(new WorkflowStatusNotification(
                $proposal,
                'Proposal Returned by Receiving Staff',
                "Your proposal '{$proposal->title}' was returned by Receiving Staff due to administrative incompleteness. Reason: {$request->return_reason}",
                'bi-clipboard-x-fill',
                'text-danger'
            ));

            // Notify Department Coordinator so they are aware
            $coordinators = User::where('role', 'coordinator')
                ->where('department', $proposal->user->department)
                ->get();
            foreach ($coordinators as $coord) {
                $coord->notify(new WorkflowStatusNotification(
                    $proposal,
                    'Proposal Returned by Receiving Staff',
                    "Receiving Staff returned proposal '{$proposal->title}' ({$proposal->user->name}) for administrative incompleteness.",
                    'bi-clipboard-x-fill',
                    'text-warning'
                ));
            }

            return redirect()->back()->with('success', 'Proposal returned to researcher for administrative revision with remarks.');
        }

        // Action is 'forward' (all checks completed)
        $proposal->update([
            'status' => 'pending_director_review',
            'return_reason' => null,
            'returned_by_role' => null,
        ]);

        // Notify Research Director / Admins
        $directors = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($directors as $dir) {
            $dir->notify(new WorkflowStatusNotification(
                $proposal,
                'Proposal Routed for Director Review',
                "Proposal '{$proposal->title}' ({$proposal->user->name}) passed administrative completeness and is awaiting your In-House review decision.",
                'bi-inbox-fill',
                'text-primary'
            ));
        }

        // Notify Lead Researcher
        $proposal->user->notify(new WorkflowStatusNotification(
            $proposal,
            'Administrative Completeness Verified',
            "Your proposal '{$proposal->title}' passed administrative completeness checking and was forwarded to the Research Director.",
            'bi-check2-circle',
            'text-success'
        ));

        return redirect()->back()->with('success', 'Manuscript received, verified, and forwarded to Research Director successfully.');
    }
}
