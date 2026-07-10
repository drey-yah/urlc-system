<?php

namespace App\Http\Controllers;

use App\Models\ResearchProposal;
use Illuminate\Http\Request;

class DeanController extends Controller
{
    public function dashboard()
    {
        $department = auth()->user()->department;

        if (empty($department)) {
            abort(403, 'Your account is not assigned to a college/department. Please contact an administrator.');
        }

        // Proposals waiting for Dean to note initial Coordinator endorsement
        $pendingNoting = ResearchProposal::with(['user', 'documents'])
            ->where('status', 'pending_dean_noting')
            ->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            })
            ->latest()
            ->get();

        // Proposals waiting for Dean to note the submitted final copy
        $finalNoting = ResearchProposal::with(['user', 'documents'])
            ->where('status', 'final_copy_submitted')
            ->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            })
            ->latest()
            ->get();

        return view('dean.dashboard', compact('pendingNoting', 'finalNoting', 'department'));
    }

    public function noteEndorsement(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if ($proposal->user->department !== auth()->user()->department) {
            abort(403, 'Unauthorized.');
        }

        if ($request->action === 'return') {
            $proposal->update([
                'status' => 'returned_for_revision'
            ]);
            return redirect()->back()->with('success', 'Proposal returned to coordinator/researcher.');
        }

        $proposal->update([
            'status' => 'noted_by_dean'
        ]);

        return redirect()->back()->with('success', 'Proposal noted successfully. Coordinator will be notified to submit it to the Research Unit.');
    }

    public function noteFinalCopy(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if ($proposal->user->department !== auth()->user()->department) {
            abort(403, 'Unauthorized.');
        }

        if ($proposal->status !== 'final_copy_submitted') {
            return redirect()->back()->with('error', 'Proposal final copy is not submitted.');
        }

        $proposal->update([
            'status' => 'final_copy_noted_by_dean'
        ]);

        return redirect()->back()->with('success', 'Final copy noted successfully. Awaiting Research Director endorsement to VPREI.');
    }
}
