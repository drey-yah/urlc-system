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

        <a href="{{ route('repository.index') }}" class="sidebar-link {{ request()->routeIs('repository.index') ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark-fill"></i>
            <span>Research Repository</span>
        </a>

        <a href="{{ route('email.templates') }}" class="sidebar-link {{ request()->routeIs('email.templates') ? 'active' : '' }}">
            <i class="bi bi-envelope-paper-fill"></i>
            <span>Email Templates</span>
        </a>

        <!-- Notifications (Collapsible or just a link) -->
        <div class="mt-4 pt-4 border-top">
            <h6 class="text-muted small fw-bold px-3 mb-3 text-uppercase">Notifications</h6>
            @forelse(auth()->user()->unreadNotifications->take(3) as $notification)
                <a href="{{ $notification->data['proposal_id'] ?? false ? route('proposal.show', $notification->data['proposal_id']) : route('announcements.index') }}" class="sidebar-link py-2" style="font-size: 0.8rem;">
                    <i class="bi bi-dot text-primary fs-4"></i>
                    <span class="text-truncate">{{ $notification->data['title'] }}</span>
                </a>
            @empty
                <p class="text-muted x-small px-3 italic">No new notifications</p>
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
