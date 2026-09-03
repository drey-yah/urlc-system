<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResearchProposal;

class CoordinatorController extends Controller
{
    public function dashboard()
    {
        return redirect()->route('coordinator.proposals');
    }

    public function index()
    {
        $department = auth()->user()->department;
        
        if (empty($department)) {
            abort(403, 'Your account is not assigned to a department. Please update your profile or contact an administrator.');
        }
        
        // Proposals needing coordinator endorsement
        $proposals = ResearchProposal::with(['user', 'documents'])
            ->where('status', 'pending_coordinator_endorsement')
            ->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            })
            ->latest()
            ->get();

        // Proposals noted by Dean, ready to be submitted to Research Unit
        $notedProposals = ResearchProposal::with(['user', 'documents'])
            ->where('status', 'noted_by_dean')
            ->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            })
            ->latest()
            ->get();

        // All Department Proposals
        $allDeptProposals = ResearchProposal::with(['user'])
            ->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            })
            ->latest()
            ->get();

        // Appendix D: Local Research Forum Submissions for this department
        $forumSubmissions = \App\Models\LocalForumSubmission::with(['proposal.user', 'forum', 'user'])
            ->where(function($q) use ($department) {
                $q->whereHas('user', function($u) use ($department) {
                    $u->where('department', $department);
                })->orWhereHas('proposal.user', function($u) use ($department) {
                    $u->where('department', $department);
                });
            })
            ->latest()
            ->get();

        $pendingForumSubmissions = $forumSubmissions->where('coordinator_endorsed', false);

        $stats = [
            'total_dept' => $allDeptProposals->count(),
            'awaiting_endorsement' => $proposals->count(),
            'pending_dean' => $allDeptProposals->where('status', 'pending_dean_noting')->count(),
            'ready_for_unit' => $notedProposals->count(),
            'approved_completed' => $allDeptProposals->whereIn('status', ['approved', 'final_approved', 'completed'])->count(),
            'pending_forum_endorsements' => $pendingForumSubmissions->count(),
        ];
            
        return view('coordinator.dashboard', compact('proposals', 'notedProposals', 'allDeptProposals', 'forumSubmissions', 'pendingForumSubmissions', 'department', 'stats'));
    }

    public function endorse(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);
        
        // Ensure it belongs to the same department
        if ($proposal->user->department !== auth()->user()->department) {
            abort(403, 'Unauthorized. You can only endorse proposals from your department.');
        }

        if ($request->action === 'return') {
            $proposal->update([
                'status' => 'returned_for_revision'
            ]);
            return redirect()->back()->with('success', 'Proposal returned to researcher for revision.');
        }

        $proposal->update([
            'status' => 'pending_dean_noting' // Moves to Dean for noting
        ]);

        return redirect()->back()->with('success', 'Proposal endorsed successfully. It is now awaiting Dean Noting.');
    }

    public function submitToResearchUnit(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);
        
        if ($proposal->user->department !== auth()->user()->department) {
            abort(403, 'Unauthorized.');
        }

        if ($proposal->status !== 'noted_by_dean') {
            return redirect()->back()->with('error', 'Only Dean-noted proposals can be submitted to the Research Unit.');
        }

        $proposal->update([
            'status' => 'submitted_to_research_unit'
        ]);

        return redirect()->back()->with('success', 'Endorsement list submitted to Research Unit successfully.');
    }

    public function generateEndorsementForm($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'dean', 'admin', 'super_admin', 'staff'])) {
            abort(403, 'Unauthorized. The College Research Proposal Endorsement Form (RESU-FM-003) is restricted to College Research Coordinators, Deans, and Research Administrators.');
        }

        $proposal = ResearchProposal::with(['user', 'collaborators'])->findOrFail($id);
        
        $department = $proposal->user->department ?? $user->department ?? 'Computer Studies';
        
        // Find coordinator name
        $coordinator = \App\Models\User::where('role', 'coordinator')
            ->where('department', $department)
            ->first();
        
        // Find dean name
        $dean = \App\Models\User::where('role', 'dean')
            ->where('department', $department)
            ->first();

        $collegeName = $department;
        $coordinatorName = $coordinator->name ?? auth()->user()->name;
        $deanName = $dean->name ?? 'College Dean';

        $proposals = collect([$proposal]);

        return view('reports.endorsement_form', compact(
            'proposals', 'proposal', 'collegeName', 'coordinatorName', 'deanName'
        ));
    }

    public function generateBatchEndorsementForm(Request $request)
    {
        $department = auth()->user()->department;
        
        $proposals = ResearchProposal::with(['user', 'collaborators'])
            ->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            })
            ->whereIn('status', ['pending_dean_noting', 'noted_by_dean', 'submitted_to_research_unit'])
            ->latest()
            ->get();

        if ($proposals->isEmpty()) {
            $proposals = ResearchProposal::with(['user', 'collaborators'])
                ->whereHas('user', function($q) use ($department) {
                    $q->where('department', $department);
                })
                ->latest()
                ->take(5)
                ->get();
        }

        // Find coordinator name
        $coordinator = \App\Models\User::where('role', 'coordinator')
            ->where('department', $department)
            ->first();
        
        // Find dean name
        $dean = \App\Models\User::where('role', 'dean')
            ->where('department', $department)
            ->first();

        $collegeName = $department;
        $coordinatorName = $coordinator->name ?? auth()->user()->name;
        $deanName = $dean->name ?? 'College Dean';

        return view('reports.endorsement_form', compact(
            'proposals', 'collegeName', 'coordinatorName', 'deanName'
        ));
    }
}
