<nav class="navbar navbar-expand-lg navbar-light border-bottom sticky-top shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <i class="bi bi-file-earmark-text-fill text-primary h3 mb-0"></i>
            <span style="font-size: 1.25rem; letter-spacing: -0.025em;">URLC Research Portal</span>
        </a>

        <!-- Hamburger -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Navigation Links -->
            <ul class="navbar-nav ms-auto align-items-center">
                @auth
                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link px-2 position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell h5 mb-0"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 mt-2" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold small">Notifications</span>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <form action="{{ route('notifications.markRead') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-link p-0 small text-decoration-none" style="font-size: 0.75rem;">Mark all as read</button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <li>
                                    <a class="dropdown-item p-3 rounded d-flex align-items-start gap-3 border-bottom border-light" href="{{ $notification->data['proposal_id'] ?? false ? route('proposal.show', $notification->data['proposal_id']) : route('announcements.index') }}">
                                        <div class="bg-light p-2 rounded-circle {{ $notification->data['color'] ?? 'text-primary' }}">
                                            <i class="bi {{ $notification->data['icon'] ?? 'bi-info-circle' }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold small text-dark">{{ $notification->data['title'] }}</div>
                                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $notification->data['message'] }}</div>
                                            <div class="text-muted small mt-1" style="font-size: 0.65rem;">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="px-3 py-4 text-center text-muted">
                                    <i class="bi bi-bell-slash d-block h3 mb-2"></i>
                                    <p class="small mb-0">No new notifications</p>
                                </li>
                            @endforelse
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('announcements.index') ? 'active' : '' }}" href="{{ route('announcements.index') }}">
                            Call for Papers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('email.templates') ? 'active' : '' }}" href="{{ route('email.templates') }}">
                            <i class="bi bi-envelope"></i> Email Templates
                        </a>
                    </li>
                    
                    <li class="nav-item ms-lg-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
