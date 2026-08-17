<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Welcome back, {{ Auth::user()->name }} 👋</h1>
            <p class="text-muted small mb-0">Track your research proposals, monitor review progress, and submit new research.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('proposal.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm rounded-3">
                <i class="bi bi-plus-circle"></i> Submit New Proposal
            </a>
            <a href="{{ route('proposal.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm rounded-3">
                <i class="bi bi-folder2-open"></i> My Proposals
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                            <i class="bi bi-journal-album h4 mb-0"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-medium">All Time</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['total'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Total Submissions</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                            <i class="bi bi-hourglass-split h4 mb-0"></i>
                        </div>
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-medium">In Progress</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['under_review'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Under Review / Processing</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                            <i class="bi bi-exclamation-circle h4 mb-0"></i>
                        </div>
                        @if(($stats['action_required'] ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill px-3 py-2 fw-medium">Action Required</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-3 py-2 fw-medium">Clear</span>
                        @endif
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['action_required'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Revisions Needed</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                            <i class="bi bi-patch-check h4 mb-0"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-medium">Active</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['approved'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Approved / Ongoing Research</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4 mb-4">
        <!-- My Submissions Overview (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-4">
                <!-- Recent Proposals Table Card -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">My Active Proposals</h5>
                        <a href="{{ route('proposal.index') }}" class="small fw-semibold text-primary text-decoration-none">View All <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
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
                    </div>
                </div>

                <!-- Latest Announcements Card -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Portal Announcements & Calls</h5>
                        <a href="{{ route('announcements.index') }}" class="small fw-semibold text-primary text-decoration-none">All Updates <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="card-body p-4 pt-2">
                        @forelse($recentAnnouncements as $announcement)
                            <div class="p-3 mb-3 rounded-3 border-start border-4 border-primary bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold text-dark mb-0">{{ $announcement->title }}</h6>
                                    <span class="text-muted fs-7">{{ $announcement->created_at ? $announcement->created_at->diffForHumans() : '' }}</span>
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
            <div class="d-flex flex-column gap-4">
                <!-- Submit Proposal Highlight Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-primary bg-gradient text-white p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-white bg-opacity-20 rounded-3 p-3 text-white">
                            <i class="bi bi-send-check fs-4"></i>
                        </div>
                        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold">Call Open</span>
                    </div>
                    <h5 class="fw-bold mb-2">Ready to Submit?</h5>
                    <p class="text-white text-opacity-75 small mb-3">Submit your research grant proposal for review and institutional funding approval.</p>
                    <a href="{{ route('proposal.create') }}" class="btn btn-light text-primary fw-bold rounded-3 py-2 w-100 shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i> Start New Proposal
                    </a>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Researcher Tools</h5>
                    </div>
                    <div class="card-body p-4 pt-1">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('proposal.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-files"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">My Proposals</div>
                                    <div class="text-muted fs-7">Manage your lead & co-authored papers</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-purple bg-opacity-10 text-purple rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; color: #8B5CF6;">
                                    <i class="bi bi-archive"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted fs-7">Browse institutional publications</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-chat-left-text"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Messages & Review Feedback</div>
                                    <div class="text-muted fs-7">Direct communications with reviewers</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>