<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\ActivityDesign;

class FinanceOfficerController extends Controller
{
    public function dashboard()
    {
        $pendingPRs = PurchaseRequest::with(['proposal.user'])
            ->where('status', 'pending_finance_approval')
            ->latest()
            ->get();

        $approvedPRs = PurchaseRequest::with(['proposal.user'])
            ->where('status', 'approved')
            ->latest()
            ->get();

        $allPRs = PurchaseRequest::with(['proposal.user'])
            ->latest()
            ->get();

        $stats = [
            'pending_prs' => $pendingPRs->count(),
            'approved_prs' => $approvedPRs->count(),
            'total_prs' => $allPRs->count(),
            'total_amount_approved' => $approvedPRs->sum('total_amount'),
        ];

        return view('finance.dashboard', compact('pendingPRs', 'approvedPRs', 'allPRs', 'stats'));
    }

    public function approvePR(Request $request, $id)
    {
        $pr = PurchaseRequest::findOrFail($id);

        $pr->update([
            'finance_approved' => \Illuminate\Support\Facades\DB::raw('true'),
            'finance_approved_at' => now(),
            'status' => 'approved',
        ]);

        // Notify Researcher
        $pr->proposal->user->notify(new \App\Notifications\WorkflowStatusNotification(
            $pr->proposal,
            'Purchase Request Approved by Finance',
            "Finance Officer approved your Purchase Request (₱" . number_format($pr->total_amount, 2) . ") for '{$pr->proposal->title}'. Procurement can proceed.",
            'bi-cart-check-fill',
            'text-success'
        ));

        return redirect()->back()->with('success', "Purchase Request #{$pr->id} has been approved successfully for procurement.");
    }

    public function rejectPR(Request $request, $id)
    {
        $pr = PurchaseRequest::findOrFail($id);

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $pr->update([
            'status' => 'rejected',
        ]);

        // Notify Researcher
        $pr->proposal->user->notify(new \App\Notifications\WorkflowStatusNotification(
            $pr->proposal,
            'Purchase Request Returned by Finance',
            "Finance Officer returned/rejected your Purchase Request for '{$pr->proposal->title}'. Reason: {$request->reason}",
            'bi-x-circle-fill',
            'text-danger'
        ));

        return redirect()->back()->with('success', "Purchase Request #{$pr->id} has been returned/rejected.");
    }
}
