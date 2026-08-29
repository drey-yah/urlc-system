<aside class="sidebar">
    <!-- Brand -->
    <a class="sidebar-brand" href="{{ route('dashboard') }}">
        <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
            <i class="bi bi-file-earmark-text-fill h4 mb-0"></i>
        </div>
        <div class="lh-sm">
            <span class="fw-bold d-block" style="font-size: 1.1rem; letter-spacing: -0.02em;">URLC</span>
            <span class="text-muted x-small" style="font-size: 0.65rem;">Research Portal</span>
        </div>
    </a>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>

        @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Super Admin</span>
            </a>
            <a href="{{ route('superadmin.users') }}" class="sidebar-link {{ request()->routeIs('superadmin.users') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>User Management</span>
            </a>
            <a href="{{ route('superadmin.settings') }}" class="sidebar-link {{ request()->routeIs('superadmin.settings') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i>
                <span>System Settings</span>
            </a>
            <a href="{{ route('superadmin.logs') }}" class="sidebar-link {{ request()->routeIs('superadmin.logs') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Activity Logs</span>
            </a>
        @endif

        @if(auth()->user()->role === 'admin' && !auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.proposals') }}" class="sidebar-link {{ request()->routeIs('admin.proposals') ? 'active' : '' }}">
                <i class="bi bi-collection-fill"></i>
                <span>All Proposals</span>
            </a>
        @endif

        @if(auth()->user()->role === 'reviewer')
            <a href="{{ route('reviewer.proposals') }}" class="sidebar-link {{ request()->routeIs('reviewer.proposals') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check-fill"></i>
                <span>Review Queue</span>
            </a>
        @endif

        @if(auth()->user()->role === 'coordinator')
            <a href="{{ route('coordinator.proposals') }}" class="sidebar-link {{ request()->routeIs('coordinator.proposals') ? 'active' : '' }}">
                <i class="bi bi-check-circle-fill"></i>
                <span>Endorsements</span>
            </a>
        @endif

        @if(auth()->user()->role === 'staff')
            <a href="{{ route('staff.proposals') }}" class="sidebar-link {{ request()->routeIs('staff.proposals') ? 'active' : '' }}">
                <i class="bi bi-inbox-fill"></i>
                <span>Receiving Queue</span>
            </a>
        @endif

        @if(auth()->user()->role === 'recording_staff')
            <a href="{{ route('recording_staff.dashboard') }}" class="sidebar-link {{ request()->routeIs('recording_staff.dashboard') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Tracking Dashboard</span>
            </a>
        @endif

        @if(auth()->user()->role === 'dean')
            <a href="{{ route('dean.dashboard') }}" class="sidebar-link {{ request()->routeIs('dean.dashboard') ? 'active' : '' }}">
                <i class="bi bi-bank2"></i>
                <span>Dean Noting</span>
            </a>
        @endif

        @if(auth()->user()->role === 'vprei')
            <a href="{{ route('vprei.dashboard') }}" class="sidebar-link {{ request()->routeIs('vprei.dashboard') ? 'active' : '' }}">
                <i class="bi bi-award-fill"></i>
                <span>VPREI Approvals</span>
            </a>
        @endif

        @if(auth()->user()->role === 'president')
            <a href="{{ route('president.dashboard') }}" class="sidebar-link {{ request()->routeIs('president.dashboard') ? 'active' : '' }}">
                <i class="bi bi-person-workspace"></i>
                <span>President Authorization</span>
            </a>
        @endif

        @if(auth()->user()->role === 'budget_officer')
            <a href="{{ route('budget.dashboard') }}" class="sidebar-link {{ request()->routeIs('budget.dashboard') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Budget Certification</span>
            </a>
        @endif

        @if(auth()->user()->role === 'sao_finance')
            <a href="{{ route('finance.dashboard') }}" class="sidebar-link {{ request()->routeIs('finance.dashboard') ? 'active' : '' }}">
                <i class="bi bi-bank"></i>
                <span>Procurement PRs</span>
            </a>
        @endif

        @if(auth()->user()->role === 'researcher')
            <a href="{{ route('proposal.index') }}" class="sidebar-link {{ request()->routeIs('proposal.index') ? 'active' : '' }}">
                <i class="bi bi-folder-fill"></i>
                <span>My Researches</span>
            </a>
            <a href="{{ route('proposal.create') }}" class="sidebar-link {{ request()->routeIs('proposal.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Submit Proposal</span>
            </a>
        @endif

        <a href="{{ route('announcements.index') }}" class="sidebar-link {{ request()->routeIs('announcements.index') ? 'active' : '' }}">
            <i class="bi bi-megaphone-fill"></i>
            <span>Announcements</span>
        </a>

        <button type="button" class="sidebar-link w-100 border-0 bg-transparent text-start" data-bs-toggle="modal" data-bs-target="#submissionGuidelinesModal">
            <i class="bi bi-book-half"></i>
            <span>Submission Guidelines</span>
        </button>

        <a href="{{ route('repository.index') }}" class="sidebar-link {{ request()->routeIs('repository.index') ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark-fill"></i>
            <span>Research Repository</span>
        </a>

        <a href="{{ route('messages.index') }}" class="sidebar-link {{ request()->routeIs('messages.index') ? 'active' : '' }}">
            <i class="bi bi-envelope-fill"></i>
            <span>Messages</span>
        </a>

        <!-- Notifications & Activity Log History Section -->
        <div class="mt-4 pt-3 border-top px-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-muted small fw-bold mb-0 text-uppercase d-flex align-items-center gap-2">
                    <i class="bi bi-bell-fill text-warning"></i> Notifications
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="badge bg-danger rounded-pill fs-7">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </h6>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.markRead') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 text-muted text-decoration-none" style="font-size: 0.7rem;" title="Mark all as read">
                            Mark all read
                        </button>
                    </form>
                @endif
            </div>

            @forelse(auth()->user()->notifications->take(6) as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                @endphp
                <a href="{{ isset($notification->data['proposal_id']) && $notification->data['proposal_id'] ? route('proposal.show', $notification->data['proposal_id']) : route('announcements.index') }}" 
                   class="d-flex align-items-start gap-2 p-2 rounded-3 text-decoration-none transition-all mb-1 {{ $isUnread ? 'bg-primary bg-opacity-10 border-start border-primary border-3' : 'hover-bg-light opacity-75' }}" 
                   style="font-size: 0.78rem;">
                    <div class="overflow-hidden w-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold text-truncate text-dark" style="line-height: 1.2;">{{ $notification->data['title'] ?? 'Notification' }}</div>
                            @if($isUnread)
                                <span class="badge bg-primary rounded-circle p-1" title="Unread"></span>
                            @endif
                        </div>
                        <div class="text-muted text-truncate" style="font-size: 0.7rem;">{{ $notification->data['message'] ?? '' }}</div>
                        <div class="text-muted" style="font-size: 0.65rem;">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                </a>
            @empty
                <p class="text-muted x-small italic mb-0 text-center py-2" style="font-size: 0.72rem;">No notification history yet</p>
            @endforelse
        </div>
    </nav>

    <!-- Footer Profile -->
    <div class="sidebar-footer">
        <a href="{{ route('profile.show') }}" class="sidebar-link mb-2 {{ request()->routeIs('profile.show') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i>
            <span class="text-truncate">{{ auth()->user()->name }}</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-100 border-0 bg-transparent text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Global Submission Guidelines Modal -->
<div class="modal fade" id="submissionGuidelinesModal" tabindex="-1" aria-labelledby="submissionGuidelinesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-primary" id="submissionGuidelinesModalLabel">
                    <i class="bi bi-book-half me-2"></i> Research Proposal Submission Guidelines
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Proposal Phases Overview -->
                <div class="bg-primary bg-opacity-10 p-4 rounded-4 mb-4">
                    <h6 class="fw-bold text-primary mb-3">Proposal Lifecycle & Phases</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="bg-white p-3 rounded-3 shadow-xs">
                                <small class="text-primary fw-bold text-uppercase d-block mb-1">Phase 1</small>
                                <p class="small mb-0 text-muted">Initial proposal submission with research objectives and preliminary data.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-white p-3 rounded-3 shadow-xs">
                                <small class="text-primary fw-bold text-uppercase d-block mb-1">Phase 2-4</small>
                                <p class="small mb-0 text-muted">Detailed methodology, work plan (Gantt chart), and line-item budget execution.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-white p-3 rounded-3 shadow-xs">
                                <small class="text-primary fw-bold text-uppercase d-block mb-1">Phase 5</small>
                                <p class="small mb-0 text-muted">Final manuscript submission with research outcomes and terminal report.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guidelines Steps -->
                <h6 class="fw-bold text-dark mb-3">Step-by-Step Submission Process</h6>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
                        <div class="badge bg-primary rounded-circle p-2 px-3 fw-bold">1</div>
                        <div>
                            <strong class="text-dark d-block mb-1">Prepare Your Proposal</strong>
                            <p class="small text-muted mb-0">Ensure research objectives are clearly defined, aligned with institutional thrusts, and include required documentation.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
                        <div class="badge bg-primary rounded-circle p-2 px-3 fw-bold">2</div>
                        <div>
                            <strong class="text-dark d-block mb-1">Submit Through Portal</strong>
                            <p class="small text-muted mb-0">Navigate to 'Submit Proposal' from the sidebar, fill out the required details, upload your manuscript PDF, and save as draft or submit.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
                        <div class="badge bg-primary rounded-circle p-2 px-3 fw-bold">3</div>
                        <div>
                            <strong class="text-dark d-block mb-1">Review & Endorsement Process</strong>
                            <p class="small text-muted mb-0">Your proposal goes through College Dean noting, Coordinator endorsement, Reviewer evaluation, and Budget certification.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
                        <div class="badge bg-primary rounded-circle p-2 px-3 fw-bold">4</div>
                        <div>
                            <strong class="text-dark d-block mb-1">Automated Tracking & Notifications</strong>
                            <p class="small text-muted mb-0">Track real-time status changes and receive email updates at each phase of approval.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pb-4 px-4">
                <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Close Guidelines</button>
            </div>
        </div>
    </div>
</div>
