<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InHouseReviewList;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InHouseReviewListController extends Controller
{
    /**
     * Display a listing of In-house Reviewed Research Proposals dossiers.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff'])) {
            abort(403, 'Unauthorized access to institutional research dossiers.');
        }

        $lists = InHouseReviewList::with(['preparer', 'noter'])
            ->latest()
            ->paginate(15);

        $pendingCount = InHouseReviewList::where('status', 'pending_noting')->count();
        $notedCount = InHouseReviewList::where('status', 'noted')->count();

        return view('in_house_reviews.index', compact('lists', 'pendingCount', 'notedCount'));
    }

    /**
     * Show form for Director to prepare a new list of In-house Reviewed Proposals.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Only the Research Director or Admin can prepare the In-house Reviewed Research Proposals dossier.');
        }

        $college = $request->input('college');
        $campus = $request->input('campus');

        $query = ResearchProposal::with(['user', 'collaborators', 'assignments', 'reviewer', 'milestones'])
            ->where(function ($q) {
                $q->whereIn('status', [
                    'accepted_for_in_house_review',
                    'under_review',
                    'reviewed',
                    'approved',
                    'approved_with_revisions',
                    'revision_required',
                    'rejected',
                    'final_copy_submitted',
                    'final_copy_noted_by_dean',
                    'funds_certified',
                    'endorsed_to_vprei',
                    'final_approved',
                    'ongoing',
                    'completed',
                    'archived'
                ])
                ->orWhereHas('assignments');
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

        $eligibleProposals = $query->latest()->get();

        $colleges = User::distinct()->whereNotNull('department')->pluck('department')->filter()->values();
        $campuses = User::distinct()->whereNotNull('campus')->pluck('campus')->filter()->values();

        return view('in_house_reviews.create', compact('eligibleProposals', 'colleges', 'campuses', 'college', 'campus'));
    }

    /**
     * Store a newly prepared In-house Reviewed dossier and notify VPREI.
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
            'items' => 'nullable|array',
            'remarks' => 'nullable|string|max:2000',
        ], [
            'proposal_ids.required' => 'Please select at least one proposal to include in the In-house Reviewed list.',
            'proposal_ids.min' => 'Please select at least one proposal to include in the In-house Reviewed list.',
        ]);

        $selectedIds = array_values(array_unique($validated['proposal_ids']));
        $proposals = ResearchProposal::with(['user', 'collaborators', 'assignments', 'reviewer', 'milestones'])
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

            // College
            $college = $p->user?->department ?? 'General / University-Wide';

            // Panelists
            $panelistsList = [];
            if ($p->assignments && $p->assignments->count() > 0) {
                $panelistsList = $p->assignments->pluck('name')->toArray();
            }
            if ($p->reviewer && !in_array($p->reviewer->name, $panelistsList)) {
                $panelistsList[] = $p->reviewer->name;
            }
            $defaultPanelists = !empty($panelistsList) ? implode(', ', $panelistsList) : 'Research Unit Review Panel';

            // Remarks
            $defaultRemarks = ucfirst(str_replace('_', ' ', $p->status));
            if (!empty($p->review_comments)) {
                $defaultRemarks .= ' - ' . \Illuminate\Support\Str::limit($p->review_comments, 60);
            }

            $compiledItems[] = [
                'proposal_id' => $p->id,
                'title' => $custom['title'] ?? $p->title,
                'proponents' => !empty($custom['proponents']) ? $custom['proponents'] : $allProponents,
                'time_frame' => !empty($custom['time_frame']) ? $custom['time_frame'] : $timeFrame,
                'college' => !empty($custom['college']) ? $custom['college'] : $college,
                'panelist' => !empty($custom['panelist']) ? $custom['panelist'] : $defaultPanelists,
                'remarks' => !empty($custom['remarks']) ? $custom['remarks'] : $defaultRemarks,
            ];
        }

        $list = InHouseReviewList::create([
            'title' => $validated['title'],
            'report_date' => $validated['report_date'],
            'fiscal_year' => $validated['fiscal_year'] ?? date('Y', strtotime($validated['report_date'])),
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
                'In-house Reviewed Proposals Prepared',
                "Research Director {$user->name} prepared '{$list->title}' for your formal noting.",
                'bi-journal-check',
                'text-warning'
            ));
        }

        return redirect()->route('in_house_reviews.show', $list->id)
            ->with('success', 'In-house Reviewed Research Proposals dossier prepared successfully and transmitted to the VPREI for noting.');
    }

    /**
     * Display the In-house Reviewed dossier preview.
     */
    public function show($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff'])) {
            abort(403, 'Unauthorized.');
        }

        $list = InHouseReviewList::with(['preparer', 'noter'])->findOrFail($id);
        $items = $list->items ?? [];

        return view('in_house_reviews.show', compact('list', 'items'));
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

        $list = InHouseReviewList::with('preparer')->findOrFail($id);

        $list->update([
            'noted_by' => $user->id,
            'noted_at' => now(),
            'status' => 'noted',
        ]);

        // Notify Research Director
        if ($list->preparer) {
            $list->preparer->notify(new WorkflowStatusNotification(
                null,
                'In-house Reviewed Proposals Noted',
                "VPREI {$user->name} has officially noted '{$list->title}' on " . now()->format('F d, Y') . '.',
                'bi-check2-all',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'You have officially noted the In-house Reviewed Research Proposals dossier.');
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

        $list = InHouseReviewList::with(['preparer', 'noter'])->findOrFail($id);
        $items = $list->items ?? [];

        $logoBase64 = null;
        if (file_exists(public_path('logo.png'))) {
            $logoBase64 = base64_encode(file_get_contents(public_path('logo.png')));
        }

        $pdf = Pdf::loadView('pdfs.in_house_reviewed_proposals', compact('list', 'items', 'logoBase64'));
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'In_House_Reviewed_Proposals_' . $list->id . '_' . date('Ymd') . '.pdf';
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

        $list = InHouseReviewList::findOrFail($id);
        $list->delete();

        return redirect()->route('in_house_reviews.index')
            ->with('success', 'In-house reviewed proposals dossier removed successfully.');
    }
}
