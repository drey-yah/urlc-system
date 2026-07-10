<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResearchProposal;

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
            
        return view('staff.dashboard', compact('proposals'));
    }

    public function forward(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        // Change status to pending_director_review
        $proposal->update([
            'status' => 'pending_director_review'
        ]);

        return redirect()->back()->with('success', 'Manuscript received and forwarded to Research Director successfully.');
    }
}
