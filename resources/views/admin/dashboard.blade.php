<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 mb-md-4">
        <div>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-speedometer2 text-primary"></i> Admin Dashboard
            </h1>
            <p class="text-muted small mb-0">Overview of research proposals, user assignments, and portal operations.</p>
        </div>
        <div class="d-flex gap-2 w-100 w-sm-auto">
            <a href="{{ route('admin.proposals') }}" class="btn btn-primary btn-sm px-3 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-sm-grow-0" style="font-size: 0.84rem;">
                <i class="bi bi-file-earmark-text"></i>
                <span class="d-none d-sm-inline">Manage </span>Proposals
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-sm-grow-0" style="font-size: 0.84rem;">
                <i class="bi bi-people"></i>
                <span class="d-none d-sm-inline">Manage </span>Users
            </a>
        </div>
    </div>

    <!-- Stats Grid: 2x2 on Mobile, 4-col on Desktop -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <!-- Total Proposals -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-journal-text fs-6 fs-md-5"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-0.5 small fw-semibold" style="font-size: 0.7rem;">Total</span>
                    </div>
                    <h3 class="h4 h-md-3 fw-bold mb-0 text-dark">{{ $stats['total_proposals'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.76rem;">Research Proposals</p>
                </div>
            </div>
        </div>

        <!-- Pending Review -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-hourglass-split fs-6 fs-md-5"></i>
                        </div>
                        @if(($stats['pending_proposals'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-dark rounded-pill px-2 py-0.5 small fw-semibold text-truncate" style="font-size: 0.7rem;">Action</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-2 py-0.5 small fw-semibold" style="font-size: 0.7rem;">Clear</span>
                        @endif
                    </div>
                    <h3 class="h4 h-md-3 fw-bold mb-0 text-dark">{{ $stats['pending_proposals'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.76rem;">Pending Review</p>
                </div>
            </div>
        </div>

        <!-- Approved Projects -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-check2-circle fs-6 fs-md-5"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-0.5 small fw-semibold" style="font-size: 0.7rem;">Approved</span>
                    </div>
                    <h3 class="h4 h-md-3 fw-bold mb-0 text-dark">{{ $stats['approved_proposals'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.76rem;">Approved Projects</p>
                </div>
            </div>
        </div>

        <!-- System Users -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-people fs-6 fs-md-5"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 py-0.5 small fw-semibold" style="font-size: 0.7rem;">Registered</span>
                    </div>
                    <h3 class="h4 h-md-3 fw-bold mb-0 text-dark">{{ $stats['total_users'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.76rem;">System Users</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- Recent Submissions & System Users (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Portal Activity</h5>
                        <ul class="nav nav-pills card-header-pills" id="adminActivityTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active small py-1 px-3 fw-semibold rounded-pill" id="recent-proposals-tab" data-bs-toggle="tab" data-bs-target="#recent-proposals" type="button" role="tab" style="font-size: 0.78rem;">Proposals</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill" id="recent-users-tab" data-bs-toggle="tab" data-bs-target="#recent-users" type="button" role="tab" style="font-size: 0.78rem;">Users</button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="adminActivityTabsContent">
                        <!-- Recent Proposals Tab -->
                        <div class="tab-pane fade show active" id="recent-proposals" role="tabpanel">
                            
                            <!-- Mobile View: Proposal Cards (d-md-none) -->
                            <div class="d-flex flex-column gap-2.5 p-3 d-md-none">
                                @forelse($recentProposals as $proposal)
                                    @php
                                        $statusClass = match($proposal->status) {
                                            'approved', 'final_approved', 'completed' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                            'pending', 'submitted', 'under_review' => 'bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25',
                                            'rejected', 'final_rejected' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                            default => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25'
                                        };
                                    @endphp
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="d-flex justify-content-between align-items-center gap-1.5 mb-2">
                                            <span class="badge bg-white text-dark border fw-mono" style="font-size: 0.7rem;">
                                                {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                            </span>
                                            <span class="badge {{ $statusClass }} rounded-pill px-2 py-0.5 text-capitalize text-truncate" style="font-size: 0.68rem; max-width: 55%;">
                                                {{ str_replace('_', ' ', $proposal->status) }}
                                            </span>
                                        </div>

                                        <h6 class="fw-bold text-dark mb-1.5" style="font-size: 0.9rem; line-height: 1.35;">{{ $proposal->title }}</h6>
                                        
                                        <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                            <div class="d-flex align-items-center gap-1.5 min-w-0" style="min-width: 0;">
                                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                    {{ strtoupper(substr($proposal->user->name ?? '?', 0, 1)) }}
                                                </div>
                                                <span class="small text-muted text-truncate" style="font-size: 0.78rem;">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                            </div>
                                            <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold shadow-xs flex-shrink-0 d-inline-flex align-items-center gap-1" style="font-size: 0.76rem;">
                                                View <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary"></i>
                                        <span class="small">No research proposals found.</span>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop View: Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">Code</th>
                                            <th class="py-3 fw-semibold border-0">Title</th>
                                            <th class="py-3 fw-semibold border-0">Researcher</th>
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
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $proposal->title }}">
                                                        {{ $proposal->title }}
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                                            {{ substr($proposal->user->name ?? '?', 0, 1) }}
                                                        </div>
                                                        <span class="small text-truncate" style="max-width: 130px;">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    @php
                                                        $statusClass = match($proposal->status) {
                                                            'approved', 'final_approved', 'completed' => 'bg-success bg-opacity-10 text-success',
                                                            'pending', 'submitted', 'under_review' => 'bg-warning bg-opacity-10 text-warning',
                                                            'rejected', 'final_rejected' => 'bg-danger bg-opacity-10 text-danger',
                                                            default => 'bg-secondary bg-opacity-10 text-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $statusClass }} rounded-pill px-3 py-1 fw-medium text-capitalize">
                                                        {{ str_replace('_', ' ', $proposal->status) }}
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                        View <i class="bi bi-arrow-right ms-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                                    No research proposals found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Recent Users Tab -->
                        <div class="tab-pane fade" id="recent-users" role="tabpanel">
                            
                            <!-- Mobile View: User Cards (d-md-none) -->
                            <div class="d-flex flex-column gap-2.5 p-3 d-md-none">
                                @forelse($recentUsers as $user)
                                    <div class="p-2.5 bg-light rounded-3 border d-flex align-items-center justify-content-between gap-2">
                                        <div class="d-flex align-items-center gap-2.5 min-w-0" style="min-width: 0;">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0" style="min-width: 0;">
                                                <div class="fw-bold text-dark small text-truncate" style="font-size: 0.82rem;">{{ $user->name }}</div>
                                                <div class="text-muted small text-truncate" style="font-size: 0.72rem;">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                        <div class="text-end flex-shrink-0">
                                            <span class="badge bg-white text-dark border px-2 py-0.5 text-capitalize" style="font-size: 0.68rem;">
                                                {{ str_replace('_', ' ', $user->role) }}
                                            </span>
                                            <div class="text-muted" style="font-size: 0.68rem;">{{ $user->created_at ? $user->created_at->diffForHumans(null, true) : 'N/A' }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-people fs-2 d-block mb-1 text-secondary"></i>
                                        <span class="small">No recent users found.</span>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop View: User Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">User</th>
                                            <th class="py-3 fw-semibold border-0">Role</th>
                                            <th class="py-3 fw-semibold border-0">Department</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($recentUsers as $user)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                                            {{ substr($user->name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold text-dark small">{{ $user->name }}</div>
                                                            <div class="text-muted fs-7">{{ $user->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-light text-dark border px-2 py-1 text-capitalize fs-7">
                                                        {{ str_replace('_', ' ', $user->role) }}
                                                    </span>
                                                </td>
                                                <td class="py-3 text-muted small">
                                                    {{ $user->department ?: 'N/A' }}
                                                </td>
                                                <td class="pe-4 py-3 text-end text-muted small">
                                                    {{ $user->created_at ? $user->created_at->diffForHumans() : 'N/A' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted">
                                                    <i class="bi bi-people fs-2 d-block mb-2 text-secondary"></i>
                                                    No recent users found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Modules & Shortcuts (Right 4 Columns) -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3 gap-md-4">
                <!-- Administrative Actions Card -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Management Hub</h5>
                    </div>
                    <div class="card-body p-3 p-md-4 pt-0">
                        <div class="d-flex flex-column gap-3">
                            <a href="{{ route('admin.proposals') }}" class="p-3 rounded-3 border bg-light text-decoration-none d-flex align-items-center justify-content-between hover-shadow transition">
                                <div class="d-flex align-items-center gap-3 min-w-0" style="min-width: 0;">
                                    <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                                        <i class="bi bi-file-earmark-check fs-5"></i>
                                    </div>
                                    <div class="min-w-0" style="min-width: 0;">
                                        <div class="fw-bold text-dark mb-0 small">Manage Proposals</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.75rem;">Review &amp; grant decisions</div>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted small ms-2 flex-shrink-0"></i>
                            </a>

                            <a href="{{ route('admin.users.index') }}" class="p-3 rounded-3 border bg-light text-decoration-none d-flex align-items-center justify-content-between hover-shadow transition">
                                <div class="d-flex align-items-center gap-3 min-w-0" style="min-width: 0;">
                                    <div class="bg-dark text-white rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                                        <i class="bi bi-person-gear fs-5"></i>
                                    </div>
                                    <div class="min-w-0" style="min-width: 0;">
                                        <div class="fw-bold text-dark mb-0 small">Manage Users</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.75rem;">Assign reviewer &amp; admin roles</div>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted small ms-2 flex-shrink-0"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick System Shortcuts -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Quick Actions</h5>
                    </div>
                    <div class="card-body p-3 p-md-4 pt-0">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('announcements.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                    <i class="bi bi-megaphone fs-6"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                                    <div class="fw-semibold text-dark small">Announcements</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.75rem;">Post calls for papers</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted small"></i>
                            </a>
                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                                <div class="bg-purple bg-opacity-10 rounded-3 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; color: #8B5CF6; background-color: rgba(139, 92, 246, 0.1);">
                                    <i class="bi bi-archive fs-6"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.75rem;">Access published manuscripts</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted small"></i>
                            </a>
                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                    <i class="bi bi-envelope-paper fs-6"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                                    <div class="fw-semibold text-dark small">Internal Messages</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.75rem;">Direct communications</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted small"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>