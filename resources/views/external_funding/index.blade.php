<x-app-layout>
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 mb-md-4 gap-2 gap-md-3">
        <div>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-cash-coin text-primary"></i> External Funding
            </h1>
            <p class="text-muted small mb-0">Track funding-agency endorsements, agreements, implementation, and grant closure.</p>
        </div>
    </div>

    <!-- Submission Card for Faculty Researchers -->
    @if(auth()->user()->role === 'researcher')
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white border-bottom-0 pt-4 px-3 px-md-4 pb-0">
                <h5 class="fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-send-plus-fill text-primary"></i> Submit Proposal for External Funding
                </h5>
                <p class="text-muted small mb-0">Request institutional endorsement to submit your lead research proposal to external funding agencies.</p>
            </div>
            <div class="card-body p-3 p-md-4">
                @if($proposals->isEmpty())
                    <div class="alert alert-light border rounded-3 p-3 text-muted small mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle text-primary fs-5"></i>
                        All of your lead proposals are already enrolled in an external-funding workflow.
                    </div>
                @else
                    <form method="POST" action="{{ route('external-funding.store') }}" class="row g-3">
                        @csrf
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Research Proposal <span class="text-danger">*</span></label>
                            <select name="research_proposal_id" class="form-select bg-light border-0 py-2.5 px-3 rounded-3" required>
                                <option value="">Select a proposal...</option>
                                @foreach($proposals as $proposal)
                                    <option value="{{ $proposal->id }}">{{ $proposal->proposal_code }} — {{ $proposal->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Funding Agency <span class="text-danger">*</span></label>
                            <select name="funding_agency_user_id" class="form-select bg-light border-0 py-2.5 px-3 rounded-3" required>
                                <option value="">Select approved agency...</option>
                                @foreach($fundingAgencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->organization ?: $agency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Grant Program</label>
                            <input name="grant_program" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" placeholder="e.g. Grants-in-Aid (GIA)">
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Requested Amount (₱)</label>
                            <input name="requested_amount" type="number" min="0" step="0.01" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" placeholder="0.00">
                        </div>
                        <div class="col-12 pt-1">
                            <button type="submit" class="btn btn-primary px-4 py-2.5 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                                <i class="bi bi-send-fill"></i> Request University Endorsement
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

    @php
        $getStatusBadge = function($status) {
            return match($status) {
                'grant_active' => ['bg' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25', 'icon' => 'bi-check-circle-fill', 'label' => 'Grant Active'],
                'closed' => ['bg' => 'bg-secondary bg-opacity-10 text-secondary border', 'icon' => 'bi-archive-fill', 'label' => 'Closed'],
                'rejected' => ['bg' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25', 'icon' => 'bi-x-circle-fill', 'label' => 'Rejected'],
                'revision_requested' => ['bg' => 'bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25', 'icon' => 'bi-exclamation-triangle-fill', 'label' => 'Revision Requested'],
                'pending_university_endorsement' => ['bg' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25', 'icon' => 'bi-hourglass-split', 'label' => 'Pending University Endorsement'],
                'pending_president_endorsement' => ['bg' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25', 'icon' => 'bi-hourglass-split', 'label' => 'Pending President Endorsement'],
                'under_funder_review' => ['bg' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25', 'icon' => 'bi-search', 'label' => 'Under Funder Review'],
                'pending_moa_mou' => ['bg' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25', 'icon' => 'bi-file-earmark-text-fill', 'label' => 'Pending MOA/MOU'],
                'under_grant_monitoring' => ['bg' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25', 'icon' => 'bi-graph-up', 'label' => 'Grant Monitoring'],
                'pending_grant_closure' => ['bg' => 'bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25', 'icon' => 'bi-clock-history', 'label' => 'Pending Grant Closure'],
                default => ['bg' => 'bg-primary bg-opacity-10 text-primary border', 'icon' => 'bi-clock-fill', 'label' => ucwords(str_replace('_', ' ', $status))]
            };
        };
    @endphp

    <!-- Mobile View: Modern Project Cards (d-md-none) -->
    <div class="d-md-none">
        @forelse($projects as $project)
            @php
                $badgeInfo = $getStatusBadge($project->status);
            @endphp
            <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
                <div class="card-body p-3">
                    <!-- Top Row: Proposal Code on left, Status on right (wrapped to prevent collision on narrow viewports) -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <span class="badge bg-white text-dark border fw-mono" style="font-size: 0.68rem;">
                            {{ $project->proposal->proposal_code ?? 'P-'.$project->proposal->id }}
                        </span>
                        <span class="badge {{ $badgeInfo['bg'] }} rounded-pill px-2.5 py-1" style="font-size: 0.68rem; line-height: 1.2;">
                            <i class="bi {{ $badgeInfo['icon'] }} me-1"></i> {{ $badgeInfo['label'] }}
                        </span>
                    </div>

                    <!-- Title & Lead Researcher (Full Width) -->
                    <div class="mb-2.5">
                        <h6 class="fw-bold mb-1" style="font-size: 0.92rem; line-height: 1.35;">
                            <a href="{{ route('proposal.show', $project->proposal->id) }}" class="text-dark text-decoration-none hover-primary transition">
                                {{ $project->proposal->title }}
                            </a>
                        </h6>
                        <small class="text-muted d-block" style="font-size: 0.74rem;">
                            Lead: {{ $project->proposal->user->name }}
                        </small>
                    </div>

                    <!-- Agency & Program Box -->
                    <div class="bg-light p-2.5 rounded-3 mb-2.5 border">
                        <div class="d-flex align-items-center gap-2 small text-dark fw-bold">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 24px; height: 24px;">
                                <i class="bi bi-bank2" style="font-size: 0.72rem;"></i>
                            </div>
                            <span>{{ $project->funding_agency }}</span>
                        </div>
                        @if($project->grant_program)
                            <div class="text-muted small ps-4" style="font-size: 0.74rem;">
                                Program: {{ $project->grant_program }}
                            </div>
                        @endif
                    </div>

                    <!-- Financial Mini Stats (2-Column Grid) -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 rounded-3 bg-light border">
                                <span class="text-muted d-block" style="font-size: 0.68rem;">Requested</span>
                                <span class="fw-bold text-dark small">₱{{ number_format($project->requested_amount ?? 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            @if(($project->awarded_amount ?? 0) > 0)
                                <div class="p-2 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25">
                                    <span class="text-success d-block fw-medium" style="font-size: 0.68rem;">Awarded</span>
                                    <span class="fw-bold text-success small">₱{{ number_format($project->awarded_amount, 2) }}</span>
                                </div>
                            @else
                                <div class="p-2 rounded-3 bg-light border">
                                    <span class="text-muted d-block" style="font-size: 0.68rem;">Awarded</span>
                                    <span class="fw-bold text-secondary small">₱0.00</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions Area for Mobile -->
                    <div class="pt-2 border-top">
                        @if(auth()->user()->role === 'researcher' && $project->status === 'revision_requested')
                            <form method="POST" action="{{ route('external-funding.resubmit', $project->id) }}">
                                @csrf
                                <div class="mb-2">
                                    <input name="revision_notes" class="form-control form-control-sm rounded-3" placeholder="Revision summary" required>
                                </div>
                                <button class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-bold">
                                    <i class="bi bi-arrow-repeat me-1"></i> Resubmit to Agency
                                </button>
                            </form>
                        @elseif(auth()->user()->role === 'researcher' && $project->status === 'under_grant_monitoring')
                            <form method="POST" action="{{ route('external-funding.terminal-report', $project->id) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small fw-bold text-muted mb-1">Upload Terminal Report (PDF)</label>
                                    <input name="terminal_report" type="file" class="form-control form-control-sm rounded-3" accept=".pdf" required>
                                </div>
                                <button class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-bold">
                                    <i class="bi bi-file-earmark-arrow-up me-1"></i> Submit Terminal Report
                                </button>
                            </form>
                        @elseif((auth()->user()->role === 'vprei' || auth()->user()->isAdmin()) && $project->status === 'pending_university_endorsement')
                            <form method="POST" action="{{ route('external-funding.endorse-university', $project->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-success w-100 rounded-pill fw-bold shadow-xs">
                                    <i class="bi bi-check2-circle me-1"></i> Endorse for University Approval
                                </button>
                            </form>
                        @elseif(auth()->user()->role === 'president' && $project->status === 'pending_president_endorsement')
                            <form method="POST" action="{{ route('external-funding.endorse-agency', $project->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-success w-100 rounded-pill fw-bold shadow-xs">
                                    <i class="bi bi-send-check me-1"></i> Endorse to Agency
                                </button>
                            </form>
                        @elseif(auth()->user()->isFundingAgency() && $project->status === 'under_funder_review')
                            <form method="POST" action="{{ route('external-funding.agency-decision', $project->id) }}" class="d-flex flex-column gap-2">
                                @csrf
                                <select name="decision" class="form-select form-select-sm rounded-3">
                                    <option value="accepted">Accept</option>
                                    <option value="revision_requested">Request revision</option>
                                    <option value="rejected">Reject</option>
                                </select>
                                <input name="agency_reference_number" class="form-control form-control-sm rounded-3" placeholder="Agency reference number">
                                <input name="awarded_amount" type="number" step="0.01" class="form-control form-control-sm rounded-3" placeholder="Awarded amount (₱)">
                                <textarea name="agency_feedback" class="form-control form-control-sm rounded-3" rows="2" placeholder="Decision notes"></textarea>
                                <button class="btn btn-sm btn-primary w-100 rounded-pill fw-bold">
                                    Submit Funding Decision
                                </button>
                            </form>
                        @elseif(auth()->user()->role === 'president' && $project->status === 'pending_moa_mou')
                            <form method="POST" action="{{ route('external-funding.agreement', $project->id) }}" enctype="multipart/form-data" class="d-flex flex-column gap-2">
                                @csrf
                                <div>
                                    <label class="form-label small fw-bold text-muted mb-1">Signed MOA/MOU (PDF)</label>
                                    <input name="moa_mou_file" type="file" class="form-control form-control-sm rounded-3" required>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label x-small text-muted mb-0">Start Date</label>
                                        <input name="grant_start_date" type="date" class="form-control form-control-sm rounded-3" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label x-small text-muted mb-0">End Date</label>
                                        <input name="grant_end_date" type="date" class="form-control form-control-sm rounded-3" required>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-success w-100 rounded-pill fw-bold">Execute MOA/MOU</button>
                            </form>
                        @elseif((auth()->user()->role === 'vprei' || auth()->user()->isAdmin()) && $project->status === 'grant_active')
                            <form method="POST" action="{{ route('external-funding.monitor', $project->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-bold">
                                    <i class="bi bi-graph-up me-1"></i> Start Monitoring
                                </button>
                            </form>
                        @elseif((auth()->user()->role === 'vprei' || auth()->user()->isAdmin()) && $project->status === 'pending_grant_closure')
                            <form method="POST" action="{{ route('external-funding.close', $project->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-success w-100 rounded-pill fw-bold">
                                    <i class="bi bi-check-circle me-1"></i> Close Grant
                                </button>
                            </form>
                        @else
                            <div class="p-2 rounded-pill bg-light border text-muted small text-center d-flex align-items-center justify-content-center gap-1.5" style="font-size: 0.74rem;">
                                <i class="bi bi-clock-history text-secondary"></i> Waiting on next workflow milestone
                            </div>
                        @endif
                        <div class="text-center pt-2">
                            <a href="{{ route('proposal.show', $project->proposal->id) }}" class="text-primary text-decoration-none small fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                View Full Proposal <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
                <i class="bi bi-cash-stack display-6 d-block mb-2 opacity-50"></i>
                <p class="small mb-0">No external funding projects yet.</p>
            </div>
        @endforelse
    </div>

    <!-- Desktop View: Table (d-none d-md-block) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Proposal</th>
                        <th class="py-3">Funding Agency</th>
                        <th class="py-3">Grant Details</th>
                        <th class="py-3">Status</th>
                        <th class="pe-4 py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        @php
                            $badgeInfo = $getStatusBadge($project->status);
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-1.5 mb-1">
                                    <span class="badge bg-white text-dark border fw-mono" style="font-size: 0.68rem;">
                                        {{ $project->proposal->proposal_code ?? 'P-'.$project->proposal->id }}
                                    </span>
                                </div>
                                <div class="fw-bold text-dark">
                                    <a href="{{ route('proposal.show', $project->proposal->id) }}" class="text-dark text-decoration-none hover-primary">
                                        {{ $project->proposal->title }}
                                    </a>
                                </div>
                                <small class="text-muted">{{ $project->proposal->user->name }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $project->funding_agency }}</div>
                                <small class="text-muted">{{ $project->grant_program ?? 'General Grant' }}</small>
                            </td>
                            <td>
                                <div class="small text-muted">Requested: <strong class="text-dark">₱{{ number_format($project->requested_amount ?? 0, 2) }}</strong></div>
                                @if(($project->awarded_amount ?? 0) > 0)
                                    <div class="small text-success">Awarded: <strong>₱{{ number_format($project->awarded_amount, 2) }}</strong></div>
                                @else
                                    <div class="small text-muted">Awarded: <span class="text-secondary fw-semibold">₱0.00</span></div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $badgeInfo['bg'] }} px-3 py-1.5 rounded-pill fw-bold text-wrap" style="font-size: 0.72rem; line-height: 1.2;">
                                    <i class="bi {{ $badgeInfo['icon'] }} me-1"></i> {{ $badgeInfo['label'] }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                @if(auth()->user()->role === 'researcher' && $project->status === 'revision_requested')
                                    <form method="POST" action="{{ route('external-funding.resubmit', $project->id) }}" class="d-inline-flex gap-2 align-items-center">
                                        @csrf
                                        <input name="revision_notes" class="form-control form-control-sm rounded-3" placeholder="Revision summary" required style="max-width: 180px;">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill fw-bold text-nowrap">Resubmit</button>
                                    </form>
                                @elseif(auth()->user()->role === 'researcher' && $project->status === 'under_grant_monitoring')
                                    <form method="POST" action="{{ route('external-funding.terminal-report', $project->id) }}" enctype="multipart/form-data" class="d-inline-flex gap-2 align-items-center">
                                        @csrf
                                        <input name="terminal_report" type="file" class="form-control form-control-sm rounded-3" accept=".pdf" required style="max-width: 200px;">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill fw-bold text-nowrap">Submit Report</button>
                                    </form>
                                @elseif((auth()->user()->role === 'vprei' || auth()->user()->isAdmin()) && $project->status === 'pending_university_endorsement')
                                    <form method="POST" action="{{ route('external-funding.endorse-university', $project->id) }}" class="d-inline-block">
                                        @csrf
                                        <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-xs">
                                            <i class="bi bi-check2-circle me-1"></i> Endorse
                                        </button>
                                    </form>
                                @elseif(auth()->user()->role === 'president' && $project->status === 'pending_president_endorsement')
                                    <form method="POST" action="{{ route('external-funding.endorse-agency', $project->id) }}" class="d-inline-block">
                                        @csrf
                                        <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-xs">
                                            <i class="bi bi-send-check me-1"></i> Endorse to Agency
                                        </button>
                                    </form>
                                @elseif(auth()->user()->isFundingAgency() && $project->status === 'under_funder_review')
                                    <form method="POST" action="{{ route('external-funding.agency-decision', $project->id) }}" class="d-flex flex-column gap-1" style="max-width: 240px; margin-left: auto;">
                                        @csrf
                                        <select name="decision" class="form-select form-select-sm rounded-3">
                                            <option value="accepted">Accept</option>
                                            <option value="revision_requested">Request revision</option>
                                            <option value="rejected">Reject</option>
                                        </select>
                                        <input name="agency_reference_number" class="form-control form-control-sm rounded-3" placeholder="Agency Ref #">
                                        <input name="awarded_amount" type="number" step="0.01" class="form-control form-control-sm rounded-3" placeholder="Awarded ₱">
                                        <button class="btn btn-sm btn-primary rounded-pill fw-bold">Submit Decision</button>
                                    </form>
                                @elseif(auth()->user()->role === 'president' && $project->status === 'pending_moa_mou')
                                    <form method="POST" action="{{ route('external-funding.agreement', $project->id) }}" enctype="multipart/form-data" class="d-flex flex-column gap-1" style="max-width: 220px; margin-left: auto;">
                                        @csrf
                                        <input name="moa_mou_file" type="file" class="form-control form-control-sm rounded-3" required>
                                        <div class="d-flex gap-1">
                                            <input name="grant_start_date" type="date" class="form-control form-control-sm rounded-3" title="Start Date" required>
                                            <input name="grant_end_date" type="date" class="form-control form-control-sm rounded-3" title="End Date" required>
                                        </div>
                                        <button class="btn btn-sm btn-success rounded-pill fw-bold">Execute MOA</button>
                                    </form>
                                @elseif((auth()->user()->role === 'vprei' || auth()->user()->isAdmin()) && $project->status === 'grant_active')
                                    <form method="POST" action="{{ route('external-funding.monitor', $project->id) }}" class="d-inline-block">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Start Monitoring</button>
                                    </form>
                                @elseif((auth()->user()->role === 'vprei' || auth()->user()->isAdmin()) && $project->status === 'pending_grant_closure')
                                    <form method="POST" action="{{ route('external-funding.close', $project->id) }}" class="d-inline-block">
                                        @csrf
                                        <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold">Close Grant</button>
                                    </form>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-cash-stack display-6 d-block mb-2 opacity-50"></i>
                                No external funding projects yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
