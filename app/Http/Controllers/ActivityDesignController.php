<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityDesign;
use App\Models\ResearchProposal;
use Illuminate\Support\Facades\Storage;

class ActivityDesignController extends Controller
{
    public function store(Request $request, $proposalId)
    {
        $proposal = ResearchProposal::findOrFail($proposalId);

        if ($proposal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized proposal submission.');
        }

        $request->validate([
            'activity_title' => 'required|string|max:255',
            'venue' => 'nullable|string|max:255',
            'target_date' => 'nullable|date',
            'objectives' => 'nullable|string',
            'target_participants' => 'nullable|string',
            'proposed_budget' => 'required|numeric|min:0',
            'activity_design_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // HRU-FM-021
            'budget_requirement_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // BU-FM-006
        ]);

        $activityPath = null;
        if ($request->hasFile('activity_design_file')) {
            $activityPath = $request->file('activity_design_file')->store('phase2/activity_designs', config('filesystems.default', 'public'));
        }

        $budgetPath = null;
        if ($request->hasFile('budget_requirement_file')) {
            $budgetPath = $request->file('budget_requirement_file')->store('phase2/budget_requirements', config('filesystems.default', 'public'));
        }

        $activity = ActivityDesign::create([
            'research_proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'activity_title' => $request->activity_title,
            'venue' => $request->venue,
            'target_date' => $request->target_date,
            'objectives' => $request->objectives,
            'target_participants' => $request->target_participants,
            'proposed_budget' => $request->proposed_budget,
            'activity_design_file' => $activityPath,
            'budget_requirement_file' => $budgetPath,
            'status' => 'pending_director_noting',
        ]);

        // Notify Research Director
        $directors = \App\Models\User::where('role', 'admin')->get();
        foreach ($directors as $director) {
            $director->notify(new \App\Notifications\WorkflowStatusNotification(
                $proposal,
                'New Activity Design Submitted',
                "Researcher {$proposal->user->name} submitted an Activity Design (HRU-FM-021) for '{$proposal->title}'.",
                'bi-file-earmark-text-fill',
                'text-primary'
            ));
        }

        return redirect()->back()->with('success', 'Activity Design (HRU-FM-021) & Proposed Budgetary Requirement (BU-FM-006) submitted successfully!');
    }

    public function directorNote($id)
    {
        $activity = ActivityDesign::findOrFail($id);
        $activity->update([
            'director_noted' => \Illuminate\Support\Facades\DB::raw('true'),
            'director_noted_at' => now(),
            'status' => 'pending_budget_noting',
        ]);

        // Notify Researcher
        $activity->proposal->user->notify(new \App\Notifications\WorkflowStatusNotification(
            $activity->proposal,
            'Activity Design Noted by Director',
            "Research Director noted your Activity Design for '{$activity->proposal->title}'. Forwarded to Budget Officer.",
            'bi-check-circle-fill',
            'text-success'
        ));

        // Notify Budget Officers
        $budgetOfficers = \App\Models\User::where('role', 'budget_officer')->get();
        foreach ($budgetOfficers as $bo) {
            $bo->notify(new \App\Notifications\WorkflowStatusNotification(
                $activity->proposal,
                'Activity Design Awaiting Budget Clearance',
                "Activity Design for '{$activity->proposal->title}' is ready for Budgetary Requirement (BU-FM-006) noting.",
                'bi-cash-stack',
                'text-warning'
            ));
        }

        return redirect()->back()->with('success', 'Activity Design noted by Research Director. Forwarded to Budget Officer.');
    }

    public function budgetNote($id)
    {
        $activity = ActivityDesign::findOrFail($id);
        $activity->update([
            'budget_officer_noted' => \Illuminate\Support\Facades\DB::raw('true'),
            'budget_officer_noted_at' => now(),
            'status' => 'pending_vprei_approval',
        ]);

        // Notify Researcher
        $activity->proposal->user->notify(new \App\Notifications\WorkflowStatusNotification(
            $activity->proposal,
            'Budgetary Requirement Noted',
            "Budget Officer noted the Proposed Budgetary Requirement for '{$activity->proposal->title}'. Forwarded to VPREI.",
            'bi-check-circle-fill',
            'text-success'
        ));

        // Notify VPREI
        $vpreis = \App\Models\User::where('role', 'vprei')->get();
        foreach ($vpreis as $vprei) {
            $vprei->notify(new \App\Notifications\WorkflowStatusNotification(
                $activity->proposal,
                'Activity Design Awaiting VPREI Approval',
                "Activity Design for '{$activity->proposal->title}' awaits your final approval.",
                'bi-award-fill',
                'text-info'
            ));
        }

        return redirect()->back()->with('success', 'Budgetary Requirement noted by Budget Officer. Forwarded to VPREI for final approval.');
    }

    public function vpreiApprove($id)
    {
        $activity = ActivityDesign::findOrFail($id);
        $activity->update([
            'vprei_approved' => \Illuminate\Support\Facades\DB::raw('true'),
            'vprei_approved_at' => now(),
            'status' => 'approved',
        ]);

        // Notify Researcher
        $activity->proposal->user->notify(new \App\Notifications\WorkflowStatusNotification(
            $activity->proposal,
            'Activity Design Approved by VPREI',
            "VPREI has approved your Activity Design & Budget for '{$activity->proposal->title}'!",
            'bi-check-circle-fill',
            'text-success'
        ));

        return redirect()->back()->with('success', 'Activity Design approved by VPREI!');
    }
}
