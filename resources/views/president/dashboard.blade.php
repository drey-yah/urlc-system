<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Office of the SUC President — Executive Dashboard</h1>
            <p class="text-muted small mb-0">Welcome, <strong>President {{ Auth::user()->name }}</strong> | SUC Executive Office & Research Dissemination Authorization</p>
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

    <!-- Stats Grid (2x2 on Mobile, 4 Columns on Desktop) -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                            <i class="bi bi-person-workspace fs-5"></i>
                        </div>
                        @if(($stats['pending_authorization'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Action</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Clear</span>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['pending_authorization'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Awaiting Presidential Approval</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                            <i class="bi bi-award fs-5"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Authorized</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['approved_presentations'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Authorized Presentations</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                            <i class="bi bi-journal-check fs-5"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Phase 3</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['completed_dissemination'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Completed Disseminations</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                            <i class="bi bi-building fs-5"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Institutional</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['total_university_proposals'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Total University Proposals</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- President Authorization Center (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Presidential Dissemination Approval Center</h5>
                            <span class="text-muted small d-md-none" style="font-size: 0.72rem;">Swipe tabs to navigate</span>
                        </div>
                        <ul class="nav nav-pills card-header-pills flex-nowrap overflow-x-auto pb-1" id="presidentTabs" role="tablist" style="scrollbar-width: none; -ms-overflow-style: none;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="pending-pres-tab" data-bs-toggle="tab" data-bs-target="#pending-pres" type="button" role="tab">
                                    Awaiting Approval
                                    @if(($stats['pending_authorization'] ?? 0) > 0)
                                        <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_authorization'] }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="approved-pres-tab" data-bs-toggle="tab" data-bs-target="#approved-pres" type="button" role="tab">
                                    Authorized Log
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="all-univ-pres-tab" data-bs-toggle="tab" data-bs-target="#all-univ-pres" type="button" role="tab">
                                    University Researches
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="presidentTabsContent">
                        <!-- Tab 1: Presentations Awaiting SUC President Approval -->
                        <div class="tab-pane fade show active" id="pending-pres" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($pendingPresentations as $pres)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge {{ $pres->presentation_type === 'oral' ? 'bg-primary' : 'bg-info' }} bg-opacity-10 text-dark rounded-pill px-2.5 py-1 fw-semibold text-capitalize" style="font-size: 0.72rem;">
                                                    {{ $pres->presentation_type }} Presentation
                                                </span>
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                                                    <i class="bi bi-hand-thumbs-up me-1"></i>Director Endorsed
                                                </span>
                                            </div>

                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                                {{ $pres->presentation_title }}
                                            </h6>
                                            <div class="text-muted small mb-2" style="font-size: 0.76rem;">
                                                <i class="bi bi-calendar-event me-1"></i> {{ $pres->conference_name }}
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center bg-light rounded-2 px-2.5 py-2 mb-2.5" style="font-size: 0.78rem;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                        {{ substr($pres->user->name ?? '?', 0, 1) }}
                                                    </div>
                                                    <span class="text-truncate" style="max-width: 120px;">{{ $pres->user->name ?? 'Researcher' }}</span>
                                                </div>
                                                <div class="text-muted text-truncate" style="max-width: 130px; font-size: 0.72rem;">
                                                    {{ $pres->sponsoring_agency }}
                                                </div>
                                            </div>

                                            <div class="d-flex gap-2 pt-2 border-top">
                                                <a href="{{ route('proposal.show', $pres->proposal->id) }}" class="btn btn-sm btn-light border rounded-pill flex-fill text-center py-1.5" style="font-size: 0.8rem;">
                                                    View Details
                                                </a>
                                                <form action="{{ route('president.approvePresentation', $pres->id) }}" method="POST" class="flex-fill">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill w-100 py-1.5 shadow-sm text-center" style="font-size: 0.8rem;" onclick="return confirm('Officially authorize this presentation as SUC President?');">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Approve Output
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                        <p class="mb-0 fw-medium small">No presentations currently awaiting presidential sign-off.</p>
                                        <span class="text-muted" style="font-size: 0.72rem;">Presentations endorsed by the Research Director will appear here for final approval.</span>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">Presentation & Event</th>
                                            <th class="py-3 fw-semibold border-0">Type</th>
                                            <th class="py-3 fw-semibold border-0">Researcher & Agency</th>
                                            <th class="py-3 fw-semibold border-0">Director Status</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($pendingPresentations as $pres)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $pres->presentation_title }}">
                                                        {{ $pres->presentation_title }}
                                                    </div>
                                                    <div class="text-muted fs-7">
                                                        <i class="bi bi-calendar-event me-1"></i> {{ $pres->conference_name }}
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge {{ $pres->presentation_type === 'oral' ? 'bg-primary' : 'bg-info' }} bg-opacity-10 text-dark rounded-pill px-3 py-1 fw-medium text-capitalize">
                                                        {{ $pres->presentation_type }} Presentation
                                                    </span>
                                                </td>
                                                <td class="py-3">
                                                    <div class="small fw-semibold text-dark">{{ $pres->user->name ?? 'Researcher' }}</div>
                                                    <div class="text-muted fs-7">{{ $pres->sponsoring_agency }}</div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-medium">
                                                        <i class="bi bi-hand-thumbs-up me-1"></i> Director Endorsed
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('proposal.show', $pres->proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                            View
                                                        </a>
                                                        <form action="{{ route('president.approvePresentation', $pres->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" onclick="return confirm('Officially authorize this presentation as SUC President?');">
                                                                <i class="bi bi-check-circle-fill me-1"></i> Approve Output
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                                    <p class="mb-0 fw-medium">No presentations currently awaiting presidential sign-off.</p>
                                                    <span class="text-muted fs-7">Presentations endorsed by the Research Director will appear here for final approval.</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 2: Authorized Log -->
                        <div class="tab-pane fade" id="approved-pres" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($approvedPresentations as $pres)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fs-7 text-capitalize">
                                                    {{ $pres->presentation_type }} Presentation
                                                </span>
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                                                    <i class="bi bi-check-circle me-1"></i>{{ $pres->president_approved_at ? $pres->president_approved_at->format('M d, Y') : 'Approved' }}
                                                </span>
                                            </div>

                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                                {{ $pres->presentation_title }}
                                            </h6>
                                            <div class="text-muted small mb-2" style="font-size: 0.76rem;">
                                                {{ $pres->conference_name }} • {{ $pres->sponsoring_agency }}
                                            </div>

                                            <div class="d-flex align-items-center gap-2 small text-muted mb-2.5" style="font-size: 0.78rem;">
                                                <i class="bi bi-person text-muted"></i>
                                                <span>{{ $pres->user->name ?? 'Researcher' }}</span>
                                            </div>

                                            <div class="pt-2 border-top">
                                                <a href="{{ route('proposal.show', $pres->proposal->id) }}" class="btn btn-sm btn-light border rounded-pill w-100 text-center py-1.5" style="font-size: 0.8rem;">
                                                    View Proposal Details <i class="bi bi-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0 fw-medium small">No authorized presentations recorded yet.</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">Presentation Title</th>
                                            <th class="py-3 fw-semibold border-0">Conference / Agency</th>
                                            <th class="py-3 fw-semibold border-0">Researcher</th>
                                            <th class="py-3 fw-semibold border-0">Approval Date</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($approvedPresentations as $pres)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $pres->presentation_title }}">
                                                        {{ $pres->presentation_title }}
                                                    </div>
                                                    <span class="badge bg-light text-dark border fs-7 text-capitalize">{{ $pres->presentation_type }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <div class="small fw-semibold text-dark">{{ $pres->sponsoring_agency }}</div>
                                                    <div class="text-muted fs-7">{{ $pres->conference_name }}</div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="small text-muted">{{ $pres->user->name ?? 'Researcher' }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-medium">
                                                        {{ $pres->president_approved_at ? $pres->president_approved_at->format('M d, Y') : 'Approved' }}
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <a href="{{ route('proposal.show', $pres->proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                        View Proposal <i class="bi bi-arrow-right ms-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                                    <p class="mb-0 fw-medium">No authorized presentations recorded yet.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: All University Researches -->
                        <div class="tab-pane fade" id="all-univ-pres" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($allProposals as $proposal)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.72rem;">
                                                    {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                                </span>
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 fw-semibold text-capitalize" style="font-size: 0.72rem;">
                                                    Phase {{ $proposal->current_phase ?? 1 }}
                                                </span>
                                            </div>

                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                                {{ $proposal->title }}
                                            </h6>

                                            <div class="d-flex justify-content-between align-items-center bg-light rounded-2 px-2.5 py-2 mb-2.5" style="font-size: 0.78rem;">
                                                <div class="text-truncate" style="max-width: 130px;">
                                                    <span class="fw-medium text-dark">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                                    <small class="text-muted d-block">{{ $proposal->user->department ?? 'N/A' }}</small>
                                                </div>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1 text-capitalize" style="font-size: 0.68rem;">
                                                    {{ str_replace('_', ' ', $proposal->status) }}
                                                </span>
                                            </div>

                                            <div class="pt-2 border-top">
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill w-100 text-center py-1.5" style="font-size: 0.8rem;">
                                                    View Research Details <i class="bi bi-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-folder2-open fs-2 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0 fw-medium small">No institutional proposals logged.</p>
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
                                            <th class="py-3 fw-semibold border-0">Researcher & Dept</th>
                                            <th class="py-3 fw-semibold border-0">Phase & Status</th>
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
                                                    <div class="small fw-semibold text-dark">{{ $proposal->user->name ?? 'Unknown' }}</div>
                                                    <div class="text-muted fs-7">{{ $proposal->user->department ?? 'N/A' }}</div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-medium text-capitalize">
                                                        Phase {{ $proposal->current_phase ?? 1 }} — {{ str_replace('_', ' ', $proposal->status) }}
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                        Details <i class="bi bi-arrow-right ms-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    No institutional proposals logged.
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

        <!-- Executive Hub & Info (Right 4 Columns) -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <!-- SUC President Executive Card (Themed with UA Maroon & Gold) -->
                <div class="card border-0 shadow-sm rounded-4 text-white p-4" style="background: linear-gradient(135deg, #8C1D2C 0%, #4A0E18 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2);">
                            <i class="bi bi-award-fill fs-4" style="color: var(--ua-gold);"></i>
                        </div>
                        <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(229, 169, 60, 0.2); color: var(--ua-gold); border: 1px solid rgba(229, 169, 60, 0.4);">
                            SUC Executive
                        </span>
                    </div>
                    <h5 class="fw-bold mb-2 text-white">Presidential Sign-Off Office</h5>
                    <p class="text-white text-opacity-75 small mb-0 lh-base">As SUC President, your official authorization endorses university researchers to represent the institution in national and international oral & poster presentation conferences.</p>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Executive Tools</h5>
                    </div>
                    <div class="card-body p-3 p-md-4 pt-1">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('announcements.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-megaphone"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark small">Announcements</div>
                                    <div class="text-muted fs-7 text-truncate">Broadcast institutional updates</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                                    <i class="bi bi-archive"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted fs-7 text-truncate">Access full university repository</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-chat-left-dots"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark small">Internal Messages</div>
                                    <div class="text-muted fs-7 text-truncate">Executive communication center</div>
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
