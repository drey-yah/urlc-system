<x-app-layout>
    <div class="mb-5">
        <h1 class="h3 fw-bold mb-1">Super Admin Dashboard</h1>
        <p class="text-muted">Global System Management & Insights</p>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                            <i class="bi bi-people h4 mb-0"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">+5% this week</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['total_users'] }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Total Registered Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-purple bg-opacity-10 text-purple rounded-circle p-3" style="color: #8B5CF6;">
                            <i class="bi bi-file-earmark-text h4 mb-0"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">{{ $stats['pending_proposals'] }} Pending</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['total_proposals'] }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Total Research Proposals</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                            <i class="bi bi-shield-lock h4 mb-0"></i>
                        </div>
                        @if($stats['pending_admins'] > 0)
                            <span class="badge bg-danger rounded-pill px-3">{{ $stats['pending_admins'] }} Needs Approval</span>
                        @endif
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['pending_admins'] }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Pending Admin Requests</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                            <i class="bi bi-megaphone h4 mb-0"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['active_announcements'] }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Published Announcements</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-4 border-0 ps-4">
                    <h5 class="fw-bold mb-0">System Activity Overview</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs border-bottom px-4 pt-3" id="activityTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-medium text-dark border-0 pb-3" id="proposals-tab" data-bs-toggle="tab" data-bs-target="#proposals" type="button" role="tab" style="background:transparent;">Recent Proposals</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-medium text-secondary border-0 pb-3" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" style="background:transparent;">New Users</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="activityTabsContent">
                        <!-- Recent Proposals Tab -->
                        <div class="tab-pane fade show active" id="proposals" role="tabpanel" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-medium border-0 rounded-start">Proposal Code</th>
                                            <th class="py-3 fw-medium border-0">Title</th>
                                            <th class="py-3 fw-medium border-0">Researcher</th>
                                            <th class="py-3 fw-medium border-0">Status</th>
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
                                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-medium">Pending</span>
                                                    @elseif($proposal->status == 'approved')
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-medium">Approved</span>
                                                    @elseif($proposal->status == 'rejected')
                                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 fw-medium">Rejected</span>
                                                    @else
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 fw-medium">{{ ucfirst(str_replace('_', ' ', $proposal->status)) }}</span>
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
                            <div class="table-responsive">
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
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-4 border-0 ps-4">
                    <h5 class="fw-bold mb-0">Quick Actions</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="list-group list-group-flush border-0">
                        <a href="{{ route('superadmin.users') }}?role=admin&status=pending" class="list-group-item list-group-item-action border-0 px-0 py-3 d-flex align-items-center gap-3">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">Review Admin Requests</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $stats['pending_admins'] }} admins waiting for approval</div>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                        </a>
                        <a href="{{ route('superadmin.settings') }}" class="list-group-item list-group-item-action border-0 px-0 py-3 d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                                <i class="bi bi-sliders"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">Configure Portal</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Modify system limits and notifications</div>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                        </a>
                        <a href="{{ route('announcements.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-3 d-flex align-items-center gap-3">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                                <i class="bi bi-megaphone"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">Post Global Update</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Broadcast announcement to all users</div>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
