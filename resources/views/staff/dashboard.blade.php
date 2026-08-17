<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Receiving Queue & Document Logging</h1>
            <p class="text-muted small mb-0">Welcome, <strong>{{ Auth::user()->name }}</strong> | Receiving Staff & Completeness Verification</p>
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
                            <i class="bi bi-inbox h4 mb-0"></i>
                        </div>
                        @if(($stats['pending_receiving'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-medium">Action Needed</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-3 py-2 fw-medium">Clear</span>
                        @endif
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['pending_receiving'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Pending Receiving / Check</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                            <i class="bi bi-arrow-right-circle h4 mb-0"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-medium">Forwarded</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['routed_to_director'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Routed to Director Review</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                            <i class="bi bi-search h4 mb-0"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2 fw-medium">Evaluation</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['under_review'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Under Technical Review</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                            <i class="bi bi-file-earmark-check h4 mb-0"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-medium">Logged</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['total_received'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Total Received All Time</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid (2 Columns) -->
    <div class="row g-4 mb-4">
        <!-- Receiving Queues & Log (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Receiving Center</h5>
                        <ul class="nav nav-pills card-header-pills" id="staffTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active small py-1 px-3 fw-semibold rounded-pill" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-receiving" type="button" role="tab">
                                    Awaiting Check
                                    @if(($stats['pending_receiving'] ?? 0) > 0)
                                        <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_receiving'] }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill" id="routed-tab" data-bs-toggle="tab" data-bs-target="#routed-proposals" type="button" role="tab">
                                    Recently Forwarded
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill" id="all-log-tab" data-bs-toggle="tab" data-bs-target="#all-log" type="button" role="tab">
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
                            <div class="table-responsive">
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

                                                    <!-- Receive & Verification Modal -->
                                                    <div class="modal fade" id="receiveModal{{ $proposal->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered text-start">
                                                            <div class="modal-content border-0 rounded-4 shadow-lg">
                                                                <div class="modal-header border-0 bg-light rounded-top-4">
                                                                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-clipboard-check text-primary me-2"></i>Administrative Completeness Check</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <form action="{{ route('staff.proposals.forward', $proposal->id) }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-body p-4 text-start">
                                                                        <p class="text-muted small mb-4">Please verify the required technical and format checks before routing to the Admin / Director.</p>
                                                                        
                                                                        <div class="form-check mb-3">
                                                                            <input class="form-check-input border-primary" type="checkbox" id="check1{{ $proposal->id }}" required>
                                                                            <label class="form-check-label small fw-semibold text-dark" for="check1{{ $proposal->id }}">
                                                                                Technical Checking Completed
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check mb-3">
                                                                            <input class="form-check-input border-primary" type="checkbox" id="check2{{ $proposal->id }}" required>
                                                                            <label class="form-check-label small fw-semibold text-dark" for="check2{{ $proposal->id }}">
                                                                                Format & Template Compliance Checked
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check mb-4">
                                                                            <input class="form-check-input border-primary" type="checkbox" id="check3{{ $proposal->id }}" required>
                                                                            <label class="form-check-label small fw-semibold text-dark" for="check3{{ $proposal->id }}">
                                                                                Similarity / Plagiarism Check Verified
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer border-0 p-4 pt-0">
                                                                        <button type="button" class="btn btn-light px-4 py-2 rounded-pill fw-medium text-muted" data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm">
                                                                            Log & Route to Admin <i class="bi bi-arrow-right ms-1"></i>
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
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
                        </div>

                        <!-- Tab 2: Recently Forwarded Proposals -->
                        <div class="tab-pane fade" id="routed-proposals" role="tabpanel">
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
            <div class="d-flex flex-column gap-4">
                <!-- Completeness Check Protocol Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-white bg-opacity-10 rounded-3 p-3 text-white">
                            <i class="bi bi-clipboard-check fs-4"></i>
                        </div>
                        <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold">Verification Standard</span>
                    </div>
                    <h5 class="fw-bold mb-2">Administrative Check</h5>
                    <p class="text-white text-opacity-75 small mb-0">Verify technical formatting, proposal code compliance, and similarity check before routing to the Admin for evaluation assignment.</p>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Staff Tools</h5>
                    </div>
                    <div class="card-body p-4 pt-1">
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
    </div>
</x-app-layout>
