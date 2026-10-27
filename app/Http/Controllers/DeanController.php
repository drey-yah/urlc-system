<?php

namespace App\Http\Controllers;

use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Http\Request;

class DeanController extends Controller
{
    public function dashboard()
    {
        $department = auth()->user()->department;

        if (empty($department)) {
            abort(403, 'Your account is not assigned to a college/department. Please contact an administrator.');
        }

        // Proposals waiting for Dean to note initial Coordinator endorsement
        $pendingNoting = ResearchProposal::with(['user', 'documents'])
            ->where('status', 'pending_dean_noting')
            ->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            })
            ->latest()
            ->get();

        // Proposals waiting for Dean to note the submitted final copy
        $finalNoting = ResearchProposal::with(['user', 'documents'])
            ->where('status', 'final_copy_submitted')
            ->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            })
            ->latest()
            ->get();

        // All College Proposals
        $allCollegeProposals = ResearchProposal::with(['user'])
            ->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            })
            ->latest()
            ->get();

        $stats = [
            'total_college' => $allCollegeProposals->count(),
            'pending_noting' => $pendingNoting->count(),
            'final_noting' => $finalNoting->count(),
            'approved_college' => $allCollegeProposals->whereIn('status', ['approved', 'final_approved', 'completed'])->count(),
        ];

        return view('dean.dashboard', compact('pendingNoting', 'finalNoting', 'allCollegeProposals', 'department', 'stats'));
    }

    public function noteEndorsement(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if ($proposal->user->department !== auth()->user()->department) {
            abort(403, 'Unauthorized.');
        }

        if ($request->action === 'return') {
            $request->validate([
                'return_reason' => 'required|string|max:3000',
            ], [
                'return_reason.required' => 'Please provide the reason or feedback for returning this proposal for revision.',
            ]);

            $proposal->update([
                'status' => 'returned_for_revision',
                'return_reason' => $request->return_reason,
                'returned_by_role' => 'dean',
                'returned_at' => now(),
            ]);

            // Notify Researcher
            $proposal->user->notify(new WorkflowStatusNotification(
                $proposal,
                'Proposal Returned for Revision by Dean',
                "Your proposal '{$proposal->title}' was returned by College Dean ({$proposal->user->department}) for revision. Remarks: {$request->return_reason}",
                'bi-arrow-return-left',
                'text-danger'
            ));

            // Notify Department Coordinator(s)
            $coordinators = User::where('role', 'coordinator')
                ->where('department', $proposal->user->department)
                ->get();
            foreach ($coordinators as $coord) {
                $coord->notify(new WorkflowStatusNotification(
                    $proposal,
                    'Endorsement Returned by Dean',
                    "Dean returned proposal '{$proposal->title}' for revision. Remarks: {$request->return_reason}",
                    'bi-arrow-return-left',
                    'text-warning'
                ));
            }

            return redirect()->back()->with('success', 'Proposal returned for revision with your remarks. Both the researcher and coordinator have been notified.');
        }

        $proposal->update([
            'status' => 'noted_by_dean',
            'return_reason' => null,
            'returned_by_role' => null,
        ]);

        // Notify College Coordinator(s)
        $coordinators = User::where('role', 'coordinator')
            ->where('department', $proposal->user->department)
            ->get();
        foreach ($coordinators as $coord) {
            $coord->notify(new WorkflowStatusNotification(
                $proposal,
                'Endorsement Noted by Dean',
                "Dean officially noted proposal '{$proposal->title}' ({$proposal->user->name}). You may now transmit it to the Research Unit.",
                'bi-check2-all',
                'text-success'
            ));
        }

        // Notify Lead Researcher
        $proposal->user->notify(new WorkflowStatusNotification(
            $proposal,
            'Endorsement Noted by College Dean',
            "Your proposal '{$proposal->title}' was officially noted by the College Dean. Next, the Coordinator will transmit it to the Research Unit.",
            'bi-patch-check-fill',
            'text-info'
        ));

        return redirect()->back()->with('success', 'Proposal noted successfully. Coordinator will be notified to submit it to the Research Unit.');
    }

    public function noteFinalCopy(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if ($proposal->user->department !== auth()->user()->department) {
            abort(403, 'Unauthorized.');
        }

        if ($proposal->status !== 'final_copy_submitted') {
            return redirect()->back()->with('error', 'Proposal final copy is not submitted.');
        }

        $proposal->update([
            'status' => 'final_copy_noted_by_dean'
        ]);

        // Notify Budget Officers
        $budgetOfficers = User::where('role', 'budget_officer')->get();
        foreach ($budgetOfficers as $bo) {
            $bo->notify(new WorkflowStatusNotification(
                $proposal,
                'Pending Budget Certification',
                "Dean noted the final copy of '{$proposal->title}' ({$proposal->user->name}). Awaiting your certification of funds availability.",
                'bi-cash-coin',
                'text-warning'
            ));
        }

        // Notify Lead Researcher
        $proposal->user->notify(new WorkflowStatusNotification(
            $proposal,
            'Final Copy Noted by Dean',
            "Your final copy for '{$proposal->title}' has been noted by the College Dean and routed to the Budget Officer for fund certification.",
            'bi-clipboard-check',
            'text-success'
        ));

        return redirect()->back()->with('success', 'Final copy noted successfully. Awaiting Budget Officer certification of funds.');
    }
}
