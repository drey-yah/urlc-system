<?php

namespace App\Http\Controllers;

use App\Models\ProposalBudgetItem;
use App\Models\ResearchProposal;
use Illuminate\Http\Request;

class ProposalBudgetItemController extends Controller
{
    public function store(Request $request, $proposal_id)
    {
        $proposal = ResearchProposal::findOrFail($proposal_id);

        // Only lead researcher can manage budget items
        if ($proposal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'category_type' => 'required|in:mooe,co',
            'category_group' => 'required|string',
            'item_name' => 'required|string',
            'funding_agency' => 'nullable|string',
            'equivalent_teaching_unit' => 'nullable|string',
            'existing_resources' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ]);

        ProposalBudgetItem::create([
            'research_proposal_id' => $proposal->id,
            'category_type' => $request->category_type,
            'category_group' => $request->category_group,
            'item_name' => $request->item_name,
            'funding_agency' => $request->funding_agency,
            'equivalent_teaching_unit' => $request->equivalent_teaching_unit,
            'existing_resources' => $request->existing_resources,
            'amount' => $request->amount,
        ]);

        // Recalculate total budget for proposal
        $totalBudget = $proposal->budgetItems()->sum('amount');
        $proposal->update(['total_budget' => $totalBudget]);

        return redirect()->back()->with('success', 'Line Item Budget entry added successfully.');
    }

    public function destroy($id)
    {
        $item = ProposalBudgetItem::findOrFail($id);
        $proposal = $item->proposal;

        if ($proposal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $item->delete();

        // Recalculate total budget for proposal
        $totalBudget = $proposal->budgetItems()->sum('amount');
        $proposal->update(['total_budget' => $totalBudget]);

        return redirect()->back()->with('success', 'Line Item Budget entry removed successfully.');
    }
}
