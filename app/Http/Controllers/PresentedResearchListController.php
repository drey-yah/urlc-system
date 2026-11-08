<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresentedResearchList;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PresentedResearchListController extends Controller
{
    /**
     * Display a listing of Presented Research dossiers.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['staff', 'recording_staff', 'admin', 'super_admin', 'vprei', 'president'])) {
            abort(403, 'Unauthorized access to institutional research dossiers.');
        }

        $lists = PresentedResearchList::with(['preparer', 'noter'])
            ->latest()
            ->paginate(15);

        $pendingCount = PresentedResearchList::where('status', 'pending_noting')->count();
        $notedCount = PresentedResearchList::where('status', 'noted')->count();

        return view('presented_researches.index', compact('lists', 'pendingCount', 'notedCount'));
    }

    /**
     * Show form for Receiving Staff to prepare a new List of Presented Researches.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['staff', 'recording_staff', 'admin', 'super_admin'])) {
            abort(403, 'Only Receiving Staff or Research Unit Administrators can prepare the List of Presented Researches dossier.');
        }

        $college = $request->input('college');
        $campus = $request->input('campus');

        $query = ResearchProposal::with(['user', 'collaborators', 'researchPresentations', 'localForumSubmissions'])
            ->where(function ($q) {
                $q->whereHas('researchPresentations')
                    ->orWhereHas('localForumSubmissions')
                    ->orWhere('current_phase', '>=', 3)
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

        return view('presented_researches.create', compact('eligibleProposals', 'colleges', 'campuses', 'college', 'campus'));
    }

    /**
     * Store a newly prepared Presented Researches dossier and notify Research Director.
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
            'proposal_ids.required' => 'Please select at least one proposal to include in the List of Presented Researches.',
            'proposal_ids.min' => 'Please select at least one proposal to include in the List of Presented Researches.',
        ]);

        $selectedIds = array_values(array_unique($validated['proposal_ids']));
        $proposals = ResearchProposal::with(['user', 'collaborators', 'researchPresentations', 'localForumSubmissions'])
            ->whereIn('id', $selectedIds)
            ->get()
            ->keyBy('id');

        $compiledItems = [];
        foreach ($selectedIds as $proposalId) {
            $p = $proposals->get($proposalId);
            if (!$p) continue;

            $custom = $request->input("items.{$proposalId}", []);
            $latestPres = $p->researchPresentations ? $p->researchPresentations->last() : null;

            // Authors
            $lead = $p->user ? $p->user->name : 'N/A';
            $collabs = $p->collaborators ? $p->collaborators->pluck('name')->toArray() : [];
            $allAuthors = implode(', ', array_merge([$lead], $collabs));

            // Date Presented
            $presDate = Carbon::parse($validated['report_date'])->format('F d, Y');
            if ($latestPres && $latestPres->event_date) {
                $presDate = $latestPres->event_date->format('F d, Y');
            }

            // Title of Conference
            $confTitle = 'Annual University In-House & Research Colloquium';
            if ($latestPres && !empty($latestPres->conference_name)) {
                $confTitle = $latestPres->conference_name;
            }

            // Venue of Conference
            $venue = 'University of Antique Main Campus, Sibalom';
            if ($latestPres && !empty($latestPres->venue)) {
                $venue = $latestPres->venue;
            }

            // Type of conference (Local, Regional, International)
            $confType = 'Regional';
            if ($latestPres && !empty($latestPres->presentation_type)) {
                $confType = ucfirst($latestPres->presentation_type);
            }

            $compiledItems[] = [
                'proposal_id' => $p->id,
                'title' => $custom['title'] ?? $p->title,
                'authors' => !empty($custom['authors']) ? $custom['authors'] : $allAuthors,
                'date_presented' => !empty($custom['date_presented']) ? $custom['date_presented'] : $presDate,
                'conference_title' => !empty($custom['conference_title']) ? $custom['conference_title'] : $confTitle,
                'venue' => !empty($custom['venue']) ? $custom['venue'] : $venue,
                'conference_type' => !empty($custom['conference_type']) ? $custom['conference_type'] : $confType,
            ];
        }

        $list = PresentedResearchList::create([
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
                'List of Presented Researches Prepared',
                "Receiving Staff {$user->name} prepared '{$list->title}' for your formal noting.",
                'bi-journal-check',
                'text-warning'
            ));
        }

        return redirect()->route('presented_researches.show', $list->id)
            ->with('success', 'List of Presented Researches dossier prepared successfully and transmitted to the Research Director for noting.');
    }

    /**
     * Display the Presented Researches dossier preview.
     */
    public function show($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['staff', 'recording_staff', 'admin', 'super_admin', 'vprei', 'president'])) {
            abort(403, 'Unauthorized.');
        }

        $list = PresentedResearchList::with(['preparer', 'noter'])->findOrFail($id);
        $items = $list->items ?? [];

        return view('presented_researches.show', compact('list', 'items'));
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

        $list = PresentedResearchList::with('preparer')->findOrFail($id);

        $list->update([
            'noted_by' => $user->id,
            'noted_at' => now(),
            'status' => 'noted',
        ]);

        // Notify Receiving Staff
        if ($list->preparer) {
            $list->preparer->notify(new WorkflowStatusNotification(
                null,
                'List of Presented Researches Noted',
                "Research Director {$user->name} has officially noted '{$list->title}' on " . now()->format('F d, Y') . '.',
                'bi-check2-all',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'You have officially noted the List of Presented Researches dossier.');
    }

    /**
     * Export the dossier to print-ready PDF matching University of Antique landscape layout.
     */
    public function exportPdf($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['staff', 'recording_staff', 'admin', 'super_admin', 'vprei', 'president'])) {
            abort(403, 'Unauthorized.');
        }

        $list = PresentedResearchList::with(['preparer', 'noter'])->findOrFail($id);
        $items = $list->items ?? [];

        $logoBase64 = null;
        if (file_exists(public_path('logo.png'))) {
            $logoBase64 = base64_encode(file_get_contents(public_path('logo.png')));
        }

        $pdf = Pdf::loadView('pdfs.presented_researches_list', compact('list', 'items', 'logoBase64'));
        $pdf->setPaper('A4', 'landscape');

        $fileName = 'List_of_Presented_Researches_' . $list->id . '_' . date('Ymd') . '.pdf';
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

        $list = PresentedResearchList::findOrFail($id);
        $list->delete();

        return redirect()->route('presented_researches.index')
            ->with('success', 'Presented researches dossier removed successfully.');
    }
}
