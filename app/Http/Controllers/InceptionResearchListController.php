<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InceptionResearchList;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InceptionResearchListController extends Controller
{
    /**
     * Display a listing of Inception Research lists (dossiers).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['staff', 'recording_staff', 'admin', 'super_admin', 'vprei', 'president'])) {
            abort(403, 'Unauthorized access to institutional research dossiers.');
        }

        $lists = InceptionResearchList::with(['preparer', 'noter'])
            ->latest()
            ->paginate(15);

        $pendingCount = InceptionResearchList::where('status', 'pending_noting')->count();
        $notedCount = InceptionResearchList::where('status', 'noted')->count();

        return view('inception_researches.index', compact('lists', 'pendingCount', 'notedCount'));
    }

    /**
     * Show form for Recording Staff to prepare a new List of Research for Inception.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['staff', 'recording_staff', 'admin', 'super_admin'])) {
            abort(403, 'Only Recording Staff or Research Unit Administrators can prepare this list.');
        }

        $college = $request->input('college');
        $campus = $request->input('campus');

        $query = ResearchProposal::with(['user', 'collaborators', 'budgetItems'])
            ->where(function ($q) {
                $q->whereIn('status', ['approved', 'funds_certified', 'in_house_reviewed', 'endorsed', 'ongoing', 'final_approved'])
                    ->orWhere('current_phase', '>=', 1);
            });

        if ($college && $college !== 'all') {
            $query->where(function ($q) use ($college) {
                $q->where('college_affiliation', $college)
                    ->orWhereHas('user', function ($uq) use ($college) {
                        $uq->where('department', $college);
                    });
            });
        }

        if ($campus && $campus !== 'all') {
            $query->whereHas('user', function ($q) use ($campus) {
                $q->where('campus', $campus);
            });
        }

        $eligibleProposals = $query->latest()->get();

        $colleges = User::distinct()->whereNotNull('department')->pluck('department')->filter()->values();
        $campuses = User::distinct()->whereNotNull('campus')->pluck('campus')->filter()->values();

        return view('inception_researches.create', compact('eligibleProposals', 'colleges', 'campuses', 'college', 'campus'));
    }

    /**
     * Store a newly prepared Inception Research dossier and notify Research Director.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['staff', 'recording_staff', 'admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'report_date' => 'required|date',
            'fiscal_year' => 'nullable|integer|min:2020|max:2050',
            'proposal_ids' => 'required|array|min:1',
            'proposal_ids.*' => 'exists:research_proposals,id',
            'items' => 'nullable|array',
            'remarks' => 'nullable|string|max:2000',
        ], [
            'proposal_ids.required' => 'Please select at least one proposal to include in the List of Research for Inception.',
            'proposal_ids.min' => 'Please select at least one proposal to include in the List of Research for Inception.',
        ]);

        $selectedIds = array_values(array_unique($validated['proposal_ids']));
        $proposals = ResearchProposal::with(['user', 'collaborators', 'budgetItems'])
            ->whereIn('id', $selectedIds)
            ->get()
            ->keyBy('id');

        $compiledItems = [];
        foreach ($selectedIds as $proposalId) {
            $p = $proposals->get($proposalId);
            if (!$p) continue;

            $custom = $request->input("items.{$proposalId}", []);

            // Authors
            $lead = $p->user ? $p->user->name : 'N/A';
            $collabs = $p->collaborators ? $p->collaborators->pluck('name')->toArray() : [];
            $allAuthors = implode(', ', array_merge([$lead], $collabs));

            // College
            $collegeName = $p->college_affiliation ?? ($p->user ? $p->user->department : 'College of Arts and Sciences');

            // Supplies & Travel Budget calculation
            $defaultSupplies = 0;
            $defaultTravel = 0;
            if ($p->budgetItems && $p->budgetItems->isNotEmpty()) {
                $defaultSupplies = $p->budgetItems->filter(function ($b) {
                    $haystack = strtolower(($b->item_name ?? '') . ' ' . ($b->category_type ?? '') . ' ' . ($b->category_group ?? ''));
                    return strpos($haystack, 'suppl') !== false || strpos($haystack, 'material') !== false;
                })->sum('amount');

                $defaultTravel = $p->budgetItems->filter(function ($b) {
                    $haystack = strtolower(($b->item_name ?? '') . ' ' . ($b->category_type ?? '') . ' ' . ($b->category_group ?? ''));
                    return strpos($haystack, 'travel') !== false || strpos($haystack, 'transport') !== false;
                })->sum('amount');
            }

            if ($defaultSupplies == 0 && $defaultTravel == 0 && !empty($p->total_budget) && $p->total_budget > 0) {
                $defaultSupplies = round($p->total_budget * 0.6, 2);
                $defaultTravel = round($p->total_budget * 0.4, 2);
            }

            $suppliesVal = isset($custom['supplies_budget']) && $custom['supplies_budget'] !== '' 
                ? (float) str_replace(',', '', $custom['supplies_budget']) 
                : $defaultSupplies;

            $travelVal = isset($custom['travel_budget']) && $custom['travel_budget'] !== '' 
                ? (float) str_replace(',', '', $custom['travel_budget']) 
                : $defaultTravel;

            $compiledItems[] = [
                'proposal_id' => $p->id,
                'title' => $custom['title'] ?? $p->title,
                'authors' => !empty($custom['authors']) ? $custom['authors'] : $allAuthors,
                'college' => !empty($custom['college']) ? $custom['college'] : $collegeName,
                'supplies_budget' => number_format($suppliesVal, 2),
                'travel_budget' => number_format($travelVal, 2),
            ];
        }

        $list = InceptionResearchList::create([
            'title' => $validated['title'],
            'report_date' => $validated['report_date'],
            'fiscal_year' => $validated['fiscal_year'] ?? date('Y', strtotime($validated['report_date'])),
            'prepared_by' => $user->id,
            'status' => 'pending_noting',
            'proposal_ids' => $selectedIds,
            'items' => $compiledItems,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Notify Research Directors (Admin users)
        $directorUsers = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($directorUsers as $director) {
            $director->notify(new WorkflowStatusNotification(
                null,
                'List of Research for Inception Prepared',
                "Recording Staff {$user->name} prepared '{$list->title}' for your formal review and noting.",
                'bi-file-earmark-spreadsheet',
                'text-primary'
            ));
        }

        return redirect()->route('inception_researches.show', $list->id)
            ->with('success', 'List of Research for Inception prepared successfully and submitted to the Research Director for noting.');
    }

    /**
     * Display the Inception Researches dossier preview.
     */
    public function show($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['staff', 'recording_staff', 'admin', 'super_admin', 'vprei', 'president'])) {
            abort(403, 'Unauthorized.');
        }

        $list = InceptionResearchList::with(['preparer', 'noter'])->findOrFail($id);
        $items = $list->items ?? [];

        return view('inception_researches.show', compact('list', 'items'));
    }

    /**
     * Research Director formally notes the dossier.
     */
    public function note(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Only the Director of the Research Unit can note this list.');
        }

        $list = InceptionResearchList::with('preparer')->findOrFail($id);

        $list->update([
            'noted_by' => $user->id,
            'noted_at' => now(),
            'status' => 'noted',
        ]);

        // Notify Recording Staff
        if ($list->preparer) {
            $list->preparer->notify(new WorkflowStatusNotification(
                null,
                'List of Research for Inception Noted',
                "Research Director {$user->name} has officially noted '{$list->title}' on " . now()->format('F d, Y') . '.',
                'bi-check2-all',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'You have officially noted the List of Research for Inception dossier.');
    }

    /**
     * Export the dossier to print-ready PDF matching University of Antique portrait layout.
     */
    public function exportPdf($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['staff', 'recording_staff', 'admin', 'super_admin', 'vprei', 'president'])) {
            abort(403, 'Unauthorized.');
        }

        $list = InceptionResearchList::with(['preparer', 'noter'])->findOrFail($id);
        $items = $list->items ?? [];

        $logoBase64 = null;
        if (extension_loaded('gd') && function_exists('imagecreatefrompng') && file_exists(public_path('logo.png'))) {
            $logoBase64 = base64_encode(file_get_contents(public_path('logo.png')));
        }

        $pdf = Pdf::loadView('pdfs.inception_researches_list', compact('list', 'items', 'logoBase64'));
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'List_of_Research_for_Inception_' . $list->id . '_' . date('Ymd') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Delete an un-noted or draft dossier.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['staff', 'recording_staff', 'admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $list = InceptionResearchList::findOrFail($id);
        $list->delete();

        return redirect()->route('inception_researches.index')
            ->with('success', 'Inception research list removed successfully.');
    }
}
