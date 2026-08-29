<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Office of the SUC President — Executive Dashboard</h1>
            <p class="text-muted small mb-0">Welcome, <strong>President {{ Auth::user()->name }}</strong> | SUC Executive Office & Research Dissemination Authorization</p>
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
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                            <i class="bi bi-person-workspace h4 mb-0"></i>
                        </div>
                        @if(($stats['pending_authorization'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-medium">Action Needed</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-3 py-2 fw-medium">Clear</span>
                        @endif
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['pending_authorization'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Awaiting Presidential Approval</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                            <i class="bi bi-award h4 mb-0"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-medium">Authorized</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['approved_presentations'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Authorized Presentations</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                            <i class="bi bi-journal-check h4 mb-0"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2 fw-medium">Phase 3</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['completed_dissemination'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Completed Dissemination Records</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                            <i class="bi bi-building h4 mb-0"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-medium">Institutional</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['total_university_proposals'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Total University Proposals</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- President Authorization Center (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Presidential Dissemination Approval Center</h5>
                        <ul class="nav nav-pills card-header-pills" id="presidentTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active small py-1 px-3 fw-semibold rounded-pill" id="pending-pres-tab" data-bs-toggle="tab" data-bs-target="#pending-pres" type="button" role="tab">
                                    Awaiting Approval
                                    @if(($stats['pending_authorization'] ?? 0) > 0)
                                        <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_authorization'] }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill" id="approved-pres-tab" data-bs-toggle="tab" data-bs-target="#approved-pres" type="button" role="tab">
                                    Authorized Log
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill" id="all-univ-pres-tab" data-bs-toggle="tab" data-bs-target="#all-univ-pres" type="button" role="tab">
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
                            <div class="table-responsive">
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
                            <div class="table-responsive">
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
                            <div class="table-responsive">
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
                <!-- SUC President Executive Card -->
                <div class="card border-0 shadow-sm rounded-4 text-white p-4" style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-white bg-opacity-15 rounded-3 p-3 text-white">
                            <i class="bi bi-person-workspace fs-4"></i>
                        </div>
                        <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold">SUC Executive</span>
                    </div>
                    <h5 class="fw-bold mb-2">Presidential Sign-Off Office</h5>
                    <p class="text-white text-opacity-85 small mb-0">As SUC President, your official authorization endorses university researchers to represent the institution in national and international oral & poster presentation conferences.</p>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Executive Tools</h5>
                    </div>
                    <div class="card-body p-4 pt-1">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('announcements.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-megaphone"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Announcements</div>
                                    <div class="text-muted fs-7">Broadcast institutional updates</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-purple bg-opacity-10 text-purple rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; color: #8B5CF6;">
                                    <i class="bi bi-archive"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted fs-7">Access full university repository</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-chat-left-dots"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Internal Messages</div>
                                    <div class="text-muted fs-7">Executive communication center</div>
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
