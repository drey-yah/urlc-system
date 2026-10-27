<x-app-layout>
    <style>
        .kpi-card-clickable {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent !important;
        }
        .kpi-card-clickable:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(31, 8, 12, 0.08) !important;
        }
        .kpi-card-clickable.active-kpi {
            border-color: #8C1D2C !important;
            background-color: #fff9f9 !important;
        }
        .proposal-nav-pills {
            background: #ffffff;
            border: 1px solid #e8ded6;
            box-shadow: 0 2px 6px rgba(31, 8, 12, 0.04);
        }
        .proposal-nav-pills .nav-link {
            color: #736760;
            font-size: 0.88rem;
            padding: 0.65rem 1.15rem;
            border-radius: 9999px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            font-weight: 600;
        }
        .proposal-nav-pills .nav-link:hover {
            color: #1F080C;
            background-color: #faf7f5;
        }
        .proposal-nav-pills .nav-link.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #8C1D2C 0%, #4A0E18 100%) !important;
            box-shadow: 0 4px 12px rgba(140, 29, 44, 0.25);
            border-color: transparent;
        }
        .proposal-nav-pills .nav-link.active i {
            color: #E5A93C !important;
        }
        .proposal-nav-pills .nav-link.active .badge {
            background-color: rgba(255, 255, 255, 0.22) !important;
            color: #ffffff !important;
        }
        .quick-filter-btn {
            transition: all 0.15s ease;
        }
        .quick-filter-btn.active {
            background-color: #8C1D2C !important;
            color: #ffffff !important;
            border-color: #8C1D2C !important;
        }
        .quick-filter-btn.active i {
            color: #ffffff !important;
        }
    </style>

    @php
        $totalCount = $leadProposals->count();
        $approvedCount = $leadProposals->filter(fn($p) => in_array($p->status, ['approved', 'final_approved', 'completed', 'ongoing']))->count();
        $inReviewCount = $leadProposals->filter(fn($p) => in_array($p->status, [
            'pending', 'pending_coordinator_endorsement', 'pending_dean_noting', 'noted_by_dean',
            'submitted_to_research_unit', 'pending_director_review', 'accepted_for_in_house_review',
            'pending_reviewer_evaluation', 'under_review', 'final_copy_submitted', 'final_copy_noted_by_dean',
            'endorsed_to_vprei', 'funds_certified', 'endorsed', 'endorsed_by_coordinator'
        ]))->count();
        $revisionCount = $leadProposals->filter(fn($p) => in_array($p->status, ['returned_for_revision', 'revision_required', 'draft']))->count();

        // Compute scheduled milestones
        $allMilestones = collect();
        foreach($leadProposals as $p) {
            foreach($p->milestones as $m) {
                $m->proposal_title = $p->title;
                $m->proposal_id = $p->id;
                $allMilestones->push($m);
            }
        }
        foreach($collaboratedProposals as $p) {
            foreach($p->milestones as $m) {
                $m->proposal_title = $p->title;
                $m->proposal_id = $p->id;
                $allMilestones->push($m);
            }
        }
    @endphp

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 mb-md-4">
        <div>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-folder2-open text-primary"></i> My Researches
            </h1>
            <p class="text-muted small mb-0">Manage your lead research projects, collaborations, and work plans.</p>
        </div>
        <a href="{{ route('proposal.create') }}" class="btn btn-primary py-2 px-3 px-md-4 shadow-sm rounded-pill fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-sm-inline">Submit Proposal</span>
            <span class="d-sm-none">Submit</span>
        </a>
    </div>

    <!-- 4 Clickable Summary Metric Cards (Quick KPI Filter) -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 h-100 bg-white kpi-card-clickable active-kpi" id="kpi-all" onclick="applyQuickFilter('all')">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Total Researches</small>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                        <i class="bi bi-folder2-open" style="font-size: 0.85rem;"></i>
                    </div>
                </div>
                <h2 class="h4 fs-md-3 fw-bold mb-0 text-dark">{{ $totalCount }}</h2>
                <small class="text-muted" style="font-size: 0.72rem;">All lead proposals</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 h-100 bg-white kpi-card-clickable" id="kpi-approved" onclick="applyQuickFilter('approved')">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Approved / Final</small>
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                        <i class="bi bi-check2-circle" style="font-size: 0.95rem;"></i>
                    </div>
                </div>
                <h2 class="h4 fs-md-3 fw-bold mb-0 text-success">{{ $approvedCount }}</h2>
                <small class="text-muted" style="font-size: 0.72rem;">Click to filter approved</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 h-100 bg-white kpi-card-clickable" id="kpi-pending" onclick="applyQuickFilter('pending')">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">In-Review / Routing</small>
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                        <i class="bi bi-hourglass-split" style="font-size: 0.9rem;"></i>
                    </div>
                </div>
                <h2 class="h4 fs-md-3 fw-bold mb-0 text-warning">{{ $inReviewCount }}</h2>
                <small class="text-muted" style="font-size: 0.72rem;">In active evaluation</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 h-100 bg-white kpi-card-clickable" id="kpi-returned" onclick="applyQuickFilter('returned')">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Needs Attention</small>
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                        <i class="bi bi-arrow-return-left" style="font-size: 0.85rem;"></i>
                    </div>
                </div>
                <h2 class="h4 fs-md-3 fw-bold mb-0 {{ $revisionCount > 0 ? 'text-danger' : 'text-muted' }}">{{ $revisionCount }}</h2>
                <small class="text-muted" style="font-size: 0.72rem;">Revisions &amp; drafts</small>
            </div>
        </div>
    </div>

    <!-- Master Navigation Tabs -->
    <div class="card border-0 shadow-sm rounded-4 p-2 mb-4 bg-white">
        <ul class="nav nav-pills proposal-nav-pills nav-fill flex-column flex-sm-row gap-1 gap-md-2 p-1 rounded-pill" id="researchesNavTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active py-2.5 px-3 rounded-pill fw-bold d-inline-flex align-items-center justify-content-center gap-2 w-100" 
                        id="tab-lead-pill" 
                        data-bs-toggle="pill" 
                        data-bs-target="#tab-lead" 
                        type="button" 
                        role="tab" 
                        aria-controls="tab-lead" 
                        aria-selected="true">
                    <i class="bi bi-person-fill-gear"></i>
                    <span>1. Lead Researches</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">{{ $leadProposals->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-2.5 px-3 rounded-pill fw-bold d-inline-flex align-items-center justify-content-center gap-2 w-100" 
                        id="tab-collab-pill" 
                        data-bs-toggle="pill" 
                        data-bs-target="#tab-collab" 
                        type="button" 
                        role="tab" 
                        aria-controls="tab-collab" 
                        aria-selected="false">
                    <i class="bi bi-people-fill"></i>
                    <span>2. Collaborations</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">{{ $collaboratedProposals->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-2.5 px-3 rounded-pill fw-bold d-inline-flex align-items-center justify-content-center gap-2 w-100" 
                        id="tab-gantt-pill" 
                        data-bs-toggle="pill" 
                        data-bs-target="#tab-gantt" 
                        type="button" 
                        role="tab" 
                        aria-controls="tab-gantt" 
                        aria-selected="false">
                    <i class="bi bi-bar-chart-steps"></i>
                    <span>3. Work Plans &amp; Schedule</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">{{ $allMilestones->count() }}</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Contents Container -->
    <div class="tab-content" id="researchesNavTabsContent">
        <!-- ========================================== -->
        <!-- TAB 1: Lead Researches                     -->
        <!-- ========================================== -->
        <div class="tab-pane fade show active" id="tab-lead" role="tabpanel" aria-labelledby="tab-lead-pill">
            
            <!-- Real-Time Search & Status Filter Control Card -->
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4 bg-white">
                <div class="row g-2 g-md-3 align-items-center">
                    <!-- Live Search Box -->
                    <div class="col-12 col-md-5 col-lg-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                   id="proposalSearchInput" 
                                   class="form-control bg-light border-start-0 rounded-end-pill py-2 text-dark" 
                                   placeholder="Search title, RP code (e.g. RP-2026-022), or field..."
                                   onkeyup="filterProposals()">
                        </div>
                    </div>

                    <!-- Status Filter Dropdown -->
                    <div class="col-6 col-md-3 col-lg-3">
                        <select id="proposalStatusSelect" class="form-select bg-light rounded-pill py-2 text-dark fw-semibold" onchange="filterProposals()">
                            <option value="all">All Statuses ({{ $totalCount }})</option>
                            <option value="approved">Approved / Final Approved</option>
                            <option value="accepted_for_in_house_review">Accepted for In-House Review</option>
                            <option value="funds_certified">Funds Certified</option>
                            <option value="endorsed">Endorsed / Noted</option>
                            <option value="pending">Pending Endorsement / Review</option>
                            <option value="returned">Returned for Revision</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>

                    <!-- Phase Filter Dropdown -->
                    <div class="col-6 col-md-2 col-lg-2">
                        <select id="proposalPhaseSelect" class="form-select bg-light rounded-pill py-2 text-dark fw-semibold" onchange="filterProposals()">
                            <option value="all">All Phases</option>
                            <option value="1">Phase 1: Review</option>
                            <option value="2">Phase 2: Implementation</option>
                            <option value="3">Phase 3: Dissemination</option>
                            <option value="4">Phase 4: Publication</option>
                            <option value="5">Phase 5: Forum</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-12 col-md-2 col-lg-2 text-md-end">
                        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-1.5" onclick="resetFilters()">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- One-Click Quick Filter Pills Row -->
                <div class="d-flex align-items-center gap-1.5 flex-wrap mt-3 pt-3 border-top">
                    <small class="text-muted fw-bold text-uppercase me-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Quick Filter:</small>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small fw-semibold quick-filter-btn active" data-filter="all" onclick="applyQuickFilter('all')">
                        All ({{ $totalCount }})
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small fw-semibold quick-filter-btn" data-filter="approved" onclick="applyQuickFilter('approved')">
                        <i class="bi bi-check-circle-fill text-success me-1"></i> Approved ({{ $approvedCount }})
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small fw-semibold quick-filter-btn" data-filter="accepted_for_in_house_review" onclick="applyQuickFilter('accepted_for_in_house_review')">
                        <i class="bi bi-card-checklist text-primary me-1"></i> In-House Review
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small fw-semibold quick-filter-btn" data-filter="funds_certified" onclick="applyQuickFilter('funds_certified')">
                        <i class="bi bi-cash-stack text-warning me-1"></i> Funds Certified
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small fw-semibold quick-filter-btn" data-filter="pending" onclick="applyQuickFilter('pending')">
                        <i class="bi bi-clock-history text-secondary me-1"></i> Pending Routing
                    </button>
                    @if($revisionCount > 0)
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small fw-semibold quick-filter-btn" data-filter="returned" onclick="applyQuickFilter('returned')">
                        <i class="bi bi-arrow-return-left text-danger me-1"></i> Revisions ({{ $revisionCount }})
                    </button>
                    @endif
                    
                    <div class="ms-auto text-muted small" id="filterResultCount">
                        Showing <strong class="text-dark">{{ $totalCount }}</strong> of {{ $totalCount }} researches
                    </div>
                </div>
            </div>

            <!-- Empty Results Notice (Shown dynamically when filters yield zero rows) -->
            <div id="filterNoResults" class="card border-0 shadow-sm rounded-4 p-5 text-center bg-light mb-4 d-none">
                <i class="bi bi-funnel-fill text-muted display-6 d-block mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">No Researches Found</h6>
                <p class="text-muted small mb-3">No lead research proposals match your search or filter criteria.</p>
                <div>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-semibold" onclick="resetFilters()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Filters
                    </button>
                </div>
            </div>

            <!-- Mobile Card View: As Lead (d-md-none) -->
            <div class="d-flex flex-column gap-3 d-md-none mb-4" id="leadProposalsMobileList">
                @forelse($leadProposals as $proposal)
                    @php
                        $statusClass = match($proposal->status) {
                            'approved', 'final_approved', 'completed' => 'bg-success text-success border-success',
                            'pending', 'pending_coordinator_endorsement' => 'bg-warning text-dark border-warning',
                            'funds_certified' => 'bg-purple text-purple border-purple',
                            'rejected', 'final_rejected' => 'bg-danger text-danger border-danger',
                            'revision_required', 'returned_for_revision' => 'bg-danger text-danger border-danger',
                            default => 'bg-secondary text-secondary border-secondary'
                        };
                    @endphp
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden proposal-item-card"
                         data-title="{{ strtolower($proposal->title) }}"
                         data-code="{{ strtolower($proposal->proposal_code ?? '') }}"
                         data-field="{{ strtolower($proposal->research_field ?? '') }}"
                         data-status="{{ strtolower($proposal->status) }}"
                         data-phase="{{ $proposal->current_phase }}">
                        <div class="card-body p-3">
                            <!-- Top Badges Row -->
                            <div class="d-flex justify-content-between align-items-center gap-1.5 mb-2">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill fw-bold flex-shrink-0" style="font-size: 0.72rem;">
                                    {{ $proposal->proposal_code ?? 'NO CODE' }}
                                </span>
                                <span class="badge {{ $statusClass }} bg-opacity-10 border border-opacity-25 px-2 py-1 rounded-pill fw-bold text-truncate" style="font-size: 0.7rem; max-width: 60%;" title="{{ strtoupper(str_replace('_', ' ', $proposal->status)) }}">
                                    {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                                </span>
                            </div>

                            <!-- Title & Field -->
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem; line-height: 1.4;">{{ $proposal->title }}</h6>
                            @if($proposal->research_field)
                                <div class="text-muted small mb-2" style="font-size: 0.78rem;">
                                    <i class="bi bi-bookmark-fill me-1 text-primary"></i>{{ $proposal->research_field }}
                                </div>
                            @endif

                            @if($proposal->status === 'returned_for_revision' && $proposal->return_reason)
                                <div class="p-2 mb-2 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 text-danger small" style="font-size: 0.76rem;">
                                    <i class="bi bi-arrow-return-left me-1 fw-bold"></i><strong>Returned:</strong> {{ \Illuminate\Support\Str::limit($proposal->return_reason, 90) }}
                                </div>
                            @endif

                            <!-- Collaborators -->
                            <div class="mb-2">
                                <small class="text-muted fw-semibold d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;">COLLABORATORS:</small>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($proposal->collaborators as $collab)
                                        <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                                            <i class="bi bi-person me-0.5"></i>{{ $collab->name }}
                                        </span>
                                    @empty
                                        <span class="text-muted small fst-italic" style="font-size: 0.75rem;">None</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Phase Progress Indicator -->
                            <div class="p-2 bg-light rounded-3 mb-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="text-muted fw-semibold" style="font-size: 0.74rem;">Lifecycle Progress</span>
                                    <span class="fw-bold text-primary" style="font-size: 0.74rem;">Phase {{ $proposal->current_phase }} of 5</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 6px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($proposal->current_phase / 5) * 100 }}%"></div>
                                </div>
                            </div>

                            <!-- Action Bar -->
                            <div class="d-flex align-items-center gap-2 pt-2 border-top">
                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-primary btn-sm flex-grow-1 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5 py-1.5" style="font-size: 0.82rem;">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                                @if(in_array($proposal->status, ['revision_required', 'draft', 'returned_for_revision']))
                                    <a href="{{ route('proposal.edit', $proposal->id) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-3 py-1.5" style="font-size: 0.82rem;" title="Edit Proposal">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                                @if(in_array($proposal->status, ['draft', 'pending_coordinator_endorsement', 'returned_for_revision', 'pending']))
                                    <form action="{{ route('proposal.destroy', $proposal->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this proposal? This action cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1.5" style="font-size: 0.82rem;" title="Delete Proposal">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-light">
                        <i class="bi bi-folder-x text-muted display-6 d-block mb-2"></i>
                        <h6 class="fw-bold text-muted mb-1">No Lead Researches Yet</h6>
                        <p class="text-muted small mb-0">You haven't submitted any research proposals as lead author.</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table: As Lead (d-none d-md-block) -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-none d-md-block mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="leadProposalsTable">
                        <thead class="bg-primary bg-opacity-10">
                            <tr>
                                <th class="ps-4 py-3 small fw-bold text-primary border-0" style="letter-spacing: 0.04em;">PROPOSAL CODE</th>
                                <th class="py-3 small fw-bold text-primary border-0" style="letter-spacing: 0.04em;">TITLE &amp; FIELD</th>
                                <th class="py-3 small fw-bold text-primary border-0" style="letter-spacing: 0.04em;">COLLABORATORS</th>
                                <th class="py-3 small fw-bold text-primary border-0" style="letter-spacing: 0.04em;">STATUS</th>
                                <th class="py-3 small fw-bold text-primary border-0" style="letter-spacing: 0.04em;">CURRENT PHASE</th>
                                <th class="pe-4 py-3 small fw-bold text-primary border-0 text-end" style="letter-spacing: 0.04em;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leadProposals as $proposal)
                                @php
                                    $statusClass = match($proposal->status) {
                                        'approved', 'final_approved', 'completed' => 'bg-success text-success',
                                        'pending', 'pending_coordinator_endorsement' => 'bg-warning text-warning',
                                        'funds_certified' => 'bg-purple text-purple',
                                        'rejected', 'final_rejected' => 'bg-danger text-danger',
                                        'revision_required', 'returned_for_revision' => 'bg-danger text-danger',
                                        default => 'bg-secondary text-secondary'
                                    };
                                @endphp
                                <tr class="proposal-item-row"
                                    data-title="{{ strtolower($proposal->title) }}"
                                    data-code="{{ strtolower($proposal->proposal_code ?? '') }}"
                                    data-field="{{ strtolower($proposal->research_field ?? '') }}"
                                    data-status="{{ strtolower($proposal->status) }}"
                                    data-phase="{{ $proposal->current_phase }}">
                                    <td class="ps-4 py-3">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 font-monospace" style="font-size: 0.72rem;">
                                            {{ $proposal->proposal_code ?? 'NO TAG' }}
                                        </span>
                                    </td>
                                    <td class="py-3" style="max-width: 320px;">
                                        <div class="fw-bold text-dark mb-0.5 text-break" style="font-size: 0.92rem;">{{ $proposal->title }}</div>
                                        <div class="text-muted small" style="font-size: 0.78rem;">
                                            <i class="bi bi-bookmark-fill me-1 text-primary"></i>{{ $proposal->research_field ?? 'General Research' }}
                                        </div>
                                        @if($proposal->status === 'returned_for_revision' && $proposal->return_reason)
                                            <div class="mt-1 text-danger small p-1.5 bg-danger bg-opacity-10 rounded border border-danger border-opacity-25" style="font-size: 0.74rem;">
                                                <i class="bi bi-arrow-return-left me-1"></i><span class="fw-semibold">Remarks:</span> {{ \Illuminate\Support\Str::limit($proposal->return_reason, 75) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @forelse($proposal->collaborators as $collab)
                                                <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" style="font-size: 0.68rem;">{{ $collab->name }}</span>
                                            @empty
                                                <span class="text-muted small fst-italic" style="font-size: 0.75rem;">None</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClass }} bg-opacity-10 px-3 py-2 rounded-pill small fw-bold">
                                            {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                                        </span>
                                    </td>
                                    <td style="min-width: 140px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1 rounded-pill" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($proposal->current_phase / 5) * 100 }}%"></div>
                                            </div>
                                            <span class="small fw-bold text-dark text-nowrap" style="font-size: 0.76rem;">Ph {{ $proposal->current_phase }}</span>
                                        </div>
                                    </td>
                                    <td class="pe-4 text-end text-nowrap">
                                        <!-- Primary Direct Action -->
                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold shadow-xs d-inline-flex align-items-center gap-1" style="font-size: 0.8rem;">
                                            <i class="bi bi-eye"></i> View
                                        </a>

                                        <!-- Extra Options Dropdown -->
                                        @if(in_array($proposal->status, ['revision_required', 'draft', 'returned_for_revision', 'pending_coordinator_endorsement', 'pending']))
                                        <div class="dropdown d-inline-block ms-1">
                                            <button class="btn btn-sm btn-light border-0 rounded-circle p-1" type="button" data-bs-toggle="dropdown" style="width: 28px; height: 28px;">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                                @if(in_array($proposal->status, ['revision_required', 'draft', 'returned_for_revision']))
                                                    <li><a class="dropdown-item py-2 text-primary" href="{{ route('proposal.edit', $proposal->id) }}"><i class="bi bi-pencil-square me-2"></i> Edit Proposal</a></li>
                                                @endif
                                                @if(in_array($proposal->status, ['draft', 'pending_coordinator_endorsement', 'returned_for_revision', 'pending']))
                                                    <li>
                                                        <form action="{{ route('proposal.destroy', $proposal->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this proposal? This action cannot be undone.')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-trash me-2"></i> Delete Proposal</button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted small fst-italic">You haven't submitted any research proposals as lead.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div> <!-- End of #tab-lead -->

        <!-- ========================================== -->
        <!-- TAB 2: Collaborations                      -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="tab-collab" role="tabpanel" aria-labelledby="tab-collab-pill">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <span>Research Collaborations</span>
                    <span class="badge bg-info bg-opacity-10 text-dark rounded-pill px-2.5 py-1 small fw-semibold" style="color: #0284C7 !important; font-size: 0.78rem;">
                        As Collaborator ({{ $collaboratedProposals->count() }})
                    </span>
                </h5>
            </div>

            <!-- Mobile Card View: As Collaborator (d-md-none) -->
            <div class="d-flex flex-column gap-3 d-md-none mb-4">
                @forelse($collaboratedProposals as $proposal)
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center gap-1.5 mb-2">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill fw-bold flex-shrink-0" style="font-size: 0.72rem;">
                                    {{ $proposal->proposal_code ?? 'NO CODE' }}
                                </span>
                                <span class="badge {{ 
                                    $proposal->status == 'pending' ? 'bg-warning text-dark border-warning' : 
                                    (str_contains($proposal->status, 'approved') ? 'bg-success text-success border-success' : 'bg-danger text-danger border-danger') 
                                }} bg-opacity-10 border border-opacity-25 px-2 py-1 rounded-pill fw-bold text-truncate" style="font-size: 0.7rem; max-width: 60%;" title="{{ strtoupper(str_replace('_', ' ', $proposal->status)) }}">
                                    {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                                </span>
                            </div>

                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem; line-height: 1.4;">{{ $proposal->title }}</h6>
                            @if($proposal->research_field)
                                <div class="text-muted small mb-2" style="font-size: 0.78rem;">
                                    <i class="bi bi-bookmark-fill me-1 text-info"></i>{{ $proposal->research_field }}
                                </div>
                            @endif

                            <!-- Lead Researcher Row -->
                            <div class="d-flex align-items-center gap-2 mb-2 p-2 bg-light rounded-3 border">
                                <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    {{ strtoupper(substr($proposal->user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0" style="min-width: 0;">
                                    <small class="text-muted d-block" style="font-size: 0.68rem; line-height: 1.1;">LEAD RESEARCHER</small>
                                    <span class="fw-bold text-dark small text-truncate d-block" style="font-size: 0.8rem;">{{ $proposal->user->name }}</span>
                                </div>
                            </div>

                            <!-- Phase Progress Indicator -->
                            <div class="p-2 bg-light rounded-3 mb-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="text-muted fw-semibold" style="font-size: 0.74rem;">Current Phase</span>
                                    <span class="fw-bold text-info" style="font-size: 0.74rem;">Phase {{ $proposal->current_phase }} of 5</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 6px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($proposal->current_phase / 5) * 100 }}%"></div>
                                </div>
                            </div>

                            <div class="pt-2 border-top">
                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5 py-1.5" style="font-size: 0.82rem;">
                                    <i class="bi bi-eye"></i> View Proposal Details
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-light">
                        <i class="bi bi-people text-muted display-6 d-block mb-2"></i>
                        <h6 class="fw-bold text-muted mb-1">No Collaborations Found</h6>
                        <p class="text-muted small mb-0">You are not listed as a co-researcher or collaborator in any external projects yet.</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table: As Collaborator (d-none d-md-block) -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-info bg-opacity-10">
                            <tr>
                                <th class="ps-4 py-3 small fw-bold text-info border-0">PROPOSAL CODE</th>
                                <th class="py-3 small fw-bold text-info border-0">TITLE</th>
                                <th class="py-3 small fw-bold text-info border-0">LEAD RESEARCHER</th>
                                <th class="py-3 small fw-bold text-info border-0">COLLABORATORS</th>
                                <th class="py-3 small fw-bold text-info border-0">STATUS</th>
                                <th class="pe-4 py-3 small fw-bold text-info border-0 text-end">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($collaboratedProposals as $proposal)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 font-monospace" style="font-size: 0.72rem;">{{ $proposal->proposal_code ?? 'NO TAG' }}</span>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-bold text-dark mb-1">{{ $proposal->title }}</div>
                                        <div class="text-muted small">{{ $proposal->research_field }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                {{ strtoupper(substr($proposal->user->name, 0, 1)) }}
                                            </div>
                                            <div class="small fw-bold text-dark">{{ $proposal->user->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($proposal->collaborators as $collab)
                                                @if($collab->id !== auth()->id())
                                                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" style="font-size: 0.68rem;">{{ $collab->name }}</span>
                                                @else
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border px-2 py-1 rounded-pill" style="font-size: 0.68rem;">You</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ 
                                            $proposal->status == 'pending' ? 'bg-warning text-warning' : 
                                            (str_contains($proposal->status, 'approved') ? 'bg-success text-success' : 'bg-danger text-danger') 
                                        }} bg-opacity-10 px-3 py-2 rounded-pill small fw-bold">
                                            {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold shadow-xs">
                                            <i class="bi bi-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted small fst-italic">You are not listed as a collaborator in any research project.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- End of #tab-collab -->

        <!-- ========================================== -->
        <!-- TAB 3: Work Plans & Gantt Chart            -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="tab-gantt" role="tabpanel" aria-labelledby="tab-gantt-pill">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <span>Project Work Plans</span>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 small fw-semibold" style="font-size: 0.78rem;">Gantt Chart Overview</span>
                    </h5>
                    <p class="text-muted small mb-0 mt-0.5">Overview of scheduled milestone activities across your research projects</p>
                </div>
            </div>

            @if($allMilestones->isNotEmpty())
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4">
                    <!-- Compact 3-Column Milestone Stats Grid -->
                    <div class="row g-2 g-md-3 mb-3 mb-md-4">
                        <div class="col-4">
                            <div class="p-2 p-md-3 bg-light rounded-3 text-center text-sm-start d-flex flex-column flex-sm-row align-items-center gap-1.5 gap-md-3 border">
                                <div class="bg-primary bg-opacity-10 text-primary p-1.5 p-md-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">
                                    <i class="bi bi-kanban fs-6"></i>
                                </div>
                                <div class="min-w-0" style="min-width: 0;">
                                    <div class="h5 h-md-4 fw-bold mb-0 text-dark lh-1">{{ $allMilestones->count() }}</div>
                                    <div class="text-muted small text-truncate" style="font-size: 0.7rem;">Total</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 p-md-3 bg-light rounded-3 text-center text-sm-start d-flex flex-column flex-sm-row align-items-center gap-1.5 gap-md-3 border">
                                <div class="bg-success bg-opacity-10 text-success p-1.5 p-md-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">
                                    <i class="bi bi-check-circle fs-6"></i>
                                </div>
                                <div class="min-w-0" style="min-width: 0;">
                                    <div class="h5 h-md-4 fw-bold mb-0 text-dark lh-1">{{ $allMilestones->where('status', 'approved')->count() }}</div>
                                    <div class="text-muted small text-truncate" style="font-size: 0.7rem;">Approved</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 p-md-3 bg-light rounded-3 text-center text-sm-start d-flex flex-column flex-sm-row align-items-center gap-1.5 gap-md-3 border">
                                <div class="bg-warning bg-opacity-10 text-warning p-1.5 p-md-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">
                                    <i class="bi bi-hourglass-split fs-6"></i>
                                </div>
                                <div class="min-w-0" style="min-width: 0;">
                                    <div class="h5 h-md-4 fw-bold mb-0 text-dark lh-1">{{ $allMilestones->where('status', 'pending')->count() }}</div>
                                    <div class="text-muted small text-truncate" style="font-size: 0.7rem;">Pending</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Milestone List -->
                    <div class="list-group list-group-flush">
                        @foreach($allMilestones->sortByDesc('created_at')->take(6) as $milestone)
                            <div class="list-group-item px-0 py-3 border-bottom-0 border-top">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                    <div class="min-w-0" style="min-width: 0;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;">
                                            {{ $milestone->title }}
                                        </div>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5 rounded-pill small" style="font-size: 0.72rem;">
                                            {{ $milestone->proposal_title }}
                                        </span>
                                    </div>
                                    <a href="{{ route('proposal.show', $milestone->proposal_id) }}#tab-budget" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm text-nowrap d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                        View Gantt <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>

                                <div class="d-flex justify-content-between align-items-center small text-muted mb-1.5" style="font-size: 0.75rem;">
                                    @if($milestone->start_date && $milestone->target_date)
                                        <span><i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($milestone->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($milestone->target_date)->format('M d, Y') }}</span>
                                    @else
                                        <span><i class="bi bi-clock me-1"></i> Timeline unset</span>
                                    @endif
                                    <span class="fw-semibold {{ $milestone->status == 'approved' ? 'text-success' : 'text-warning' }}">
                                        {{ $milestone->status == 'approved' ? '100% Completed' : 'In Progress' }}
                                    </span>
                                </div>

                                <div class="progress rounded-pill" style="height: 6px;">
                                    <div class="progress-bar {{ $milestone->status == 'approved' ? 'bg-success' : 'bg-warning' }}" 
                                         role="progressbar" 
                                         style="width: {{ $milestone->status == 'approved' ? '100%' : '50%' }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-light">
                    <i class="bi bi-calendar-week text-muted display-6 d-block mb-2"></i>
                    <h6 class="fw-bold text-muted mb-1">No Work Plan Scheduled Yet</h6>
                    <p class="text-muted small mb-0">Open any research proposal details to add milestone target dates and start building your Work Plan.</p>
                </div>
            @endif
        </div> <!-- End of #tab-gantt -->

    </div> <!-- End of #researchesNavTabsContent -->

    <!-- Instant Filter & Live Search Scripts -->
    <script>
        function filterProposals() {
            const searchInput = document.getElementById('proposalSearchInput');
            const searchVal = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const statusSelect = document.getElementById('proposalStatusSelect');
            const statusVal = statusSelect ? statusSelect.value : 'all';
            const phaseSelect = document.getElementById('proposalPhaseSelect');
            const phaseVal = phaseSelect ? phaseSelect.value : 'all';

            const rows = document.querySelectorAll('.proposal-item-row');
            const cards = document.querySelectorAll('.proposal-item-card');
            let visibleCount = 0;

            function matches(item) {
                const title = (item.getAttribute('data-title') || '').toLowerCase();
                const code = (item.getAttribute('data-code') || '').toLowerCase();
                const field = (item.getAttribute('data-field') || '').toLowerCase();
                const status = (item.getAttribute('data-status') || '').toLowerCase();
                const phase = item.getAttribute('data-phase') || '';

                // Search matching
                const matchesSearch = !searchVal || title.includes(searchVal) || code.includes(searchVal) || field.includes(searchVal);

                // Status matching
                let matchesStatus = (statusVal === 'all');
                if (!matchesStatus) {
                    if (statusVal === 'approved') {
                        matchesStatus = ['approved', 'final_approved', 'completed', 'ongoing'].includes(status);
                    } else if (statusVal === 'returned') {
                        matchesStatus = ['returned_for_revision', 'revision_required'].includes(status);
                    } else if (statusVal === 'pending') {
                        matchesStatus = ['pending', 'pending_coordinator_endorsement', 'pending_dean_noting', 'submitted_to_research_unit', 'pending_director_review', 'under_review'].includes(status);
                    } else if (statusVal === 'endorsed') {
                        matchesStatus = ['endorsed', 'endorsed_by_coordinator', 'noted_by_dean', 'endorsed_to_vprei', 'final_copy_noted_by_dean'].includes(status);
                    } else {
                        matchesStatus = status.includes(statusVal);
                    }
                }

                // Phase matching
                const matchesPhase = (phaseVal === 'all') || (phase === phaseVal);

                return matchesSearch && matchesStatus && matchesPhase;
            }

            rows.forEach(row => {
                if (matches(row)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            cards.forEach(card => {
                if (matches(card)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            // Update counter text
            const countEl = document.getElementById('filterResultCount');
            const totalCount = rows.length;
            if (countEl) {
                countEl.innerHTML = `Showing <strong class="text-dark">${visibleCount}</strong> of ${totalCount} researches`;
            }

            // Show empty state if 0 results
            const emptyNotice = document.getElementById('filterNoResults');
            if (emptyNotice) {
                if (visibleCount === 0 && totalCount > 0) {
                    emptyNotice.classList.remove('d-none');
                } else {
                    emptyNotice.classList.add('d-none');
                }
            }
        }

        function applyQuickFilter(statusKey) {
            // If we are on another tab, switch to Lead tab first
            const leadTabPill = document.getElementById('tab-lead-pill');
            if (leadTabPill && !leadTabPill.classList.contains('active')) {
                bootstrap.Tab.getOrCreateInstance(leadTabPill).show();
            }

            const statusSelect = document.getElementById('proposalStatusSelect');
            if (statusSelect) {
                statusSelect.value = statusKey;
            }

            // Highlight quick filter pill
            document.querySelectorAll('.quick-filter-btn').forEach(btn => {
                if (btn.getAttribute('data-filter') === statusKey) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Highlight KPI cards
            document.querySelectorAll('.kpi-card-clickable').forEach(card => {
                card.classList.remove('active-kpi');
            });
            const matchingKpi = document.getElementById('kpi-' + statusKey);
            if (matchingKpi) {
                matchingKpi.classList.add('active-kpi');
            }

            filterProposals();
        }

        function resetFilters() {
            const searchInput = document.getElementById('proposalSearchInput');
            if (searchInput) searchInput.value = '';
            
            const phaseSelect = document.getElementById('proposalPhaseSelect');
            if (phaseSelect) phaseSelect.value = 'all';

            applyQuickFilter('all');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Handle URL hashes for tab persistence
            const hash = window.location.hash;
            if (hash) {
                const targetPill = document.querySelector(`button[data-bs-target="${hash}"]`) ||
                                   document.querySelector(`button#${hash.replace('#', '')}-pill`);
                if (targetPill) {
                    bootstrap.Tab.getOrCreateInstance(targetPill).show();
                }
            }

            // Sync URL hash on tab change without scrolling
            const tabButtons = document.querySelectorAll('#researchesNavTabs button[data-bs-toggle="pill"]');
            tabButtons.forEach(btn => {
                btn.addEventListener('shown.bs.tab', function(e) {
                    const target = e.target.getAttribute('data-bs-target');
                    if (history.replaceState) {
                        history.replaceState(null, null, target);
                    }
                });
            });
        });
    </script>
</x-app-layout>