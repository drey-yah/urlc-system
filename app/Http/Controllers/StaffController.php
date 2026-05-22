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
        // Staff sees all endorsed proposals globally
        $proposals = ResearchProposal::with(['user', 'documents'])
            ->where('status', 'endorsed_by_coordinator')
            ->latest()
            ->get();
            
        return view('staff.dashboard', compact('proposals'));
    }

    public function forward(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        // Change status to pending so Admin can assign reviewer
        $proposal->update([
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Manuscript received and forwarded to Admin successfully.');
    }
}
