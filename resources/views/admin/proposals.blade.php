<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3 mb-md-4">
        <div>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-folder2-open text-primary"></i> Manage Proposals
            </h1>
            <p class="text-muted small mb-0">Review submissions, assign peer reviewers, set research phases, and track multi-phase decisions.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center gap-1 shadow-xs">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Stat Cards: 2x2 Grid on Mobile, 4-col on Desktop -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <!-- Total Proposals -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-file-earmark-text fs-6 fs-md-5"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-0.5 small fw-semibold" style="font-size: 0.7rem;">Total</span>
                    </div>
                    <h3 class="h4 h-md-3 fw-bold mb-0 text-dark">{{ $stats['total'] }}</h3>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.76rem;">All Proposals</p>
                </div>
            </div>
        </div>

        <!-- Pending Review -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-hourglass-split fs-6 fs-md-5"></i>
                        </div>
                        @if($stats['pending'] > 0)
                            <span class="badge bg-danger rounded-pill px-2 py-0.5 small fw-bold text-truncate" style="font-size: 0.68rem;">Action</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-2 py-0.5 small fw-semibold" style="font-size: 0.7rem;">Clear</span>
                        @endif
                    </div>
                    <h3 class="h4 h-md-3 fw-bold mb-0 text-dark">{{ $stats['pending'] }}</h3>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.76rem;">Pending Review</p>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-check2-circle fs-6 fs-md-5"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-0.5 small fw-semibold" style="font-size: 0.7rem;">Approved</span>
                    </div>
                    <h3 class="h4 h-md-3 fw-bold mb-0 text-dark">{{ $stats['approved'] }}</h3>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.76rem;">Approved</p>
                </div>
            </div>
        </div>

        <!-- Active Announcements -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-purple bg-opacity-10 rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; color: #8B5CF6; background-color: rgba(139, 92, 246, 0.1);">
                            <i class="bi bi-megaphone fs-6 fs-md-5"></i>
                        </div>
                        <span class="badge rounded-pill px-2 py-0.5 small fw-semibold" style="color: #8B5CF6; background-color: rgba(139, 92, 246, 0.1); font-size: 0.7rem;">Feed</span>
                    </div>
                    <h3 class="h4 h-md-3 fw-bold mb-0 text-dark">{{ $stats['announcements'] }}</h3>
                    <p class="text-muted small mb-0 fw-medium text-truncate" style="font-size: 0.76rem;">Announcements</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (Proposals List + Announcements Feed) -->
    <div class="row g-3 g-md-4">
        <!-- Proposals Section (Left 7 Cols Desktop, Full Width Mobile) -->
        <div class="col-lg-7 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">All Proposals Overview</h5>
                        <span class="text-muted small" style="font-size: 0.75rem;">Showing {{ $proposals->firstItem() ?? 0 }}–{{ $proposals->lastItem() ?? 0 }} of {{ $proposals->total() }} submissions</span>
                    </div>
                    <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill small fw-medium">
                        <i class="bi bi-journals me-1 text-primary"></i>{{ $proposals->total() }} Total
                    </span>
                </div>

                <div class="card-body p-0">
                    <!-- Mobile View: Touch Proposal Cards (d-md-none) -->
                    <div class="d-flex flex-column gap-3 p-3 d-md-none">
                        @forelse($proposals as $proposal)
                            @php
                                $statusConfig = match($proposal->status) {
                                    'approved', 'final_approved', 'completed' => [
                                        'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                        'label' => str_replace('_', ' ', $proposal->status)
                                    ],
                                    'pending', 'submitted', 'under_review' => [
                                        'class' => 'bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25',
                                        'label' => str_replace('_', ' ', $proposal->status)
                                    ],
                                    'rejected', 'final_rejected' => [
                                        'class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                        'label' => str_replace('_', ' ', $proposal->status)
                                    ],
                                    'accepted_for_in_house_review' => [
                                        'class' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                        'label' => 'In-House Review'
                                    ],
                                    'noted_by_dean', 'final_copy_noted_by_dean' => [
                                        'class' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                                        'label' => 'Dean Noted'
                                    ],
                                    'endorsed_to_vprei' => [
                                        'class' => 'bg-purple bg-opacity-10 border border-opacity-25',
                                        'label' => 'Endorsed VPREI'
                                    ],
                                    'revision_required' => [
                                        'class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                        'label' => 'Needs Revision'
                                    ],
                                    default => [
                                        'class' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                                        'label' => str_replace('_', ' ', $proposal->status)
                                    ]
                                };
                            @endphp
                            <div class="p-3 bg-light rounded-3 border">
                                <!-- Top Row: Proposal Code on left, Status on right -->
                                <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                    <span class="badge bg-white text-dark border fw-mono flex-shrink-0" style="font-size: 0.72rem;">
                                        {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                    </span>
                                    <span class="badge {{ $statusConfig['class'] }} rounded-pill px-2.5 py-1 text-capitalize text-truncate" style="font-size: 0.7rem; max-width: 55%;" title="{{ $statusConfig['label'] }}">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </div>

                                <!-- Proposal Title -->
                                <h6 class="fw-bold text-dark mb-2" style="font-size: 0.92rem; line-height: 1.35;">
                                    {{ $proposal->title }}
                                </h6>

                                <!-- Researcher Info on left, Phase & Reviewers on right -->
                                <div class="d-flex align-items-center justify-content-between text-muted small mb-2.5 pb-2 border-bottom" style="font-size: 0.76rem;">
                                    <div class="d-flex align-items-center gap-1.5 min-w-0" style="min-width: 0;">
                                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 22px; height: 22px; font-size: 0.65rem;">
                                            {{ strtoupper(substr($proposal->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="text-truncate">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5 fw-semibold" style="font-size: 0.7rem;">
                                            Phase {{ $proposal->current_phase }}
                                        </span>
                                        @if($proposal->assignments && $proposal->assignments->count() > 0)
                                            <span class="badge bg-white text-muted border px-1.5 py-0.5" style="font-size: 0.65rem;" title="{{ $proposal->assignments->count() }} assigned reviewer(s)">
                                                <i class="bi bi-person-check text-success"></i> {{ $proposal->assignments->count() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold shadow-xs d-inline-flex align-items-center gap-1" style="font-size: 0.76rem;">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>

                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold shadow-xs d-inline-flex align-items-center gap-1 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.76rem;">
                                            <i class="bi bi-sliders"></i> Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="min-width: 260px; z-index: 1050;">
                                            <li>
                                                <a class="dropdown-item rounded py-2 d-flex align-items-center gap-2" href="{{ route('proposal.show', $proposal->id) }}">
                                                    <i class="bi bi-eye text-primary"></i> View Full Details
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li class="px-3 py-2">
                                                <small class="text-muted fw-bold text-uppercase d-block mb-1.5" style="font-size: 0.65rem; letter-spacing: 0.05em;">Advance Phase</small>
                                                <form action="{{ route('admin.proposals.updatePhase', $proposal->id) }}" method="POST" class="d-flex gap-1.5">
                                                    @csrf @method('PATCH')
                                                    <select name="phase" class="form-select form-select-sm">
                                                        @for($i=1; $i<=6; $i++)
                                                            <option value="{{ $i }}" {{ $proposal->current_phase == $i ? 'selected' : '' }}>Phase {{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary flex-shrink-0">Set</button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li class="px-3 py-2">
                                                <small class="text-muted fw-bold text-uppercase d-block mb-1.5" style="font-size: 0.65rem; letter-spacing: 0.05em;">Assign Reviewer</small>
                                                <form action="{{ route('admin.proposals.assign', $proposal->id) }}" method="POST" class="d-flex gap-1.5">
                                                    @csrf
                                                    <select name="reviewer_id" class="form-select form-select-sm">
                                                        <option value="">Select Reviewer...</option>
                                                        @foreach($reviewers as $reviewer)
                                                            <option value="{{ $reviewer->id }}">{{ $reviewer->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-dark flex-shrink-0">Add</button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li class="px-3 py-2">
                                                <small class="text-muted fw-bold text-uppercase d-block mb-1.5" style="font-size: 0.65rem; letter-spacing: 0.05em;">Final Decision</small>
                                                <form method="POST" action="{{ route('admin.proposals.finalDecision', $proposal->id) }}">
                                                    @csrf
                                                    <div class="btn-group w-100">
                                                        <button type="submit" name="status" value="final_approved" class="btn btn-sm btn-success w-50 fw-semibold">Approve</button>
                                                        <button type="submit" name="status" value="final_rejected" class="btn btn-sm btn-danger w-50 fw-semibold">Reject</button>
                                                    </div>
                                                </form>
                                            </li>
                                            @if(in_array($proposal->status, ['final_approved', 'ongoing', 'completed', 'archived']))
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item py-2 text-success fw-bold d-flex align-items-center gap-2" href="{{ route('proposal.downloadNTP', $proposal->id) }}">
                                                        <i class="bi bi-file-earmark-pdf"></i> Download NTP
                                                    </a>
                                                </li>
                                            @endif
                                            @if($proposal->status === 'completed')
                                                <li><hr class="dropdown-divider"></li>
                                                <li class="px-3 py-2">
                                                    <form method="POST" action="{{ route('admin.proposals.archive', $proposal->id) }}" onsubmit="return confirm('Are you sure you want to archive this completed proposal?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-dark w-100 d-flex align-items-center justify-content-center gap-1.5">
                                                            <i class="bi bi-archive"></i> Archive Proposal
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-2 d-block mb-2 text-secondary"></i>
                                <h6 class="fw-bold mb-1">No Proposals Found</h6>
                                <span class="small">No research proposals have been submitted yet.</span>
                            </div>
                        @endforelse
                    </div>

                    <!-- Desktop View: Table (d-none d-md-block) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3 fw-medium border-0 rounded-start">Title &amp; Researcher</th>
                                    <th class="text-center py-3 fw-medium border-0">Status</th>
                                    <th class="text-center py-3 fw-medium border-0">Phase</th>
                                    <th class="pe-4 py-3 fw-medium border-0 rounded-end text-end">Management</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($proposals as $proposal)
                                    @php
                                        $statusConfig = match($proposal->status) {
                                            'approved', 'final_approved', 'completed' => [
                                                'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                                'label' => str_replace('_', ' ', $proposal->status)
                                            ],
                                            'pending', 'submitted', 'under_review' => [
                                                'class' => 'bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25',
                                                'label' => str_replace('_', ' ', $proposal->status)
                                            ],
                                            'rejected', 'final_rejected' => [
                                                'class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                                'label' => str_replace('_', ' ', $proposal->status)
                                            ],
                                            'accepted_for_in_house_review' => [
                                                'class' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                                'label' => 'In-House Review'
                                            ],
                                            'noted_by_dean', 'final_copy_noted_by_dean' => [
                                                'class' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                                                'label' => 'Dean Noted'
                                            ],
                                            'endorsed_to_vprei' => [
                                                'class' => 'bg-purple bg-opacity-10 border border-opacity-25',
                                                'label' => 'Endorsed VPREI'
                                            ],
                                            'revision_required' => [
                                                'class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                                'label' => 'Needs Revision'
                                            ],
                                            default => [
                                                'class' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                                                'label' => str_replace('_', ' ', $proposal->status)
                                            ]
                                        };
                                    @endphp
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge bg-white text-dark border fw-mono" style="font-size: 0.72rem;">
                                                    {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                                </span>
                                            </div>
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 320px;" title="{{ $proposal->title }}">
                                                {{ $proposal->title }}
                                            </div>
                                            <div class="d-flex align-items-center gap-2 text-muted small mt-1" style="font-size: 0.76rem;">
                                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 20px; height: 20px; font-size: 0.65rem;">
                                                    {{ strtoupper(substr($proposal->user->name ?? '?', 0, 1)) }}
                                                </div>
                                                <span>{{ $proposal->user->name ?? 'Unknown' }}</span>
                                                @if($proposal->assignments && $proposal->assignments->count() > 0)
                                                    <span class="text-muted">• {{ $proposal->assignments->count() }} reviewer(s)</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center py-3">
                                            <span class="badge {{ $statusConfig['class'] }} rounded-pill px-3 py-1.5 text-capitalize" style="font-size: 0.72rem;">
                                                {{ $statusConfig['label'] }}
                                            </span>
                                        </td>
                                        <td class="text-center py-3">
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                                Phase {{ $proposal->current_phase }}
                                            </span>
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 py-1" data-bs-toggle="dropdown" aria-expanded="false" title="Proposal Actions">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="min-width: 260px; z-index: 1050;">
                                                    <li>
                                                        <a class="dropdown-item rounded py-2 d-flex align-items-center gap-2" href="{{ route('proposal.show', $proposal->id) }}">
                                                            <i class="bi bi-eye text-primary"></i> View Full Details
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li class="px-3 py-2">
                                                        <small class="text-muted fw-bold text-uppercase d-block mb-1.5" style="font-size: 0.65rem; letter-spacing: 0.05em;">Advance Phase</small>
                                                        <form action="{{ route('admin.proposals.updatePhase', $proposal->id) }}" method="POST" class="d-flex gap-1.5">
                                                            @csrf @method('PATCH')
                                                            <select name="phase" class="form-select form-select-sm">
                                                                @for($i=1; $i<=6; $i++)
                                                                    <option value="{{ $i }}" {{ $proposal->current_phase == $i ? 'selected' : '' }}>Phase {{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                            <button type="submit" class="btn btn-sm btn-primary flex-shrink-0">Set</button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li class="px-3 py-2">
                                                        <small class="text-muted fw-bold text-uppercase d-block mb-1.5" style="font-size: 0.65rem; letter-spacing: 0.05em;">Assign Reviewer</small>
                                                        <form action="{{ route('admin.proposals.assign', $proposal->id) }}" method="POST" class="d-flex gap-1.5">
                                                            @csrf
                                                            <select name="reviewer_id" class="form-select form-select-sm">
                                                                <option value="">Select Reviewer...</option>
                                                                @foreach($reviewers as $reviewer)
                                                                    <option value="{{ $reviewer->id }}">{{ $reviewer->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <button type="submit" class="btn btn-sm btn-dark flex-shrink-0">Add</button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li class="px-3 py-2">
                                                        <small class="text-muted fw-bold text-uppercase d-block mb-1.5" style="font-size: 0.65rem; letter-spacing: 0.05em;">Final Decision</small>
                                                        <form method="POST" action="{{ route('admin.proposals.finalDecision', $proposal->id) }}">
                                                            @csrf
                                                            <div class="btn-group w-100">
                                                                <button type="submit" name="status" value="final_approved" class="btn btn-sm btn-success w-50 fw-semibold">Approve</button>
                                                                <button type="submit" name="status" value="final_rejected" class="btn btn-sm btn-danger w-50 fw-semibold">Reject</button>
                                                            </div>
                                                        </form>
                                                    </li>
                                                    @if(in_array($proposal->status, ['final_approved', 'ongoing', 'completed', 'archived']))
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item py-2 text-success fw-bold d-flex align-items-center gap-2" href="{{ route('proposal.downloadNTP', $proposal->id) }}">
                                                                <i class="bi bi-file-earmark-pdf"></i> Download NTP
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if($proposal->status === 'completed')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li class="px-3 py-2">
                                                            <form method="POST" action="{{ route('admin.proposals.archive', $proposal->id) }}" onsubmit="return confirm('Are you sure you want to archive this completed proposal?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-dark w-100 d-flex align-items-center justify-content-center gap-1.5">
                                                                    <i class="bi bi-archive"></i> Archive Proposal
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-journal-x fs-2 d-block mb-2 text-secondary"></i>
                                            <h6 class="fw-bold mb-1">No Proposals Found</h6>
                                            <span class="small">No research proposals have been submitted yet.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($proposals->hasPages())
                    <div class="card-footer bg-white border-top py-3 px-3 px-md-4">
                        {{ $proposals->appends(['announcements_page' => request('announcements_page')])->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Announcements Feed (Right 5 Cols Desktop, Full Width Mobile) -->
        <div class="col-lg-5 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Announcements</h5>
                        <span class="text-muted small" style="font-size: 0.75rem;">Campus &amp; portal broadcasts</span>
                    </div>
                    <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-xs d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal" style="font-size: 0.76rem;">
                        <i class="bi bi-plus-lg"></i> Post
                    </button>
                </div>
                <div class="card-body p-0">
                    <div style="max-height: 580px; overflow-y: auto;">
                        @forelse($announcements as $ann)
                            <div class="p-3 p-md-4 border-bottom">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1.5">
                                    <h6 class="fw-bold mb-0 text-dark small" style="line-height: 1.35;">{{ $ann->title }}</h6>
                                    <span class="badge bg-light text-muted border px-2 py-0.5 flex-shrink-0" style="font-size: 0.68rem;">
                                        {{ $ann->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                                <p class="text-muted mb-2 small" style="font-size: 0.78rem; line-height: 1.4;">
                                    {{ Str::limit($ann->content, 110) }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center pt-1">
                                    <div class="d-flex align-items-center gap-1.5 text-muted" style="font-size: 0.72rem;">
                                        <i class="bi bi-person-circle"></i>
                                        <span>{{ $ann->user->name ?? 'Admin' }}</span>
                                    </div>
                                    <form action="{{ route('announcements.destroy', $ann->id) }}" method="POST" onsubmit="return confirm('Delete this announcement?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none small d-inline-flex align-items-center gap-1" style="font-size: 0.74rem;">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-megaphone fs-2 d-block mb-1 text-secondary"></i>
                                <span class="small">No active announcements.</span>
                            </div>
                        @endforelse
                    </div>

                    @if($announcements->hasPages())
                        <div class="p-3 border-top bg-light rounded-bottom">
                            {{ $announcements->appends(['proposals_page' => request('proposals_page')])->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create Announcement Modal -->
    <div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 px-4 pt-4 pb-0 bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-megaphone fs-6"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" style="font-size: 1rem;">Create Announcement</h5>
                            <span class="text-muted small" style="font-size: 0.75rem;">Broadcast call for papers or updates</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('announcements.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Announcement Title</label>
                            <input type="text" name="title" class="form-control rounded-3 py-2 px-3 border" placeholder="e.g. Call for Research Papers 2026" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-dark small">Content</label>
                            <textarea name="content" class="form-control rounded-3 py-2 px-3 border" rows="5" placeholder="Enter complete announcement details..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-medium" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-xs">Publish Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>