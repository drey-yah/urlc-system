<x-app-layout>
    <!-- Top Back Navigation -->
    <div class="mb-3 d-flex align-items-center justify-content-between">
        <a href="{{ route('internally_funded_endorsements.index') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold text-muted d-inline-flex align-items-center gap-1.5 shadow-xs">
            <i class="bi bi-arrow-left"></i> <span>Back to Endorsements</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('internally_funded_endorsements.pdf', $endorsement->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-xs">
                <i class="bi bi-download"></i> <span>Download Official PDF</span>
            </a>
            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-muted fw-semibold d-inline-flex align-items-center gap-1.5 shadow-xs" onclick="window.print()">
                <i class="bi bi-printer"></i> <span>Print</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-xs border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Official Document Header Banner -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4 bg-white text-center position-relative overflow-hidden">
        <div class="d-inline-flex flex-column align-items-center mb-2">
            <img src="{{ asset('logo.png') }}" alt="University Seal" style="width: 70px; height: 70px;" class="mb-2">
            <div class="text-uppercase small text-muted" style="letter-spacing: 0.5px;">Republic of the Philippines</div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">University of Antique</h5>
            <div class="text-muted small">Sibalom, Antique</div>
        </div>

        <h3 class="fw-bold text-dark text-uppercase text-decoration-underline mt-3 mb-1" style="letter-spacing: 0.5px;">
            INTERNALLY-FUNDED RESEARCH PROPOSAL ENDORSEMENT FORM
        </h3>
        <p class="text-muted small mb-3">
            Date: <strong>{{ $endorsement->report_date ? $endorsement->report_date->format('F d, Y') : date('F d, Y') }}</strong>
            @if($endorsement->fiscal_year)
                &bull; Fiscal Year: <strong>{{ $endorsement->fiscal_year }}</strong>
            @endif
        </p>

        <div>
            <span class="badge rounded-pill {{ $endorsement->statusBadgeClass() }} px-3 py-1.5 fw-bold" style="font-size: 0.8rem;">
                {{ $endorsement->statusLabel() }}
            </span>
        </div>
    </div>

    <!-- Signatory Progression Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Prepared by Director -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">1. Prepared by</div>
                <h6 class="fw-bold text-dark mt-1 mb-0">{{ $endorsement->preparer->name ?? 'Research Director' }}</h6>
                <div class="text-muted small">Director</div>
                <div class="mt-2 text-success small fw-semibold d-flex align-items-center gap-1">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Signed {{ $endorsement->prepared_at ? $endorsement->prepared_at->format('M d, Y') : '' }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Recommending Approval: VPREI -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 {{ $endorsement->isVpreiRecommended() ? 'border-success' : 'border-warning' }}">
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">2. Recommending Approval</div>
                <h6 class="fw-bold text-dark mt-1 mb-0">{{ $endorsement->vpreiNoter->name ?? 'VP for REI' }}</h6>
                <div class="text-muted small">VP for REI</div>

                <div class="mt-2">
                    @if($endorsement->isVpreiRecommended())
                        <div class="text-success small fw-semibold d-flex align-items-center gap-1">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Recommended {{ $endorsement->vprei_signed_at->format('M d, Y') }}</span>
                        </div>
                    @else
                        <div class="text-warning text-dark small fw-semibold d-flex align-items-center gap-1 mb-2">
                            <i class="bi bi-clock-history"></i> Awaiting Recommendation
                        </div>
                        @if($isVprei)
                            <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 py-1 fw-bold text-dark shadow-xs" data-bs-toggle="modal" data-bs-target="#vpreiModal">
                                <i class="bi bi-pen-fill me-1"></i> Recommend as VPREI
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Recommending Approval: VPAF -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 {{ $endorsement->isVpafRecommended() ? 'border-success' : 'border-warning' }}">
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">3. Recommending Approval</div>
                <h6 class="fw-bold text-dark mt-1 mb-0">{{ $endorsement->vpafNoter->name ?? 'VPAF' }}</h6>
                <div class="text-muted small">VPAF (Academic &amp; Finance)</div>

                <div class="mt-2">
                    @if($endorsement->isVpafRecommended())
                        <div class="text-success small fw-semibold d-flex align-items-center gap-1">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Recommended {{ $endorsement->vpaf_signed_at->format('M d, Y') }}</span>
                        </div>
                    @else
                        <div class="text-warning text-dark small fw-semibold d-flex align-items-center gap-1 mb-2">
                            <i class="bi bi-clock-history"></i> Awaiting Recommendation
                        </div>
                        @if($isVpaf)
                            <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 py-1 fw-bold text-dark shadow-xs" data-bs-toggle="modal" data-bs-target="#vpafModal">
                                <i class="bi bi-pen-fill me-1"></i> Recommend as VPAF
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. Approved: President -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 {{ $endorsement->isPresidentApproved() ? 'border-success' : 'border-secondary' }}">
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">4. Final Approval</div>
                <h6 class="fw-bold text-dark mt-1 mb-0">{{ $endorsement->presidentApprover->name ?? 'University President' }}</h6>
                <div class="text-muted small">President</div>

                <div class="mt-2">
                    @if($endorsement->isPresidentApproved())
                        <div class="text-success small fw-semibold d-flex align-items-center gap-1">
                            <i class="bi bi-award-fill"></i>
                            <span>Approved {{ $endorsement->president_signed_at->format('M d, Y') }}</span>
                        </div>
                    @else
                        @if($endorsement->isBothRecommended())
                            <div class="text-info small fw-semibold d-flex align-items-center gap-1 mb-2">
                                <i class="bi bi-exclamation-circle-fill"></i> Ready for President Approval
                            </div>
                            @if($isPresident)
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold shadow-xs" data-bs-toggle="modal" data-bs-target="#presidentModal">
                                    <i class="bi bi-award-fill me-1"></i> Approve Endorsement
                                </button>
                            @endif
                        @else
                            <div class="text-muted small font-italic">
                                Awaiting VPREI &amp; VPAF
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Official Endorsement Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-bold text-dark mb-0">Endorsed Proposal Line Items ({{ count($endorsement->items ?? []) }})</h5>
            <span class="badge bg-light text-dark border rounded-pill px-3 py-1.5 fw-bold">
                Total Budget: ₱{{ number_format((float)$endorsement->total_budget, 2) }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th style="width: 5%; text-align: center;">#</th>
                            <th style="width: 44%;">Project Title</th>
                            <th style="width: 25%;">Proponent/s</th>
                            <th style="width: 11%; text-align: center;">College</th>
                            <th style="width: 15%; text-align: right;" class="pe-3">Budget (Php)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($endorsement->items ?? [] as $index => $item)
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}.</td>
                                <td>
                                    <strong class="text-dark">{{ $item['project_title'] ?? '' }}</strong>
                                    @if(!empty($item['proposal_id']))
                                        <div class="small mt-0.5">
                                            <a href="{{ route('proposal.show', $item['proposal_id']) }}" target="_blank" class="text-primary text-decoration-none">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> View Full Proposal
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $item['proponents'] ?? '' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $item['college'] ?? 'UA' }}</span>
                                </td>
                                <td class="text-end fw-bold text-dark pe-3">
                                    ₱{{ number_format((float)($item['budget'] ?? 0), 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No line items in this endorsement.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-light fw-bold text-dark">
                            <td colspan="4" class="text-end pe-3 py-3">TOTAL ENDORSED BUDGET:</td>
                            <td class="text-end pe-3 py-3">
                                <span class="h6 fw-bold text-primary mb-0">₱{{ number_format((float)$endorsement->total_budget, 2) }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Remarks & Feedback Dossier Notes -->
    @if($endorsement->remarks || $endorsement->vprei_remarks || $endorsement->vpaf_remarks || $endorsement->president_remarks)
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <h5 class="fw-bold text-dark mb-3">Signatory Notes &amp; Remarks</h5>
            <div class="row g-3">
                @if($endorsement->remarks)
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Director's Note:</div>
                            <p class="mb-0 small text-secondary">{{ $endorsement->remarks }}</p>
                        </div>
                    </div>
                @endif
                @if($endorsement->vprei_remarks)
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="text-muted small fw-bold text-uppercase mb-1">VPREI Note:</div>
                            <p class="mb-0 small text-secondary">{{ $endorsement->vprei_remarks }}</p>
                        </div>
                    </div>
                @endif
                @if($endorsement->vpaf_remarks)
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="text-muted small fw-bold text-uppercase mb-1">VPAF Note:</div>
                            <p class="mb-0 small text-secondary">{{ $endorsement->vpaf_remarks }}</p>
                        </div>
                    </div>
                @endif
                @if($endorsement->president_remarks)
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="text-muted small fw-bold text-uppercase mb-1">President's Directive:</div>
                            <p class="mb-0 small text-secondary">{{ $endorsement->president_remarks }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Modals for Signatories -->

    <!-- VPREI Recommending Modal -->
    @if($isVprei && !$endorsement->isVpreiRecommended())
        <div class="modal fade" id="vpreiModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('internally_funded_endorsements.recommend_vprei', $endorsement->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    @csrf
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-pen-fill text-warning"></i> VP for REI Recommending Approval
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <p class="text-muted small mb-3">
                            By clicking confirm, you are officially signing the <strong>Recommending Approval</strong> block for:
                            <br><strong>{{ $endorsement->title }}</strong> (Total: ₱{{ number_format((float)$endorsement->total_budget, 2) }})
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Remarks / Recommendations (Optional):</label>
                            <textarea name="remarks" rows="3" class="form-control rounded-3 text-sm" placeholder="e.g. Recommended following complete ethics clearance and approved budgetary ceilings."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">
                            <i class="bi bi-check-lg me-1"></i> Sign &amp; Recommend Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- VPAF Recommending Modal -->
    @if($isVpaf && !$endorsement->isVpafRecommended())
        <div class="modal fade" id="vpafModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('internally_funded_endorsements.recommend_vpaf', $endorsement->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    @csrf
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-pen-fill text-warning"></i> VPAF Recommending Approval
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <p class="text-muted small mb-3">
                            By clicking confirm, you are officially signing the <strong>VPAF Recommending Approval</strong> block for:
                            <br><strong>{{ $endorsement->title }}</strong> (Total: ₱{{ number_format((float)$endorsement->total_budget, 2) }})
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">VPAF Financial &amp; Academic Remarks (Optional):</label>
                            <textarea name="remarks" rows="3" class="form-control rounded-3 text-sm" placeholder="e.g. Budget allocation certified aligned with university GAA/income research provisions."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">
                            <i class="bi bi-check-lg me-1"></i> Sign &amp; Recommend Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- President Final Approval Modal -->
    @if($isPresident && !$endorsement->isPresidentApproved())
        <div class="modal fade" id="presidentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('internally_funded_endorsements.approve_president', $endorsement->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    @csrf
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-award-fill text-success"></i> University President Final Approval
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <p class="text-muted small mb-3">
                            Both <strong>VP for REI</strong> and <strong>VPAF</strong> have recommended this endorsement form.
                            <br>Clicking Approve will grant official presidential approval for:
                            <br><strong>{{ $endorsement->title }}</strong> (Total Approved: ₱{{ number_format((float)$endorsement->total_budget, 2) }})
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Presidential Directives / Remarks (Optional):</label>
                            <textarea name="remarks" rows="3" class="form-control rounded-3 text-sm" placeholder="e.g. Approved. Proceed with research execution and timely milestone submission."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Officially Approve Endorsement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>
