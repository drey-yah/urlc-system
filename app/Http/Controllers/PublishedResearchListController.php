<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PublishedResearchList;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PublishedResearchListController extends Controller
{
    /**
     * Display a listing of Published Research dossiers.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff'])) {
            abort(403, 'Unauthorized access to institutional research dossiers.');
        }

        $lists = PublishedResearchList::with(['preparer', 'noter'])
            ->latest()
            ->paginate(15);

        $pendingCount = PublishedResearchList::where('status', 'pending_noting')->count();
        $notedCount = PublishedResearchList::where('status', 'noted')->count();

        return view('published_researches_dossiers.index', compact('lists', 'pendingCount', 'notedCount'));
    }

    /**
     * Show form for Director to prepare a new List of Published Researches.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Only the Research Director or Admin can prepare the List of Published Researches dossier.');
        }

        $college = $request->input('college');
        $campus = $request->input('campus');

        $query = ResearchProposal::with(['user', 'collaborators', 'researchPublications'])
            ->where(function ($q) {
                $q->whereHas('researchPublications')
                    ->orWhere('current_phase', '>=', 4)
                    ->orWhereIn('status', ['final_approved', 'completed', 'approved', 'ongoing', 'archived']);
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

        return view('published_researches_dossiers.create', compact('eligibleProposals', 'colleges', 'campuses', 'college', 'campus'));
    }

    /**
     * Store a newly prepared Published Researches dossier and notify VPREI.
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
            'fiscal_year' => 'nullable|integer|min:2020|max:2050',
            'proposal_ids' => 'required|array|min:1',
            'proposal_ids.*' => 'exists:research_proposals,id',
            'items' => 'nullable|array',
            'remarks' => 'nullable|string|max:2000',
        ], [
            'proposal_ids.required' => 'Please select at least one proposal to include in the List of Published Researches.',
            'proposal_ids.min' => 'Please select at least one proposal to include in the List of Published Researches.',
        ]);

        $selectedIds = array_values(array_unique($validated['proposal_ids']));
        $proposals = ResearchProposal::with(['user', 'collaborators', 'researchPublications'])
            ->whereIn('id', $selectedIds)
            ->get()
            ->keyBy('id');

        $compiledItems = [];
        foreach ($selectedIds as $proposalId) {
            $p = $proposals->get($proposalId);
            if (!$p) continue;

            $custom = $request->input("items.{$proposalId}", []);
            $latestPub = $p->researchPublications ? $p->researchPublications->last() : null;

            // Authors
            $lead = $p->user ? $p->user->name : 'N/A';
            $collabs = $p->collaborators ? $p->collaborators->pluck('name')->toArray() : [];
            $allAuthors = implode(', ', array_merge([$lead], $collabs));

            // Date Published
            $pubDate = Carbon::parse($validated['report_date'])->format('F Y');
            if ($latestPub && $latestPub->updated_at) {
                $pubDate = $latestPub->updated_at->format('F Y');
            } elseif ($p->updated_at) {
                $pubDate = $p->updated_at->format('F Y');
            }

            // Type of Journal/Publication
            $pubType = 'Peer-Reviewed Journal';
            if ($latestPub && !empty($latestPub->indexing_agency)) {
                $pubType = $latestPub->indexing_agency;
            }

            // Name of Publication
            $pubName = 'University of Antique Research Journal';
            if ($latestPub && !empty($latestPub->journal_title)) {
                $pubName = $latestPub->journal_title;
            }

            // Vol. No.
            $volNo = 'Vol. ' . date('y', strtotime($validated['report_date'])) . ' No. 1';

            $compiledItems[] = [
                'proposal_id' => $p->id,
                'title' => $custom['title'] ?? $p->title,
                'authors' => !empty($custom['authors']) ? $custom['authors'] : $allAuthors,
                'date_published' => !empty($custom['date_published']) ? $custom['date_published'] : $pubDate,
                'type_of_publication' => !empty($custom['type_of_publication']) ? $custom['type_of_publication'] : $pubType,
                'name_of_publication' => !empty($custom['name_of_publication']) ? $custom['name_of_publication'] : $pubName,
                'volume_no' => !empty($custom['volume_no']) ? $custom['volume_no'] : $volNo,
            ];
        }

        $list = PublishedResearchList::create([
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
                'List of Published Researches Prepared',
                "Research Director {$user->name} prepared '{$list->title}' for your formal noting.",
                'bi-journal-check',
                'text-warning'
            ));
        }

        return redirect()->route('published_researches_dossiers.show', $list->id)
            ->with('success', 'List of Published Researches dossier prepared successfully and transmitted to the VPREI for noting.');
    }

    /**
     * Display the Published Researches dossier preview.
     */
    public function show($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff'])) {
            abort(403, 'Unauthorized.');
        }

        $list = PublishedResearchList::with(['preparer', 'noter'])->findOrFail($id);
        $items = $list->items ?? [];

        return view('published_researches_dossiers.show', compact('list', 'items'));
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

        $list = PublishedResearchList::with('preparer')->findOrFail($id);

        $list->update([
            'noted_by' => $user->id,
            'noted_at' => now(),
            'status' => 'noted',
        ]);

        // Notify Research Director
        if ($list->preparer) {
            $list->preparer->notify(new WorkflowStatusNotification(
                null,
                'List of Published Researches Noted',
                "VPREI {$user->name} has officially noted '{$list->title}' on " . now()->format('F d, Y') . '.',
                'bi-check2-all',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'You have officially noted the List of Published Researches dossier.');
    }

    /**
     * Export the dossier to print-ready PDF matching University of Antique landscape layout.
     */
    public function exportPdf($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff'])) {
            abort(403, 'Unauthorized.');
        }

        $list = PublishedResearchList::with(['preparer', 'noter'])->findOrFail($id);
        $items = $list->items ?? [];

        $logoBase64 = null;
        if (file_exists(public_path('logo.png'))) {
            $logoBase64 = base64_encode(file_get_contents(public_path('logo.png')));
        }

        $pdf = Pdf::loadView('pdfs.published_researches_list', compact('list', 'items', 'logoBase64'));
        $pdf->setPaper('A4', 'landscape');

        $fileName = 'List_of_Published_Researches_' . $list->id . '_' . date('Ymd') . '.pdf';
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

        $list = PublishedResearchList::findOrFail($id);
        $list->delete();

        return redirect()->route('published_researches_dossiers.index')
            ->with('success', 'Published researches dossier removed successfully.');
    }
}
