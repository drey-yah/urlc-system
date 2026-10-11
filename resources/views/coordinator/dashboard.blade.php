<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Coordinator Endorsements</h1>
            <p class="text-muted small mb-0">
                Welcome, <strong>{{ Auth::user()->name }}</strong> | 
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold">{{ strtoupper($department) }} Department</span>
            </p>
        </div>
        <div class="row g-2 w-100 w-md-auto m-0">
            <div class="col-6 col-md-auto p-0 pe-1 pe-md-0">
                <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center gap-1.5 shadow-sm rounded-3 w-100 py-2 px-2" style="font-size: 0.8rem;">
                    <i class="bi bi-megaphone"></i> <span class="text-truncate">Announcements</span>
                </a>
            </div>
            <div class="col-6 col-md-auto p-0 ps-1 ps-md-0">
                <a href="{{ route('messages.index') }}" class="btn btn-sm btn-primary d-flex align-items-center justify-content-center gap-1.5 shadow-sm rounded-3 w-100 py-2 px-2" style="font-size: 0.8rem;">
                    <i class="bi bi-envelope"></i> <span class="text-truncate">Messages</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid (2x2 on Mobile, 4 Cols on Desktop) -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                            <i class="bi bi-building fs-5"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">College</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['total_dept'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Total Submissions</p>
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
                        @if(($stats['awaiting_endorsement'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Action</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Clear</span>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['awaiting_endorsement'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Awaiting Endorse</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                            <i class="bi bi-vector-pen fs-5"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Dean Review</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['pending_dean'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Pending Dean</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                            <i class="bi bi-send-check fs-5"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Ready</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['ready_for_unit'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Ready for Unit</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- Endorsement Queues & History (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
                            <h5 class="fw-bold mb-0 text-dark">Endorsement Center</h5>
                            <a href="{{ route('coordinator.batch_endorsement_form') }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold align-self-start align-self-sm-auto">
                                <i class="bi bi-printer-fill me-1"></i> Print RESU-FM-003 Form
                            </a>
                        </div>
                        <ul class="nav nav-pills card-header-pills flex-nowrap overflow-x-auto pb-1" id="coordinatorTabs" role="tablist" style="scrollbar-width: none; -ms-overflow-style: none;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="awaiting-tab" data-bs-toggle="tab" data-bs-target="#awaiting-queue" type="button" role="tab">
                                    Awaiting Endorsement 
                                    @if(($stats['awaiting_endorsement'] ?? 0) > 0)
                                        <span class="badge bg-warning text-dark ms-1">{{ $stats['awaiting_endorsement'] }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="noted-tab" data-bs-toggle="tab" data-bs-target="#noted-queue" type="button" role="tab">
                                    Dean Noted 
                                    @if(($stats['ready_for_unit'] ?? 0) > 0)
                                        <span class="badge bg-success text-white ms-1">{{ $stats['ready_for_unit'] }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-queue" type="button" role="tab">
                                    All Submissions
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="local-forum-tab" data-bs-toggle="tab" data-bs-target="#local-forum-queue" type="button" role="tab">
                                    <i class="bi bi-people-fill me-1"></i> Local Forum (Appendix D)
                                    @if(($stats['pending_forum_endorsements'] ?? 0) > 0)
                                        <span class="badge bg-primary text-white ms-1">{{ $stats['pending_forum_endorsements'] }}</span>
                                    @endif
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="coordinatorTabsContent">
                        <!-- Tab 1: Proposals Awaiting Endorsement -->
                        <div class="tab-pane fade show active" id="awaiting-queue" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($proposals as $proposal)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.7rem;">
                                                    {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                                </span>
                                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                    <i class="bi bi-hourglass-split me-1"></i>Pending Endorsement
                                                </span>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="text-dark text-decoration-none hover-primary transition">
                                                    {{ $proposal->title }}
                                                </a>
                                            </h6>
                                            <div class="d-flex align-items-center gap-2 text-muted small mb-2" style="font-size: 0.76rem;">
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 22px; height: 22px; font-size: 0.65rem;">
                                                    {{ substr($proposal->user->name ?? '?', 0, 1) }}
                                                </div>
                                                <span>{{ $proposal->user->name ?? 'Unknown' }}</span>
                                                <span class="text-muted">• {{ $proposal->research_field ?? 'General Research' }}</span>
                                            </div>
                                            <div class="d-flex gap-1.5 pt-2 border-top">
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill flex-fill text-center" style="font-size: 0.75rem;">
                                                    View
                                                </a>
                                                <form action="{{ route('coordinator.proposals.endorse', $proposal->id) }}" method="POST" class="d-flex gap-1.5 flex-fill">
                                                    @csrf
                                                    <button type="submit" name="action" value="return" class="btn btn-sm btn-outline-danger rounded-pill flex-fill text-center" style="font-size: 0.75rem;" onclick="return confirm('Return this proposal to researcher for revision?');">
                                                        Return
                                                    </button>
                                                    <button type="submit" name="action" value="endorse" class="btn btn-sm btn-success rounded-pill flex-fill text-center shadow-xs" style="font-size: 0.75rem;" onclick="return confirm('Endorse this proposal to the Dean?');">
                                                        Endorse
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                        <p class="mb-0 fw-medium small">No proposals awaiting endorsement.</p>
                                        <span class="text-muted" style="font-size: 0.72rem;">New submissions from your department will appear here.</span>
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
                                            <th class="py-3 fw-semibold border-0">Researcher</th>
                                            <th class="py-3 fw-semibold border-0">Status</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Actions</th>
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
                                                        <span class="small text-truncate" style="max-width: 120px;">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 fw-medium">
                                                        Pending Endorsement
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                            View
                                                        </a>
                                                        <form action="{{ route('coordinator.proposals.endorse', $proposal->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" name="action" value="return" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Return this proposal to researcher for revision?');">
                                                                Return
                                                            </button>
                                                            <button type="submit" name="action" value="endorse" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" onclick="return confirm('Endorse this proposal to the Dean?');">
                                                                Endorse
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                                    <p class="mb-0 fw-medium">No proposals currently awaiting endorsement.</p>
                                                    <span class="text-muted fs-7">New submissions from your department will appear here.</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 2: Proposals Noted by Dean -->
                        <div class="tab-pane fade" id="noted-queue" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($notedProposals as $proposal)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.7rem;">
                                                    {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                                </span>
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                    <i class="bi bi-check2-circle me-1"></i>Noted by Dean
                                                </span>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="text-dark text-decoration-none hover-primary transition">
                                                    {{ $proposal->title }}
                                                </a>
                                            </h6>
                                            <div class="d-flex align-items-center gap-2 text-muted small mb-2" style="font-size: 0.76rem;">
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 22px; height: 22px; font-size: 0.65rem;">
                                                    {{ substr($proposal->user->name ?? '?', 0, 1) }}
                                                </div>
                                                <span>{{ $proposal->user->name ?? 'Unknown' }}</span>
                                            </div>
                                            <div class="d-flex gap-2 pt-2 border-top">
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3" style="font-size: 0.75rem;">
                                                    View
                                                </a>
                                                <form action="{{ route('coordinator.proposals.submitToUnit', $proposal->id) }}" method="POST" class="flex-grow-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100 shadow-xs" style="font-size: 0.75rem;" onclick="return confirm('Submit this endorsement list to the Research Unit?');">
                                                        <i class="bi bi-send me-1"></i> Submit to Unit
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0 fw-medium small">No Dean-noted proposals awaiting submission to the Research Unit.</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">Proposal Code</th>
                                            <th class="py-3 fw-semibold border-0">Title</th>
                                            <th class="py-3 fw-semibold border-0">Researcher</th>
                                            <th class="py-3 fw-semibold border-0">Status</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($notedProposals as $proposal)
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
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                                            {{ substr($proposal->user->name ?? '?', 0, 1) }}
                                                        </div>
                                                        <span class="small text-truncate" style="max-width: 120px;">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-medium">
                                                        Noted by Dean
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                            View
                                                        </a>
                                                        <form action="{{ route('coordinator.proposals.submitToUnit', $proposal->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" onclick="return confirm('Submit this endorsement list to the Research Unit?');">
                                                                <i class="bi bi-send me-1"></i> Submit to Unit
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                                    <p class="mb-0 fw-medium">No Dean-noted proposals awaiting submission to the Research Unit.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: Department History -->
                        <div class="tab-pane fade" id="history-queue" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($allDeptProposals as $proposal)
                                    @php
                                        $statusClass = match($proposal->status) {
                                            'approved', 'final_approved', 'completed' => 'bg-success bg-opacity-10 text-success',
                                            'pending', 'submitted', 'pending_coordinator_endorsement', 'pending_dean_noting' => 'bg-warning bg-opacity-10 text-warning',
                                            'rejected', 'final_rejected' => 'bg-danger bg-opacity-10 text-danger',
                                            default => 'bg-secondary bg-opacity-10 text-secondary'
                                        };
                                    @endphp
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.7rem;">
                                                    {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                                </span>
                                                <span class="badge {{ $statusClass }} rounded-pill px-2.5 py-1 text-capitalize fw-semibold" style="font-size: 0.7rem;">
                                                    {{ str_replace('_', ' ', $proposal->status) }}
                                                </span>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="text-dark text-decoration-none hover-primary transition">
                                                    {{ $proposal->title }}
                                                </a>
                                            </h6>
                                            <div class="d-flex justify-content-between align-items-center text-muted small pt-2 border-top" style="font-size: 0.76rem;">
                                                <span>Lead: {{ $proposal->user->name ?? 'Unknown' }}</span>
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="text-primary text-decoration-none fw-semibold">
                                                    View Details <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-folder2-open fs-2 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0 fw-medium small">No department proposals recorded.</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop Table (d-none d-md-block) -->
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
                                        @forelse($allDeptProposals as $proposal)
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
                                                            'pending', 'submitted', 'pending_coordinator_endorsement', 'pending_dean_noting' => 'bg-warning bg-opacity-10 text-warning',
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
                                                        View <i class="bi bi-arrow-right ms-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-folder2-open fs-2 d-block mb-2 text-secondary"></i>
                                                    No department proposals recorded.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 4: Local Forum Submissions (Appendix D) -->
                        <div class="tab-pane fade" id="local-forum-queue" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($forumSubmissions as $sub)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.7rem;">
                                                    <i class="bi bi-calendar-event me-1 text-primary"></i>{{ $sub->forum->title ?? 'Local Forum' }}
                                                </span>
                                                @if($sub->coordinator_endorsed)
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                        <i class="bi bi-check-circle me-1"></i>Endorsed
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                        <i class="bi bi-hourglass-split me-1"></i>Awaiting Endorsement
                                                    </span>
                                                @endif
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                                {{ $sub->paper_title }}
                                            </h6>
                                            <div class="d-flex align-items-center gap-2 text-muted small mb-2" style="font-size: 0.76rem;">
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 22px; height: 22px; font-size: 0.65rem;">
                                                    {{ substr($sub->user->name ?? '?', 0, 1) }}
                                                </div>
                                                <span>{{ $sub->user->name ?? 'Unknown' }}</span>
                                            </div>
                                            <div class="d-flex gap-2 pt-2 border-top">
                                                <a href="{{ route('proposal.show', $sub->research_proposal_id) }}" class="btn btn-sm btn-light border rounded-pill px-3" style="font-size: 0.75rem;">
                                                    View Proposal
                                                </a>
                                                @if(!$sub->coordinator_endorsed)
                                                    <form action="{{ route('local_forum.endorse', $sub->id) }}" method="POST" class="flex-grow-1">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100 shadow-xs" style="font-size: 0.75rem;" onclick="return confirm('Endorse this paper output to the Research Director for the Local Forum?');">
                                                            <i class="bi bi-hand-thumbs-up me-1"></i> Endorse Output
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-easel2 fs-2 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0 fw-medium small">No local forum paper submissions from your college yet.</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">Paper Title & Forum</th>
                                            <th class="py-3 fw-semibold border-0">Researcher</th>
                                            <th class="py-3 fw-semibold border-0">Status</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($forumSubmissions as $sub)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 240px;" title="{{ $sub->paper_title }}">
                                                        {{ $sub->paper_title }}
                                                    </div>
                                                    <div class="text-muted fs-7">
                                                        <i class="bi bi-calendar-event me-1 text-primary"></i> {{ $sub->forum->title ?? 'Local Forum' }}
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                                            {{ substr($sub->user->name ?? '?', 0, 1) }}
                                                        </div>
                                                        <span class="small text-truncate" style="max-width: 120px;">{{ $sub->user->name ?? 'Unknown' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    @if($sub->coordinator_endorsed)
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-medium">
                                                            <i class="bi bi-check-circle me-1"></i> Endorsed to Director
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 fw-medium">
                                                            <i class="bi bi-hourglass-split me-1"></i> Awaiting Endorsement
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('proposal.show', $sub->research_proposal_id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                            View Proposal
                                                        </a>
                                                        @if(!$sub->coordinator_endorsed)
                                                            <form action="{{ route('local_forum.endorse', $sub->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" onclick="return confirm('Endorse this paper output to the Research Director for the Local Forum?');">
                                                                    <i class="bi bi-hand-thumbs-up me-1"></i> Endorse Output
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted">
                                                    <i class="bi bi-easel2 fs-2 d-block mb-2 text-secondary"></i>
                                                    <p class="mb-0 fw-medium">No local forum paper submissions from your college yet.</p>
                                                    <span class="text-muted fs-7">Submissions for Appendix D will appear here when faculty researchers apply.</span>
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

        <!-- Department Control & Shortcuts (Right 4 Columns) -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3 gap-md-4">
                <!-- College Info Box -->
                <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2.5 text-white" style="background: rgba(255, 255, 255, 0.18);">
                            <i class="bi bi-award fs-4"></i>
                        </div>
                        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold">{{ strtoupper($department) }}</span>
                    </div>
                    <h5 class="fw-bold mb-2">College Endorsement Hub</h5>
                    <p class="text-white text-opacity-75 small mb-0">Screen and evaluate proposals submitted by faculty researchers within {{ strtoupper($department) }} before endorsing to the Dean.</p>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Coordinator Tools</h5>
                    </div>
                    <div class="card-body p-3 p-md-4 pt-1">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('announcements.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-megaphone"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Announcements</div>
                                    <div class="text-muted fs-7">Call for papers & guidelines</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-purple bg-opacity-10 text-purple rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; color: #8B5CF6;">
                                    <i class="bi bi-archive"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted fs-7">Browse published college studies</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-chat-dots"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Internal Messages</div>
                                    <div class="text-muted fs-7">Communicate with Deans & Staff</div>
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
