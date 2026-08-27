<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TerminalReport;
use App\Models\ResearchProposal;

class TerminalReportController extends Controller
{
    public function store(Request $request, $proposalId)
    {
        $proposal = ResearchProposal::findOrFail($proposalId);

        if ($proposal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized submission.');
        }

        $request->validate([
            'executive_summary' => 'nullable|string',
            'terminal_report_file' => 'nullable|file|mimes:pdf,doc,docx|max:20480', // RESU-FM-017
            'full_paper_file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
            'supporting_docs_file' => 'nullable|file|mimes:pdf,zip,rar|max:51200',
        ]);

        $existingReport = TerminalReport::where('research_proposal_id', $proposal->id)->first();

        $terminalPath = $existingReport ? $existingReport->terminal_report_file : null;
        if ($request->hasFile('terminal_report_file')) {
            $terminalPath = $request->file('terminal_report_file')->store('phase2/terminal_reports', config('filesystems.default', 'public'));
        }

        $paperPath = $existingReport ? $existingReport->full_paper_file : null;
        if ($request->hasFile('full_paper_file')) {
            $paperPath = $request->file('full_paper_file')->store('phase2/full_papers', config('filesystems.default', 'public'));
        }

        $supportingPath = $existingReport ? $existingReport->supporting_docs_file : null;
        if ($request->hasFile('supporting_docs_file')) {
            $supportingPath = $request->file('supporting_docs_file')->store('phase2/supporting_docs', config('filesystems.default', 'public'));
        }

        TerminalReport::updateOrCreate(
            ['research_proposal_id' => $proposal->id],
            [
                'user_id' => auth()->id(),
                'executive_summary' => $request->executive_summary,
                'terminal_report_file' => $terminalPath,
                'full_paper_file' => $paperPath,
                'supporting_docs_file' => $supportingPath,
                'status' => 'submitted_to_unit',
            ]
        );

        // Notify Reviewers / Panel
        $reviewers = \App\Models\User::where('role', 'reviewer')->get();
        foreach ($reviewers as $rev) {
            $rev->notify(new \App\Notifications\WorkflowStatusNotification(
                $proposal,
                'Terminal Report Submitted for Evaluation',
                "Terminal Report (RESU-FM-017) and Full Manuscript submitted for '{$proposal->title}'. Ready for Panel Evaluation (RESU-FM-001).",
                'bi-journal-text',
                'text-primary'
            ));
        }

        return redirect()->back()->with('success', 'Terminal Report (RESU-FM-017), Full Paper, and supporting documents submitted successfully to the Research Unit!');
    }

    public function evaluate(Request $request, $id)
    {
        $report = TerminalReport::findOrFail($id);

        $request->validate([
            'evaluator_score' => 'required|numeric|min:0|max:100',
            'evaluator_comments' => 'nullable|string',
            'evaluation_form_file' => 'nullable|file|mimes:pdf|max:10240', // RESU-FM-001
        ]);

        $evalPath = $report->evaluation_form_file;
        if ($request->hasFile('evaluation_form_file')) {
            $evalPath = $request->file('evaluation_form_file')->store('phase2/evaluations', config('filesystems.default', 'public'));
        }

        $status = $request->evaluator_score >= 75 ? 'final_report_submitted' : 'revisions_required';

        $report->update([
            'evaluator_score' => $request->evaluator_score,
            'evaluator_comments' => $request->evaluator_comments,
            'evaluation_form_file' => $evalPath,
            'status' => $status,
        ]);

        // Notify Researcher
        $title = $status === 'final_report_submitted' ? 'Terminal Report Passed Evaluation!' : 'Terminal Report Revisions Required';
        $report->proposal->user->notify(new \App\Notifications\WorkflowStatusNotification(
            $report->proposal,
            $title,
            "Panel Evaluator score: {$request->evaluator_score}/100. Comments: " . ($request->evaluator_comments ?? 'None'),
            $status === 'final_report_submitted' ? 'bi-star-fill' : 'bi-exclamation-triangle-fill',
            $status === 'final_report_submitted' ? 'text-success' : 'text-danger'
        ));

        // Notify Director if passed
        if ($status === 'final_report_submitted') {
            $directors = \App\Models\User::where('role', 'admin')->get();
            foreach ($directors as $dir) {
                $dir->notify(new \App\Notifications\WorkflowStatusNotification(
                    $report->proposal,
                    'Terminal Report Ready for Certificate of Completion',
                    "Terminal Report for '{$report->proposal->title}' passed evaluation (Score: {$request->evaluator_score}). Ready for Certificate of Completion (RESU-FM-028).",
                    'bi-award-fill',
                    'text-success'
                ));
            }
        }

        return redirect()->back()->with('success', 'Panel Evaluation (RESU-FM-001) submitted successfully.');
    }

    public function issueCompletion(Request $request, $id)
    {
        $report = TerminalReport::findOrFail($id);
        $proposal = $report->proposal;

        $report->update([
            'status' => 'completed',
        ]);

        $proposal->update([
            'status' => 'completed',
            'current_phase' => 5,
        ]);

        // Notify Researcher
        $proposal->user->notify(new \App\Notifications\WorkflowStatusNotification(
            $proposal,
            '🎉 Certificate of Research Completion Issued!',
            "Congratulations! Research Director issued your Certificate of Research Completion (RESU-FM-028) for '{$proposal->title}'.",
            'bi-award-fill',
            'text-success'
        ));

        return redirect()->back()->with('success', 'Certificate of Research Completion (RESU-FM-028) issued! Project completed.');
    }
}
