<x-app-layout>
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-link text-muted p-0 text-decoration-none d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <!-- Main Header Banner -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="gradient-banner p-4 p-lg-5">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="display-6 fw-bold mb-3">{{ $proposal->title }}</h1>
                    <div class="d-flex align-items-center gap-4 opacity-90">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-person"></i> {{ $proposal->user->name }}
                        </span>
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-tag"></i> {{ $proposal->proposal_code ?? 'No Tag Yet' }}
                        </span>
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar3"></i> Submitted: {{ $proposal->created_at->format('Y-m-d') }}
                        </span>
                    </div>
                </div>
                @php
                    $statusColor = match($proposal->status) {
                        'approved', 'final_approved' => '#10B981',
                        'pending', 'pending_coordinator_endorsement' => '#F59E0B',
                        'endorsed_by_coordinator', 'revision_required' => '#3B82F6',
                        'rejected', 'final_rejected' => '#EF4444',
                        'funds_certified' => '#8B5CF6',
                        'final_copy_noted_by_dean' => '#6366F1',
                        default => '#6B7280'
                    };
                @endphp
                <span class="badge py-2 px-4" style="background-color: rgba(255,255,255,0.2); backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.3); color: white;">
                    {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4">
                <small class="text-muted fw-bold text-uppercase mb-2 d-block">Phase</small>
                <h2 class="h3 fw-bold mb-0">Phase {{ $proposal->current_phase }}</h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4">
                <small class="text-muted fw-bold text-uppercase mb-2 d-block">Status</small>
                <h2 class="h3 fw-bold mb-0 text-capitalize">{{ str_replace('_', ' ', $proposal->status) }}</h2>
            </div>
        </div>
    </div>

    <!-- Workflow Actions Card -->
    @php
        $showActionsCard = false;
        $userRole = auth()->user()->role;
        $userId = auth()->id();
        
        if ($userRole === 'coordinator' && ($proposal->status === 'pending_coordinator_endorsement' || $proposal->status === 'noted_by_dean')) {
            $showActionsCard = true;
        } elseif ($userRole === 'dean' && ($proposal->status === 'pending_dean_noting' || $proposal->status === 'final_copy_submitted')) {
            $showActionsCard = true;
        } elseif ($userRole === 'staff' && $proposal->status === 'submitted_to_research_unit') {
            $showActionsCard = true;
        } elseif (($userRole === 'admin' || auth()->user()->isSuperAdmin()) && ($proposal->status === 'pending_director_review' || $proposal->status === 'funds_certified')) {
            $showActionsCard = true;
        } elseif ($userRole === 'vprei' && $proposal->status === 'endorsed_to_vprei') {
            $showActionsCard = true;
        } elseif ($userRole === 'budget_officer' && $proposal->status === 'final_copy_noted_by_dean') {
            $showActionsCard = true;
        } elseif ($userId === $proposal->user_id && $proposal->status === 'approved') {
            $showActionsCard = true;
        }
    @endphp

    @if($showActionsCard)
    <div class="card border-0 shadow-sm mb-4 border-start border-primary border-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="h5 fw-bold d-flex align-items-center gap-3 mb-3 text-primary">
                <i class="bi bi-gear-wide-connected fs-4"></i> Workflow Actions Required
            </h3>
            <p class="text-muted small mb-4">Please review the proposal details below and perform the required action for the next step in the workflow.</p>

            <!-- Coordinator Actions -->
            @if($userRole === 'coordinator')
                @if($proposal->status === 'pending_coordinator_endorsement')
                    <form action="{{ route('coordinator.proposals.endorse', $proposal->id) }}" method="POST" class="d-flex gap-3">
                        @csrf
                        <button type="submit" name="action" value="return" class="btn btn-outline-danger px-4 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Are you sure you want to return this proposal for revision?');">
                            <i class="bi bi-arrow-return-left fs-6"></i> Return for Revision
                        </button>
                        <button type="submit" name="action" value="endorse" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Are you sure you want to endorse this proposal?');">
                            <i class="bi bi-check2-circle fs-6"></i> Endorse & Forward to Dean
                        </button>
                    </form>
                @elseif($proposal->status === 'noted_by_dean')
                    <form action="{{ route('coordinator.proposals.submitToUnit', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Submit this noted proposal list to the Research Unit support staff?');">
                            <i class="bi bi-send-fill fs-6"></i> Submit to Research Unit
                        </button>
                    </form>
                @endif
            @endif

            <!-- Dean Actions -->
            @if($userRole === 'dean')
                @if($proposal->status === 'pending_dean_noting')
                    <form action="{{ route('dean.noteEndorsement', $proposal->id) }}" method="POST" class="d-flex gap-3">
                        @csrf
                        <button type="submit" name="action" value="return" class="btn btn-outline-danger px-4 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Are you sure you want to return this endorsement?');">
                            <i class="bi bi-arrow-return-left fs-6"></i> Return to Coordinator
                        </button>
                        <button type="submit" name="action" value="note" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Are you sure you want to note this endorsement?');">
                            <i class="bi bi-journal-check fs-6"></i> Note Endorsement
                        </button>
                    </form>
                @elseif($proposal->status === 'final_copy_submitted')
                    <form action="{{ route('dean.noteFinal', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Note the final copy submitted for this proposal?');">
                            <i class="bi bi-check-circle-fill fs-6"></i> Note Final Copy
                        </button>
                    </form>
                @endif
            @endif

            <!-- Support Staff Actions -->
            @if($userRole === 'staff' && $proposal->status === 'submitted_to_research_unit')
                <form action="{{ route('staff.proposals.forward', $proposal->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Forward this proposal to the Research Director for review?');">
                        <i class="bi bi-box-arrow-in-right fs-6"></i> Forward to Research Director
                    </button>
                </form>
            @endif

            <!-- Research Director (Admin) Actions -->
            @if($userRole === 'admin' || auth()->user()->isSuperAdmin())
                @if($proposal->status === 'pending_director_review')
                    <form action="{{ route('admin.proposals.acceptInHouse', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Accept this proposal and issue the In-House Review Acceptance Form?');">
                            <i class="bi bi-card-checklist fs-6"></i> Accept for In-House Review
                        </button>
                    </form>
                @elseif($proposal->status === 'funds_certified')
                    <form action="{{ route('admin.proposals.endorseVprei', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Endorse this revised proposal to the VPREI for final approval?');">
                            <i class="bi bi-award-fill fs-6"></i> Endorse to VPREI
                        </button>
                    </form>
                @endif
            @endif

            <!-- Budget Officer Actions -->
            @if($userRole === 'budget_officer' && $proposal->status === 'final_copy_noted_by_dean')
                <form action="{{ route('budget.certify', $proposal->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Certify this research proposal for availability of funds?');">
                        <i class="bi bi-cash-stack fs-6"></i> Certify Availability of Funds
                    </button>
                </form>
            @endif

            <!-- VPREI Actions -->
            @if($userRole === 'vprei' && $proposal->status === 'endorsed_to_vprei')
                <form action="{{ route('vprei.approve', $proposal->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Approve this proposal and issue the Notice to Proceed?');">
                        <i class="bi bi-check-circle-fill fs-6"></i> Grant Final Approval & Issue NTP
                    </button>
                </form>
            @endif

            <!-- Researcher Submit Final Copy Action -->
            @if($userId === $proposal->user_id && $proposal->status === 'approved')
                <form action="{{ route('proposal.submitFinalCopy', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload Final Copy of Research Proposal (PDF)</label>
                        <p class="text-muted small">Please submit the final version of your research proposal incorporating all reviewer suggestions. This is required before the Dean and Director can endorse it to VPREI.</p>
                        <input type="file" name="final_copy" class="form-control" accept=".pdf" required>
                    </div>
                    <button type="submit" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-cloud-upload-fill fs-6"></i> Submit Final Copy
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endif

    {{-- Collaborators section: visible to staff/admin roles only --}}
    @if(!in_array(auth()->user()->role, ['researcher']))
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="h5 fw-bold d-flex align-items-center gap-3 mb-4">
                <i class="bi bi-people text-info"></i> Collaborators
            </h3>

            @if($proposal->collaborators->isNotEmpty())
                <div class="d-flex flex-wrap gap-3">
                    @foreach($proposal->collaborators as $collab)
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 border bg-light" style="min-width: 220px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                             style="width: 42px; height: 42px; font-size: 1rem; background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                            {{ strtoupper(substr($collab->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-semibold text-dark small">{{ $collab->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                {{ $collab->department ? strtoupper($collab->department) : 'No Department' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0 small fst-italic">No collaborators listed for this proposal.</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Reviewer Feedback Section -->
    @if(auth()->user()->role !== 'coordinator')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="h5 fw-bold d-flex align-items-center gap-3 mb-4">
                <i class="bi bi-chat-left-text text-primary"></i> Reviewer Feedback
            </h3>

            <p class="text-muted mb-4 small fw-medium">Reviewed by: {{ $proposal->reviewer->name ?? 'Under Review' }}</p>

            <div class="mb-4">
                <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Comments:</label>
                <div class="feedback-box">
                    {{ $proposal->review_comments ?? 'No comments available yet.' }}
                </div>
            </div>

            <div>
                <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Suggestions:</label>
                <div class="feedback-box">
                    {{ $proposal->review_suggestions ?? 'No suggestions available yet.' }}
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Document History Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="h5 fw-bold d-flex align-items-center gap-3 mb-4">
                <i class="bi bi-file-earmark-pdf text-danger"></i> Document History
            </h3>
            
            @if($proposal->documents && $proposal->documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Document Tag</th>
                                <th>Version</th>
                                <th>Phase</th>
                                <th>Date Uploaded</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposal->documents->sortByDesc('version') as $doc)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $doc->document_tag }}</span></td>
                                <td>V{{ $doc->version }}</td>
                                <td>Phase {{ $doc->phase }}</td>
                                <td>{{ $doc->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <a href="{{ route('file.serve', ['path' => $doc->file_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> View PDF
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif($proposal->document_path)
                <!-- Fallback for old proposals -->
                <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                    <div>
                        <h6 class="mb-0 fw-bold">Original Proposal Document</h6>
                        <small class="text-muted">Legacy Upload</small>
                    </div>
                    <a href="{{ route('file.serve', ['path' => $proposal->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary px-4">
                        View PDF
                    </a>
                </div>
            @else
                <p class="text-muted mb-0">No documents uploaded.</p>
            @endif
        </div>
    </div>

    <!-- Official Documents (Notice & Certificate) -->
    @if(in_array($proposal->status, ['accepted_for_in_house_review', 'revision_required', 'approved', 'final_copy_submitted', 'final_copy_noted_by_dean', 'endorsed_to_vprei', 'final_approved', 'ongoing', 'completed', 'archived']))
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-award text-warning"></i> Official Documents</h5>
            <div class="d-flex gap-3">
                <a href="{{ route('proposal.downloadNotice', $proposal->id) }}" class="btn btn-primary">
                    <i class="bi bi-download"></i> In-House Review Acceptance Form
                </a>
                
                @if(in_array($proposal->status, ['final_approved', 'ongoing', 'completed', 'archived']))
                <a href="{{ route('proposal.downloadNTP', $proposal->id) }}" class="btn btn-info text-white">
                    <i class="bi bi-download"></i> Notice to Proceed
                </a>
                @endif
                
                @if($proposal->current_phase >= 5)
                <a href="{{ route('proposal.downloadCertificate', $proposal->id) }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Certificate of Completion
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Phase 6: Final Manuscript Submission -->
    @if(auth()->id() == $proposal->user_id && $proposal->current_phase >= 5 && $proposal->status !== 'archived')
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">
                <h3 class="h5 fw-bold d-flex align-items-center gap-3 mb-4">
                    <i class="bi bi-journal-check text-primary"></i> Phase 6: Final Manuscript Submission
                </h3>
                <form action="{{ route('proposal.submitFinal', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Upload Final Manuscript (PDF)</label>
                        <input type="file" name="final_manuscript" class="form-control" accept=".pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Final Manuscript</button>
                </form>
            </div>
        </div>
    @endif

    <!-- Line Item Budget (LIB) Section -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 px-lg-5 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="h5 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-calculator-fill text-success"></i> Line Item Budget (LIB)
                </h3>
                <p class="text-muted small mb-0">Detailed breakdown of Maintenance & Operating Expenses (MOOE) and Capital Outlay (CO).</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success px-4 py-2 rounded-pill fw-bold fs-6">
                    Grand Total: ₱{{ number_format($proposal->total_budget ?? $proposal->budgetItems->sum('amount'), 2) }}
                </div>
                @if(auth()->id() == $proposal->user_id)
                    <button type="button" class="btn btn-success btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addBudgetItemModal">
                        <i class="bi bi-plus-circle-fill"></i> Add Budget Entry
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body p-4 p-lg-5">
            @php
                $mooeItems = $proposal->budgetItems->where('category_type', 'mooe');
                $coItems = $proposal->budgetItems->where('category_type', 'co');
                $subtotalMOOE = $mooeItems->sum('amount');
                $subtotalCO = $coItems->sum('amount');
                $grandTotal = $subtotalMOOE + $subtotalCO;
            @endphp

            <div class="table-responsive border rounded-4 bg-white shadow-sm mb-3">
                <table class="table table-bordered align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th rowspan="2" style="width: 240px;" class="py-3">Particulars / Category</th>
                            <th rowspan="2" class="py-3">Details</th>
                            <th rowspan="2" style="width: 140px;" class="py-3">Funding Agency / Org</th>
                            <th colspan="3" class="py-2 bg-primary bg-opacity-10 text-primary fw-bold">University of Antique</th>
                            <th rowspan="2" style="width: 130px;" class="py-3 bg-light">Total (₱)</th>
                            @if(auth()->id() == $proposal->user_id)
                                <th rowspan="2" style="width: 60px;" class="py-3">Action</th>
                            @endif
                        </tr>
                        <tr>
                            <th style="width: 120px;" class="small py-2">Equivalent Teaching Unit</th>
                            <th style="width: 120px;" class="small py-2">Existing Resources</th>
                            <th style="width: 140px;" class="small py-2 fw-bold text-success">Proposed Expenditures</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Section I: MOOE -->
                        <tr class="table-secondary fw-bold">
                            <td colspan="{{ auth()->id() == $proposal->user_id ? 8 : 7 }}" class="py-2.5 ps-3 text-dark">
                                I. Maintenance and Other Operating Expenses (MOOE)
                            </td>
                        </tr>

                        @php
                            $groups = [
                                'supplies' => 'A. Supplies and Materials Expenses',
                                'semi_expandable' => 'B. Semi-Expandable Expenses',
                                'travel' => 'C. Travelling Expenses (Local)',
                                'transportation' => 'D. Transportation',
                                'professional_services' => 'E. Other Professional Services',
                                'other_mooe' => 'F. Other MOOE',
                            ];
                        @endphp

                        @foreach($groups as $groupKey => $groupLabel)
                            @php
                                $groupItems = $mooeItems->where('category_group', $groupKey);
                            @endphp
                            @if($groupItems->isNotEmpty())
                                <tr>
                                    <td colspan="{{ auth()->id() == $proposal->user_id ? 8 : 7 }}" class="bg-light fw-semibold text-muted py-2 ps-4">
                                        {{ $groupLabel }}
                                    </td>
                                </tr>
                                @foreach($groupItems as $item)
                                    <tr>
                                        <td class="ps-5 text-dark fw-medium">{{ $item->item_name }}</td>
                                        <td class="text-muted">{{ $item->item_name }}</td>
                                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $item->funding_agency ?? '—' }}</span></td>
                                        <td class="text-center text-muted">{{ $item->equivalent_teaching_unit ?? '—' }}</td>
                                        <td class="text-center text-muted">{{ $item->existing_resources ?? '—' }}</td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($item->amount, 2) }}</td>
                                        <td class="text-end fw-bold text-success bg-light">₱{{ number_format($item->amount, 2) }}</td>
                                        @if(auth()->id() == $proposal->user_id)
                                            <td class="text-center">
                                                <form action="{{ route('budget_items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this budget entry?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger border-0 p-1" title="Delete Entry">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach

                        @if($mooeItems->isEmpty())
                            <tr>
                                <td colspan="{{ auth()->id() == $proposal->user_id ? 8 : 7 }}" class="text-center py-3 text-muted italic small">No MOOE items listed yet.</td>
                            </tr>
                        @endif

                        <!-- Sub-total for MOOE -->
                        <tr class="table-warning fw-bold">
                            <td colspan="5" class="py-2.5 text-end text-dark">Sub-total for MOOE:</td>
                            <td class="text-end text-dark">₱{{ number_format($subtotalMOOE, 2) }}</td>
                            <td class="text-end text-dark">₱{{ number_format($subtotalMOOE, 2) }}</td>
                            @if(auth()->id() == $proposal->user_id) <td></td> @endif
                        </tr>

                        <!-- Section II: Capital Outlay -->
                        <tr class="table-secondary fw-bold">
                            <td colspan="{{ auth()->id() == $proposal->user_id ? 8 : 7 }}" class="py-2.5 ps-3 text-dark">
                                II. Capital Outlay (CO)
                            </td>
                        </tr>

                        @if($coItems->isNotEmpty())
                            @foreach($coItems as $item)
                                <tr>
                                    <td class="ps-4 text-dark fw-medium">{{ $item->item_name }}</td>
                                    <td class="text-muted">{{ $item->item_name }}</td>
                                    <td class="text-center"><span class="badge bg-light text-dark border">{{ $item->funding_agency ?? '—' }}</span></td>
                                    <td class="text-center text-muted">{{ $item->equivalent_teaching_unit ?? '—' }}</td>
                                    <td class="text-center text-muted">{{ $item->existing_resources ?? '—' }}</td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($item->amount, 2) }}</td>
                                    <td class="text-end fw-bold text-success bg-light">₱{{ number_format($item->amount, 2) }}</td>
                                    @if(auth()->id() == $proposal->user_id)
                                        <td class="text-center">
                                            <form action="{{ route('budget_items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this budget entry?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger border-0 p-1" title="Delete Entry">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="{{ auth()->id() == $proposal->user_id ? 8 : 7 }}" class="text-center py-3 text-muted italic small">No Capital Outlay items listed yet.</td>
                            </tr>
                        @endif

                        <!-- Sub-total for Capital Outlay -->
                        <tr class="table-warning fw-bold">
                            <td colspan="5" class="py-2.5 text-end text-dark">Sub-total for CO:</td>
                            <td class="text-end text-dark">₱{{ number_format($subtotalCO, 2) }}</td>
                            <td class="text-end text-dark">₱{{ number_format($subtotalCO, 2) }}</td>
                            @if(auth()->id() == $proposal->user_id) <td></td> @endif
                        </tr>

                        <!-- GRAND TOTAL -->
                        <tr class="table-success fw-bold fs-6">
                            <td colspan="5" class="py-3 text-end text-uppercase text-dark">GRAND TOTAL:</td>
                            <td class="text-end text-success fs-5">₱{{ number_format($grandTotal, 2) }}</td>
                            <td class="text-end text-success fs-5">₱{{ number_format($grandTotal, 2) }}</td>
                            @if(auth()->id() == $proposal->user_id) <td></td> @endif
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal: Add Budget Entry -->
    @if(auth()->id() == $proposal->user_id)
    <div class="modal fade" id="addBudgetItemModal" tabindex="-1" aria-labelledby="addBudgetItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-success" id="addBudgetItemModalLabel">
                        <i class="bi bi-plus-circle me-2"></i> Add Line Item Budget Entry
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('budget_items.store', $proposal->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted uppercase">Expenditure Type</label>
                            <select name="category_type" class="form-select bg-light border-0 py-2 px-3 rounded-3" required>
                                <option value="mooe">Section I: Maintenance & Other Operating Expenses (MOOE)</option>
                                <option value="co">Section II: Capital Outlay (CO)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted uppercase">Budget Category</label>
                            <select name="category_group" class="form-select bg-light border-0 py-2 px-3 rounded-3" required>
                                <option value="supplies">A. Supplies and Materials Expenses</option>
                                <option value="semi_expandable">B. Semi-Expandable Expenses</option>
                                <option value="travel">C. Travelling Expenses (Local)</option>
                                <option value="transportation">D. Transportation</option>
                                <option value="professional_services">E. Other Professional Services</option>
                                <option value="other_mooe">F. Other MOOE</option>
                                <option value="capital_outlay">II. Capital Outlay (CO - Equipment/Hardware)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted uppercase">Item Description / Details</label>
                            <textarea name="item_name" class="form-control bg-light border-0 py-2 px-3 rounded-3" rows="2" placeholder="e.g. Bookpaper [A4;80gsm] @ Php 315 x 2 reams" required></textarea>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted uppercase">Funding Agency / Org</label>
                                <input type="text" name="funding_agency" class="form-control bg-light border-0 py-2 px-3 rounded-3" placeholder="e.g. RESU">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted uppercase">Proposed Amount (₱)</label>
                                <input type="number" step="0.01" min="0" name="amount" class="form-control bg-light border-0 py-2 px-3 rounded-3" placeholder="e.g. 630.00" required>
                            </div>
                        </div>
                        <div class="row g-3 mb-0">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted uppercase">Equivalent Teaching Unit</label>
                                <input type="text" name="equivalent_teaching_unit" class="form-control bg-light border-0 py-2 px-3 rounded-3" placeholder="Optional">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted uppercase">Existing Resources</label>
                                <input type="text" name="existing_resources" class="form-control bg-light border-0 py-2 px-3 rounded-3" placeholder="Optional">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 rounded-pill fw-bold shadow-sm">Save Budget Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Work Plan & Gantt Chart Matrix Section -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 px-lg-5 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="h5 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-bar-chart-line-fill text-primary"></i> 12-Month Research Work Plan (Gantt Chart)
                </h3>
                <p class="text-muted small mb-0">Visual Gantt chart timeline mapping project phases from inception to final submission.</p>
            </div>
            @if(auth()->id() == $proposal->user_id)
                <button type="button" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                    <i class="bi bi-plus-circle-fill"></i> Add Work Plan Phase
                </button>
            @endif
        </div>

        <div class="card-body p-4 p-lg-5">
            @php
                $months = [
                    ['num' => 'M1', 'label' => 'Jan'],
                    ['num' => 'M2', 'label' => 'Feb'],
                    ['num' => 'M3', 'label' => 'Mar'],
                    ['num' => 'M4', 'label' => 'Apr'],
                    ['num' => 'M5', 'label' => 'May'],
                    ['num' => 'M6', 'label' => 'Jun'],
                    ['num' => 'M7', 'label' => 'Jul'],
                    ['num' => 'M8', 'label' => 'Aug'],
                    ['num' => 'M9', 'label' => 'Sep'],
                    ['num' => 'M10', 'label' => 'Oct'],
                    ['num' => 'M11', 'label' => 'Nov'],
                    ['num' => 'M12', 'label' => 'Dec'],
                ];
            @endphp

            @if($proposal->milestones && $proposal->milestones->count() > 0)
                <div class="gantt-matrix-container border rounded-4 p-4 bg-white shadow-sm overflow-x-auto">
                    <!-- Gantt Header (Timeline Month Columns) -->
                    <div class="gantt-header d-flex align-items-center border-bottom pb-3 mb-2" style="min-width: 950px;">
                        <div class="gantt-phase-col fw-bold text-uppercase text-secondary small" style="width: 260px; flex-shrink: 0;">
                            Phase / Activity Name
                        </div>
                        <div class="gantt-timeline-header d-flex flex-grow-1 text-center">
                            @foreach($months as $m)
                                <div style="width: 8.333%;" class="border-start px-1">
                                    <span class="d-block fw-bold text-dark small">{{ $m['num'] }}</span>
                                    <span class="text-muted fw-semibold" style="font-size: 0.72rem;">{{ $m['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Gantt Body Rows -->
                    <div class="gantt-body" style="min-width: 950px;">
                        @foreach($proposal->milestones as $milestone)
                            @php
                                $mCount = $proposal->milestones->count();
                                if ($milestone->start_date && $milestone->target_date) {
                                    $startDate = \Carbon\Carbon::parse($milestone->start_date);
                                    $targetDate = \Carbon\Carbon::parse($milestone->target_date);
                                    
                                    $mStartIdx = (int)$startDate->format('n');
                                    $mEndIdx = (int)$targetDate->format('n');
                                    if ($mEndIdx < $mStartIdx) {
                                        $mEndIdx = $mStartIdx;
                                    }
                                } elseif ($milestone->start_date) {
                                    $startDate = \Carbon\Carbon::parse($milestone->start_date);
                                    $mStartIdx = (int)$startDate->format('n');
                                    $mEndIdx = min(12, $mStartIdx + 1);
                                } else {
                                    $createdMonth = (int)($proposal->created_at ? $proposal->created_at->format('n') : 1);
                                    $mStartIdx = min(12, max(1, $createdMonth + $loop->index));
                                    $mEndIdx = min(12, $mStartIdx);
                                }
                                
                                $leftPercent = (($mStartIdx - 1) / 12) * 100;
                                $widthPercent = max(8.333, (($mEndIdx - $mStartIdx + 1) / 12) * 100);
                                if ($leftPercent + $widthPercent > 100) {
                                    $widthPercent = 100 - $leftPercent;
                                }

                                $barGradient = match($milestone->status) {
                                    'approved' => 'linear-gradient(135deg, #10B981, #059669)',
                                    'rejected' => 'linear-gradient(135deg, #EF4444, #DC2626)',
                                    default => 'linear-gradient(135deg, #3B82F6, #1D4ED8)'
                                };

                                $statusBadge = match($milestone->status) {
                                    'approved' => 'bg-success text-white',
                                    'rejected' => 'bg-danger text-white',
                                    default => 'bg-warning text-dark'
                                };
                            @endphp

                            <div class="gantt-row d-flex align-items-center py-3 border-bottom position-relative">
                                <!-- Left Column: Phase Title & Description -->
                                <div class="gantt-phase-col pe-3" style="width: 260px; flex-shrink: 0;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-bold text-dark text-truncate" title="{{ $milestone->title }}">{{ $milestone->title }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $statusBadge }} px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">{{ ucfirst($milestone->status) }}</span>
                                        @if($milestone->document_path)
                                            <a href="{{ route('file.serve', ['path' => $milestone->document_path]) }}" target="_blank" class="text-primary small" style="font-size: 0.75rem;" title="View Attachment">
                                                <i class="bi bi-paperclip"></i> File
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Timeline Track with Horizontal Gantt Bar -->
                                <div class="gantt-track-col flex-grow-1 position-relative d-flex align-items-center" 
                                     style="height: 42px; background-size: 8.333% 100%; background-image: linear-gradient(to right, #f1f5f9 1px, transparent 1px);">
                                    
                                    <!-- Horizontal Bar -->
                                    <div class="gantt-bar rounded-pill shadow-sm d-flex align-items-center justify-content-between px-3 text-white position-absolute"
                                         style="left: {{ $leftPercent }}%; width: {{ $widthPercent }}%; height: 32px; background: {{ $barGradient }}; transition: all 0.3s ease;"
                                         data-bs-toggle="tooltip" data-bs-html="true" 
                                         title="<strong>{{ $milestone->title }}</strong><br>{{ $milestone->description }}">
                                        
                                        <span class="small fw-bold text-truncate me-2" style="font-size: 0.75rem;">
                                            {{ $milestone->title }}
                                        </span>
                                        <span class="badge bg-white text-dark rounded-pill shadow-xs" style="font-size: 0.65rem;">
                                            {{ $months[$mStartIdx-1]['num'] }}@if($mEndIdx > $mStartIdx)-{{ $months[$mEndIdx-1]['num'] }}@endif
                                        </span>
                                    </div>
                                </div>

                                <!-- Reviewer/Admin Actions inline -->
                                @if(in_array(auth()->user()->role, ['admin', 'super_admin', 'reviewer']) && $milestone->status == 'pending')
                                    <div class="ms-3 d-flex gap-1 flex-shrink-0">
                                        <form action="{{ route('admin.milestones.updateStatus', $milestone->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-xs btn-success py-1 px-2 rounded-pill fw-bold" style="font-size: 0.7rem;" title="Approve Phase">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.milestones.updateStatus', $milestone->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2 rounded-pill fw-bold" style="font-size: 0.7rem;" title="Reject Phase">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Gantt Legend -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-4 pt-3 border-top small text-muted">
                        <div class="d-flex align-items-center gap-4">
                            <span class="fw-bold text-uppercase" style="font-size: 0.75rem;">Legend:</span>
                            <span class="d-flex align-items-center gap-2"><span class="rounded-pill d-inline-block" style="width: 12px; height: 12px; background: linear-gradient(135deg, #10B981, #059669);"></span> Approved / Completed</span>
                            <span class="d-flex align-items-center gap-2"><span class="rounded-pill d-inline-block" style="width: 12px; height: 12px; background: linear-gradient(135deg, #3B82F6, #1D4ED8);"></span> Pending Review</span>
                            <span class="d-flex align-items-center gap-2"><span class="rounded-pill d-inline-block" style="width: 12px; height: 12px; background: linear-gradient(135deg, #EF4444, #DC2626);"></span> Revision Required</span>
                        </div>
                        <div class="text-muted italic" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle me-1"></i> M1 to M12 represent Month 1 through Month 12 of the research duration.
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5 bg-light rounded-4 border">
                    <i class="bi bi-kanban text-muted display-5 d-block mb-3"></i>
                    <h6 class="fw-bold text-muted mb-1">No Work Plan Phases Created Yet</h6>
                    <p class="text-muted small mb-3">Add research phases (e.g., Literature review, Proposal, Ethics, Data collection, Analysis, Final submission) to populate the Gantt Chart.</p>
                    @if(auth()->id() == $proposal->user_id)
                        <button type="button" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                            <i class="bi bi-plus-circle me-1"></i> Add First Work Plan Phase
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Modal: Add Work Plan Item -->
    @if(auth()->id() == $proposal->user_id)
    <div class="modal fade" id="addMilestoneModal" tabindex="-1" aria-labelledby="addMilestoneModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-primary" id="addMilestoneModalLabel">
                        <i class="bi bi-calendar-plus me-2"></i> Add Work Plan Phase / Milestone
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('milestones.store', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted uppercase">Phase / Activity Title</label>
                            <input type="text" name="title" class="form-control bg-light border-0 py-2 px-3 rounded-3" placeholder="e.g. Data Collection & Field Work" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted uppercase">Start Date</label>
                                <input type="date" name="start_date" class="form-control bg-light border-0 py-2 px-3 rounded-3">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted uppercase">Target Date</label>
                                <input type="date" name="target_date" class="form-control bg-light border-0 py-2 px-3 rounded-3">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted uppercase">Phase Description & Deliverables</label>
                            <textarea name="description" class="form-control bg-light border-0 py-2 px-3 rounded-3" rows="3" placeholder="Describe key activities, methodology, and deliverables..." required></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted uppercase">Supporting Document (Optional PDF)</label>
                            <input type="file" name="document" class="form-control bg-light border-0 py-2 px-3 rounded-3" accept=".pdf,.doc,.docx">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">Save Work Plan Phase</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
