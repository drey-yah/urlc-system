<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Dean Endorsements & Executive Noting</h1>
            <p class="text-muted small mb-0">
                Welcome, <strong>Dean {{ Auth::user()->name }}</strong> | 
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold">{{ strtoupper($department) }} College</span>
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
                            <i class="bi bi-mortarboard fs-5"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">College</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['total_college'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">College Proposals</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                            <i class="bi bi-vector-pen fs-5"></i>
                        </div>
                        @if(($stats['pending_noting'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Action</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Clear</span>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['pending_noting'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Pending Initial Noting</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                            <i class="bi bi-file-earmark-check fs-5"></i>
                        </div>
                        @if(($stats['final_noting'] ?? 0) > 0)
                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Final Copy</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Clear</span>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['final_noting'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Pending Final Copy</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                            <i class="bi bi-patch-check fs-5"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Approved</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['approved_college'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Active Research</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- Noting Center (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <h5 class="fw-bold mb-0 text-dark">Dean Executive Center</h5>
                        <ul class="nav nav-pills card-header-pills flex-nowrap overflow-x-auto pb-1" id="deanTabs" role="tablist" style="scrollbar-width: none; -ms-overflow-style: none;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="initial-tab" data-bs-toggle="tab" data-bs-target="#initial-noting" type="button" role="tab">
                                    Initial Endorsements
                                    @if(($stats['pending_noting'] ?? 0) > 0)
                                        <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_noting'] }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="final-tab" data-bs-toggle="tab" data-bs-target="#final-noting" type="button" role="tab">
                                    Final Copies
                                    @if(($stats['final_noting'] ?? 0) > 0)
                                        <span class="badge bg-info text-white ms-1">{{ $stats['final_noting'] }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="college-log-tab" data-bs-toggle="tab" data-bs-target="#college-log" type="button" role="tab">
                                    College Log
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="deanTabsContent">
                        <!-- Tab 1: Proposals Awaiting Initial Endorsement Noting -->
                        <div class="tab-pane fade show active" id="initial-noting" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($pendingNoting as $proposal)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.7rem;">
                                                    {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                                </span>
                                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                    <i class="bi bi-vector-pen me-1"></i>Pending Dean Noting
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
                                                <div class="d-flex gap-1.5 flex-fill">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger rounded-pill flex-fill text-center" 
                                                            style="font-size: 0.75rem;" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deanReturnProposalModal"
                                                            data-proposal-id="{{ $proposal->id }}"
                                                            data-proposal-title="{{ htmlspecialchars($proposal->title, ENT_QUOTES) }}"
                                                            data-proposal-code="{{ $proposal->proposal_code ?? 'P-'.$proposal->id }}">
                                                        Return
                                                    </button>
                                                    <form action="{{ route('dean.noteEndorsement', $proposal->id) }}" method="POST" class="flex-fill d-flex m-0">
                                                        @csrf
                                                        <input type="hidden" name="action" value="note">
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill flex-fill text-center shadow-xs" style="font-size: 0.75rem;" onclick="return confirm('Note this coordinator endorsement?');">
                                                            <i class="bi bi-journal-check me-1"></i> Note
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                        <p class="mb-0 fw-medium small">No initial coordinator endorsements awaiting noting.</p>
                                        <span class="text-muted" style="font-size: 0.72rem;">Endorsements submitted by department coordinators will appear here.</span>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop Table (d-none d-md-block) -->
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
                                        @forelse($pendingNoting as $proposal)
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
                                                        Pending Dean Noting
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <div class="d-flex justify-content-end gap-1.5 align-items-center">
                                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                            View
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deanReturnProposalModal"
                                                                data-proposal-id="{{ $proposal->id }}"
                                                                data-proposal-title="{{ htmlspecialchars($proposal->title, ENT_QUOTES) }}"
                                                                data-proposal-code="{{ $proposal->proposal_code ?? 'P-'.$proposal->id }}">
                                                            Return
                                                        </button>
                                                        <form action="{{ route('dean.noteEndorsement', $proposal->id) }}" method="POST" class="d-inline m-0">
                                                            @csrf
                                                            <input type="hidden" name="action" value="note">
                                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" onclick="return confirm('Note this coordinator endorsement?');">
                                                                <i class="bi bi-journal-check me-1"></i> Note
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                                    <p class="mb-0 fw-medium">No initial coordinator endorsements awaiting noting.</p>
                                                    <span class="text-muted fs-7">Endorsements submitted by department coordinators will appear here.</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 2: Proposals Awaiting Final Copy Noting -->
                        <div class="tab-pane fade" id="final-noting" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($finalNoting as $proposal)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.7rem;">
                                                    {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                                </span>
                                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                    <i class="bi bi-file-earmark-check me-1"></i>Final Copy Submitted
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
                                                <form action="{{ route('dean.noteFinal', $proposal->id) }}" method="POST" class="flex-grow-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill w-100 shadow-xs" style="font-size: 0.75rem;" onclick="return confirm('Note this final copy manuscript?');">
                                                        <i class="bi bi-check-circle me-1"></i> Note Final Copy
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-file-earmark-check fs-2 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0 fw-medium small">No final copy proposals awaiting Dean noting.</p>
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
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($finalNoting as $proposal)
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
                                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1 fw-medium">
                                                        Final Copy Submitted
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                            View
                                                        </a>
                                                        <form action="{{ route('dean.noteFinal', $proposal->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" onclick="return confirm('Note this final copy manuscript?');">
                                                                <i class="bi bi-check-circle me-1"></i> Note Final Copy
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-file-earmark-check fs-2 d-block mb-2 text-secondary"></i>
                                                    <p class="mb-0 fw-medium">No final copy proposals awaiting Dean noting.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: College Proposal History -->
                        <div class="tab-pane fade" id="college-log" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($allCollegeProposals as $proposal)
                                    @php
                                        $statusClass = match($proposal->status) {
                                            'approved', 'final_approved', 'completed' => 'bg-success bg-opacity-10 text-success',
                                            'pending', 'submitted', 'pending_dean_noting', 'noted_by_dean' => 'bg-warning bg-opacity-10 text-warning',
                                            'rejected', 'final_rejected' => 'bg-danger bg-opacity-10 text-danger',
                                            'returned_for_revision' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
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
                                        <p class="mb-0 fw-medium small">No college research records found.</p>
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
                                        @forelse($allCollegeProposals as $proposal)
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
                                                            'pending', 'submitted', 'pending_dean_noting', 'noted_by_dean' => 'bg-warning bg-opacity-10 text-warning',
                                                            'rejected', 'final_rejected' => 'bg-danger bg-opacity-10 text-danger',
                                                            'returned_for_revision' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
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
                                                    No college research records found.
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
                <!-- College Executive Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2.5 text-white" style="background: rgba(255, 255, 255, 0.18);">
                            <i class="bi bi-mortarboard-fill fs-4"></i>
                        </div>
                        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold">{{ strtoupper($department) }}</span>
                    </div>
                    <h5 class="fw-bold mb-2">College Executive Office</h5>
                    <p class="text-white text-opacity-75 small mb-0">Review departmental endorsements submitted by college research coordinators and note official manuscripts for University elevation.</p>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Executive Tools</h5>
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
                                    <div class="text-muted fs-7">Communicate with Coordinators & Staff</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>
                        </div>
    <!-- Modal: Dean Return Proposal for Revision -->
    <div class="modal fade" id="deanReturnProposalModal" tabindex="-1" aria-labelledby="deanReturnProposalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-arrow-return-left fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="deanReturnProposalModalLabel">Return Endorsement for Revision</h5>
                            <p class="text-muted small mb-0">Provide reasons for the coordinator and researcher</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deanReturnProposalForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="action" value="return">
                    <div class="modal-body px-4 py-3">
                        <div class="bg-light p-3 rounded-3 mb-3 border">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-white text-dark border fw-mono" id="deanReturnModalCode"></span>
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">Pending Dean Noting</span>
                            </div>
                            <div class="fw-semibold text-dark text-truncate" id="deanReturnModalTitle"></div>
                        </div>

                        <div class="mb-2">
                            <label for="deanReturnReasonText" class="form-label fw-semibold text-dark small">
                                Reason for Return / Revision Remarks <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="deanReturnReasonText" 
                                      name="return_reason" 
                                      rows="4" 
                                      required 
                                      placeholder="State your reasons for returning this endorsement (e.g., college alignment concerns, budget revision needed, missing prerequisites)..."></textarea>
                            <div class="form-text text-muted" style="font-size: 0.74rem;">
                                This feedback will notify both the college coordinator and the lead researcher.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-arrow-return-left me-1"></i> Confirm Return
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deanReturnModal = document.getElementById('deanReturnProposalModal');
            if (deanReturnModal) {
                deanReturnModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;
                    const proposalId = button.getAttribute('data-proposal-id');
                    const proposalTitle = button.getAttribute('data-proposal-title');
                    const proposalCode = button.getAttribute('data-proposal-code');
                    
                    const form = document.getElementById('deanReturnProposalForm');
                    form.action = `/dean/proposals/${proposalId}/note-endorsement`;
                    
                    document.getElementById('deanReturnModalCode').textContent = proposalCode;
                    document.getElementById('deanReturnModalTitle').textContent = proposalTitle;
                    document.getElementById('deanReturnReasonText').value = '';
                });
            }
        });
    </script>
</x-app-layout>
