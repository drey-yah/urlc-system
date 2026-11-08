<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResearchAccomplishmentReport;
use App\Models\ResearchProposal;
use App\Models\ResearchPresentation;
use App\Models\ResearchPublication;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ResearchAccomplishmentReportController extends Controller
{
    /**
     * Display a listing of Research Accomplishment Reports.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'dean', 'admin', 'super_admin', 'vprei', 'president', 'staff', 'recording_staff'])) {
            abort(403, 'Unauthorized access to institutional accomplishment reports.');
        }

        $query = ResearchAccomplishmentReport::with(['preparer', 'noter', 'receiver'])->latest();

        // If Coordinator or Dean, scope by their assigned college/department
        if (in_array($user->role, ['coordinator', 'dean']) && !empty($user->department)) {
            $query->where('college', $user->department);
        }

        $reports = $query->paginate(15);

        $pendingDeanQuery = ResearchAccomplishmentReport::where('status', 'pending_dean_noting');
        $submittedDirectorQuery = ResearchAccomplishmentReport::where('status', 'submitted_to_director');
        $acknowledgedQuery = ResearchAccomplishmentReport::where('status', 'acknowledged');

        if (in_array($user->role, ['coordinator', 'dean']) && !empty($user->department)) {
            $pendingDeanQuery->where('college', $user->department);
            $submittedDirectorQuery->where('college', $user->department);
            $acknowledgedQuery->where('college', $user->department);
        }

        $pendingDeanCount = $pendingDeanQuery->count();
        $submittedDirectorCount = $submittedDirectorQuery->count();
        $acknowledgedCount = $acknowledgedQuery->count();

        return view('accomplishment_reports.index', compact('reports', 'pendingDeanCount', 'submittedDirectorCount', 'acknowledgedCount'));
    }

    /**
     * Show form for College Research Coordinator to prepare an Accomplishment Report.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'admin', 'super_admin'])) {
            abort(403, 'Only College Research Coordinators or Administrators can prepare the Research Accomplishment Report.');
        }

        $coordinatorCollege = $user->department ?? 'College of Teacher Education';
        $college = $request->input('college', $coordinatorCollege);

        // Fetch ongoing proposals for Section C
        $ongoingProposals = ResearchProposal::with(['user', 'collaborators'])
            ->where(function ($q) use ($college) {
                $q->where('college_affiliation', $college)
                    ->orWhereHas('user', function ($uq) use ($college) {
                        $uq->where('department', $college);
                    });
            })
            ->where(function ($q) {
                $q->whereIn('status', ['ongoing', 'approved', 'in_house_reviewed', 'final_approved'])
                    ->orWhere('current_phase', 2);
            })
            ->latest()
            ->get();

        // Fetch completed proposals for Section D
        $completedProposals = ResearchProposal::with(['user', 'collaborators', 'terminalReport'])
            ->where(function ($q) use ($college) {
                $q->where('college_affiliation', $college)
                    ->orWhereHas('user', function ($uq) use ($college) {
                        $uq->where('department', $college);
                    });
            })
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhereHas('terminalReport');
            })
            ->latest()
            ->get();

        // Fetch presentations for Section E
        $presentations = ResearchPresentation::with(['proposal.user', 'user'])
            ->whereHas('proposal', function ($q) use ($college) {
                $q->where('college_affiliation', $college)
                    ->orWhereHas('user', function ($uq) use ($college) {
                        $uq->where('department', $college);
                    });
            })
            ->latest()
            ->get();

        // Fetch publications for Section F
        $publications = ResearchPublication::with(['proposal.user', 'user'])
            ->whereHas('proposal', function ($q) use ($college) {
                $q->where('college_affiliation', $college)
                    ->orWhereHas('user', function ($uq) use ($college) {
                        $uq->where('department', $college);
                    });
            })
            ->latest()
            ->get();

        // College list for dropdown
        $colleges = User::distinct()->whereNotNull('department')->pluck('department')->filter()->values();
        if (!$colleges->contains($coordinatorCollege)) {
            $colleges->push($coordinatorCollege);
        }

        // Section A indicators defaults
        $indicators = [
            1 => 'Number of research outputs completed within the year',
            2 => 'Percentage of research outputs published in internationally-refereed or CHED-recognized journals within the year',
            3 => 'Number of research outputs in the last 3 years utilized by the industry or by other beneficiary',
            4 => 'No. of Research outputs presented within the year',
            5 => 'No. of On-going Researches',
            6 => 'No. of Research Proposals Reviewed for Funding',
            7 => 'No. of Researchers Involved in Funded Researches',
            8 => 'No. of Researchers granted incentives',
        ];

        // Calculated accomplishment stats
        $counts = [
            1 => $completedProposals->count(),
            2 => $publications->count() > 0 ? $publications->count() . ' (' . min(100, round(($publications->count() / max(1, $completedProposals->count())) * 100)) . '%)' : '0',
            3 => 0,
            4 => $presentations->count(),
            5 => $ongoingProposals->count(),
            6 => ResearchProposal::where('college_affiliation', $college)->whereIn('status', ['approved', 'funds_certified', 'in_house_reviewed'])->count(),
            7 => ResearchProposal::where('college_affiliation', $college)->whereNotNull('total_budget')->distinct('user_id')->count('user_id'),
            8 => 0,
        ];

        return view('accomplishment_reports.create', compact(
            'college', 'coordinatorCollege', 'colleges', 'indicators', 'counts',
            'ongoingProposals', 'completedProposals', 'presentations', 'publications'
        ));
    }

    /**
     * Store a newly prepared Accomplishment Report and notify College Dean.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'college' => 'required|string|max:255',
            'quarter' => 'required|string|max:100',
            'school_year' => 'required|string|max:100',
            'fiscal_year' => 'nullable|integer|min:2020|max:2050',
            'report_date' => 'required|date',
            'section_a' => 'nullable|array',
            'section_b' => 'nullable|array',
            'section_c' => 'nullable|array',
            'section_d' => 'nullable|array',
            'section_e' => 'nullable|array',
            'section_f' => 'nullable|array',
            'section_g' => 'nullable|array',
            'section_h' => 'nullable|array',
            'remarks' => 'nullable|string|max:3000',
        ]);

        $report = ResearchAccomplishmentReport::create([
            'form_code' => 'RESU - FM-004',
            'revision_no' => 'Rev. 3/08-11-25',
            'college' => $validated['college'],
            'quarter' => $validated['quarter'],
            'school_year' => $validated['school_year'],
            'fiscal_year' => $validated['fiscal_year'] ?? date('Y', strtotime($validated['report_date'])),
            'report_date' => $validated['report_date'],
            'prepared_by' => $user->id,
            'status' => 'pending_dean_noting',
            'section_a' => $request->input('section_a', []),
            'section_b' => $request->input('section_b', []),
            'section_c' => $request->input('section_c', []),
            'section_d' => $request->input('section_d', []),
            'section_e' => $request->input('section_e', []),
            'section_f' => $request->input('section_f', []),
            'section_g' => $request->input('section_g', []),
            'section_h' => $request->input('section_h', []),
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Notify College Dean for this college
        $deans = User::where('role', 'dean')
            ->where('department', $report->college)
            ->get();

        if ($deans->isEmpty()) {
            $deans = User::whereIn('role', ['dean', 'super_admin'])->get();
        }

        foreach ($deans as $dean) {
            $dean->notify(new WorkflowStatusNotification(
                null,
                'Research Accomplishment Report Prepared',
                "College Research Coordinator {$user->name} prepared the {$report->quarter} Accomplishment Report for {$report->college} for your review and formal noting.",
                'bi-file-earmark-ruled-fill',
                'text-warning'
            ));
        }

        return redirect()->route('accomplishment_reports.show', $report->id)
            ->with('success', "Research Accomplishment Report for {$report->college} ({$report->quarter}, {$report->school_year}) prepared successfully and transmitted to College Dean for noting.");
    }

    /**
     * Display the 4-page Accomplishment Report preview.
     */
    public function show($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'dean', 'admin', 'super_admin', 'vprei', 'president', 'staff', 'recording_staff'])) {
            abort(403, 'Unauthorized.');
        }

        $report = ResearchAccomplishmentReport::with(['preparer', 'noter', 'receiver'])->findOrFail($id);

        return view('accomplishment_reports.show', compact('report'));
    }

    /**
     * College Dean notes the report and formally submits it to the Research Director.
     */
    public function noteAndSubmit(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['dean', 'super_admin'])) {
            abort(403, 'Only the College Dean can officially note and submit this report to the Research Director.');
        }

        $report = ResearchAccomplishmentReport::with('preparer')->findOrFail($id);

        // Verify college match unless super_admin
        if ($user->role === 'dean' && !empty($user->department) && $user->department !== $report->college) {
            abort(403, "You can only note accomplishment reports belonging to your assigned college ({$user->department}).");
        }

        $report->update([
            'noted_by' => $user->id,
            'noted_at' => now(),
            'status' => 'submitted_to_director',
        ]);

        // Notify Research Directors (Admin users)
        $directors = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($directors as $director) {
            $director->notify(new WorkflowStatusNotification(
                null,
                'Research Accomplishment Report Submitted',
                "Dean {$user->name} has officially noted and submitted the {$report->quarter} Accomplishment Report for {$report->college} to the Research Unit.",
                'bi-send-check-fill',
                'text-primary'
            ));
        }

        // Notify College Coordinator (preparer)
        if ($report->preparer) {
            $report->preparer->notify(new WorkflowStatusNotification(
                null,
                'Accomplishment Report Noted & Submitted',
                "Dean {$user->name} has noted and submitted your {$report->quarter} Accomplishment Report to the Research Director.",
                'bi-check2-all',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'You have officially noted the Accomplishment Report and submitted it to the Director of the Research Unit.');
    }

    /**
     * Research Director officially receives / acknowledges the submitted report.
     */
    public function acknowledge(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Only the Director of the Research Unit can acknowledge receipt of this report.');
        }

        $report = ResearchAccomplishmentReport::with(['preparer', 'noter'])->findOrFail($id);

        $report->update([
            'received_by' => $user->id,
            'received_at' => now(),
            'status' => 'acknowledged',
        ]);

        // Notify College Dean
        if ($report->noter) {
            $report->noter->notify(new WorkflowStatusNotification(
                null,
                'Accomplishment Report Received by Research Director',
                "Research Director {$user->name} has officially acknowledged and received the {$report->quarter} Accomplishment Report for {$report->college}.",
                'bi-check2-circle',
                'text-success'
            ));
        }

        // Notify College Coordinator
        if ($report->preparer) {
            $report->preparer->notify(new WorkflowStatusNotification(
                null,
                'Accomplishment Report Received by Research Director',
                "Research Director {$user->name} has officially acknowledged and received your {$report->quarter} Accomplishment Report.",
                'bi-check2-circle',
                'text-success'
            ));
        }

        return redirect()->back()->with('success', 'You have officially acknowledged and received the Research Accomplishment Report.');
    }

    /**
     * Export the 4-page report to print-ready PDF in Landscape matching RESU-FM-004 layout.
     */
    public function exportPdf($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'dean', 'admin', 'super_admin', 'vprei', 'president', 'staff', 'recording_staff'])) {
            abort(403, 'Unauthorized.');
        }

        $report = ResearchAccomplishmentReport::with(['preparer', 'noter', 'receiver'])->findOrFail($id);

        $logoBase64 = null;
        if (file_exists(public_path('logo.png'))) {
            $logoBase64 = base64_encode(file_get_contents(public_path('logo.png')));
        }

        $pdf = Pdf::loadView('pdfs.research_accomplishment_report', compact('report', 'logoBase64'));
        $pdf->setPaper('A4', 'landscape');

        $cleanCollege = preg_replace('/[^A-Za-z0-9_-]/', '_', $report->college);
        $cleanQuarter = preg_replace('/[^A-Za-z0-9_-]/', '_', $report->quarter);
        $fileName = 'Accomplishment_Report_' . $cleanCollege . '_' . $cleanQuarter . '_' . $report->id . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Delete an un-noted or draft report.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['coordinator', 'admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $report = ResearchAccomplishmentReport::findOrFail($id);
        $report->delete();

        return redirect()->route('accomplishment_reports.index')
            ->with('success', 'Research Accomplishment Report deleted successfully.');
    }
}
