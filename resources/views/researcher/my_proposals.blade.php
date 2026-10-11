<x-app-layout>
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

    <!-- Section 1: Researches / As Lead -->
    <div class="mb-4 mb-md-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span>Researches</span>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 small fw-semibold" style="font-size: 0.78rem;">
                    As Lead ({{ $leadProposals->count() }})
                </span>
            </h5>
        </div>

        <!-- Mobile Card View: As Lead (d-md-none) -->
        <div class="d-flex flex-column gap-3 d-md-none mb-4">
            @forelse($leadProposals as $proposal)
                @php
                    $statusClass = match($proposal->status) {
                        'approved', 'final_approved' => 'bg-success text-success border-success',
                        'pending' => 'bg-warning text-dark border-warning',
                        'rejected', 'final_rejected' => 'bg-danger text-danger border-danger',
                        'revision_required' => 'bg-info text-dark border-info',
                        default => 'bg-secondary text-secondary border-secondary'
                    };
                @endphp
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
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
                            @if($proposal->status === 'pending')
                                <form action="{{ route('proposal.destroy', $proposal->id) }}" method="POST" class="m-0" onsubmit="return confirm('Cancel this submission?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1.5" style="font-size: 0.82rem;" title="Cancel Submission">
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
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-none d-md-block">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-primary bg-opacity-10">
                        <tr>
                            <th class="ps-4 py-3 small fw-bold text-primary border-0">PROPOSAL CODE</th>
                            <th class="py-3 small fw-bold text-primary border-0">TITLE</th>
                            <th class="py-3 small fw-bold text-primary border-0">COLLABORATORS</th>
                            <th class="py-3 small fw-bold text-primary border-0">STATUS</th>
                            <th class="py-3 small fw-bold text-primary border-0">CURRENT PHASE</th>
                            <th class="pe-4 py-3 small fw-bold text-primary border-0 text-end">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leadProposals as $proposal)
                            <tr>
                                <td class="ps-4 py-3">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.65rem;">{{ $proposal->proposal_code ?? 'NO TAG' }}</span>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-dark mb-1">{{ $proposal->title }}</div>
                                    <div class="text-muted small italic">{{ $proposal->research_field }}</div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($proposal->collaborators as $collab)
                                            <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.65rem;">{{ $collab->name }}</span>
                                        @empty
                                            <span class="text-muted small italic">None</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($proposal->status) {
                                            'approved', 'final_approved' => 'bg-success text-success',
                                            'pending' => 'bg-warning text-warning',
                                            'rejected', 'final_rejected' => 'bg-danger text-danger',
                                            'revision_required' => 'bg-info text-info',
                                            default => 'bg-secondary text-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} bg-opacity-10 px-3 py-2 rounded-pill small fw-bold">
                                        {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($proposal->current_phase / 5) * 100 }}%"></div>
                                        </div>
                                        <span class="small fw-bold text-muted">Ph {{ $proposal->current_phase }}</span>
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                            <li><a class="dropdown-item py-2" href="{{ route('proposal.show', $proposal->id) }}"><i class="bi bi-eye me-2"></i> View Details</a></li>
                                            @if(in_array($proposal->status, ['revision_required', 'draft', 'returned_for_revision']))
                                                <li><a class="dropdown-item py-2 text-primary" href="{{ route('proposal.edit', $proposal->id) }}"><i class="bi bi-pencil-square me-2"></i> Edit Proposal</a></li>
                                            @endif
                                            @if($proposal->status === 'pending')
                                                <li>
                                                    <form action="{{ route('proposal.destroy', $proposal->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this submission?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-x-circle me-2"></i> Cancel Submission</button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted small italic">You haven't submitted any research proposals as lead.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 2: Researches / As Collaborator -->
    <div class="mb-4 mb-md-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span>Researches</span>
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
                            <th class="pe-4 py-3 small fw-bold text-info border-0 text-end">CURRENT PHASE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($collaboratedProposals as $proposal)
                            <tr>
                                <td class="ps-4 py-3">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.65rem;">{{ $proposal->proposal_code ?? 'NO TAG' }}</span>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-dark mb-1">{{ $proposal->title }}</div>
                                    <div class="text-muted small italic">{{ $proposal->research_field }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                            {{ strtoupper(substr($proposal->user->name, 0, 1)) }}
                                        </div>
                                        <div class="small fw-bold">{{ $proposal->user->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($proposal->collaborators as $collab)
                                            @if($collab->id !== auth()->id())
                                                <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.65rem;">{{ $collab->name }}</span>
                                            @else
                                                <span class="badge bg-primary bg-opacity-10 text-primary border px-2 py-1" style="font-size: 0.65rem;">You</span>
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
                                    <span class="fw-bold text-muted small">Phase {{ $proposal->current_phase }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted small italic">You are not listed as a collaborator in any research project.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 3: Work Plan & Gantt Chart Overview Section -->
    <div class="mt-4 mt-md-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <span>Project Work Plans</span>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 small fw-semibold" style="font-size: 0.78rem;">Gantt Chart Overview</span>
                </h5>
                <p class="text-muted small mb-0 mt-0.5">Overview of scheduled milestone activities across your research projects</p>
            </div>
        </div>

        @php
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
                    @foreach($allMilestones->sortByDesc('created_at')->take(5) as $milestone)
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
                                <a href="{{ route('proposal.show', $milestone->proposal_id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm text-nowrap d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
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
    </div>
</x-app-layout>