<x-app-layout>
    <!-- Header Section -->
    <div class="mb-3 mb-md-4">
        <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-shield-lock-fill text-primary"></i> Super Admin Dashboard
        </h1>
        <p class="text-muted small mb-0">Global system management, role administrative approvals, and portal audit insights.</p>
    </div>

    <!-- Stats Grid: 2x2 on Mobile, 4-col on Desktop -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <!-- Total Users -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-people fs-6 fs-md-5"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-0.5 small fw-semibold" style="font-size: 0.7rem;">Active</span>
                    </div>
                    <h3 class="h4 h-md-3 fw-bold mb-0 text-dark">{{ $stats['total_users'] }}</h3>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.76rem;">Registered Users</p>
                </div>
            </div>
        </div>

        <!-- Total Proposals -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-purple bg-opacity-10 rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; color: #8B5CF6; background-color: rgba(139, 92, 246, 0.1);">
                            <i class="bi bi-file-earmark-text fs-6 fs-md-5"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-0.5 small fw-semibold text-truncate" style="font-size: 0.7rem;">{{ $stats['pending_proposals'] }} Pending</span>
                    </div>
                    <h3 class="h4 h-md-3 fw-bold mb-0 text-dark">{{ $stats['total_proposals'] }}</h3>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.76rem;">Proposals Filed</p>
                </div>
            </div>
        </div>

        <!-- Pending Admin Requests -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-shield-lock fs-6 fs-md-5"></i>
                        </div>
                        @if($stats['pending_admins'] > 0)
                            <span class="badge bg-danger rounded-pill px-2 py-0.5 small fw-bold text-truncate" style="font-size: 0.68rem;">Action</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-2 py-0.5 small fw-semibold" style="font-size: 0.7rem;">Clear</span>
                        @endif
                    </div>
                    <h3 class="h4 h-md-3 fw-bold mb-0 text-dark">{{ $stats['pending_admins'] }}</h3>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.76rem;">Admin Approvals</p>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-megaphone fs-6 fs-md-5"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 py-0.5 small fw-semibold" style="font-size: 0.7rem;">Feed</span>
                    </div>
                    <h3 class="h4 h-md-3 fw-bold mb-0 text-dark">{{ $stats['active_announcements'] }}</h3>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.76rem;">Announcements</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- System Activity Overview (Left 8 Cols) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">System Activity</h5>
                        <ul class="nav nav-pills card-header-pills" id="activityTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active small py-1 px-3 fw-semibold rounded-pill" id="proposals-tab" data-bs-toggle="tab" data-bs-target="#proposals" type="button" role="tab" style="font-size: 0.78rem;">Proposals</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" style="font-size: 0.78rem;">New Users</button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="activityTabsContent">
                        <!-- Recent Proposals Tab -->
                        <div class="tab-pane fade show active" id="proposals" role="tabpanel" tabindex="0">
                            <!-- Mobile View (d-md-none) -->
                            <div class="d-flex flex-column gap-2.5 p-3 d-md-none">
                                @forelse($recentProposals as $proposal)
                                    @php
                                        $statusBadge = match($proposal->status) {
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
                                            <span class="badge {{ $statusBadge }} rounded-pill px-2 py-0.5 text-capitalize text-truncate" style="font-size: 0.68rem; max-width: 55%;">
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
                                        <span class="small">No recent proposals found.</span>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop View: Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-medium border-0 rounded-start">Proposal Code</th>
                                            <th class="py-3 fw-medium border-0">Title</th>
                                            <th class="py-3 fw-medium border-0">Researcher</th>
                                            <th class="py-3 fw-semibold border-0">Status</th>
                                            <th class="pe-4 py-3 fw-medium border-0 rounded-end text-end">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($recentProposals as $proposal)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <span class="fw-semibold text-primary">{{ $proposal->proposal_code ?? 'N/A' }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <div class="text-dark fw-medium text-truncate" style="max-width: 250px;" title="{{ $proposal->title }}">{{ $proposal->title }}</div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-secondary fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            {{ substr($proposal->user->name ?? '?', 0, 1) }}
                                                        </div>
                                                        <span class="small">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    @if($proposal->status == 'pending')
                                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 fw-medium">Pending</span>
                                                    @elseif($proposal->status == 'approved')
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-medium">Approved</span>
                                                    @elseif($proposal->status == 'rejected')
                                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 fw-medium">Rejected</span>
                                                    @else
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1 fw-medium">{{ ucfirst(str_replace('_', ' ', $proposal->status)) }}</span>
                                                    @endif
                                                </td>
                                                <td class="pe-4 py-3 text-end text-muted small">
                                                    {{ $proposal->created_at->diffForHumans() }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-inbox d-block h3 mb-2"></i>
                                                    No recent proposals found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- New Users Tab -->
                        <div class="tab-pane fade" id="users" role="tabpanel" tabindex="0">
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
                                            <div>
                                                @if($user->is_approved)
                                                    <span class="text-success small fw-medium" style="font-size: 0.68rem;"><i class="bi bi-check-circle me-0.5"></i>Approved</span>
                                                @else
                                                    <span class="text-warning small fw-medium" style="font-size: 0.68rem;"><i class="bi bi-hourglass-split me-0.5"></i>Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-people fs-2 d-block mb-1 text-secondary"></i>
                                        <span class="small">No recent users found.</span>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop View: Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-medium border-0 rounded-start">User</th>
                                            <th class="py-3 fw-medium border-0">Role</th>
                                            <th class="py-3 fw-medium border-0">Department</th>
                                            <th class="py-3 fw-medium border-0">Approval</th>
                                            <th class="pe-4 py-3 fw-medium border-0 rounded-end text-end">Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($recentUsers as $user)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 40px; height: 40px;">
                                                            {{ substr($user->name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <div class="text-dark fw-medium">{{ $user->name }}</div>
                                                            <div class="text-muted small">{{ $user->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-light text-dark border px-2 py-1">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                                                </td>
                                                <td class="py-3 text-muted small">
                                                    {{ $user->department ?: 'N/A' }}
                                                </td>
                                                <td class="py-3">
                                                    @if($user->is_approved)
                                                        <span class="text-success small fw-medium"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                                    @else
                                                        <span class="text-warning small fw-medium"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                                    @endif
                                                </td>
                                                <td class="pe-4 py-3 text-end text-muted small">
                                                    {{ $user->created_at->diffForHumans() }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-people d-block h3 mb-2"></i>
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

        <!-- Super Admin Quick Actions (Right 4 Cols) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Administrative Control</h5>
                </div>
                <div class="card-body p-3 p-md-4 pt-0">
                    <div class="list-group list-group-flush border-0">
                        <a href="{{ route('superadmin.users') }}?role=admin&status=pending" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                <i class="bi bi-person-check fs-6"></i>
                            </div>
                            <div class="min-w-0" style="min-width: 0;">
                                <div class="fw-bold small text-dark">Review Admin Requests</div>
                                <div class="text-muted text-truncate" style="font-size: 0.75rem;">{{ $stats['pending_admins'] }} admins awaiting approval</div>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                        </a>
                        <a href="{{ route('superadmin.settings') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                <i class="bi bi-sliders fs-6"></i>
                            </div>
                            <div class="min-w-0" style="min-width: 0;">
                                <div class="fw-bold small text-dark">Configure Portal</div>
                                <div class="text-muted text-truncate" style="font-size: 0.75rem;">System settings and limits</div>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                        </a>
                        <a href="{{ route('announcements.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                <i class="bi bi-megaphone fs-6"></i>
                            </div>
                            <div class="min-w-0" style="min-width: 0;">
                                <div class="fw-bold small text-dark">Post Global Update</div>
                                <div class="text-muted text-truncate" style="font-size: 0.75rem;">Broadcast university announcement</div>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
