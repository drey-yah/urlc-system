<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Receiving Queue & Document Logging</h1>
            <p class="text-muted small mb-0">Welcome, <strong>{{ Auth::user()->name }}</strong> | Receiving Staff & Completeness Verification</p>
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
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                            <i class="bi bi-inbox fs-5"></i>
                        </div>
                        @if(($stats['pending_receiving'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Action</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Clear</span>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['pending_receiving'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Pending Check</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                            <i class="bi bi-arrow-right-circle fs-5"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Forwarded</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['routed_to_director'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Routed to Director</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                            <i class="bi bi-search fs-5"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Evaluation</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['under_review'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Technical Review</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                            <i class="bi bi-file-earmark-check fs-5"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Logged</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['total_received'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Total Received</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid (2 Columns) -->
    <div class="row g-4 mb-4">
        <!-- Receiving Queues & Log (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <h5 class="fw-bold mb-0 text-dark">Receiving Center</h5>
                        <ul class="nav nav-pills card-header-pills flex-nowrap overflow-x-auto pb-1" id="staffTabs" role="tablist" style="scrollbar-width: none; -ms-overflow-style: none;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-receiving" type="button" role="tab">
                                    Awaiting Check
                                    @if(($stats['pending_receiving'] ?? 0) > 0)
                                        <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_receiving'] }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="routed-tab" data-bs-toggle="tab" data-bs-target="#routed-proposals" type="button" role="tab">
                                    Recently Forwarded
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="all-log-tab" data-bs-toggle="tab" data-bs-target="#all-log" type="button" role="tab">
                                    Full Log
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="staffTabsContent">
                        <!-- Tab 1: Proposals Awaiting Receiving & Checking -->
                        <div class="tab-pane fade show active" id="pending-receiving" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($proposals as $proposal)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.7rem;">
                                                    {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                                </span>
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                    Endorsed
                                                </span>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="text-dark text-decoration-none hover-primary transition">
                                                    {{ $proposal->title }}
                                                </a>
                                            </h6>
                                            <div class="d-flex align-items-center gap-2 text-muted small mb-2" style="font-size: 0.76rem;">
                                                <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                                    {{ substr($proposal->user->name ?? '?', 0, 1) }}
                                                </div>
                                                <span class="fw-semibold text-dark">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                                <span class="text-muted">• {{ $proposal->user->department ?? 'General' }}</span>
                                            </div>
                                            <div class="d-flex gap-2 pt-2 border-top">
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3" style="font-size: 0.75rem;">
                                                    View
                                                </a>
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill flex-grow-1 shadow-xs" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#receiveModal{{ $proposal->id }}">
                                                    <i class="bi bi-send-check me-1"></i> Receive & Forward
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-check2-circle fs-2 d-block mb-2 text-success"></i>
                                        <p class="mb-0 fw-medium small">No endorsed proposals waiting to be received.</p>
                                        <span class="text-muted" style="font-size: 0.72rem;">Newly endorsed manuscripts from college coordinators will appear here.</span>
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
                                                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
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
                                                        Endorsed
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                            View
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#receiveModal{{ $proposal->id }}">
                                                            <i class="bi bi-send-check me-1"></i> Receive & Forward
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-check2-circle fs-2 d-block mb-2 text-success"></i>
                                                    <p class="mb-0 fw-medium">No endorsed proposals waiting to be received.</p>
                                                    <span class="text-muted fs-7">Newly endorsed manuscripts from college coordinators will appear here.</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Receive & Verification Modals (Shared for Mobile and Desktop) -->
                            @foreach($proposals as $proposal)
                                <div class="modal fade" id="receiveModal{{ $proposal->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <div class="modal-content border-0 rounded-4 shadow-lg">
                                            <div class="modal-header border-0 bg-light rounded-top-4 px-4 pt-4 pb-3">
                                                <div class="d-flex align-items-center gap-2.5">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-clipboard-check fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="modal-title fw-bold text-dark mb-0">Administrative Completeness Check</h5>
                                                        <p class="text-muted small mb-0">{{ $proposal->proposal_code ?? 'P-'.$proposal->id }} • {{ \Illuminate\Support\Str::limit($proposal->title, 35) }}</p>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('staff.proposals.forward', $proposal->id) }}" method="POST" id="staffVerifyForm{{ $proposal->id }}">
                                                @csrf
                                                <input type="hidden" name="action" id="staffAction{{ $proposal->id }}" value="return">
                                                <div class="modal-body p-4 text-start">
                                                    <p class="text-muted small mb-3">Please verify the administrative compliance items below. If all pass, you can route the proposal to the Director. If any item is unchecked, you can return it to the researcher with required remarks.</p>
                                                    
                                                    <div class="p-3 bg-light rounded-3 mb-3 border">
                                                        <div class="form-check mb-2.5">
                                                            <input class="form-check-input border-primary staff-check-input" 
                                                                   type="checkbox" 
                                                                   name="check_technical" 
                                                                   value="1" 
                                                                   id="check1{{ $proposal->id }}" 
                                                                   onchange="toggleStaffChecklist({{ $proposal->id }})">
                                                            <label class="form-check-label small fw-semibold text-dark" for="check1{{ $proposal->id }}">
                                                                1. Technical Checking Completed
                                                            </label>
                                                            <div class="text-muted" style="font-size: 0.72rem; margin-left: 1.5rem;">Verified proposal completeness, signatures, and endorsements.</div>
                                                        </div>
                                                        <div class="form-check mb-2.5">
                                                            <input class="form-check-input border-primary staff-check-input" 
                                                                   type="checkbox" 
                                                                   name="check_format" 
                                                                   value="1" 
                                                                   id="check2{{ $proposal->id }}" 
                                                                   onchange="toggleStaffChecklist({{ $proposal->id }})">
                                                            <label class="form-check-label small fw-semibold text-dark" for="check2{{ $proposal->id }}">
                                                                2. Format & Template Compliance Checked
                                                            </label>
                                                            <div class="text-muted" style="font-size: 0.72rem; margin-left: 1.5rem;">Compliant with prescribed University Research manual & font/margins.</div>
                                                        </div>
                                                        <div class="form-check mb-0">
                                                            <input class="form-check-input border-primary staff-check-input" 
                                                                   type="checkbox" 
                                                                   name="check_similarity" 
                                                                   value="1" 
                                                                   id="check3{{ $proposal->id }}" 
                                                                   onchange="toggleStaffChecklist({{ $proposal->id }})">
                                                            <label class="form-check-label small fw-semibold text-dark" for="check3{{ $proposal->id }}">
                                                                3. Similarity / Plagiarism Check Verified
                                                            </label>
                                                            <div class="text-muted" style="font-size: 0.72rem; margin-left: 1.5rem;">Similarity index passed and within acceptable institutional ceiling.</div>
                                                        </div>
                                                    </div>

                                                    <!-- Deficiencies Box (Shown when partially or not fully checked) -->
                                                    <div id="deficienciesBox{{ $proposal->id }}" class="mb-2">
                                                        <div class="alert alert-warning border-0 p-2.5 rounded-3 mb-2 small d-flex align-items-center gap-2">
                                                            <i class="bi bi-exclamation-triangle-fill text-warning flex-shrink-0"></i>
                                                            <div>
                                                                <strong class="text-dark">Checklist is incomplete.</strong>
                                                                <span class="text-muted d-block" style="font-size: 0.74rem;">Proposal will be returned directly to researcher for revision.</span>
                                                            </div>
                                                        </div>
                                                        <label for="returnReasonStaff{{ $proposal->id }}" class="form-label fw-semibold text-dark small mb-1">
                                                            Deficiencies / Revision Remarks <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea class="form-control" 
                                                                  id="returnReasonStaff{{ $proposal->id }}" 
                                                                  name="return_reason" 
                                                                  rows="3" 
                                                                  required
                                                                  placeholder="Specify why the check is incomplete and what needs to be fixed (e.g., Plagiarism index is 26%, reduce below 15%; or use the prescribed RESU template)..."></textarea>
                                                        <div class="form-text text-muted" style="font-size: 0.72rem;">
                                                            Once the researcher updates this, it will be fast-tracked directly back to this queue.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between align-items-center">
                                                    <button type="button" class="btn btn-light px-4 py-2 rounded-pill fw-medium text-muted" data-bs-dismiss="modal">Cancel</button>
                                                    
                                                    <!-- Forward Button (Hidden initially until all 3 checked) -->
                                                    <button type="submit" 
                                                            id="btnForward{{ $proposal->id }}" 
                                                            class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm d-none">
                                                        <i class="bi bi-send-check me-1"></i> Log & Route to Admin
                                                    </button>
                                                    
                                                    <!-- Return Button (Active when 1 or more boxes unchecked) -->
                                                    <button type="submit" 
                                                            id="btnReturn{{ $proposal->id }}" 
                                                            class="btn btn-danger px-4 py-2 rounded-pill fw-bold shadow-sm">
                                                        <i class="bi bi-arrow-return-left me-1"></i> Return for Incompleteness
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Tab 2: Recently Forwarded Proposals -->
                        <div class="tab-pane fade" id="routed-proposals" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($routedProposals as $proposal)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.7rem;">
                                                    {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                                </span>
                                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                    Pending Director Review
                                                </span>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="text-dark text-decoration-none hover-primary transition">
                                                    {{ $proposal->title }}
                                                </a>
                                            </h6>
                                            <div class="d-flex justify-content-between align-items-center text-muted small pt-2 border-top mt-2" style="font-size: 0.76rem;">
                                                <span>Lead: <strong class="text-dark">{{ $proposal->user->name ?? 'Unknown' }}</strong></span>
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="text-primary text-decoration-none fw-semibold">
                                                    View Details <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0 fw-medium small">No recently forwarded proposals logged.</p>
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
                                        @forelse($routedProposals as $proposal)
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
                                                        Pending Director Review
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
                                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                                    <p class="mb-0 fw-medium">No recently forwarded proposals logged.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: All Received Log -->
                        <div class="tab-pane fade" id="all-log" role="tabpanel">
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($allProposals as $proposal)
                                    @php
                                        $statusClass = match($proposal->status) {
                                            'approved', 'final_approved', 'completed' => 'bg-success bg-opacity-10 text-success',
                                            'pending', 'submitted', 'pending_director_review', 'accepted_for_in_house_review' => 'bg-warning bg-opacity-10 text-warning',
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
                                            <div class="d-flex justify-content-between align-items-center text-muted small pt-2 border-top mt-2" style="font-size: 0.76rem;">
                                                <span>Lead: <strong class="text-dark">{{ $proposal->user->name ?? 'Unknown' }}</strong></span>
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="text-primary text-decoration-none fw-semibold">
                                                    View Details <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-folder2-open fs-2 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0 fw-medium small">No receiving logs found.</p>
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
                                                            'pending', 'submitted', 'pending_director_review', 'accepted_for_in_house_review' => 'bg-warning bg-opacity-10 text-warning',
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
                                                    No receiving logs found.
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

        <!-- Verification Checklist & Tools (Right 4 Columns) -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3 gap-md-4">
                <!-- Completeness Check Protocol Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2.5 text-white" style="background: rgba(255, 255, 255, 0.15);">
                            <i class="bi bi-clipboard-check fs-4"></i>
                        </div>
                        <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold">Verification Standard</span>
                    </div>
                    <h5 class="fw-bold mb-2">Administrative Check</h5>
                    <p class="text-white text-opacity-75 small mb-0">Verify technical formatting, proposal code compliance, and similarity check before routing to the Admin for evaluation assignment.</p>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Staff Tools</h5>
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
                                    <div class="text-muted fs-7">Browse institutional manuscripts</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-chat-left-dots"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Internal Messages</div>
                                    <div class="text-muted fs-7">Communicate with researchers & admin</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <script>
        function toggleStaffChecklist(proposalId) {
            const c1 = document.getElementById('check1' + proposalId);
            const c2 = document.getElementById('check2' + proposalId);
            const c3 = document.getElementById('check3' + proposalId);
            const defBox = document.getElementById('deficienciesBox' + proposalId);
            const reasonInput = document.getElementById('returnReasonStaff' + proposalId);
            const btnForward = document.getElementById('btnForward' + proposalId);
            const btnReturn = document.getElementById('btnReturn' + proposalId);
            const actionInput = document.getElementById('staffAction' + proposalId);

            const allChecked = c1 && c2 && c3 && c1.checked && c2.checked && c3.checked;

            if (allChecked) {
                actionInput.value = 'forward';
                defBox.classList.add('d-none');
                reasonInput.removeAttribute('required');
                btnForward.classList.remove('d-none');
                btnReturn.classList.add('d-none');
            } else {
                actionInput.value = 'return';
                defBox.classList.remove('d-none');
                reasonInput.setAttribute('required', 'required');
                btnForward.classList.add('d-none');
                btnReturn.classList.remove('d-none');
            }
        }
    </script>
</x-app-layout>
