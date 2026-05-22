<?php

namespace App\Http\Controllers;

use App\Models\ResearchProposal;
use Illuminate\Http\Request;

class RepositoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ResearchProposal::with('user', 'collaborators')
            ->where('current_phase', 5) // Phase 5 = Completed
            ->whereIn('status', ['final_approved', 'approved']);
            
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%")
                  ->orWhere('research_field', 'like', "%{$search}%")
                  ->orWhere('proposal_code', 'like', "%{$search}%");
            });
        }

        $completedResearches = $query->latest()->paginate(10);

        return view('repository.index', compact('completedResearches'));
    }
}
