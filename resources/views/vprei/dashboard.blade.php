<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fs-4 fs-md-3 fw-bold text-dark mb-1">VPREI Executive Approvals & NTP Issuance</h1>
            <p class="text-muted small mb-0">Welcome, <strong>Vice President {{ Auth::user()->name }}</strong> | Office of the Vice President for Research, Extension & Innovation</p>
        </div>
        <div class="d-flex gap-2 w-100 w-md-auto">
            <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 shadow-sm rounded-pill px-3 py-2 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.85rem;">
                <i class="bi bi-megaphone"></i> Announcements
            </a>
            <a href="{{ route('messages.index') }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm rounded-pill px-3 py-2 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.85rem;">
                <i class="bi bi-envelope"></i> Messages
            </a>
        </div>
    </div>

    <!-- Stats Grid (2x2 on Mobile, 4x1 on Desktop) -->
    <div class="row g-2 g-md-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 mb-md-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 p-md-3">
                            <i class="bi bi-building fs-5 fs-md-4 mb-0"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 px-md-3 py-md-1.5 fw-medium small d-none d-sm-inline-block">University</span>
                    </div>
                    <h2 class="h3 fs-4 fs-md-3 fw-bold mb-1">{{ $stats['total_reviews'] ?? 0 }}</h2>
                    <p class="text-muted mb-0 fw-medium text-wrap" style="font-size: 0.72rem; line-height: 1.25;">Total University Proposals</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 mb-md-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 p-md-3">
                            <i class="bi bi-award fs-5 fs-md-4 mb-0"></i>
                        </div>
                        @if(($stats['pending_approval'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 px-md-3 py-md-1.5 fw-medium small d-none d-sm-inline-block">Action Needed</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-2 py-1 px-md-3 py-md-1.5 fw-medium small d-none d-sm-inline-block">Clear</span>
                        @endif
                    </div>
                    <h2 class="h3 fs-4 fs-md-3 fw-bold mb-1">{{ $stats['pending_approval'] ?? 0 }}</h2>
                    <p class="text-muted mb-0 fw-medium text-wrap" style="font-size: 0.72rem; line-height: 1.25;">Awaiting Final VPREI</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 mb-md-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 p-md-3">
                            <i class="bi bi-patch-check fs-5 fs-md-4 mb-0"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 px-md-3 py-md-1.5 fw-medium small d-none d-sm-inline-block">NTP Issued</span>
                    </div>
                    <h2 class="h3 fs-4 fs-md-3 fw-bold mb-1">{{ $stats['final_approved'] ?? 0 }}</h2>
                    <p class="text-muted mb-0 fw-medium text-wrap" style="font-size: 0.72rem; line-height: 1.25;">Approved & NTP Issued</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 mb-md-3">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 p-md-3">
                            <i class="bi bi-play-circle fs-5 fs-md-4 mb-0"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 py-1 px-md-3 py-md-1.5 fw-medium small d-none d-sm-inline-block">Phase 4</span>
                    </div>
                    <h2 class="h3 fs-4 fs-md-3 fw-bold mb-1">{{ $stats['active_ongoing'] ?? 0 }}</h2>
                    <p class="text-muted mb-0 fw-medium text-wrap" style="font-size: 0.72rem; line-height: 1.25;">Active Ongoing Projects</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- VPREI Executive Center (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2.5">
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-primary"></i> Executive Approval Center
                        </h5>
                        <div class="w-100 w-md-auto overflow-x-auto pb-1 pb-md-0" style="-webkit-overflow-scrolling: touch;">
                            <ul class="nav nav-pills card-header-pills flex-nowrap gap-1.5" id="vpreiTabs" role="tablist" style="width: max-content;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active small py-1.5 px-3 fw-semibold rounded-pill text-nowrap" id="pending-vprei-tab" data-bs-toggle="tab" data-bs-target="#pending-vprei" type="button" role="tab">
                                        Awaiting Approval
                                        @if(($stats['pending_approval'] ?? 0) > 0)
                                            <span class="badge bg-warning text-dark ms-1 rounded-pill">{{ $stats['pending_approval'] }}</span>
                                        @endif
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link small py-1.5 px-3 fw-semibold rounded-pill text-nowrap" id="approved-ntp-tab" data-bs-toggle="tab" data-bs-target="#approved-ntp" type="button" role="tab">
                                        Approved & NTP Issued
                                        @if(($stats['final_approved'] ?? 0) > 0)
                                            <span class="badge bg-success bg-opacity-25 text-white ms-1 rounded-pill">{{ $stats['final_approved'] }}</span>
                                        @endif
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link small py-1.5 px-3 fw-semibold rounded-pill text-nowrap" id="all-univ-tab" data-bs-toggle="tab" data-bs-target="#all-univ" type="button" role="tab">
                                        Institutional Log
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="vpreiTabsContent">
                        <!-- Tab 1: Proposals Awaiting Final VPREI Approval -->
                        <div class="tab-pane fade show active" id="pending-vprei" role="tabpanel">
                            <!-- Mobile Proposal Cards (d-md-none) -->
                            <div class="p-3 d-flex flex-column gap-3 d-md-none">
                                @forelse($proposals as $proposal)
                                    <div class="p-3 bg-light rounded-3 border shadow-xs">
                                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                            <span class="badge bg-white text-dark border fw-mono fs-7">{{ $proposal->proposal_code ?? 'P-'.$proposal->id }}</span>
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                                Endorsed to VPREI
                                            </span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1 text-break" style="font-size: 0.95rem; line-height: 1.35;">
                                            {{ $proposal->title }}
                                        </h6>
                                        <div class="text-muted small mb-2.5" style="font-size: 0.78rem;">
                                            <i class="bi bi-tag me-1"></i> {{ $proposal->research_field ?? 'General Research' }}
                                        </div>
                                        
                                        <div class="p-2.5 bg-white rounded-3 border mb-3 d-flex align-items-center gap-2">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ substr($proposal->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="small fw-semibold text-dark text-truncate">{{ $proposal->user->name ?? 'Unknown' }}</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">{{ $proposal->user->department ?? 'N/A' }}</div>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-column gap-2">
                                            <form action="{{ route('vprei.approve', $proposal->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-1.5" onclick="return confirm('Grant final approval and issue the Notice to Proceed (NTP) for this proposal?');">
                                                    <i class="bi bi-check-circle-fill"></i> Approve & Issue NTP
                                                </button>
                                            </form>
                                            <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-outline-secondary w-100 rounded-pill py-1.5 fw-semibold d-flex align-items-center justify-content-center gap-1">
                                                View Full Dossier <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                        <p class="mb-1 fw-bold text-dark">No proposals currently awaiting approval</p>
                                        <span class="text-muted small">Proposals endorsed by the Research Director will appear here for final NTP issuance.</span>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">Proposal Code</th>
                                            <th class="py-3 fw-semibold border-0">Proposal Title</th>
                                            <th class="py-3 fw-semibold border-0">Researcher & Dept</th>
                                            <th class="py-3 fw-semibold border-0">Status</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($proposals as $proposal)
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
                                                        <div>
                                                            <div class="small fw-semibold text-dark">{{ $proposal->user->name ?? 'Unknown' }}</div>
                                                            <div class="text-muted fs-7">{{ $proposal->user->department ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-medium">
                                                        Endorsed to VPREI
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                            View
                                                        </a>
                                                        <form action="{{ route('vprei.approve', $proposal->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" onclick="return confirm('Grant final approval and issue the Notice to Proceed (NTP) for this proposal?');">
                                                                <i class="bi bi-check-circle-fill me-1"></i> Approve & Issue NTP
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                                    <p class="mb-0 fw-medium">No proposals currently awaiting final VPREI approval.</p>
                                                    <span class="text-muted fs-7">Proposals endorsed by the Research Director will appear here for final NTP issuance.</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 2: Approved & NTP Issued History -->
                        <div class="tab-pane fade" id="approved-ntp" role="tabpanel">
                            <!-- Mobile Approved Cards (d-md-none) -->
                            <div class="p-3 d-flex flex-column gap-3 d-md-none">
                                @forelse($approvedProposals as $proposal)
                                    <div class="p-3 bg-light rounded-3 border shadow-xs">
                                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                            <span class="badge bg-white text-dark border fw-mono fs-7">{{ $proposal->proposal_code ?? 'P-'.$proposal->id }}</span>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                                <i class="bi bi-file-earmark-check me-1"></i> NTP Issued
                                            </span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1 text-break" style="font-size: 0.95rem; line-height: 1.35;">
                                            {{ $proposal->title }}
                                        </h6>
                                        <div class="text-muted small mb-2.5" style="font-size: 0.78rem;">
                                            <i class="bi bi-person me-1"></i> {{ $proposal->user->name ?? 'Unknown' }}
                                        </div>
                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-outline-primary w-100 rounded-pill py-1.5 fw-semibold d-flex align-items-center justify-content-center gap-1">
                                            View Details <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0 fw-medium">No approved proposals recorded yet.</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop Approved Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
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
                                        @forelse($approvedProposals as $proposal)
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
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-medium">
                                                        <i class="bi bi-file-earmark-check me-1"></i> NTP Issued
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
                                                    <p class="mb-0 fw-medium">No approved proposals recorded yet.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: University Institutional Log -->
                        <div class="tab-pane fade" id="all-univ" role="tabpanel">
                            <!-- Mobile All Proposals Cards (d-md-none) -->
                            <div class="p-3 d-flex flex-column gap-3 d-md-none">
                                @forelse($allProposals as $proposal)
                                    <div class="p-3 bg-light rounded-3 border shadow-xs">
                                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                            <span class="badge bg-white text-dark border fw-mono fs-7">{{ $proposal->proposal_code ?? 'P-'.$proposal->id }}</span>
                                            @php
                                                $statusClass = match($proposal->status) {
                                                    'approved', 'final_approved', 'completed' => 'bg-success bg-opacity-10 text-success',
                                                    'pending', 'submitted', 'endorsed_to_vprei' => 'bg-warning bg-opacity-10 text-warning',
                                                    'rejected', 'final_rejected' => 'bg-danger bg-opacity-10 text-danger',
                                                    default => 'bg-secondary bg-opacity-10 text-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill px-2.5 py-1 fw-bold text-capitalize" style="font-size: 0.72rem;">
                                                {{ str_replace('_', ' ', $proposal->status) }}
                                            </span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1 text-break" style="font-size: 0.95rem; line-height: 1.35;">
                                            {{ $proposal->title }}
                                        </h6>
                                        <div class="text-muted small mb-2.5" style="font-size: 0.78rem;">
                                            <i class="bi bi-person me-1"></i> {{ $proposal->user->name ?? 'Unknown' }}
                                        </div>
                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-outline-secondary w-100 rounded-pill py-1.5 fw-semibold d-flex align-items-center justify-content-center gap-1">
                                            View Details <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-folder2-open fs-2 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0 fw-medium">No institutional proposals recorded.</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop All Proposals Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
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
                                                            'approved', 'final_approved', 'completed' => 'bg-success bg-opacity-10 text-success',
                                                            'pending', 'submitted', 'endorsed_to_vprei' => 'bg-warning bg-opacity-10 text-warning',
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
                                                    No institutional proposals recorded.
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

        <!-- Executive Hub & Shortcuts (Right 4 Columns) -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3 gap-md-4">
                <!-- VPREI Executive Card (UA Maroon & Gold Gradient) -->
                <div class="card border-0 shadow-sm rounded-4 text-white p-4 position-relative overflow-hidden" 
                     style="background: linear-gradient(135deg, #8C1D2C 0%, #4A0E18 100%); border-left: 5px solid #E5A93C !important;">
                    <div class="position-absolute end-0 bottom-0 pe-none" style="pointer-events: none; z-index: 0; line-height: 1; transform: translate(15%, 20%);">
                        <i class="bi bi-award-fill" style="font-size: 7.5rem; color: rgba(229, 169, 60, 0.08);"></i>
                    </div>
                    <div class="position-relative z-1">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 p-2.5 text-white" style="background: rgba(229, 169, 60, 0.2); border: 1px solid rgba(229, 169, 60, 0.4);">
                                <i class="bi bi-award fs-4 text-warning"></i>
                            </div>
                            <span class="badge rounded-pill px-3 py-1 fw-bold text-uppercase" style="background-color: rgba(229, 169, 60, 0.25); color: #FDE047; border: 1px solid rgba(229, 169, 60, 0.5); font-size: 0.72rem; letter-spacing: 0.05em;">
                                Executive Office
                            </span>
                        </div>
                        <h5 class="fw-bold mb-2 text-white" style="letter-spacing: -0.01em;">VPREI Final Approval</h5>
                        <p class="text-white text-opacity-85 small mb-0" style="line-height: 1.5;">
                            Grant final executive authorization and issue the official <strong>Notice to Proceed (NTP)</strong> to transition institutional research proposals into Phase 4 (Ongoing Implementation).
                        </p>
                    </div>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-grid-fill text-muted"></i> Executive Tools
                        </h5>
                    </div>
                    <div class="card-body p-3 p-md-4 pt-1">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('announcements.index') }}" class="list-group-item list-group-item-action border-0 px-2 py-2.5 rounded-3 d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                    <i class="bi bi-megaphone fs-5"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark small">Announcements</div>
                                    <div class="text-muted" style="font-size: 0.72rem; line-height: 1.25;">Broadcast university guidelines</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-2 py-2.5 rounded-3 d-flex align-items-center gap-3">
                                <div class="bg-purple bg-opacity-10 text-purple rounded-3 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; color: #8B5CF6;">
                                    <i class="bi bi-archive fs-5"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted" style="font-size: 0.72rem; line-height: 1.25;">Browse completed studies</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-2 py-2.5 rounded-3 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                    <i class="bi bi-chat-left-dots fs-5"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark small">Internal Messages</div>
                                    <div class="text-muted" style="font-size: 0.72rem; line-height: 1.25;">Executive communications</div>
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
