<?php

namespace App\Http\Controllers;

use App\Models\ResearchPresentation;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresidentController extends Controller
{
    public function dashboard()
    {
        // Phase 3 Oral/Poster Presentations waiting for President's approval
        $pendingPresentations = ResearchPresentation::with(['proposal.user', 'user'])
            ->whereRaw('director_recommended = true')
            ->whereRaw('president_approved = false')
            ->latest()
            ->get();

        // Presentations officially approved by SUC President
        $approvedPresentations = ResearchPresentation::with(['proposal.user', 'user'])
            ->whereRaw('president_approved = true')
            ->latest()
            ->get();

        // All university research proposals
        $allProposals = ResearchProposal::with(['user'])->latest()->get();

        $stats = [
            'pending_authorization' => $pendingPresentations->count(),
            'approved_presentations' => $approvedPresentations->count(),
            'completed_dissemination' => ResearchPresentation::where('status', 'completed')->count(),
            'total_university_proposals' => $allProposals->count(),
        ];

        return view('president.dashboard', compact('pendingPresentations', 'approvedPresentations', 'allProposals', 'stats'));
    }

    public function approvePresentation(Request $request, $id)
    {
        $presentation = ResearchPresentation::findOrFail($id);

        $presentation->update([
            'president_approved' => DB::raw('true'),
            'president_approved_at' => now(),
            'status' => 'approved_by_president',
        ]);

        // Notify Researcher
        $presentation->user->notify(new WorkflowStatusNotification(
            $presentation->proposal,
            '🎉 Approved by University President!',
            "The SUC President officially approved your research presentation '{$presentation->presentation_title}' for {$presentation->sponsoring_agency}.",
            'bi-award-fill',
            'text-success'
        ));

        return redirect()->back()->with('success', "Research output presentation '{$presentation->presentation_title}' approved by SUC President for conference representation!");
    }
}
