<?php

namespace App\Http\Controllers;

use App\Models\ResearchProposal;
use Illuminate\Http\Request;

class RecordingStaffController extends Controller
{
    public function dashboard()
    {
        // Routing Tracker: Active proposals not archived, not draft
        $routingHistory = ResearchProposal::with(['user', 'reviewer'])
            ->whereNotIn('status', ['draft', 'archived'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Ongoing Researches: Phase 4 & 5
        $ongoingResearches = ResearchProposal::with(['user'])
            ->whereIn('current_phase', [4, 5])
            ->whereIn('status', ['ongoing', 'delayed', 'final_approved'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Completed Researches: Phase 6 / Archived
        $completedResearches = ResearchProposal::with(['user'])
            ->whereIn('status', ['completed', 'archived'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('recording_staff.dashboard', compact('routingHistory', 'ongoingResearches', 'completedResearches'));
    }
}
