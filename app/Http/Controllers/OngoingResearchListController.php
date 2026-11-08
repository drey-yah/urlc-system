<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OngoingResearchList;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class OngoingResearchListController extends Controller
{
    /**
     * Display a listing of List of On-going Researches dossiers.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff'])) {
            abort(403, 'Unauthorized access to institutional research dossiers.');
        }

        $lists = OngoingResearchList::with(['preparer', 'noter'])
            ->latest()
            ->paginate(15);

        $pendingCount = OngoingResearchList::where('status', 'pending_noting')->count();
        $notedCount = OngoingResearchList::where('status', 'noted')->count();

        return view('ongoing_researches.index', compact('lists', 'pendingCount', 'notedCount'));
    }

    /**
     * Show form for Director to prepare a new List of On-going Researches.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Only the Research Director or Admin can prepare the List of On-going Researches dossier.');
        }

        $college = $request->input('college');
        $campus = $request->input('campus');

        $query = ResearchProposal::with(['user', 'collaborators', 'milestones'])
            ->where(function ($q) {
                $q->whereIn('status', [
                    'ongoing',
                    'final_approved',
                    'delayed',
                    'funds_certified',
                    'approved',
                    'final_copy_submitted',
                    'final_copy_noted_by_dean',
                    'completed'
                ])
                ->orWhere('current_phase', '>=', 2);
            });

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

        $ongoingProposals = $query->latest()->get();

        $colleges = User::distinct()->whereNotNull('department')->pluck('department')->filter()->values();
        $campuses = User::distinct()->whereNotNull('campus')->pluck('campus')->filter()->values();

        return view('ongoing_researches.create', compact('ongoingProposals', 'colleges', 'campuses', 'college', 'campus'));
    }

    /**
     * Store a newly prepared On-going Researches dossier and notify VPREI.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'fiscal_year' => 'required|integer|min:2020|max:2050',
            'report_date' => 'nullable|date',
            'proposal_ids' => 'required|array|min:1',
            'proposal_ids.*' => 'exists:research_proposals,id',
            'items' => 'nullable|array',
            'remarks' => 'nullable|string|max:2000',
        ], [
            'proposal_ids.required' => 'Please select at least one proposal to include in the On-going Researches list.',
            'proposal_ids.min' => 'Please select at least one proposal to include in the On-going Researches list.',
        ]);

        $selectedIds = array_values(array_unique($validated['proposal_ids']));
        $proposals = ResearchProposal::with(['user', 'collaborators', 'milestones'])
            ->whereIn('id', $selectedIds)
            ->get()
            ->keyBy('id');

        $compiledItems = [];
        foreach ($selectedIds as $proposalId) {
            $p = $proposals->get($proposalId);
            if (!$p) continue;

            $custom = $request->input("items.{$proposalId}", []);

            // Proponents
            $lead = $p->user ? $p->user->name : 'N/A';
            $collabs = $p->collaborators ? $p->collaborators->pluck('name')->toArray() : [];
            $allProponents = implode(', ', array_merge([$lead], $collabs));

            // College
            $college = $p->user?->department ?? 'General';

            // Time Frame
            $timeFrame = '12 Months';
            if ($p->milestones && $p->milestones->count() > 0) {
                $minDate = $p->milestones->min('start_date');
                $maxDate = $p->milestones->max('target_date');
                if ($minDate && $maxDate) {
                    $timeFrame = Carbon::parse($minDate)->format('M Y') . ' - ' . Carbon::parse($maxDate)->format('M Y');
                } elseif ($maxDate) {
                    $timeFrame = 'Until ' . Carbon::parse($maxDate)->format('M Y');
                }
            } elseif ($p->created_at) {
                $timeFrame = 'FY ' . $p->created_at->format('Y');
            }

            $compiledItems[] = [
                'proposal_id' => $p->id,
                'title' => $custom['title'] ?? $p->title,
                'proponents' => !empty($custom['proponents']) ? $custom['proponents'] : $allProponents,
                'college' => !empty($custom['college']) ? $custom['college'] : $college,
                'time_frame' => !empty($custom['time_frame']) ? $custom['time_frame'] : $timeFrame,
            ];
        }

        $list = OngoingResearchList::create([
            'title' => $validated['title'],
            'fiscal_year' => $validated['fiscal_year'],
            'report_date' => $validated['report_date'] ?? now()->toDateString(),
            'prepared_by' => $user->id,
            'status' => 'pending_noting',
            'proposal_ids' => $selectedIds,
            'items' => $compiledItems,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Notify VPREI users
        $vpreiUsers = User::where('role', 'vprei')->get();
        foreach ($vpreiUsers as $vprei) {
            $vprei->notify(new WorkflowStatusNotification(
                null,
                'List of On-going Researches Prepared',
                "Research Director {$user->name} prepared '{$list->title}' (FY {$list->fiscal_year}) for your formal noting.",
                'bi-journal-check',
                'text-warning'
            ));
        }

        return redirect()->route('ongoing_researches.show', $list->id)
            ->with('success', 'List of On-going Researches dossier prepared successfully and transmitted to the VPREI for noting.');
    }

    /**
     * Display the On-going Researches dossier preview.
     */
    public function show($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff'])) {
            abort(403, 'Unauthorized.');
        }

        $list = OngoingResearchList::with(['preparer', 'noter'])->findOrFail($id);
        $items = $list->items ?? [];

        return view('ongoing_researches.show', compact('list', 'items'));
    }

    /**
     * VPREI formally notes the dossier.
     */
    public function note(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['vprei', 'super_admin'])) {
            abort(403, 'Only the Vice President for Research, Extension, and Innovation (VPREI) can note this list.');
        }

        $list = OngoingResearchList::with('preparer')->findOrFail($id);

        $list->update([
            'noted_by' => $user->id,
            'noted_at' => now(),
            'status' => 'noted',
        ]);

        // Notify Research Director
        if ($list->preparer) {
            $list->preparer->notify(new WorkflowStatusNotification(
                null,
                'List of On-going Researches Noted',
                "VPREI {$user->name} has officially noted '{$list->title}' (FY {$list->fiscal_year}) on " . now()->format('F d, Y') . '.',
                'bi-check2-all',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'You have officially noted the List of On-going Researches dossier.');
    }

    /**
     * Export the dossier to print-ready PDF matching University of Antique layout.
     */
    public function exportPdf($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff'])) {
            abort(403, 'Unauthorized.');
        }

        $list = OngoingResearchList::with(['preparer', 'noter'])->findOrFail($id);
        $items = $list->items ?? [];

        $logoBase64 = null;
        if (file_exists(public_path('logo.png'))) {
            $logoBase64 = base64_encode(file_get_contents(public_path('logo.png')));
        }

        $pdf = Pdf::loadView('pdfs.ongoing_researches_list', compact('list', 'items', 'logoBase64'));
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'List_of_Ongoing_Researches_FY_' . $list->fiscal_year . '_' . $list->id . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Delete an un-noted or draft dossier.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $list = OngoingResearchList::findOrFail($id);
        $list->delete();

        return redirect()->route('ongoing_researches.index')
            ->with('success', 'On-going researches dossier removed successfully.');
    }
}
