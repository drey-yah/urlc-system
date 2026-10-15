<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Finance Office & Procurement Approvals</h1>
            <p class="text-muted small mb-0">Welcome, <strong>Finance Officer {{ Auth::user()->name }}</strong> | Purchase Request (PR) Approval & Procurement Oversight</p>
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
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                            <i class="bi bi-file-earmark-text fs-5"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Total</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['total_prs'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Total Purchase Requests</p>
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
                        @if(($stats['pending_prs'] ?? 0) > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Action</span>
                        @else
                            <span class="badge bg-light text-muted rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Clear</span>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['pending_prs'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Awaiting Finance Approval</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                            <i class="bi bi-check-circle fs-5"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Approved</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">{{ $stats['approved_prs'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Approved Procurement PRs</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                            <i class="bi bi-currency-dollar fs-5"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.72rem;">Amount</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4 fs-sm-3">₱{{ number_format($stats['total_amount_approved'] ?? 0, 2) }}</h3>
                    <p class="text-muted small mb-0 fw-medium lh-sm" style="font-size: 0.74rem; min-height: 2.2em;">Total Procurement Amount</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- Purchase Request Queues (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Procurement PR Queue</h5>
                            <span class="text-muted small d-md-none" style="font-size: 0.72rem;">Swipe tabs to navigate</span>
                        </div>
                        <ul class="nav nav-pills card-header-pills flex-nowrap overflow-x-auto pb-1" id="prTabs" role="tablist" style="scrollbar-width: none; -ms-overflow-style: none;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="pending-pr-tab" data-bs-toggle="tab" data-bs-target="#pending-pr" type="button" role="tab">
                                    Awaiting Approval
                                    @if(($stats['pending_prs'] ?? 0) > 0)
                                        <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_prs'] }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="approved-pr-tab" data-bs-toggle="tab" data-bs-target="#approved-pr" type="button" role="tab">
                                    Approved PRs
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link small py-1 px-3 fw-semibold rounded-pill text-nowrap" id="all-pr-tab" data-bs-toggle="tab" data-bs-target="#all-pr" type="button" role="tab">
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
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($pendingPRs as $pr)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.72rem;">
                                                    PR-{{ $pr->id }}
                                                </span>
                                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                                                    <i class="bi bi-hourglass-split me-1"></i>Awaiting Approval
                                                </span>
                                            </div>

                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                                {{ $pr->proposal->title ?? 'Untitled Proposal' }}
                                            </h6>
                                            <div class="text-muted small mb-2" style="font-size: 0.76rem;">
                                                {{ $pr->purpose ?? 'Procurement Request' }}
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center bg-light rounded-2 px-2.5 py-2 mb-2.5" style="font-size: 0.78rem;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                        {{ substr($pr->proposal->user->name ?? '?', 0, 1) }}
                                                    </div>
                                                    <span class="text-truncate" style="max-width: 120px;">{{ $pr->proposal->user->name ?? 'Researcher' }}</span>
                                                </div>
                                                <div class="text-end">
                                                    <span class="text-muted d-block" style="font-size: 0.68rem;">PR Amount:</span>
                                                    <span class="fw-bold text-success">₱{{ number_format($pr->total_amount, 2) }}</span>
                                                </div>
                                            </div>

                                            <div class="d-flex gap-2 pt-2 border-top">
                                                @if($pr->document_path)
                                                    <a href="{{ route('file.serve', ['path' => $pr->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill flex-fill text-center py-1.5" style="font-size: 0.8rem;">
                                                        <i class="bi bi-file-earmark-pdf me-1"></i> View PDF
                                                    </a>
                                                @endif
                                                <form action="{{ route('finance.pr.approve', $pr->id) }}" method="POST" class="flex-fill">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill w-100 py-1.5 shadow-sm text-center" style="font-size: 0.8rem;" onclick="return confirm('Approve Purchase Request #{{ $pr->id }} for procurement?');">
                                                        <i class="bi bi-check-circle me-1"></i> Approve PR
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                        <p class="mb-0 fw-medium small">No purchase requests currently awaiting Finance approval.</p>
                                        <span class="text-muted" style="font-size: 0.72rem;">Purchase Requests countersigned by the Research Director will appear here.</span>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
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
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $pr->proposal->title ?? 'N/A' }}">
                                                        {{ $pr->proposal->title ?? 'Untitled Proposal' }}
                                                    </div>
                                                    <div class="text-muted fs-7">{{ $pr->purpose }}</div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                                            {{ substr($pr->proposal->user->name ?? '?', 0, 1) }}
                                                        </div>
                                                        <span class="small text-truncate" style="max-width: 120px;">{{ $pr->proposal->user->name ?? 'Researcher' }}</span>
                                                    </div>
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
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($approvedPRs as $pr)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.72rem;">
                                                    PR-{{ $pr->id }}
                                                </span>
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                                                    <i class="bi bi-check-circle me-1"></i>Approved for Procurement
                                                </span>
                                            </div>

                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                                {{ $pr->proposal->title ?? 'Untitled Proposal' }}
                                            </h6>

                                            <div class="d-flex justify-content-between align-items-center bg-light rounded-2 px-2.5 py-2 mb-2.5" style="font-size: 0.78rem;">
                                                <div>
                                                    <span class="text-muted d-block" style="font-size: 0.68rem;">Approved Date:</span>
                                                    <span class="text-dark fw-medium">{{ $pr->finance_approved_at ? $pr->finance_approved_at->format('M d, Y') : 'N/A' }}</span>
                                                </div>
                                                <div class="text-end">
                                                    <span class="text-muted d-block" style="font-size: 0.68rem;">PR Amount:</span>
                                                    <span class="fw-bold text-success">₱{{ number_format($pr->total_amount, 2) }}</span>
                                                </div>
                                            </div>

                                            @if($pr->document_path)
                                                <div class="pt-2 border-top">
                                                    <a href="{{ route('file.serve', ['path' => $pr->document_path]) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill w-100 text-center py-1.5" style="font-size: 0.8rem;">
                                                        <i class="bi bi-file-earmark-pdf me-1 text-danger"></i> View PR PDF
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0 fw-medium small">No approved purchase requests recorded yet.</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
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
                            <!-- Mobile Cards (d-md-none) -->
                            <div class="d-md-none p-3 bg-light bg-opacity-25">
                                @forelse($allPRs as $pr)
                                    <div class="card border shadow-none rounded-3 mb-2.5 bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.72rem;">
                                                    PR-{{ $pr->id }}
                                                </span>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2.5 py-1 fw-semibold text-capitalize" style="font-size: 0.72rem;">
                                                    {{ str_replace('_', ' ', $pr->status) }}
                                                </span>
                                            </div>

                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                                {{ $pr->proposal->title ?? 'Untitled' }}
                                            </h6>

                                            <div class="d-flex justify-content-between align-items-center bg-light rounded-2 px-2.5 py-2 mb-2.5" style="font-size: 0.78rem;">
                                                <div class="text-muted small text-truncate" style="max-width: 140px;">
                                                    {{ $pr->proposal->user->name ?? 'N/A' }}
                                                </div>
                                                <div class="text-end">
                                                    <span class="text-muted d-block" style="font-size: 0.68rem;">Amount:</span>
                                                    <span class="fw-bold text-dark">₱{{ number_format($pr->total_amount, 2) }}</span>
                                                </div>
                                            </div>

                                            @if($pr->document_path)
                                                <div class="pt-2 border-top">
                                                    <a href="{{ route('file.serve', ['path' => $pr->document_path]) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill w-100 text-center py-1.5" style="font-size: 0.8rem;">
                                                        <i class="bi bi-file-earmark-pdf me-1 text-danger"></i> View PR PDF
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-folder2-open fs-2 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0 fw-medium small">No purchase requests in database.</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Desktop Table (d-none d-md-block) -->
                            <div class="table-responsive d-none d-md-block">
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
                <!-- Finance Office Card (Themed with University Maroon & Gold) -->
                <div class="card border-0 shadow-sm rounded-4 text-white p-4" style="background: linear-gradient(135deg, #8C1D2C 0%, #4A0E18 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2);">
                            <i class="bi bi-cart-check fs-4" style="color: var(--ua-gold);"></i>
                        </div>
                        <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(229, 169, 60, 0.2); color: var(--ua-gold); border: 1px solid rgba(229, 169, 60, 0.4);">
                            Finance Office
                        </span>
                    </div>
                    <h5 class="fw-bold mb-2 text-white">Procurement Gatekeeping</h5>
                    <p class="text-white text-opacity-75 small mb-0 lh-base">Review and approve Purchase Requests (PRs) submitted by research proponents for supplies, materials, and equipment during Phase 2 project implementation.</p>
                </div>

                <!-- Shortcuts Hub -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                        <h5 class="fw-bold mb-0 text-dark">Finance Tools</h5>
                    </div>
                    <div class="card-body p-3 p-md-4 pt-1">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('announcements.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-megaphone"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark small">Announcements</div>
                                    <div class="text-muted fs-7 text-truncate">View institutional notices & announcements</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('repository.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                                    <i class="bi bi-archive"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark small">Research Repository</div>
                                    <div class="text-muted fs-7 text-truncate">Browse completed project archives</div>
                                </div>
                                <i class="bi bi-arrow-right text-muted fs-7"></i>
                            </a>

                            <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action border-0 px-0 py-2.5 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-chat-left-dots"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark small">Internal Messaging</div>
                                    <div class="text-muted fs-7 text-truncate">Communicate with Director & Proponents</div>
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
