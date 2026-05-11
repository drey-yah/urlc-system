<x-app-layout>
    <div class="mb-5">
        <h1 class="h3 fw-bold mb-1">Admin Dashboard</h1>
        <p class="text-muted">System Overview & Management</p>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card shadow-sm border-0">
                <div>
                    <small class="text-muted fw-bold text-uppercase d-block mb-1">Total Proposals</small>
                    <h2 class="fw-bold mb-0">{{ $stats['total'] }}</h2>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card shadow-sm border-0">
                <div>
                    <small class="text-muted fw-bold text-uppercase d-block mb-1">Pending Review</small>
                    <h2 class="fw-bold mb-0">{{ $stats['pending'] }}</h2>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card shadow-sm border-0">
                <div>
                    <small class="text-muted fw-bold text-uppercase d-block mb-1">Approved</small>
                    <h2 class="fw-bold mb-0">{{ $stats['approved'] }}</h2>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-bar-chart"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card shadow-sm border-0">
                <div>
                    <small class="text-muted fw-bold text-uppercase d-block mb-1">Active Announcements</small>
                    <h2 class="fw-bold mb-0">{{ $stats['announcements'] }}</h2>
                </div>
                <div class="stat-icon bg-purple bg-opacity-10" style="color: #8B5CF6;">
                    <i class="bi bi-bell"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Proposals List -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm overflow-visible">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold">All Proposals Overview</h5>
                </div>
                <div>
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Title</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Phase</th>
                                <th class="pe-4 text-end">Management</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $proposal)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-semibold text-dark">{{ Str::limit($proposal->title, 40) }}</div>
                                    <small class="text-muted">{{ $proposal->user->name }}</small>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = match($proposal->status) {
                                            'final_approved' => 'badge-approved',
                                            'final_rejected' => 'badge-rejected',
                                            'approved' => 'badge-approved',
                                            'pending' => 'badge-pending',
                                            'revision_required' => 'badge-in-review',
                                            default => 'badge-pending'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="text-muted small fw-medium">Phase {{ $proposal->current_phase }}</span>
                                </td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2">
                                        <li><a class="dropdown-item rounded py-2" href="{{ route('proposal.show', $proposal->id) }}"><i class="bi bi-eye me-2"></i> View Details</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li class="px-3 py-2">
                                            <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.6rem;">Set Phase</small>
                                            <form action="{{ route('admin.proposals.updatePhase', $proposal->id) }}" method="POST" class="d-flex gap-1">
                                                @csrf @method('PATCH')
                                                <select name="phase" class="form-select form-select-sm">
                                                    @for($i=1; $i<=5; $i++)
                                                        <option value="{{ $i }}" {{ $proposal->current_phase == $i ? 'selected' : '' }}>Ph {{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">Ok</button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li class="px-3 py-2">
                                            <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.6rem;">Assign Reviewer</small>
                                            <form action="{{ route('admin.proposals.assign', $proposal->id) }}" method="POST" class="d-flex gap-1">
                                                @csrf
                                                <select name="reviewer_id" class="form-select form-select-sm">
                                                    <option value="">Select...</option>
                                                    @foreach($reviewers as $reviewer)
                                                        <option value="{{ $reviewer->id }}">{{ $reviewer->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-dark">Add</button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li class="px-3 py-2 text-center">
                                            <form method="POST" action="{{ route('admin.proposals.finalDecision', $proposal->id) }}">
                                                @csrf
                                                <div class="btn-group w-100">
                                                    <button type="submit" name="status" value="final_approved" class="btn btn-sm btn-success w-50">Approve</button>
                                                    <button type="submit" name="status" value="final_rejected" class="btn btn-sm btn-danger w-50">Reject</button>
                                                </div>
                                            </form>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Announcements Feed -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Announcements</h5>
                    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                        <i class="bi bi-plus-lg"></i> Create New
                    </button>
                </div>
                <div class="card-body p-0">
                    <div style="max-height: 600px; overflow-y: auto;">
                        @foreach($announcements as $ann)
                        <div class="p-4 border-bottom border-light">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-dark">{{ $ann->title }}</h6>
                                <small class="text-muted">{{ $ann->created_at->format('Y-m-d') }}</small>
                            </div>
                            <p class="small text-muted mb-2">{{ Str::limit($ann->content, 120) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">By {{ $ann->user->name }}</small>
                                <form action="{{ route('announcements.destroy', $ann->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none small">Delete</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Announcement Modal -->
    <div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h5 class="modal-title fw-bold">Create New Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('announcements.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small uppercase">Announcement Title</label>
                            <input type="text" name="title" class="form-control bg-light border-0 py-2 px-3" placeholder="Enter announcement title" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-muted small uppercase">Content</label>
                            <textarea name="content" class="form-control bg-light border-0 py-2 px-3" rows="5" placeholder="Enter announcement content" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Create Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>