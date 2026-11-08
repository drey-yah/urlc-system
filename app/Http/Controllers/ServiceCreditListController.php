<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceCreditList;
use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ServiceCreditListController extends Controller
{
    /**
     * Display a listing of Service Credit dossiers.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff', 'recording_staff'])) {
            abort(403, 'Unauthorized access to institutional research dossiers.');
        }

        $lists = ServiceCreditList::with(['preparer', 'approver'])
            ->latest()
            ->paginate(15);

        $pendingCount = ServiceCreditList::where('status', 'pending_approval')->count();
        $approvedCount = ServiceCreditList::where('status', 'approved')->count();

        return view('service_credits.index', compact('lists', 'pendingCount', 'approvedCount'));
    }

    /**
     * Show form for Research Director to prepare a new List of Researchers Qualified to Avail Service Credits.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Only the Director of the Research Unit can prepare the Service Credits qualification list.');
        }

        $college = $request->input('college');
        $campus = $request->input('campus');

        $query = ResearchProposal::with(['user', 'collaborators', 'terminalReport'])
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhereHas('terminalReport', function ($tq) {
                        $tq->whereIn('status', ['completed', 'submitted', 'evaluated']);
                    })
                    ->orWhere('current_phase', '>=', 2)
                    ->orWhereIn('status', ['final_approved', 'approved', 'ongoing']);
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

        return view('service_credits.create', compact('eligibleProposals', 'colleges', 'campuses', 'college', 'campus'));
    }

    /**
     * Store a newly prepared Service Credits list and transmit to VPREI for approval.
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
            'proposal_ids.required' => 'Please select at least one completed research proposal to qualify researchers for service credits.',
            'proposal_ids.min' => 'Please select at least one completed research proposal to qualify researchers for service credits.',
        ]);

        $selectedIds = array_values(array_unique($validated['proposal_ids']));
        $proposals = ResearchProposal::with(['user', 'collaborators', 'terminalReport'])
            ->whereIn('id', $selectedIds)
            ->get()
            ->keyBy('id');

        $compiledItems = [];
        $counter = 1;
        foreach ($selectedIds as $proposalId) {
            $p = $proposals->get($proposalId);
            if (!$p) continue;

            $custom = $request->input("items.{$proposalId}", []);

            // Researchers
            $lead = $p->user ? $p->user->name : 'N/A';
            $collabs = $p->collaborators ? $p->collaborators->pluck('name')->toArray() : [];
            $allResearchers = implode(', ', array_merge([$lead], $collabs));

            // College
            $collegeName = $p->college_affiliation ?? ($p->user ? $p->user->department : 'College of Arts and Sciences');

            // Date Completed
            $completionDate = date('F d, Y');
            if ($p->terminalReport && $p->terminalReport->updated_at) {
                $completionDate = $p->terminalReport->updated_at->format('F d, Y');
            } elseif ($p->status === 'completed' && $p->updated_at) {
                $completionDate = $p->updated_at->format('F d, Y');
            }

            // Service credits granted (default 15 Days)
            $serviceCredits = !empty($custom['service_credits']) ? $custom['service_credits'] : '15 Days';

            $compiledItems[] = [
                'no' => $counter++,
                'proposal_id' => $p->id,
                'researchers' => !empty($custom['researchers']) ? $custom['researchers'] : $allResearchers,
                'college' => !empty($custom['college']) ? $custom['college'] : $collegeName,
                'title' => !empty($custom['title']) ? $custom['title'] : $p->title,
                'date_completed' => !empty($custom['date_completed']) ? $custom['date_completed'] : $completionDate,
                'service_credits' => $serviceCredits,
                'has_terminal_report' => (bool) $p->terminalReport,
                'has_certificate' => (bool) ($p->terminalReport && $p->terminalReport->certificate_completion_file),
            ];
        }

        $list = ServiceCreditList::create([
            'title' => $validated['title'],
            'report_date' => $validated['report_date'],
            'fiscal_year' => $validated['fiscal_year'] ?? date('Y', strtotime($validated['report_date'])),
            'prepared_by' => $user->id,
            'status' => 'pending_approval',
            'proposal_ids' => $selectedIds,
            'items' => $compiledItems,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Notify VPREI users
        $vpreiUsers = User::whereIn('role', ['vprei', 'super_admin'])->get();
        foreach ($vpreiUsers as $vprei) {
            $vprei->notify(new WorkflowStatusNotification(
                null,
                'List of Researchers Qualified to Avail Service Credits Submitted',
                "Research Director {$user->name} prepared '{$list->title}' for your formal executive approval.",
                'bi-award-fill',
                'text-warning'
            ));
        }

        return redirect()->route('service_credits.show', $list->id)
            ->with('success', 'List of Researchers Qualified to Avail Service Credits prepared successfully and forwarded to the VPREI for approval.');
    }

    /**
     * Display the Service Credits dossier preview.
     */
    public function show($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff', 'recording_staff'])) {
            abort(403, 'Unauthorized.');
        }

        $list = ServiceCreditList::with(['preparer', 'approver'])->findOrFail($id);
        $items = $list->items ?? [];

        return view('service_credits.show', compact('list', 'items'));
    }

    /**
     * VP for Research, Extension, and Innovation formally approves the service credits list.
     */
    public function approve(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['vprei', 'super_admin'])) {
            abort(403, 'Only the VP for Research, Extension, and Innovation can approve the Service Credits qualification list.');
        }

        $list = ServiceCreditList::with(['preparer'])->findOrFail($id);

        $list->update([
            'approved_by' => $user->id,
            'approved_at' => now(),
            'status' => 'approved',
        ]);

        // Notify the Director (preparer)
        if ($list->preparer) {
            $list->preparer->notify(new WorkflowStatusNotification(
                null,
                'Service Credits List Approved by VPREI',
                "VP for REI {$user->name} has officially approved '{$list->title}' on " . now()->format('F d, Y') . '.',
                'bi-patch-check-fill',
                'text-success'
            ));
        }

        // Notify each qualified researcher in the list
        if (!empty($list->proposal_ids)) {
            $proposals = ResearchProposal::with('user')->whereIn('id', $list->proposal_ids)->get();
            foreach ($proposals as $prop) {
                if ($prop->user) {
                    $prop->user->notify(new WorkflowStatusNotification(
                        $prop,
                        '🎖️ Service Credits Qualification Approved!',
                        "Congratulations! Your research '{$prop->title}' was approved by the VPREI for service credits.",
                        'bi-award-fill',
                        'text-success'
                    ));
                }
            }
        }

        return redirect()->back()->with('success', 'You have officially approved the List of Researchers Qualified to Avail Service Credits.');
    }

    /**
     * Export the dossier to print-ready PDF matching University of Antique portrait layout.
     */
    public function exportPdf($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin', 'vprei', 'president', 'staff', 'recording_staff'])) {
            abort(403, 'Unauthorized.');
        }

        $list = ServiceCreditList::with(['preparer', 'approver'])->findOrFail($id);
        $items = $list->items ?? [];

        $logoBase64 = null;
        if (file_exists(public_path('logo.png'))) {
            $logoBase64 = base64_encode(file_get_contents(public_path('logo.png')));
        }

        $pdf = Pdf::loadView('pdfs.service_credits_list', compact('list', 'items', 'logoBase64'));
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'List_of_Researchers_Qualified_for_Service_Credits_' . $list->id . '_' . date('Ymd') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Delete an unapproved or draft list.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $list = ServiceCreditList::findOrFail($id);
        $list->delete();

        return redirect()->route('service_credits.index')
            ->with('success', 'Service credit list removed successfully.');
    }
}
