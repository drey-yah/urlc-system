<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <x-application-logo class="d-inline-block" style="height:32px;width:auto;" />
        </a>

        <!-- Hamburger -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Navigation Links -->
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('announcements.index') ? 'active fw-bold border-bottom border-primary' : '' }}" href="{{ route('announcements.index') }}">Call for Papers</a>
                    </li>

                    @if(auth()->user()->role == 'researcher')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('researcher') ? 'active fw-bold' : '' }}" href="/researcher">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('proposal/create') ? 'active fw-bold' : '' }}" href="{{ route('proposal.create') }}">Create Proposal</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('proposal/my') ? 'active fw-bold' : '' }}" href="{{ route('proposal.index') }}">My Proposals</a>
                        </li>
                    @elseif(auth()->user()->role == 'reviewer')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('reviewer') ? 'active fw-bold' : '' }}" href="/reviewer">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('reviewer/proposals') ? 'active fw-bold' : '' }}" href="{{ route('reviewer.proposals') }}">Review Proposals</a>
                        </li>
                    @elseif(auth()->user()->role == 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin') ? 'active fw-bold' : '' }}" href="/admin">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/proposals') ? 'active fw-bold' : '' }}" href="{{ route('admin.proposals') }}">Manage Proposals</a>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- User Dropdown -->
            @auth
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Log Out</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>
