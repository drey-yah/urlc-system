<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompletedResearchSummary;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CompletedResearchSummaryController extends Controller
{
    /**
     * Display a listing of Completed Researches Summary Forms.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'dean', 'admin', 'super_admin', 'vprei', 'president', 'staff', 'recording_staff'])) {
            abort(403, 'Unauthorized access to institutional research dossiers.');
        }

        $query = CompletedResearchSummary::with(['preparer', 'noter'])->latest();

        // If Coordinator or Dean, scope by their assigned college/department
        if (in_array($user->role, ['coordinator', 'dean']) && !empty($user->department)) {
            $query->where('college', $user->department);
        }

        $lists = $query->paginate(15);

        $pendingCountQuery = CompletedResearchSummary::where('status', 'pending_noting');
        $notedCountQuery = CompletedResearchSummary::where('status', 'noted');

        if (in_array($user->role, ['coordinator', 'dean']) && !empty($user->department)) {
            $pendingCountQuery->where('college', $user->department);
            $notedCountQuery->where('college', $user->department);
        }

        $pendingCount = $pendingCountQuery->count();
        $notedCount = $notedCountQuery->count();

        return view('completed_researches_summary.index', compact('lists', 'pendingCount', 'notedCount'));
    }

    /**
     * Show form for College Research Coordinator to prepare a new Completed Researches Summary Form.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'admin', 'super_admin'])) {
            abort(403, 'Only College Research Coordinators or Administrators can prepare the Completed Researches Summary Form.');
        }

        $coordinatorCollege = $user->department ?? 'College of Teacher Education';
        $college = $request->input('college', $coordinatorCollege);

        $query = ResearchProposal::with(['user', 'collaborators', 'terminalReport', 'milestones', 'purchaseRequests'])
            ->where(function ($q) use ($college) {
                $q->where('college_affiliation', $college)
                    ->orWhereHas('user', function ($uq) use ($college) {
                        $uq->where('department', $college);
                    });
            })
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhereHas('terminalReport')
                    ->orWhere('current_phase', '>=', 2)
                    ->orWhereIn('status', ['final_approved', 'approved', 'ongoing']);
            });

        $eligibleProposals = $query->latest()->get();

        $colleges = User::distinct()->whereNotNull('department')->pluck('department')->filter()->values();
        if (!$colleges->contains($coordinatorCollege)) {
            $colleges->push($coordinatorCollege);
        }

        return view('completed_researches_summary.create', compact('eligibleProposals', 'colleges', 'college', 'coordinatorCollege'));
    }

    /**
     * Store a newly prepared Completed Researches Summary Form and transmit to College Dean for noting.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'college' => 'required|string|max:255',
            'report_date' => 'required|date',
            'fiscal_year' => 'nullable|integer|min:2020|max:2050',
            'proposal_ids' => 'required|array|min:1',
            'proposal_ids.*' => 'exists:research_proposals,id',
            'items' => 'nullable|array',
            'remarks' => 'nullable|string|max:2000',
        ], [
            'proposal_ids.required' => 'Please select at least one completed research project to include in the summary form.',
            'proposal_ids.min' => 'Please select at least one completed research project to include in the summary form.',
        ]);

        $selectedIds = array_values(array_unique($validated['proposal_ids']));
        $proposals = ResearchProposal::with(['user', 'collaborators', 'terminalReport', 'milestones', 'purchaseRequests'])
            ->whereIn('id', $selectedIds)
            ->get()
            ->keyBy('id');

        $compiledItems = [];
        $counter = 1;
        foreach ($selectedIds as $proposalId) {
            $p = $proposals->get($proposalId);
            if (!$p) continue;

            $custom = $request->input("items.{$proposalId}", []);

            // Proponents
            $lead = $p->user ? $p->user->name : 'N/A';
            $collabs = $p->collaborators ? $p->collaborators->pluck('name')->toArray() : [];
            $allProponents = implode(', ', array_merge([$lead], $collabs));

            // Duration
            $duration = '12 Months';
            if ($p->milestones && $p->milestones->count() > 0) {
                $minDate = $p->milestones->min('start_date');
                $maxDate = $p->milestones->max('target_date');
                if ($minDate && $maxDate) {
                    $months = Carbon::parse($minDate)->diffInMonths(Carbon::parse($maxDate));
                    if ($months > 0) {
                        $duration = $months . ' Months';
                    }
                }
            }

            // Dates Started & Completed
            $dateStarted = $p->created_at ? $p->created_at->format('M d, Y') : date('M d, Y');
            if ($p->milestones && $p->milestones->count() > 0 && $p->milestones->min('start_date')) {
                $dateStarted = Carbon::parse($p->milestones->min('start_date'))->format('M d, Y');
            }

            $dateCompleted = date('M d, Y');
            if ($p->terminalReport && $p->terminalReport->updated_at) {
                $dateCompleted = $p->terminalReport->updated_at->format('M d, Y');
            } elseif ($p->status === 'completed' && $p->updated_at) {
                $dateCompleted = $p->updated_at->format('M d, Y');
            }

            // Budget & Expenditure
            $budgetAmount = $p->total_budget ? number_format($p->total_budget, 2) : '50,000.00';
            $expenditureAmount = $budgetAmount;
            if ($p->purchaseRequests && $p->purchaseRequests->count() > 0) {
                $sumPR = $p->purchaseRequests->sum('total_amount');
                if ($sumPR > 0) {
                    $expenditureAmount = number_format($sumPR, 2);
                }
            }

            $compiledItems[] = [
                'no' => $counter++,
                'proposal_id' => $p->id,
                'project_title' => !empty($custom['project_title']) ? $custom['project_title'] : $p->title,
                'proponents' => !empty($custom['proponents']) ? $custom['proponents'] : $allProponents,
                'duration' => !empty($custom['duration']) ? $custom['duration'] : $duration,
                'date_started' => !empty($custom['date_started']) ? $custom['date_started'] : $dateStarted,
                'date_completed' => !empty($custom['date_completed']) ? $custom['date_completed'] : $dateCompleted,
                'budget' => !empty($custom['budget']) ? $custom['budget'] : $budgetAmount,
                'expenditure' => !empty($custom['expenditure']) ? $custom['expenditure'] : $expenditureAmount,
            ];
        }

        $summary = CompletedResearchSummary::create([
            'title' => $validated['title'],
            'college' => $validated['college'],
            'report_date' => $validated['report_date'],
            'fiscal_year' => $validated['fiscal_year'] ?? date('Y', strtotime($validated['report_date'])),
            'prepared_by' => $user->id,
            'status' => 'pending_noting',
            'proposal_ids' => $selectedIds,
            'items' => $compiledItems,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Notify College Dean for this college
        $deans = User::where('role', 'dean')
            ->where('department', $summary->college)
            ->get();

        if ($deans->isEmpty()) {
            // Also notify any general dean or super_admin
            $deans = User::whereIn('role', ['dean', 'super_admin'])->get();
        }

        foreach ($deans as $dean) {
            $dean->notify(new WorkflowStatusNotification(
                null,
                'Completed Researches Summary Form Prepared',
                "College Research Coordinator {$user->name} prepared the Completed Researches Summary Form for {$summary->college} for your review and formal noting.",
                'bi-file-earmark-ruled-fill',
                'text-warning'
            ));
        }

        return redirect()->route('completed_researches_summary.show', $summary->id)
            ->with('success', "Completed Researches Summary Form for {$summary->college} prepared successfully and transmitted to the College Dean for noting.");
    }

    /**
     * Display the Completed Researches Summary Form preview.
     */
    public function show($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'dean', 'admin', 'super_admin', 'vprei', 'president', 'staff', 'recording_staff'])) {
            abort(403, 'Unauthorized.');
        }

        $summary = CompletedResearchSummary::with(['preparer', 'noter'])->findOrFail($id);
        $items = $summary->items ?? [];

        return view('completed_researches_summary.show', compact('summary', 'items'));
    }

    /**
     * College Dean formally notes the Completed Researches Summary Form.
     */
    public function note(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['dean', 'super_admin'])) {
            abort(403, 'Only the College Dean can officially note this Completed Researches Summary Form.');
        }

        $summary = CompletedResearchSummary::with('preparer')->findOrFail($id);

        // Verify college match unless super_admin
        if ($user->role === 'dean' && !empty($user->department) && $user->department !== $summary->college) {
            abort(403, "You can only note summary forms belonging to your assigned college ({$user->department}).");
        }

        $summary->update([
            'noted_by' => $user->id,
            'noted_at' => now(),
            'status' => 'noted',
        ]);

        // Notify the College Research Coordinator (preparer)
        if ($summary->preparer) {
            $summary->preparer->notify(new WorkflowStatusNotification(
                null,
                'Completed Researches Summary Form Noted',
                "College Dean {$user->name} has officially noted the Completed Researches Summary Form for {$summary->college} on " . now()->format('F d, Y') . '.',
                'bi-check2-all',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'You have officially noted the Completed Researches Summary Form.');
    }

    /**
     * Export the summary form to print-ready PDF matching University of Antique portrait layout.
     */
    public function exportPdf($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'dean', 'admin', 'super_admin', 'vprei', 'president', 'staff', 'recording_staff'])) {
            abort(403, 'Unauthorized.');
        }

        $summary = CompletedResearchSummary::with(['preparer', 'noter'])->findOrFail($id);
        $items = $summary->items ?? [];

        $logoBase64 = null;
        if (extension_loaded('gd') && function_exists('imagecreatefrompng') && file_exists(public_path('logo.png'))) {
            $logoBase64 = base64_encode(file_get_contents(public_path('logo.png')));
        }

        $pdf = Pdf::loadView('pdfs.completed_researches_summary', compact('summary', 'items', 'logoBase64'));
        $pdf->setPaper('A4', 'portrait');

        $cleanCollege = preg_replace('/[^A-Za-z0-9_-]/', '_', $summary->college);
        $fileName = 'Completed_Researches_Summary_' . $cleanCollege . '_' . $summary->id . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Delete an un-noted or draft summary form.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $summary = CompletedResearchSummary::findOrFail($id);
        $summary->delete();

        return redirect()->route('completed_researches_summary.index')
            ->with('success', 'Completed researches summary form removed successfully.');
    }
}
