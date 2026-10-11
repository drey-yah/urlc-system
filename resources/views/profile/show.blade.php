<x-app-layout>
    <div class="mb-5">
        <!-- Profile Banner (Branded UA Maroon Gradient) -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative mb-4" style="background: linear-gradient(135deg, var(--ua-maroon) 0%, var(--ua-maroon-dark) 55%, var(--ua-maroon-deep) 100%); min-height: 220px;">
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-white p-4 p-md-5 text-center">
                <div class="position-relative mb-3">
                    <div class="bg-white rounded-circle p-1 shadow-sm" style="width: 100px; height: 100px;">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center h-100 w-100 fs-1 fw-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    </div>
                    @if($user->id === auth()->id())
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-light rounded-circle position-absolute bottom-0 end-0 shadow-sm" title="Edit Profile" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-pencil-fill" style="font-size: 0.75rem;"></i>
                        </a>
                    @endif
                </div>
                <h2 class="fw-bold mb-1 text-white fs-3 fs-md-2">{{ $user->name }}</h2>
                <div class="mb-3">
                    <span class="badge rounded-pill px-3 py-1.5 fw-semibold text-uppercase tracking-wider shadow-sm d-inline-flex align-items-center gap-1.5" style="background: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.35); font-size: 0.76rem; letter-spacing: 0.5px; backdrop-filter: blur(4px);">
                        <i class="bi bi-shield-check text-warning"></i>
                        <span>{{ str_replace('_', ' ', $user->role) }}</span>
                        <span class="opacity-50 mx-1">|</span>
                        <span>{{ $user->department ?? 'General' }}</span>
                    </span>
                </div>
                
                @if($user->id === auth()->id())
                    <a href="{{ route('profile.edit') }}" class="btn btn-light text-dark px-4 py-2 rounded-pill small fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-primary"></i> Edit Profile
                    </a>
                @endif
            </div>
        </div>

        @if($user->role === 'researcher')
            <!-- Researcher Stats Row (Compact 3-Column Grid) -->
            <div class="row g-2 g-md-3 mb-4 mb-md-5">
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-2.5 p-md-4 h-100">
                        <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['total'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.72rem;">
                            <span class="d-none d-sm-inline">Total </span>Researches
                        </p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-2.5 p-md-4 h-100 border-start border-4 border-primary">
                        <h3 class="fw-bold mb-1 text-primary fs-4 fs-sm-3">{{ $stats['lead'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.72rem;">
                            <span class="d-none d-sm-inline">As </span>Lead
                        </p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-2.5 p-md-4 h-100 border-start border-4 border-info">
                        <h3 class="fw-bold mb-1 text-info fs-4 fs-sm-3">{{ $stats['collaborated'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.72rem;">
                            <span class="d-none d-sm-inline">As </span>Collab
                        </p>
                    </div>
                </div>
            </div>

            <!-- Researches As Lead -->
            <div class="mb-4 mb-md-5">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="fw-bold mb-0 text-dark">Researches <span class="text-primary small fw-normal ms-1">/ As Lead</span></h5>
                    @if($user->id === auth()->id())
                        <a href="{{ route('proposal.create') }}" class="btn btn-sm btn-primary rounded-pill shadow-sm px-3 fw-bold">
                            <i class="bi bi-plus-lg me-1"></i> Submit Proposal
                        </a>
                    @endif
                </div>
                @include('profile.partials.research_table', ['proposals' => $leadProposals, 'type' => 'lead'])
            </div>

            <!-- Researches As Collaborator -->
            <div>
                <h5 class="fw-bold mb-3 text-dark">Researches <span class="text-info small fw-normal ms-1">/ As Collaborator</span></h5>
                @include('profile.partials.research_table', ['proposals' => $collaboratedProposals, 'type' => 'collaborator'])
            </div>

        @elseif($user->role === 'reviewer')
            <!-- Reviewer Stats Row (Compact 3-Column Grid) -->
            <div class="row g-2 g-md-3 mb-4 mb-md-5">
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-2.5 p-md-4 h-100">
                        <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['total_assigned'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.72rem;">
                            <span class="d-none d-sm-inline">Total </span>Assigned
                        </p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-2.5 p-md-4 h-100 border-start border-4 border-warning">
                        <h3 class="fw-bold mb-1 text-warning fs-4 fs-sm-3">{{ $stats['pending_review'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.72rem;">Pending</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-2.5 p-md-4 h-100 border-start border-4 border-success">
                        <h3 class="fw-bold mb-1 text-success fs-4 fs-sm-3">{{ $stats['completed_review'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.72rem;">Completed</p>
                    </div>
                </div>
            </div>

            <!-- Assigned for Review -->
            <div>
                <h5 class="fw-bold mb-3 text-dark">Review Queue <span class="text-success small fw-normal ms-1">/ Assigned Proposals</span></h5>
                
                <!-- Mobile View: Cards (d-md-none) -->
                <div class="d-md-none">
                    @forelse($assignedProposals as $proposal)
                        <div class="card border-0 shadow-sm rounded-4 mb-2.5 p-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                <span class="badge rounded-pill px-2.5 py-1 small {{ $proposal->status === 'pending' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-success bg-opacity-10 text-success' }}" style="font-size: 0.7rem;">
                                    {{ ucfirst($proposal->status) }}
                                </span>
                                <span class="text-muted small" style="font-size: 0.72rem;">{{ $proposal->research_field }}</span>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                {{ $proposal->title }}
                            </h6>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2" style="font-size: 0.76rem;">
                                <span class="text-muted">Lead: <strong class="text-dark">{{ $proposal->user->name }}</strong></span>
                                <a href="{{ route('reviewer.proposals') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.74rem;">
                                    Go to Review
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="card border-0 shadow-sm rounded-4 text-center py-4 text-muted">
                            <p class="small mb-0">No proposals assigned for review.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Desktop View: Table (d-none d-md-block) -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 border-0">Title</th>
                                    <th class="py-3 border-0">Researcher</th>
                                    <th class="py-3 border-0">Status</th>
                                    <th class="pe-4 py-3 border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignedProposals as $proposal)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold text-dark">{{ $proposal->title }}</div>
                                            <div class="text-muted small">{{ $proposal->research_field }}</div>
                                        </td>
                                        <td>{{ $proposal->user->name }}</td>
                                        <td>
                                            <span class="badge rounded-pill px-3 py-2 small {{ $proposal->status === 'pending' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-success bg-opacity-10 text-success' }}">
                                                {{ ucfirst($proposal->status) }}
                                            </span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="{{ route('reviewer.proposals') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Go to Review</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">No proposals assigned for review.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @else
            <!-- Admin / Dean / Staff Stats Row (Compact 3-Column Grid) -->
            <div class="row g-2 g-md-3 mb-4 mb-md-5">
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-2.5 p-md-4 h-100">
                        <h3 class="fw-bold mb-1 fs-4 fs-sm-3 text-dark">{{ $stats['total_proposals'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.72rem;">
                            <span class="d-none d-sm-inline">Global </span>Proposals
                        </p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-2.5 p-md-4 h-100 border-start border-4 border-primary">
                        <h3 class="fw-bold mb-1 text-primary fs-4 fs-sm-3">{{ $stats['total_users'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.72rem;">
                            <span class="d-none d-sm-inline">Registered </span>Users
                        </p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-2.5 p-md-4 h-100 border-start border-4 border-info">
                        <h3 class="fw-bold mb-1 text-info fs-4 fs-sm-3">{{ $stats['active_announcements'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.72rem;">
                            <span class="d-none d-sm-inline">Announcements</span><span class="d-sm-none">Notices</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Proposals -->
            <div>
                @php
                    $userRole = auth()->user()->role ?? $user->role;
                    $viewAllUrl = match($userRole) {
                        'admin' => route('admin.proposals'),
                        'staff' => route('staff.proposals'),
                        'recording_staff' => route('recording_staff.dashboard'),
                        'dean' => route('dean.dashboard'),
                        'coordinator' => route('coordinator.dashboard'),
                        'superadmin' => route('superadmin.dashboard'),
                        'finance' => route('finance.dashboard'),
                        default => route('dashboard'),
                    };
                @endphp
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="fw-bold mb-0 text-dark">Recent Proposals <span class="text-primary small fw-normal ms-1">/ System-wide</span></h5>
                    <a href="{{ $viewAllUrl }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">VIEW ALL</a>
                </div>

                <!-- Mobile View: Cards (d-md-none) -->
                <div class="d-md-none">
                    @forelse($recent_proposals as $proposal)
                        <div class="card border-0 shadow-sm rounded-4 mb-2.5 p-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-1.5">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                                    Phase {{ $proposal->current_phase }}
                                </span>
                                <span class="text-muted small" style="font-size: 0.72rem;">{{ $proposal->research_field ?? 'General Research' }}</span>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                <a href="{{ route('proposal.show', $proposal->id) }}" class="text-dark text-decoration-none hover-primary transition">
                                    {{ $proposal->title }}
                                </a>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2" style="font-size: 0.76rem;">
                                <span class="text-muted">Lead: <strong class="text-dark">{{ $proposal->user->name ?? 'Unknown' }}</strong></span>
                                @if($userRole === 'admin')
                                    <a href="{{ route('admin.proposals') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1" style="font-size: 0.74rem;">
                                        Manage
                                    </a>
                                @else
                                    <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1" style="font-size: 0.74rem;">
                                        View Details
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="card border-0 shadow-sm rounded-4 text-center py-4 text-muted">
                            <p class="small mb-0">No recent proposals recorded.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Desktop View: Table (d-none d-md-block) -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 border-0">Title</th>
                                    <th class="py-3 border-0">Lead Researcher</th>
                                    <th class="py-3 border-0">Phase</th>
                                    <th class="pe-4 py-3 border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_proposals as $proposal)
                                    <tr>
                                        <td class="ps-4 py-3">
                                             <div class="fw-bold text-dark">{{ $proposal->title }}</div>
                                            <div class="text-muted small">{{ $proposal->research_field }}</div>
                                        </td>
                                        <td>{{ $proposal->user->name }}</td>
                                        <td><span class="badge bg-light text-dark border rounded-pill px-3">Phase {{ $proposal->current_phase }}</span></td>
                                        <td class="pe-4 text-end">
                                            @if($userRole === 'admin')
                                                <a href="{{ route('admin.proposals') }}" class="btn btn-sm btn-link text-decoration-none fw-bold">Manage</a>
                                            @else
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-link text-decoration-none fw-bold">View Details</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
