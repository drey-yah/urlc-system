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
                <div class="card-body p-4">
                    <div class="bg-light rounded-4 p-5 text-center">
                        <i class="bi bi-graph-up text-muted h1 d-block mb-3"></i>
                        <p class="text-muted mb-0">System analytics and activity logs will be displayed here in the next phase.</p>
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
