<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Budget Certification & Funds Verification</h1>
            <p class="text-muted small mb-0">Welcome, <strong>Budget Officer {{ Auth::user()->name }}</strong> | Institutional Funds & Budget Allocation</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm rounded-3">
                <i class="bi bi-megaphone"></i> Announcements
            </a>
            <a href="{{ route('messages.index') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm rounded-3">
                <i class="bi bi-envelope"></i> Messages
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
                            <i class="bi bi-wallet2 h4 mb-0"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-medium">Processed</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['total_processed'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Total Financial Reviews</p>
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
                        @if(($stats['pending_certification'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-medium">Action Needed</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-3 py-2 fw-medium">Clear</span>
                        @endif
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['pending_certification'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Awaiting Budget Certification</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                            <i class="bi bi-cash-stack h4 mb-0"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-medium">Certified</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['certified_funds'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Funds Certified</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                            <i class="bi bi-currency-dollar h4 mb-0"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2 fw-medium">Active</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['active_funded'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Active Funded Grants</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- Budget Queues (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Budget Certification Center</h5>
                        <ul class="nav nav-pills card-header-pills" id="budgetTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active small py-1 px-3 fw-semibold rounded-pill" id="pending-budget-tab" data-bs-toggle="tab" data-bs-target="#pending-budget" type="button" role="tab">
                                    Awaiting Certification
                                    @if(($stats['pending_certification'] ?? 0) > 0)
                                        <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_certification'] }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill" id="certified-history-tab" data-bs-toggle="tab" data-bs-target="#certified-history" type="button" role="tab">
                                    Certified History
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill" id="all-proposals-tab" data-bs-toggle="tab" data-bs-target="#all-proposals" type="button" role="tab">
                                    Institutional Log
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="budgetTabsContent">
                        <!-- Tab 1: Proposals Awaiting Budget Certification -->
                        <div class="tab-pane fade show active" id="pending-budget" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">Proposal Code</th>
                                            <th class="py-3 fw-semibold border-0">Proposal Title</th>
                                            <th class="py-3 fw-semibold border-0">Researcher</th>
                                            <th class="py-3 fw-semibold border-0">Status</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($pendingCertification as $proposal)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <span class="badge bg-light text-dark border fw-mono fs-7">{{ $proposal->proposal_code ?? 'P-'.$proposal->id }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 200px;" title="{{ $proposal->title }}">
                                                        {{ $proposal->title }}
                                                    </div>
                                                    <div class="text-muted fs-7">{{ $proposal->research_field ?? 'General Research' }}</div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                                            {{ substr($proposal->user->name ?? '?', 0, 1) }}
                                                        </div>
                                                        <span class="small text-truncate" style="max-width: 120px;">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 fw-medium">
                                                        Awaiting Funds Cert.
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                            View Details
                                                        </a>
                                                        <form action="{{ route('budget.certify', $proposal->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" onclick="return confirm('Certify availability of funds for this proposal?');">
                                                                <i class="bi bi-cash-stack me-1"></i> Certify Funds
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                                    <p class="mb-0 fw-medium">No proposals currently awaiting budget certification.</p>
                                                    <span class="text-muted fs-7">Proposals noted by College Deans will appear here for fund verification.</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 2: Certified Proposals History -->
                        <div class="tab-pane fade" id="certified-history" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">Code</th>
                                            <th class="py-3 fw-semibold border-0">Title</th>
                                            <th class="py-3 fw-semibold border-0">Researcher</th>
                                            <th class="py-3 fw-semibold border-0">Status</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($certifiedProposals as $proposal)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <span class="badge bg-light text-dark border fw-mono fs-7">{{ $proposal->proposal_code ?? 'P-'.$proposal->id }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $proposal->title }}">
                                                        {{ $proposal->title }}
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="small text-muted">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-medium text-capitalize">
                                                        {{ str_replace('_', ' ', $proposal->status) }}
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                        View Details <i class="bi bi-arrow-right ms-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                                    <p class="mb-0 fw-medium">No certified proposals recorded yet.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: Institutional Log -->
                        <div class="tab-pane fade" id="all-proposals" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">Code</th>
                                            <th class="py-3 fw-semibold border-0">Title</th>
                                            <th class="py-3 fw-semibold border-0">Researcher</th>
                                            <th class="py-3 fw-semibold border-0">Status</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">View</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($allProposals as $proposal)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <span class="badge bg-light text-dark border fw-mono fs-7">{{ $proposal->proposal_code ?? 'P-'.$proposal->id }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $proposal->title }}">
                                                        {{ $proposal->title }}
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="small text-muted">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                                </td>
                                                <td class="py-3">
                                                    @php
                                                        $statusClass = match($proposal->status) {
                                                            'approved', 'final_approved', 'completed', 'funds_certified' => 'bg-success bg-opacity-10 text-success',
                                                            'pending', 'submitted', 'final_copy_noted_by_dean' => 'bg-warning bg-opacity-10 text-warning',
                                                            'rejected', 'final_rejected' => 'bg-danger bg-opacity-10 text-danger',
                                                            default => 'bg-secondary bg-opacity-10 text-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $statusClass }} rounded-pill px-3 py-1 fw-medium text-capitalize">
                                                        {{ str_replace('_', ' ', $proposal->status) }}
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                        View Details <i class="bi bi-arrow-right ms-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-folder2-open fs-2 d-block mb-2 text-secondary"></i>
                                                    No proposals recorded in the system.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Office Hub & Shortcuts (Right 4 Columns) -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <!-- Budget Office Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-white bg-opacity-20 rounded-3 p-3 text-white">
                            <i class="bi bi-bank fs-4"></i>
                        </div>
                        <span class="badge bg-white text-success rounded-pill px-3 py-1 fw-bold">Budget Office</span>
                    </div>
                    <h5 class="fw-bold mb-2">Funds Certification</h5>
                    <p class="text-white text-opacity-75 small mb-0">Verify budgetary allocation and issue official funds availability certification for institutional research grants prior to VPREI approval.</p>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Budget Tools</h5>
                    </div>
                    <div class="card-body p-4 pt-1">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('announcements.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-megaphone"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Announcements</div>
                                    <div class="text-muted fs-7">Institutional notices & guidelines</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-purple bg-opacity-10 text-purple rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; color: #8B5CF6;">
                                    <i class="bi bi-archive"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted fs-7">Browse funded research archives</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-chat-left-dots"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Internal Messages</div>
                                    <div class="text-muted fs-7">Communicate with Director & Deans</div>
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
