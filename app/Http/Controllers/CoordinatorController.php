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

        if ($request->action === 'return') {
            $proposal->update([
                'status' => 'returned_for_revision'
            ]);
            return redirect()->back()->with('success', 'Proposal returned to researcher for revision.');
        }

        $proposal->update([
            'status' => 'endorsed' // Formal status instead of endorsed_by_coordinator
        ]);

        return redirect()->back()->with('success', 'Proposal endorsed successfully. It is now awaiting Administrative Review.');
    }
}
