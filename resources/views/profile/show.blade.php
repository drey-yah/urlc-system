<x-app-layout>
    <div class="mb-5">
        <!-- Profile Banner -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative mb-4" style="background: linear-gradient(135deg, {{ $user->role === 'researcher' ? '#1e3a8a, #3b82f6' : ($user->role === 'reviewer' ? '#065f46, #10b981' : '#4c1d95, #8b5cf6') }} 100%); min-height: 250px;">
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-white p-5">
                <div class="position-relative mb-3">
                    <div class="bg-white rounded-circle p-1" style="width: 110px; height: 110px;">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center h-100 w-100 fs-1 fw-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    </div>
                    @if($user->id === auth()->id())
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-light rounded-circle position-absolute bottom-0 end-0 shadow-sm" title="Edit Profile">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                    @endif
                </div>
                <h2 class="fw-bold mb-1">{{ $user->name }}</h2>
                <p class="opacity-75 mb-3 text-uppercase small fw-bold tracking-widest">
                    {{ str_replace('_', ' ', $user->role) }} | {{ $user->department ?? 'General' }}
                </p>
                
                @if($user->id === auth()->id())
                    <a href="{{ route('profile.edit') }}" class="btn btn-light bg-opacity-25 border-white text-white px-4 rounded-pill small fw-bold">
                        <i class="bi bi-pencil me-2"></i> EDIT PROFILE
                    </a>
                @endif
            </div>
        </div>

        @if($user->role === 'researcher')
            <!-- Researcher Stats Row -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                        <h3 class="fw-bold mb-1">{{ $stats['total'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium">Total Researches</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4 border-start border-4 border-primary">
                        <h3 class="fw-bold mb-1 text-primary">{{ $stats['lead'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium">As Lead Researcher</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4 border-start border-4 border-info">
                        <h3 class="fw-bold mb-1 text-info">{{ $stats['collaborated'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium">As Collaborator</p>
                    </div>
                </div>
            </div>

            <!-- Researches As Lead -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Researches <span class="text-primary fs-6 fw-normal ms-2">/ As Lead</span></h4>
                    @if($user->id === auth()->id())
                        <a href="{{ route('proposal.create') }}" class="btn btn-primary shadow-sm px-4">
                            <i class="bi bi-plus-lg me-2"></i> Submit Proposal
                        </a>
                    @endif
                </div>
                @include('profile.partials.research_table', ['proposals' => $leadProposals, 'type' => 'lead'])
            </div>

            <!-- Researches As Collaborator -->
            <div>
                <h4 class="fw-bold mb-4">Researches <span class="text-info fs-6 fw-normal ms-2">/ As Collaborator</span></h4>
                @include('profile.partials.research_table', ['proposals' => $collaboratedProposals, 'type' => 'collaborator'])
            </div>

        @elseif($user->role === 'reviewer')
            <!-- Reviewer Stats Row -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                        <h3 class="fw-bold mb-1">{{ $stats['total_assigned'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium">Total Assigned</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4 border-start border-4 border-warning">
                        <h3 class="fw-bold mb-1 text-warning">{{ $stats['pending_review'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium">Pending Reviews</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4 border-start border-4 border-success">
                        <h3 class="fw-bold mb-1 text-success">{{ $stats['completed_review'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium">Completed Reviews</p>
                    </div>
                </div>
            </div>

            <!-- Assigned for Review -->
            <div>
                <h4 class="fw-bold mb-4">Review Queue <span class="text-success fs-6 fw-normal ms-2">/ Assigned Proposals</span></h4>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
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
            <!-- Admin Stats Row -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                        <h3 class="fw-bold mb-1">{{ $stats['total_proposals'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium">Global Proposals</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4 border-start border-4 border-purple" style="border-color: #8b5cf6 !important;">
                        <h3 class="fw-bold mb-1" style="color: #8b5cf6;">{{ $stats['total_users'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium">Registered Users</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4 border-start border-4 border-info">
                        <h3 class="fw-bold mb-1 text-info">{{ $stats['active_announcements'] }}</h3>
                        <p class="text-muted small mb-0 fw-medium">Announcements</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Recent Proposals <span class="text-purple fs-6 fw-normal ms-2" style="color: #8b5cf6;">/ System-wide</span></h4>
                    <a href="{{ route('admin.proposals') }}" class="btn btn-purple bg-opacity-10 px-4 rounded-pill small fw-bold" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: none;">VIEW ALL</a>
                </div>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
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
                                            <a href="{{ route('admin.proposals') }}" class="btn btn-sm btn-link text-decoration-none fw-bold">Manage</a>
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
