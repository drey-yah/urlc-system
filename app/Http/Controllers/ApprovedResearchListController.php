<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovedResearchList;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ApprovedResearchListController extends Controller
{
    /**
     * Display a listing of prepared Approved Research lists.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff'])) {
            abort(403, 'Unauthorized access to institutional research dossiers.');
        }

        $lists = ApprovedResearchList::with(['preparer', 'noter'])
            ->latest()
            ->paginate(15);

        $pendingCount = ApprovedResearchList::where('status', 'pending_noting')->count();
        $notedCount = ApprovedResearchList::where('status', 'noted')->count();

        return view('approved_researches.index', compact('lists', 'pendingCount', 'notedCount'));
    }

    /**
     * Show form for Director to prepare a new list of approved researches.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Only the Research Director or Admin can prepare the List of Approved Researches.');
        }

        $college = $request->input('college');
        $campus = $request->input('campus');

        $query = ResearchProposal::with(['user', 'collaborators', 'budgetItems', 'milestones'])
            ->whereIn('status', [
                'approved',
                'approved_with_revisions',
                'final_copy_submitted',
                'final_copy_noted_by_dean',
                'funds_certified',
                'endorsed_to_vprei',
                'final_approved',
                'ongoing',
                'completed',
                'archived'
            ]);

        if ($college && $college !== 'all') {
            $query->whereHas('user', function ($q) use ($college) {
                $q->where('department', $college);
            });
        }

        if ($campus && $campus !== 'all') {
            $query->whereHas('user', function ($q) use ($campus) {
                $q->where('campus', $campus);
            });
        }

        $approvedProposals = $query->latest()->get();

        $colleges = User::distinct()->whereNotNull('department')->pluck('department')->filter()->values();
        $campuses = User::distinct()->whereNotNull('campus')->pluck('campus')->filter()->values();

        return view('approved_researches.create', compact('approvedProposals', 'colleges', 'campuses', 'college', 'campus'));
    }

    /**
     * Store a newly prepared list and notify the VPREI.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'report_date' => 'required|date',
            'fiscal_year' => 'nullable|integer',
            'proposal_ids' => 'required|array|min:1',
            'proposal_ids.*' => 'exists:research_proposals,id',
            'remarks' => 'nullable|string|max:2000',
        ], [
            'proposal_ids.required' => 'Please select at least one approved research to include in the list.',
            'proposal_ids.min' => 'Please select at least one approved research to include in the list.',
        ]);

        $list = ApprovedResearchList::create([
            'title' => $validated['title'],
            'report_date' => $validated['report_date'],
            'fiscal_year' => $validated['fiscal_year'] ?? date('Y', strtotime($validated['report_date'])),
            'prepared_by' => $user->id,
            'status' => 'pending_noting',
            'proposal_ids' => array_values(array_unique($validated['proposal_ids'])),
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Notify VPREI users
        $vpreiUsers = User::where('role', 'vprei')->get();
        foreach ($vpreiUsers as $vprei) {
            $vprei->notify(new WorkflowStatusNotification(
                null,
                'List of Approved Researches Prepared',
                "Research Director {$user->name} prepared '{$list->title}' for your formal noting.",
                'bi-journal-check',
                'text-warning'
            ));
        }

        return redirect()->route('approved_researches.show', $list->id)
            ->with('success', 'List of Approved Researches prepared successfully and transmitted to the VPREI for noting.');
    }

    /**
     * Display the list document preview.
     */
    public function show($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff'])) {
            abort(403, 'Unauthorized.');
        }

        $list = ApprovedResearchList::with(['preparer', 'noter'])->findOrFail($id);
        $proposals = $list->proposals;

        return view('approved_researches.show', compact('list', 'proposals'));
    }

    /**
     * VPREI formally notes the list.
     */
    public function note(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['vprei', 'super_admin'])) {
            abort(403, 'Only the Vice President for Research, Extension, and Innovation (VPREI) can note this list.');
        }

        $list = ApprovedResearchList::with('preparer')->findOrFail($id);

        $list->update([
            'noted_by' => $user->id,
            'noted_at' => now(),
            'status' => 'noted',
        ]);

        // Notify Research Director
        if ($list->preparer) {
            $list->preparer->notify(new WorkflowStatusNotification(
                null,
                'List of Approved Researches Noted',
                "VPREI {$user->name} has officially noted '{$list->title}' on " . now()->format('F d, Y') . '.',
                'bi-check2-all',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'You have officially noted the List of Approved Researches.');
    }

    /**
     * Export the list to a print-ready PDF matching the official University of Antique format.
     */
    public function exportPdf($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff'])) {
            abort(403, 'Unauthorized.');
        }

        $list = ApprovedResearchList::with(['preparer', 'noter'])->findOrFail($id);
        $proposals = $list->proposals;

        $logoBase64 = null;
        if (file_exists(public_path('logo.png'))) {
            $logoBase64 = base64_encode(file_get_contents(public_path('logo.png')));
        }

        $pdf = Pdf::loadView('pdfs.approved_researches_list', compact('list', 'proposals', 'logoBase64'));
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'List_of_Approved_Researches_' . $list->id . '_' . date('Ymd') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Delete an un-noted or draft list.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $list = ApprovedResearchList::findOrFail($id);
        $list->delete();

        return redirect()->route('approved_researches.index')
            ->with('success', 'Approved researches list removed successfully.');
    }
}
