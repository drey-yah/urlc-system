<?php

namespace App\Http\Controllers;

use App\Models\ExternalFundingProject;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Http\Request;

class ExternalFundingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $projects = ExternalFundingProject::with(['proposal.user', 'fundingAgencyUser'])->latest()->get();

        if ($user->role === 'researcher') {
            $projects = $projects->filter(function ($project) use ($user) {
                return $project->proposal->user_id === $user->id;
            });
            $proposals = $user->leadProposals()
                ->whereDoesntHave('externalFundingProject')
                ->latest()
                ->get();
        } elseif ($user->isFundingAgency()) {
            $projects = $projects->where('funding_agency_user_id', $user->id);
            $proposals = collect();
        } elseif (!in_array($user->role, ['vprei', 'president', 'admin', 'super_admin'])) {
            abort(403, 'Unauthorized access.');
        } else {
            $proposals = collect();
        }

        $fundingAgencies = $user->isResearcher()
            ? User::where('role', 'funding_agency')->whereRaw('is_approved = true')->orderBy('organization')->get()
            : collect();

        return view('external_funding.index', compact('projects', 'proposals', 'fundingAgencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'research_proposal_id' => 'required|exists:research_proposals,id',
            'funding_agency_user_id' => 'required|exists:users,id',
            'grant_program' => 'nullable|string|max:255',
            'requested_amount' => 'nullable|numeric|min:0',
        ]);

        $proposal = ResearchProposal::where('user_id', auth()->id())->findOrFail($request->research_proposal_id);

        if ($proposal->externalFundingProject) {
            return back()->with('error', 'This proposal is already in the external funding workflow.');
        }

        $fundingAgency = User::where('role', 'funding_agency')->whereRaw('is_approved = true')
            ->findOrFail($request->funding_agency_user_id);
        $project = ExternalFundingProject::create([
            'research_proposal_id' => $proposal->id,
            'funding_agency_user_id' => $fundingAgency->id,
            'funding_agency' => $fundingAgency->organization ?: $fundingAgency->name,
            'grant_program' => $request->grant_program,
            'requested_amount' => $request->requested_amount,
        ]);
        $proposal->update(['funding_type' => 'external']);

        $this->notifyRoles(['vprei', 'admin'], $proposal, 'External Funding Endorsement Requested',
            "{$proposal->title} was submitted for external funding endorsement.");

        return redirect()->route('external-funding.index')->with('success', 'External funding request submitted for University endorsement.');
    }

    public function endorseUniversity($id)
    {
        $project = ExternalFundingProject::findOrFail($id);
        $this->requireRole(['vprei', 'admin', 'super_admin']);
        $this->requireStatus($project, 'pending_university_endorsement');

        $project->update(['status' => 'pending_president_endorsement', 'university_endorsed_at' => now()]);
        $this->notifyRoles(['president'], $project->proposal, 'External Proposal Awaiting Presidential Endorsement',
            "{$project->proposal->title} is ready to be endorsed to {$project->funding_agency}.");

        return back()->with('success', 'Proposal endorsed for University approval.');
    }

    public function endorseToAgency($id)
    {
        $project = ExternalFundingProject::findOrFail($id);
        $this->requireRole(['president']);
        $this->requireStatus($project, 'pending_president_endorsement');

        $project->update(['status' => 'under_funder_review', 'president_endorsed_at' => now()]);
        $this->notifyFundingAgency($project, 'External Proposal Submitted for Review',
            "{$project->proposal->title} is ready for your funding decision.");
        return back()->with('success', 'Proposal endorsed to the funding agency.');
    }

    public function recordAgencyDecision(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:accepted,revision_requested,rejected',
            'agency_feedback' => 'nullable|string|max:3000',
            'agency_reference_number' => 'nullable|string|max:255',
            'awarded_amount' => 'nullable|numeric|min:0',
        ]);

        $project = ExternalFundingProject::with('proposal')->findOrFail($id);
        $this->requireRole(['funding_agency']);
        abort_unless($project->funding_agency_user_id === auth()->id(), 403, 'This grant is assigned to another funding agency.');
        $this->requireStatus($project, 'under_funder_review');

        $status = $request->decision === 'accepted' ? 'pending_moa_mou' : $request->decision;
        $project->update(array_merge($request->only(['agency_feedback', 'agency_reference_number', 'awarded_amount']), [
            'status' => $status,
            'agency_decided_at' => now(),
        ]));

        $this->notifyResearcher($project, 'Funding Agency Decision Recorded',
            "{$project->funding_agency} recorded the decision: " . str_replace('_', ' ', $status) . '.');

        return back()->with('success', 'Funding agency decision recorded.');
    }

    public function resubmit(Request $request, $id)
    {
        $request->validate(['revision_notes' => 'required|string|max:3000']);
        $project = ExternalFundingProject::with('proposal')->findOrFail($id);
        $this->requireOwner($project);
        $this->requireStatus($project, 'revision_requested');

        $project->update(['status' => 'under_funder_review', 'agency_feedback' => $request->revision_notes]);
        return back()->with('success', 'Revised proposal resubmitted for funding-agency review.');
    }

    public function executeAgreement(Request $request, $id)
    {
        $request->validate([
            'moa_mou_file' => 'required|file|mimes:pdf|max:20480',
            'grant_start_date' => 'required|date',
            'grant_end_date' => 'required|date|after_or_equal:grant_start_date',
        ]);

        $project = ExternalFundingProject::findOrFail($id);
        $this->requireRole(['president']);
        $this->requireStatus($project, 'pending_moa_mou');

        $path = $request->file('moa_mou_file')->store('external_funding/agreements', config('filesystems.default', 'public'));
        $project->update(array_merge($request->only(['grant_start_date', 'grant_end_date']), [
            'moa_mou_path' => $path,
            'status' => 'grant_active',
            'agreement_executed_at' => now(),
        ]));

        $this->notifyResearcher($project, 'External Grant Activated', 'The MOA/MOU was executed and the grant is now active.');
        return back()->with('success', 'MOA/MOU recorded and grant activated.');
    }

    public function markMonitoring($id)
    {
        $project = ExternalFundingProject::findOrFail($id);
        $this->requireRole(['vprei', 'admin', 'super_admin']);
        $this->requireStatus($project, 'grant_active');
        $project->update(['status' => 'under_grant_monitoring']);

        return back()->with('success', 'Grant moved to the monitoring and evaluation stage.');
    }

    public function submitTerminalReport(Request $request, $id)
    {
        $request->validate(['terminal_report' => 'required|file|mimes:pdf,doc,docx|max:20480']);
        $project = ExternalFundingProject::with('proposal')->findOrFail($id);
        $this->requireOwner($project);
        $this->requireStatus($project, 'under_grant_monitoring');

        $path = $request->file('terminal_report')->store('external_funding/terminal_reports', config('filesystems.default', 'public'));
        $project->update(['terminal_report_path' => $path, 'status' => 'pending_grant_closure']);
        $this->notifyRoles(['vprei', 'admin'], $project->proposal, 'External Grant Ready for Closure',
            "{$project->proposal->title} has a terminal report ready for grant closure.");

        return back()->with('success', 'Terminal report submitted for grant closure.');
    }

    public function closeGrant($id)
    {
        $project = ExternalFundingProject::with('proposal')->findOrFail($id);
        $this->requireRole(['vprei', 'admin', 'super_admin']);
        $this->requireStatus($project, 'pending_grant_closure');

        $project->update(['status' => 'grant_closed', 'closed_at' => now()]);
        $project->proposal->update(['status' => 'completed']);
        $this->notifyResearcher($project, 'External Grant Closed', 'The funding agency workflow has been closed successfully.');

        return back()->with('success', 'External grant closed and proposal marked completed.');
    }

    private function requireRole(array $roles)
    {
        abort_unless(in_array(auth()->user()->role, $roles), 403, 'Unauthorized action.');
    }

    private function requireOwner(ExternalFundingProject $project)
    {
        abort_unless($project->proposal->user_id === auth()->id(), 403, 'Unauthorized action.');
    }

    private function requireStatus(ExternalFundingProject $project, $status)
    {
        if ($project->status !== $status) {
            abort(422, 'This action is not available in the current grant status.');
        }
    }

    private function notifyRoles(array $roles, ResearchProposal $proposal, $title, $message)
    {
        User::whereIn('role', $roles)->get()->each(function ($user) use ($proposal, $title, $message) {
            $user->notify(new WorkflowStatusNotification($proposal, $title, $message, 'bi-cash-coin', 'text-primary'));
        });
    }

    private function notifyResearcher(ExternalFundingProject $project, $title, $message)
    {
        $project->proposal->user->notify(new WorkflowStatusNotification($project->proposal, $title, $message, 'bi-cash-coin', 'text-primary'));
    }

    private function notifyFundingAgency(ExternalFundingProject $project, $title, $message)
    {
        if ($project->fundingAgencyUser) {
            $project->fundingAgencyUser->notify(new WorkflowStatusNotification($project->proposal, $title, $message, 'bi-cash-coin', 'text-primary'));
        }
    }
}
