<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Reviewer Dashboard</h1>
            <p class="text-muted small mb-0">Evaluate assigned research proposals, submit peer feedback, and manage review tasks.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reviewer.proposals') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm rounded-3">
                <i class="bi bi-journal-check"></i> Go to Review Queue
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                            <i class="bi bi-file-earmark-person h4 mb-0"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-medium">Assigned</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['assigned'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Total Assigned Proposals</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                            <i class="bi bi-hourglass-split h4 mb-0"></i>
                        </div>
                        @if(($stats['pending_review'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-medium">Action Needed</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-3 py-2 fw-medium">Clear</span>
                        @endif
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['pending_review'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Pending Evaluation</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                            <i class="bi bi-check2-circle h4 mb-0"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-medium">Completed</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['evaluated'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Evaluations Submitted</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                            <i class="bi bi-award h4 mb-0"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2 fw-medium">Finished</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['completed'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Completed Studies</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- Assigned Proposals Table (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Assigned Review Queue</h5>
                    <a href="{{ route('reviewer.proposals') }}" class="small fw-semibold text-primary text-decoration-none">View All Queue <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
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
                                            <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                                <i class="bi bi-search me-1"></i> Review
                                            </a>
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
                <!-- Review Queue CTA Box -->
                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-white bg-opacity-10 rounded-3 p-3 text-white">
                            <i class="bi bi-file-earmark-check fs-4"></i>
                        </div>
                        <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold">Peer Review</span>
                    </div>
                    <h5 class="fw-bold mb-2">Review Queue</h5>
                    <p class="text-white text-opacity-75 small mb-3">Provide constructive evaluation, grade methodology, and issue recommendations.</p>
                    <a href="{{ route('reviewer.proposals') }}" class="btn btn-primary fw-bold rounded-3 py-2 w-100 shadow-sm">
                        <i class="bi bi-arrow-right-circle me-1"></i> Open Review Queue
                    </a>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Reviewer Tools</h5>
                    </div>
                    <div class="card-body p-4 pt-1">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('reviewer.proposals') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-list-check"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Assigned Queue</div>
                                    <div class="text-muted fs-7">Proposals pending your decision</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Internal Messages</div>
                                    <div class="text-muted fs-7">Communicate with administrators</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-purple bg-opacity-10 text-purple rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; color: #8B5CF6;">
                                    <i class="bi bi-archive"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted fs-7">Reference past approved studies</div>
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