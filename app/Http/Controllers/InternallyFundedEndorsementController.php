<?php

namespace App\Http\Controllers;

use App\Models\InternallyFundedEndorsement;
use App\Models\ResearchProposal;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InternallyFundedEndorsementController extends Controller
{
    /**
     * Display a listing of Internally-Funded Proposal Endorsement dossiers.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$this->isAuthorizedDossierUser($user)) {
            abort(403, 'Unauthorized access to institutional research dossiers.');
        }

        $endorsements = InternallyFundedEndorsement::with(['preparer', 'vpreiNoter', 'vpafNoter', 'presidentApprover'])
            ->latest('report_date')
            ->latest('id')
            ->paginate(15);

        $stats = [
            'total' => InternallyFundedEndorsement::count(),
            'pending_recommending' => InternallyFundedEndorsement::where('status', 'pending_recommending')->count(),
            'pending_president' => InternallyFundedEndorsement::where('status', 'pending_president_approval')->count(),
            'approved' => InternallyFundedEndorsement::where('status', 'approved')->count(),
        ];

        $isDirector = $user->isDirector() || $user->isAdmin();
        $isVprei = $user->isVprei() || $user->hasRole('vprei');
        $isVpaf = $user->isVpaf() || $user->hasRole('vpaf');
        $isPresident = $user->isPresident() || $user->hasRole('president');

        return view('internally_funded_endorsements.index', compact('endorsements', 'stats', 'isDirector', 'isVprei', 'isVpaf', 'isPresident'));
    }

    /**
     * Show form for Director to prepare a new Internally-Funded Research Proposal Endorsement form.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user->isDirector() && !$user->isAdmin()) {
            abort(403, 'Only the Research Director or System Admin can prepare the Internally-Funded Proposal Endorsement Form.');
        }

        // Available proposals (internally funded or approved proposals)
        $proposals = ResearchProposal::with(['user', 'collaborators', 'budgetItems'])
            ->where(function ($q) {
                $q->where('funding_type', 'internally_funded')
                  ->orWhereNull('funding_type')
                  ->orWhereIn('status', [
                      'approved', 'final_approved', 'accepted_for_in_house_review', 
                      'under_review', 'funds_certified', 'ongoing'
                  ]);
            })
            ->latest()
            ->get();

        return view('internally_funded_endorsements.create', compact('proposals'));
    }

    /**
     * Store a newly created endorsement form.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isDirector() && !$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'fiscal_year' => 'nullable|integer',
            'report_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.project_title' => 'required|string',
            'items.*.proponents' => 'required|string',
            'items.*.college' => 'nullable|string',
            'items.*.budget' => 'required|numeric|min:0',
            'proposal_ids' => 'nullable|array',
            'remarks' => 'nullable|string',
        ]);

        $items = array_values($validated['items']);
        $totalBudget = collect($items)->sum(function ($item) {
            return (float) ($item['budget'] ?? 0);
        });

        $endorsement = InternallyFundedEndorsement::create([
            'title' => $validated['title'],
            'fiscal_year' => $validated['fiscal_year'] ?? date('Y'),
            'report_date' => $validated['report_date'],
            'status' => 'pending_recommending',
            'proposal_ids' => $validated['proposal_ids'] ?? [],
            'items' => $items,
            'total_budget' => $totalBudget,
            'remarks' => $validated['remarks'] ?? null,
            'prepared_by' => $user->id,
            'prepared_at' => now(),
            'vprei_status' => 'pending',
            'vpaf_status' => 'pending',
            'president_status' => 'pending',
        ]);

        // Notify VPREI and VPAF users
        $recommenders = User::where(function ($q) {
            $q->where('role', 'vprei')
              ->orWhere('role', 'vpaf')
              ->orWhere('additional_roles', 'like', '%"vprei"%')
              ->orWhere('additional_roles', 'like', '%"vpaf"%');
        })->get();

        foreach ($recommenders as $rec) {
            $roleLabel = ($rec->role === 'vprei' || $rec->hasRole('vprei')) ? 'VPREI' : 'VPAF';
            $this->safeDatabaseNotify(
                $rec,
                "New Endorsement Awaiting Recommending Approval",
                "Director {$user->name} has prepared '{$endorsement->title}' (Total: ₱" . number_format($endorsement->total_budget, 2) . ") awaiting your recommendation as {$roleLabel}.",
                'bi-file-earmark-ruled-fill',
                route('internally_funded_endorsements.show', $endorsement->id)
            );
        }

        return redirect()->route('internally_funded_endorsements.show', $endorsement->id)
            ->with('success', 'Internally-Funded Research Proposal Endorsement Form prepared and transmitted to VPREI and VPAF for recommending approval!');
    }

    /**
     * Display the specified endorsement form dossier.
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$this->isAuthorizedDossierUser($user)) {
            abort(403, 'Unauthorized.');
        }

        $endorsement = InternallyFundedEndorsement::with(['preparer', 'vpreiNoter', 'vpafNoter', 'presidentApprover'])
            ->findOrFail($id);

        $isDirector = $user->isDirector() || $user->isAdmin();
        $isVprei = $user->isVprei() || $user->hasRole('vprei') || $user->isSuperAdmin();
        $isVpaf = $user->isVpaf() || $user->hasRole('vpaf') || $user->isSuperAdmin();
        $isPresident = $user->isPresident() || $user->hasRole('president') || $user->isSuperAdmin();

        return view('internally_funded_endorsements.show', compact('endorsement', 'isDirector', 'isVprei', 'isVpaf', 'isPresident'));
    }

    /**
     * VPREI Recommending Approval Action.
     */
    public function recommendVprei(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isVprei() && !$user->hasRole('vprei') && !$user->isSuperAdmin()) {
            abort(403, 'Only the Vice President for Research, Extension & Innovation (VPREI) can perform this recommendation.');
        }

        $endorsement = InternallyFundedEndorsement::findOrFail($id);

        $endorsement->update([
            'vprei_by' => $user->id,
            'vprei_status' => 'recommended',
            'vprei_signed_at' => now(),
            'vprei_remarks' => $request->input('remarks'),
        ]);

        // If VPAF has already recommended, advance to President approval
        if ($endorsement->isVpafRecommended()) {
            $endorsement->update(['status' => 'pending_president_approval']);
            $this->notifyPresident($endorsement);
        }

        // Notify Director
        if ($endorsement->preparer) {
            $this->safeDatabaseNotify(
                $endorsement->preparer,
                "VPREI Recommended Endorsement Form",
                "VPREI {$user->name} has recommended approval for '{$endorsement->title}'.",
                'bi-check-circle-fill',
                route('internally_funded_endorsements.show', $endorsement->id)
            );
        }

        return redirect()->back()->with('success', 'You have officially recommended approval as VP for REI!');
    }

    /**
     * VPAF Recommending Approval Action.
     */
    public function recommendVpaf(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isVpaf() && !$user->hasRole('vpaf') && !$user->isSuperAdmin()) {
            abort(403, 'Only the Vice President for Academic and Finance (VPAF) can perform this recommendation.');
        }

        $endorsement = InternallyFundedEndorsement::findOrFail($id);

        $endorsement->update([
            'vpaf_by' => $user->id,
            'vpaf_status' => 'recommended',
            'vpaf_signed_at' => now(),
            'vpaf_remarks' => $request->input('remarks'),
        ]);

        // If VPREI has already recommended, advance to President approval
        if ($endorsement->isVpreiRecommended()) {
            $endorsement->update(['status' => 'pending_president_approval']);
            $this->notifyPresident($endorsement);
        }

        // Notify Director
        if ($endorsement->preparer) {
            $this->safeDatabaseNotify(
                $endorsement->preparer,
                "VPAF Recommended Endorsement Form",
                "VPAF {$user->name} has recommended approval for '{$endorsement->title}'.",
                'bi-check-circle-fill',
                route('internally_funded_endorsements.show', $endorsement->id)
            );
        }

        return redirect()->back()->with('success', 'You have officially recommended approval as VPAF!');
    }

    /**
     * President Final Approval Action.
     */
    public function approvePresident(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isPresident() && !$user->hasRole('president') && !$user->isSuperAdmin()) {
            abort(403, 'Only the University President can give final approval.');
        }

        $endorsement = InternallyFundedEndorsement::findOrFail($id);

        $endorsement->update([
            'president_by' => $user->id,
            'president_status' => 'approved',
            'president_signed_at' => now(),
            'president_remarks' => $request->input('remarks'),
            'status' => 'approved',
        ]);

        // Notify Director, VPREI, VPAF
        $officials = User::where(function ($q) use ($endorsement) {
            $q->where('id', $endorsement->prepared_by)
              ->orWhere('role', 'vprei')
              ->orWhere('role', 'vpaf')
              ->orWhere('additional_roles', 'like', '%"vprei"%')
              ->orWhere('additional_roles', 'like', '%"vpaf"%');
        })->get();

        foreach ($officials as $official) {
            $this->safeDatabaseNotify(
                $official,
                "🎉 President Approved: {$endorsement->title}",
                "University President {$user->name} has officially APPROVED the Internally-Funded Research Proposal Endorsement Form (Total: ₱" . number_format($endorsement->total_budget, 2) . ")!",
                'bi-award-fill',
                route('internally_funded_endorsements.show', $endorsement->id)
            );
        }

        // Notify proposal proponents
        if (!empty($endorsement->proposal_ids)) {
            $proposals = ResearchProposal::with('user')->whereIn('id', $endorsement->proposal_ids)->get();
            foreach ($proposals as $prop) {
                if ($prop->user) {
                    $this->safeDatabaseNotify(
                        $prop->user,
                        "🎉 Endorsement Approved: {$prop->title}",
                        "Your internally-funded research proposal has been officially APPROVED by University President {$user->name} via the Institutional Endorsement Form!",
                        'bi-check2-circle',
                        route('proposal.show', $prop->id)
                    );
                }
            }
        }

        return redirect()->back()->with('success', 'Endorsement Form officially APPROVED by the University President!');
    }

    /**
     * Return dossier with revisions requested.
     */
    public function returnDossier(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isPresident() && !$user->isVprei() && !$user->isVpaf() && !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate(['remarks' => 'required|string|max:1000']);
        $endorsement = InternallyFundedEndorsement::findOrFail($id);

        $roleLabel = $user->isPresident() ? 'President' : ($user->isVprei() ? 'VPREI' : 'VPAF');

        $endorsement->update([
            'status' => 'returned',
            'remarks' => "Returned by {$roleLabel} ({$user->name}): " . $request->remarks,
        ]);

        if ($endorsement->preparer) {
            $this->safeDatabaseNotify(
                $endorsement->preparer,
                "Endorsement Form Returned for Revision",
                "{$roleLabel} ({$user->name}) returned '{$endorsement->title}': {$request->remarks}",
                'bi-arrow-return-left',
                route('internally_funded_endorsements.show', $endorsement->id)
            );
        }

        return redirect()->back()->with('success', "Endorsement dossier returned to Director with revision remarks.");
    }

    /**
     * Export official printable PDF.
     */
    public function exportPdf($id)
    {
        $user = Auth::user();
        if (!$this->isAuthorizedDossierUser($user)) {
            abort(403, 'Unauthorized.');
        }

        $endorsement = InternallyFundedEndorsement::with(['preparer', 'vpreiNoter', 'vpafNoter', 'presidentApprover'])
            ->findOrFail($id);

        $items = $endorsement->items ?? [];

        $logoBase64 = null;
        if (extension_loaded('gd') && function_exists('imagecreatefrompng') && file_exists(public_path('logo.png'))) {
            $logoBase64 = base64_encode(file_get_contents(public_path('logo.png')));
        }

        $pdf = Pdf::loadView('pdfs.internally_funded_endorsement', compact('endorsement', 'items', 'logoBase64'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'INTERNALLY-FUNDED-RESEARCH-ENDORSEMENT-' . ($endorsement->fiscal_year ?? date('Y')) . '-' . $endorsement->id . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Delete an endorsement form (Director / Admin only).
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isDirector() && !$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $endorsement = InternallyFundedEndorsement::findOrFail($id);
        $endorsement->delete();

        return redirect()->route('internally_funded_endorsements.index')
            ->with('success', 'Endorsement form deleted successfully.');
    }

    /**
     * Helper to verify if user can view institutional research dossiers.
     */
    protected function isAuthorizedDossierUser(?User $user): bool
    {
        if (!$user) return false;
        return in_array($user->role, [
            'admin', 'super_admin', 'vprei', 'vpaf', 'president', 'staff', 'recording_staff', 'budget_officer', 'sao_finance'
        ]) || $user->isDirector() || $user->hasRole('vprei') || $user->hasRole('vpaf') || $user->hasRole('president');
    }

    /**
     * Notify University President when both recommenders have approved.
     */
    protected function notifyPresident(InternallyFundedEndorsement $endorsement): void
    {
        $presidents = User::where(function ($q) {
            $q->where('role', 'president')
              ->orWhere('additional_roles', 'like', '%"president"%');
        })->get();

        foreach ($presidents as $pres) {
            $this->safeDatabaseNotify(
                $pres,
                "Action Required: Endorsement Form Recommended for Final Approval",
                "Both the VPREI and VPAF have recommended approval for '{$endorsement->title}' (Total: ₱" . number_format($endorsement->total_budget, 2) . "). Your executive approval is requested.",
                'bi-file-earmark-check-fill',
                route('internally_funded_endorsements.show', $endorsement->id)
            );
        }
    }

    /**
     * Safe database notification helper that never throws or blocks execution.
     */
    protected function safeDatabaseNotify(User $user, string $title, string $message, string $icon, string $url): void
    {
        try {
            $user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'type' => 'App\Notifications\GenericSystemNotification',
                'data' => [
                    'title' => $title,
                    'message' => $message,
                    'icon' => $icon,
                    'target_url' => $url,
                ],
                'read_at' => null,
            ]);
        } catch (\Throwable $e) {
            Log::warning("Could not write database notification: " . $e->getMessage());
        }
    }
}
