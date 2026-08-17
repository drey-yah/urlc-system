<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Admin Dashboard</h1>
            <p class="text-muted small mb-0">Overview of research proposals, user assignments, and portal operations.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.proposals') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm rounded-3">
                <i class="bi bi-file-earmark-text"></i> Manage Proposals
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm rounded-3">
                <i class="bi bi-people"></i> Manage Users
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
                            <i class="bi bi-journal-text h4 mb-0"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-medium">Total</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['total_proposals'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Research Proposals</p>
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
                        @if(($stats['pending_proposals'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-medium">Action Needed</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-3 py-2 fw-medium">Clear</span>
                        @endif
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['pending_proposals'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Pending Review / Decision</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                            <i class="bi bi-check2-circle h4 mb-0"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-medium">Approved</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['approved_proposals'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Active / Approved Projects</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                            <i class="bi bi-people h4 mb-0"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2 fw-medium">Registered</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['total_users'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">System Users</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- Recent Submissions & System Users (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Portal Activity</h5>
                        <ul class="nav nav-pills card-header-pills" id="adminActivityTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active small py-1 px-3 fw-semibold rounded-pill" id="recent-proposals-tab" data-bs-toggle="tab" data-bs-target="#recent-proposals" type="button" role="tab">Proposals</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill" id="recent-users-tab" data-bs-toggle="tab" data-bs-target="#recent-users" type="button" role="tab">Users</button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content" id="adminActivityTabsContent">
                        <!-- Recent Proposals Tab -->
                        <div class="tab-pane fade show active" id="recent-proposals" role="tabpanel">
                            <div class="table-responsive">
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
                            <div class="table-responsive">
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
            <div class="d-flex flex-column gap-4">
                <!-- Administrative Actions Card -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Management Hub</h5>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="d-grid gap-3">
                            <a href="{{ route('admin.proposals') }}" class="p-3 rounded-3 border bg-light text-decoration-none d-flex align-items-center justify-content-between hover-shadow transition">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                        <i class="bi bi-file-earmark-check fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0">Manage Proposals</div>
                                        <div class="text-muted small">Review & grant decisions</div>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>

                            <a href="{{ route('admin.users.index') }}" class="p-3 rounded-3 border bg-light text-decoration-none d-flex align-items-center justify-content-between hover-shadow transition">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-dark text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                        <i class="bi bi-person-gear fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0">Manage Users</div>
                                        <div class="text-muted small">Assign reviewer & admin roles</div>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick System Shortcuts -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Quick Actions</h5>
                    </div>
                    <div class="card-body p-4 pt-1">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('announcements.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-megaphone"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Announcements</div>
                                    <div class="text-muted fs-7">Post calls for papers</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>
                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-purple bg-opacity-10 text-purple rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; color: #8B5CF6;">
                                    <i class="bi bi-archive"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted fs-7">Access published manuscripts</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>
                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-envelope-paper"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Internal Messages</div>
                                    <div class="text-muted fs-7">Direct communications</div>
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