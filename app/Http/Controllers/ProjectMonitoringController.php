<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectMonitoring;
use App\Models\ResearchProposal;

class ProjectMonitoringController extends Controller
{
    public function store(Request $request, $proposalId)
    {
        $proposal = ResearchProposal::findOrFail($proposalId);

        if ($proposal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized submission.');
        }

        $request->validate([
            'period_covered' => 'required|string|max:255',
            'progress_summary' => 'nullable|string',
            'monitoring_form_path' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // RESU-FM-014
        ]);

        $filePath = null;
        if ($request->hasFile('monitoring_form_path')) {
            $filePath = $request->file('monitoring_form_path')->store('phase2/monitoring_forms', config('filesystems.default', 'public'));
        }

        $monitoring = ProjectMonitoring::create([
            'research_proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'period_covered' => $request->period_covered,
            'progress_summary' => $request->progress_summary,
            'monitoring_form_path' => $filePath,
            'status' => 'submitted',
        ]);

        // Notify College Coordinator
        $coordinators = \App\Models\User::where('role', 'coordinator')->get();
        foreach ($coordinators as $coord) {
            $coord->notify(new \App\Notifications\WorkflowStatusNotification(
                $proposal,
                'New Monitoring Form Submitted',
                "Researcher {$proposal->user->name} submitted a Monitoring Form (RESU-FM-014) for period '{$request->period_covered}'.",
                'bi-journal-check',
                'text-warning'
            ));
        }

        return redirect()->back()->with('success', 'Research Project Monitoring Form (RESU-FM-014) submitted to College Coordinator!');
    }

    public function coordinatorVerify($id)
    {
        $monitoring = ProjectMonitoring::findOrFail($id);
        $monitoring->update([
            'coordinator_verified' => \Illuminate\Support\Facades\DB::raw('true'),
            'coordinator_verified_at' => now(),
            'status' => 'verified',
        ]);

        // Notify Researcher
        $monitoring->proposal->user->notify(new \App\Notifications\WorkflowStatusNotification(
            $monitoring->proposal,
            'Monitoring Form Verified',
            "College Coordinator verified and recorded your Monitoring Form (RESU-FM-014) for period '{$monitoring->period_covered}'.",
            'bi-check-circle-fill',
            'text-success'
        ));

        return redirect()->back()->with('success', 'Monitoring form verified and recorded by College Research Coordinator.');
    }
}
