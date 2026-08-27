<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\ResearchProposal;

class PurchaseRequestController extends Controller
{
    public function store(Request $request, $proposalId)
    {
        $proposal = ResearchProposal::findOrFail($proposalId);

        if ($proposal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized submission.');
        }

        $request->validate([
            'pr_number' => 'nullable|string|max:100',
            'purpose' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
            'document_path' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // Purchase Request PDF
        ]);

        $filePath = null;
        if ($request->hasFile('document_path')) {
            $filePath = $request->file('document_path')->store('phase2/purchase_requests', config('filesystems.default', 'public'));
        }

        $pr = PurchaseRequest::create([
            'research_proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'pr_number' => $request->pr_number,
            'purpose' => $request->purpose,
            'total_amount' => $request->total_amount,
            'document_path' => $filePath,
            'status' => 'pending_director_countersign',
        ]);

        // Notify Research Director
        $directors = \App\Models\User::where('role', 'admin')->get();
        foreach ($directors as $director) {
            $director->notify(new \App\Notifications\WorkflowStatusNotification(
                $proposal,
                'New Purchase Request Submitted',
                "Researcher {$proposal->user->name} submitted a Purchase Request for '{$proposal->title}'.",
                'bi-cart-plus-fill',
                'text-primary'
            ));
        }

        return redirect()->back()->with('success', 'Purchase Request (PR) submitted successfully! Awaiting Research Director countersignature.');
    }

    public function directorCountersign($id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        $pr->update([
            'director_countersigned' => \Illuminate\Support\Facades\DB::raw('true'),
            'director_countersigned_at' => now(),
            'status' => 'pending_finance_approval',
        ]);

        // Notify Finance Officer
        $financeOfficers = \App\Models\User::where('role', 'sao_finance')->get();
        foreach ($financeOfficers as $fo) {
            $fo->notify(new \App\Notifications\WorkflowStatusNotification(
                $pr->proposal,
                'PR Countersigned — Pending Finance Approval',
                "Purchase Request for '{$pr->proposal->title}' countersigned by Director. Ready for Finance Officer approval.",
                'bi-bank',
                'text-warning'
            ));
        }

        // Notify Researcher
        $pr->proposal->user->notify(new \App\Notifications\WorkflowStatusNotification(
            $pr->proposal,
            'PR Countersigned by Director',
            "Research Director countersigned your Purchase Request for '{$pr->proposal->title}'. Forwarded to Finance Officer.",
            'bi-check-circle-fill',
            'text-success'
        ));

        return redirect()->back()->with('success', 'Purchase Request countersigned by Research Director. Forwarded to Finance Officer for procurement approval.');
    }
}
