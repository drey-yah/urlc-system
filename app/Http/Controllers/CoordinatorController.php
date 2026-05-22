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
        
        // Coordinator sees proposals from their department that need endorsement
        $proposals = ResearchProposal::with(['user', 'documents'])
            ->where('status', 'pending_coordinator_endorsement')
            ->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            })
            ->latest()
            ->get();
            
        return view('coordinator.dashboard', compact('proposals', 'department'));
    }

    public function endorse(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);
        
        // Ensure it belongs to the same department
        if ($proposal->user->department !== auth()->user()->department) {
            abort(403, 'Unauthorized. You can only endorse proposals from your department.');
        }

        $proposal->update([
            'status' => 'endorsed_by_coordinator'
        ]);

        return redirect()->back()->with('success', 'Proposal endorsed successfully. It is now awaiting receiving by Support Staff.');
    }
}
