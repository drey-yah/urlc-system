<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 mb-md-4 gap-2 gap-md-3">
        <div>
            <h1 class="h4 h-md-3 fw-bold text-dark mb-1">Research Dashboard</h1>
            <p class="text-muted small mb-0">Track your research proposals, monitor review progress, and submit new research.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('proposal.create') }}" class="btn btn-sm btn-md-md btn-primary d-flex align-items-center gap-2 shadow-sm rounded-3 py-2 px-3">
                <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Submit New Proposal</span><span class="d-sm-none">Submit</span>
            </a>
            <a href="{{ route('proposal.index') }}" class="btn btn-sm btn-md-md btn-outline-secondary d-flex align-items-center gap-2 shadow-sm rounded-3 py-2 px-3">
                <i class="bi bi-folder2-open"></i> <span class="d-none d-sm-inline">My Proposals</span><span class="d-sm-none">Proposals</span>
            </a>
        </div>
    </div>

    <!-- Stats Grid (2x2 on Mobile, 4-col on Desktop) -->
    <div class="row g-2 g-md-3 g-xl-4 mb-3 mb-md-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 mb-md-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 p-md-3">
                            <i class="bi bi-journal-album fs-5 fs-md-4 mb-0"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 px-md-3 py-md-2 fw-medium" style="font-size: 0.7rem;">All Time</span>
                    </div>
                    <h2 class="fw-bold mb-0 mb-md-1 fs-3 fs-md-2">{{ $stats['total'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.75rem;">Total Submissions</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 mb-md-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 p-md-3">
                            <i class="bi bi-hourglass-split fs-5 fs-md-4 mb-0"></i>
                        </div>
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 px-md-3 py-md-2 fw-medium" style="font-size: 0.7rem;">In Progress</span>
                    </div>
                    <h2 class="fw-bold mb-0 mb-md-1 fs-3 fs-md-2">{{ $stats['under_review'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.75rem;">Under Review</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 mb-md-3">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 p-md-3">
                            <i class="bi bi-exclamation-circle fs-5 fs-md-4 mb-0"></i>
                        </div>
                        @if(($stats['action_required'] ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill px-2 py-1 px-md-3 py-md-2 fw-medium" style="font-size: 0.7rem;">Action Req</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-2 py-1 px-md-3 py-md-2 fw-medium" style="font-size: 0.7rem;">Clear</span>
                        @endif
                    </div>
                    <h2 class="fw-bold mb-0 mb-md-1 fs-3 fs-md-2">{{ $stats['action_required'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.75rem;">Revisions Needed</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 mb-md-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 p-md-3">
                            <i class="bi bi-patch-check fs-5 fs-md-4 mb-0"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 px-md-3 py-md-2 fw-medium" style="font-size: 0.7rem;">Active</span>
                    </div>
                    <h2 class="fw-bold mb-0 mb-md-1 fs-3 fs-md-2">{{ $stats['approved'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.75rem;">Approved Research</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- My Submissions Overview (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-3 gap-md-4">
                <!-- Recent Proposals Card -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-2 py-md-3 px-3 px-md-4 border-0 rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark fs-6 fs-md-5">My Active Proposals</h5>
                        <a href="{{ route('proposal.index') }}" class="small fw-semibold text-primary text-decoration-none">View All <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <!-- Desktop Table View -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0 border-0">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="ps-4 py-3 fw-semibold border-0">Proposal Code</th>
                                        <th class="py-3 fw-semibold border-0">Title</th>
                                        <th class="py-3 fw-semibold border-0">Role</th>
                                        <th class="py-3 fw-semibold border-0">Status</th>
                                        <th class="pe-4 py-3 fw-semibold border-0 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @forelse($recentProposals as $proposal)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <span class="badge bg-light text-dark border fw-mono fs-7">{{ $proposal->proposal_code ?? 'P-'.$proposal->id }}</span>
                                            </td>
                                            <td class="py-3">
                                                <div class="fw-semibold text-dark text-truncate" style="max-width: 230px;" title="{{ $proposal->title }}">
                                                    {{ $proposal->title }}
                                                </div>
                                                <div class="text-muted fs-7">Phase {{ $proposal->current_phase ?? 1 }}</div>
                                            </td>
                                            <td class="py-3">
                                                @if($proposal->user_id === Auth::id())
                                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 fs-7">Lead</span>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1 fs-7">Co-Author</span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                @php
                                                    $statusClass = match($proposal->status) {
                                                        'approved', 'final_approved', 'completed' => 'bg-success bg-opacity-10 text-success',
                                                        'revision_required', 'returned_for_revision', 'approved_with_revisions' => 'bg-danger bg-opacity-10 text-danger',
                                                        'pending', 'submitted', 'under_review' => 'bg-warning bg-opacity-10 text-warning',
                                                        'draft' => 'bg-secondary bg-opacity-10 text-secondary',
                                                        default => 'bg-info bg-opacity-10 text-info'
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }} rounded-pill px-3 py-1 fw-medium text-capitalize">
                                                    {{ str_replace('_', ' ', $proposal->status) }}
                                                </span>
                                            </td>
                                            <td class="pe-4 py-3 text-end">
                                                @if(in_array($proposal->status, ['revision_required', 'returned_for_revision', 'approved_with_revisions', 'draft']) && $proposal->user_id === Auth::id())
                                                    <a href="{{ route('proposal.edit', $proposal->id) }}" class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-medium shadow-sm">
                                                        <i class="bi bi-pencil-square me-1"></i> Revise
                                                    </a>
                                                @else
                                                    <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                        View <i class="bi bi-arrow-right ms-1"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="bi bi-journal-plus fs-2 d-block mb-2 text-secondary"></i>
                                                <p class="mb-2 fw-medium">No research proposals submitted yet.</p>
                                                <a href="{{ route('proposal.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                                    Create Proposal Now
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile List Cards View (Zero Horizontal Clipping) -->
                        <div class="d-md-none p-3 d-flex flex-column gap-2.5">
                            @forelse($recentProposals as $proposal)
                                @php
                                    $statusClass = match($proposal->status) {
                                        'approved', 'final_approved', 'completed' => 'bg-success bg-opacity-10 text-success',
                                        'revision_required', 'returned_for_revision', 'approved_with_revisions' => 'bg-danger bg-opacity-10 text-danger',
                                        'pending', 'submitted', 'under_review' => 'bg-warning bg-opacity-10 text-warning',
                                        'draft' => 'bg-secondary bg-opacity-10 text-secondary',
                                        default => 'bg-info bg-opacity-10 text-info'
                                    };
                                @endphp
                                <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                                    <!-- Row 1: Proposal Code & Role/Phase -->
                                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                                        <span class="badge bg-white text-dark border fw-mono" style="font-size: 0.72rem;">
                                            {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                        </span>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <span class="text-muted" style="font-size: 0.72rem;">Phase {{ $proposal->current_phase ?? 1 }}</span>
                                            @if($proposal->user_id === Auth::id())
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2" style="font-size: 0.65rem;">Lead</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2" style="font-size: 0.65rem;">Co-Author</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Row 2: Title -->
                                    <h6 class="fw-bold text-dark mb-2 text-truncate" title="{{ $proposal->title }}">
                                        {{ $proposal->title }}
                                    </h6>

                                    <!-- Row 3: Status Badge (Wraps cleanly without overflow) & Action Button -->
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top gap-2">
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <span class="badge {{ $statusClass }} rounded-pill px-2.5 py-1 text-capitalize text-wrap text-start d-inline-block" 
                                                  style="font-size: 0.7rem; line-height: 1.25; max-width: 100%; white-space: normal;">
                                                {{ str_replace('_', ' ', $proposal->status) }}
                                            </span>
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if(in_array($proposal->status, ['revision_required', 'returned_for_revision', 'approved_with_revisions', 'draft']) && $proposal->user_id === Auth::id())
                                                <a href="{{ route('proposal.edit', $proposal->id) }}" class="btn btn-sm btn-warning text-dark rounded-pill px-3 py-1 fw-semibold text-nowrap" style="font-size: 0.75rem;">
                                                    <i class="bi bi-pencil-square me-1"></i> Revise
                                                </a>
                                            @else
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-white border bg-white rounded-pill px-3 py-1 shadow-2xs text-nowrap" style="font-size: 0.75rem;">
                                                    View <i class="bi bi-arrow-right ms-1"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-journal-plus fs-2 d-block mb-2 text-secondary"></i>
                                    <p class="mb-2 fw-medium small">No proposals submitted yet.</p>
                                    <a href="{{ route('proposal.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                        Create Proposal Now
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Latest Announcements Card -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-2 py-md-3 px-3 px-md-4 border-0 rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark fs-6 fs-md-5">Portal Announcements & Calls</h5>
                        <a href="{{ route('announcements.index') }}" class="small fw-semibold text-primary text-decoration-none">All Updates <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="card-body p-3 p-md-4 pt-2">
                        @forelse($recentAnnouncements as $announcement)
                            <div class="p-3 mb-2 mb-md-3 rounded-3 border-start border-4 border-primary bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold text-dark mb-0 fs-6">{{ $announcement->title }}</h6>
                                    <span class="text-muted" style="font-size: 0.72rem;">{{ $announcement->created_at ? $announcement->created_at->diffForHumans() : '' }}</span>
                                </div>
                                <p class="text-muted small mb-0 text-truncate" style="max-width: 95%;">{{ Str::limit(strip_tags($announcement->content), 120) }}</p>
                            </div>
                        @empty
                            <p class="text-muted small mb-0 text-center py-3">No recent announcements published.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Guidelines (Right 4 Columns) -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3 gap-md-4">
                <!-- Submit Proposal Highlight Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-primary bg-gradient text-white p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2 mb-md-3">
                        <div class="bg-white bg-opacity-20 rounded-3 p-2 text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="bi bi-send-check fs-5"></i>
                        </div>
                        <span class="badge bg-white text-primary rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">Call Open</span>
                    </div>
                    <h5 class="fw-bold mb-1 fs-6 fs-md-5">Ready to Submit?</h5>
                    <p class="text-white text-opacity-75 small mb-3" style="font-size: 0.8rem;">Submit your research grant proposal for review and institutional funding approval.</p>
                    <a href="{{ route('proposal.create') }}" class="btn btn-light text-primary fw-bold rounded-3 py-2 w-100 shadow-sm btn-sm btn-md-md">
                        <i class="bi bi-plus-lg me-1"></i> Start New Proposal
                    </a>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-2 py-md-3 px-3 px-md-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark fs-6 fs-md-5">Researcher Tools</h5>
                    </div>
                    <div class="card-body p-3 p-md-4 pt-1">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('proposal.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-files"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">My Proposals</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Manage your lead & co-authored papers</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted" style="font-size: 0.75rem;"></i>
                            </a>

                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-purple bg-opacity-10 text-purple rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; color: #8B5CF6;">
                                    <i class="bi bi-archive"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Browse institutional publications</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted" style="font-size: 0.75rem;"></i>
                            </a>

                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-chat-left-text"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Messages & Review Feedback</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Direct communications with reviewers</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted" style="font-size: 0.75rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>