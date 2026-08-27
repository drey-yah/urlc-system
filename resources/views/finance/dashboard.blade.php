<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Finance Office & Procurement Approvals</h1>
            <p class="text-muted small mb-0">Welcome, <strong>Finance Officer {{ Auth::user()->name }}</strong> | Purchase Request (PR) Approval & Procurement Oversight</p>
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
                            <i class="bi bi-file-earmark-text h4 mb-0"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-medium">Total</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['total_prs'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Total Purchase Requests</p>
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
                        @if(($stats['pending_prs'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-medium">Action Needed</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-3 py-2 fw-medium">Clear</span>
                        @endif
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['pending_prs'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Awaiting Finance Approval</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                            <i class="bi bi-check-circle h4 mb-0"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-medium">Approved</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $stats['approved_prs'] ?? 0 }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Approved Procurement PRs</p>
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
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2 fw-medium">Amount</span>
                    </div>
                    <h2 class="fw-bold mb-1">₱{{ number_format($stats['total_amount_approved'] ?? 0, 2) }}</h2>
                    <p class="text-muted small mb-0 fw-medium">Total Procurement Amount</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- Purchase Request Queues (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Procurement PR Queue</h5>
                        <ul class="nav nav-pills card-header-pills" id="prTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active small py-1 px-3 fw-semibold rounded-pill" id="pending-pr-tab" data-bs-toggle="tab" data-bs-target="#pending-pr" type="button" role="tab">
                                    Awaiting Approval
                                    @if(($stats['pending_prs'] ?? 0) > 0)
                                        <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_prs'] }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill" id="approved-pr-tab" data-bs-toggle="tab" data-bs-target="#approved-pr" type="button" role="tab">
                                    Approved PRs
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill" id="all-pr-tab" data-bs-toggle="tab" data-bs-target="#all-pr" type="button" role="tab">
                                    All PR History
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="prTabsContent">
                        <!-- Tab 1: Pending PR Approvals -->
                        <div class="tab-pane fade show active" id="pending-pr" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">PR ID</th>
                                            <th class="py-3 fw-semibold border-0">Research Proposal</th>
                                            <th class="py-3 fw-semibold border-0">Proponent</th>
                                            <th class="py-3 fw-semibold border-0">Amount</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($pendingPRs as $pr)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <span class="badge bg-light text-dark border fw-mono fs-7">PR-{{ $pr->id }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 200px;" title="{{ $pr->proposal->title ?? 'N/A' }}">
                                                        {{ $pr->proposal->title ?? 'Untitled Proposal' }}
                                                    </div>
                                                    <div class="text-muted fs-7">{{ $pr->purpose }}</div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="small text-dark">{{ $pr->proposal->user->name ?? 'Researcher' }}</span>
                                                </td>
                                                <td class="py-3 fw-bold text-success">
                                                    ₱{{ number_format($pr->total_amount, 2) }}
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                                        @if($pr->document_path)
                                                            <a href="{{ route('file.serve', ['path' => $pr->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                                                <i class="bi bi-file-earmark-pdf me-1"></i> View PR PDF
                                                            </a>
                                                        @endif
                                                        <form action="{{ route('finance.pr.approve', $pr->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" onclick="return confirm('Approve Purchase Request #{{ $pr->id }} for procurement?');">
                                                                <i class="bi bi-check-circle me-1"></i> Approve PR
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                                    <p class="mb-0 fw-medium">No purchase requests currently awaiting Finance approval.</p>
                                                    <span class="text-muted fs-7">Purchase Requests countersigned by the Research Director will appear here.</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 2: Approved PRs -->
                        <div class="tab-pane fade" id="approved-pr" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">PR ID</th>
                                            <th class="py-3 fw-semibold border-0">Proposal</th>
                                            <th class="py-3 fw-semibold border-0">Approved Date</th>
                                            <th class="py-3 fw-semibold border-0">Amount</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Action & Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($approvedPRs as $pr)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <span class="badge bg-light text-dark border fw-mono fs-7">PR-{{ $pr->id }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;">
                                                        {{ $pr->proposal->title ?? 'Untitled Proposal' }}
                                                    </div>
                                                </td>
                                                <td class="py-3 text-muted small">
                                                    {{ $pr->finance_approved_at ? $pr->finance_approved_at->format('M d, Y') : 'N/A' }}
                                                </td>
                                                <td class="py-3 fw-bold text-success">
                                                    ₱{{ number_format($pr->total_amount, 2) }}
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                                        @if($pr->document_path)
                                                            <a href="{{ route('file.serve', ['path' => $pr->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                                                <i class="bi bi-file-earmark-pdf me-1"></i> View PR PDF
                                                            </a>
                                                        @endif
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-medium">
                                                            Approved for Procurement
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                                    <p class="mb-0 fw-medium">No approved purchase requests recorded yet.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: All PR History -->
                        <div class="tab-pane fade" id="all-pr" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 border-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3 fw-semibold border-0">PR ID</th>
                                            <th class="py-3 fw-semibold border-0">Proposal</th>
                                            <th class="py-3 fw-semibold border-0">Proponent</th>
                                            <th class="py-3 fw-semibold border-0">Amount</th>
                                            <th class="pe-4 py-3 fw-semibold border-0 text-end">Action & Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($allPRs as $pr)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <span class="badge bg-light text-dark border fw-mono fs-7">PR-{{ $pr->id }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;">
                                                        {{ $pr->proposal->title ?? 'Untitled' }}
                                                    </div>
                                                </td>
                                                <td class="py-3 small text-muted">
                                                    {{ $pr->proposal->user->name ?? 'N/A' }}
                                                </td>
                                                <td class="py-3 fw-bold text-dark">
                                                    ₱{{ number_format($pr->total_amount, 2) }}
                                                </td>
                                                <td class="pe-4 py-3 text-end">
                                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                                        @if($pr->document_path)
                                                            <a href="{{ route('file.serve', ['path' => $pr->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                                                <i class="bi bi-file-earmark-pdf me-1"></i> View PR PDF
                                                            </a>
                                                        @endif
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 fw-medium text-capitalize">
                                                            {{ str_replace('_', ' ', $pr->status) }}
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-folder2-open fs-2 d-block mb-2 text-secondary"></i>
                                                    No purchase requests in database.
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

        <!-- Finance Officer Hub Sidebar (Right 4 Columns) -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <!-- Finance Office Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-white bg-opacity-20 rounded-3 p-3 text-white">
                            <i class="bi bi-bank fs-4"></i>
                        </div>
                        <span class="badge bg-white text-success rounded-pill px-3 py-1 fw-bold">Finance Office</span>
                    </div>
                    <h5 class="fw-bold mb-2">Procurement Gatekeeping</h5>
                    <p class="text-white text-opacity-75 small mb-0">Review and approve Purchase Requests (PRs) submitted by research proponents for supplies, materials, and equipment during Phase 2 project implementation.</p>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Finance Tools</h5>
                    </div>
                    <div class="card-body p-4 pt-1">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('announcements.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-megaphone"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Announcements</div>
                                    <div class="text-muted fs-7">View institutional notices & announcements</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-purple bg-opacity-10 text-purple rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; color: #8B5CF6;">
                                    <i class="bi bi-archive"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted fs-7">Browse completed project archives</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-chat-left-dots"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">Internal Messaging</div>
                                    <div class="text-muted fs-7">Communicate with Director & Proponents</div>
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
