<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Reviewer Dashboard</h1>
            <p class="text-muted small mb-0">Evaluate assigned research proposals, submit peer feedback, and manage review tasks.</p>
        </div>
        <div class="row g-2 w-100 w-md-auto m-0">
            <div class="col-12 col-md-auto p-0">
                <a href="{{ route('reviewer.proposals') }}" class="btn btn-sm btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm rounded-3 w-100 py-2 px-3 fw-semibold" style="font-size: 0.85rem;">
                    <i class="bi bi-journal-check"></i> <span>Go to Review Queue</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid (2x2 on Mobile, 4 Columns on Desktop) -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                            <i class="bi bi-file-earmark-person fs-5"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Assigned</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['assigned'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Total Assigned Proposals</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                            <i class="bi bi-hourglass-split fs-5"></i>
                        </div>
                        @if(($stats['pending_review'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Action</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Clear</span>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['pending_review'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Pending Evaluation</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                            <i class="bi bi-check2-circle fs-5"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Evaluated</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['evaluated'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Evaluations Submitted</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                            <i class="bi bi-award fs-5"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Finished</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['completed'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Completed Studies</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- Assigned Proposals (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Assigned Review Queue</h5>
                        <span class="text-muted small d-md-none" style="font-size: 0.72rem;">Proposals assigned for your peer evaluation</span>
                    </div>
                    <a href="{{ route('reviewer.proposals') }}" class="small fw-semibold text-primary text-decoration-none d-flex align-items-center gap-1 text-nowrap">
                        <span>View All</span> <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="card-body p-0">
                    <!-- Mobile View: Proposal Cards (d-md-none) -->
                    <div class="d-md-none p-3 bg-light bg-opacity-25">
                        @forelse($recentAssigned as $proposal)
                            <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                        <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.72rem;">
                                            {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                        </span>
                                        @php
                                            $statusClass = match($proposal->status) {
                                                'approved', 'final_approved', 'completed' => 'bg-success bg-opacity-10 text-success',
                                                'pending', 'submitted', 'under_review', 'accepted_for_in_house_review' => 'bg-warning bg-opacity-10 text-warning',
                                                'rejected', 'final_rejected' => 'bg-danger bg-opacity-10 text-danger',
                                                default => 'bg-secondary bg-opacity-10 text-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }} rounded-pill px-2.5 py-1 fw-semibold text-capitalize" style="font-size: 0.72rem;">
                                            {{ str_replace('_', ' ', $proposal->status) }}
                                        </span>
                                    </div>

                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="text-dark text-decoration-none">
                                            {{ $proposal->title }}
                                        </a>
                                    </h6>
                                    <div class="text-muted small mb-2" style="font-size: 0.76rem;">
                                        {{ $proposal->research_field ?? 'General Research' }}
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center bg-light rounded-2 px-2.5 py-2 mb-2.5" style="font-size: 0.78rem;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                {{ substr($proposal->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <span class="text-truncate" style="max-width: 140px;">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                        </div>
                                        <span class="text-muted" style="font-size: 0.72rem;">Lead Proponent</span>
                                    </div>

                                    <div class="d-flex gap-2 pt-2 border-top">
                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill flex-fill text-center py-1.5" style="font-size: 0.8rem;">
                                            View Details
                                        </a>
                                        <a href="{{ route('reviewer.proposals') }}" class="btn btn-sm btn-primary rounded-pill flex-fill text-center py-1.5 shadow-xs" style="font-size: 0.8rem;">
                                            <i class="bi bi-pencil-square me-1"></i> Review
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                <p class="mb-1 fw-medium small">No pending proposals assigned for evaluation.</p>
                                <span class="text-muted" style="font-size: 0.72rem;">Assigned proposals will appear here automatically.</span>
                            </div>
                        @endforelse
                    </div>

                    <!-- Desktop View: Table (d-none d-md-block) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0 border-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3 fw-semibold border-0">Code</th>
                                    <th class="py-3 fw-semibold border-0">Proposal Title</th>
                                    <th class="py-3 fw-semibold border-0">Researcher</th>
                                    <th class="py-3 fw-semibold border-0">Status</th>
                                    <th class="pe-4 py-3 fw-semibold border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($recentAssigned as $proposal)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <span class="badge bg-light text-dark border fw-mono fs-7">{{ $proposal->proposal_code ?? 'P-'.$proposal->id }}</span>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $proposal->title }}">
                                                {{ $proposal->title }}
                                            </div>
                                            <div class="text-muted fs-7">{{ $proposal->research_field ?? 'General Research' }}</div>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                                    {{ substr($proposal->user->name ?? '?', 0, 1) }}
                                                </div>
                                                <span class="small text-truncate" style="max-width: 130px;">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @php
                                                $statusClass = match($proposal->status) {
                                                    'approved', 'final_approved', 'completed' => 'bg-success bg-opacity-10 text-success',
                                                    'pending', 'submitted', 'under_review', 'accepted_for_in_house_review' => 'bg-warning bg-opacity-10 text-warning',
                                                    'rejected', 'final_rejected' => 'bg-danger bg-opacity-10 text-danger',
                                                    default => 'bg-secondary bg-opacity-10 text-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill px-3 py-1 fw-medium text-capitalize">
                                                {{ str_replace('_', ' ', $proposal->status) }}
                                            </span>
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                    View Details
                                                </a>
                                                <a href="{{ route('reviewer.proposals') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                                    <i class="bi bi-pencil-square me-1"></i> Review
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                            <p class="mb-1 fw-medium">No pending proposals assigned for evaluation.</p>
                                            <span class="text-muted fs-7">Assigned proposals will appear here automatically.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviewer Shortcuts & Guidelines (Right 4 Columns) -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <!-- Review Queue CTA Box (Themed with University Maroon & Gold) -->
                <div class="card border-0 shadow-sm rounded-4 text-white p-4" style="background: linear-gradient(135deg, #8C1D2C 0%, #4A0E18 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2);">
                            <i class="bi bi-file-earmark-check fs-4" style="color: var(--ua-gold);"></i>
                        </div>
                        <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(229, 169, 60, 0.2); color: var(--ua-gold); border: 1px solid rgba(229, 169, 60, 0.4);">
                            Peer Review
                        </span>
                    </div>
                    <h5 class="fw-bold mb-2 text-white">Review Queue</h5>
                    <p class="text-white text-opacity-75 small mb-3 lh-base">Provide constructive evaluation, grade methodology, and issue recommendations for assigned research works.</p>
                    <a href="{{ route('reviewer.proposals') }}" class="btn btn-light fw-bold rounded-pill py-2 w-100 shadow-sm text-primary d-flex align-items-center justify-content-center gap-2" style="color: var(--ua-maroon) !important;">
                        <span>Open Review Queue</span> <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Reviewer Tools</h5>
                    </div>
                    <div class="card-body p-3 p-md-4 pt-1">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('reviewer.proposals') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-list-check"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark small">Assigned Queue</div>
                                    <div class="text-muted fs-7 text-truncate">Proposals pending your decision</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark small">Internal Messages</div>
                                    <div class="text-muted fs-7 text-truncate">Communicate with administrators</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                                    <i class="bi bi-archive"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted fs-7 text-truncate">Reference past approved studies</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>