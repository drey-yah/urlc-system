<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fs-4 fs-md-3 fw-bold text-dark mb-1">VPAF Executive Endorsements &amp; Finance Review</h1>
            <p class="text-muted small mb-0">Welcome, <strong>Vice President {{ Auth::user()->name }}</strong> | Office of the Vice President for Academic and Finance</p>
        </div>
        <div class="d-flex gap-2 w-100 w-md-auto flex-wrap">
            <a href="{{ route('internally_funded_endorsements.index') }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm rounded-pill px-3 py-2 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.85rem;">
                <i class="bi bi-file-earmark-ruled-fill"></i> Endorsement Forms
            </a>
            <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 shadow-sm rounded-pill px-3 py-2 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.85rem;">
                <i class="bi bi-calendar-event"></i> Schedules
            </a>
            <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 shadow-sm rounded-pill px-3 py-2 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.85rem;">
                <i class="bi bi-envelope"></i> Messages
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-2 g-md-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2.5">
                            <i class="bi bi-hourglass-split fs-4 mb-0"></i>
                        </div>
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 small fw-semibold">Action Required</span>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['pending_review'] }}</h3>
                    <p class="text-muted small mb-0 fw-medium">Pending VPAF Recommending</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2.5">
                            <i class="bi bi-check-circle-fill fs-4 mb-0"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 small fw-semibold">Endorsed</span>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['recommended'] }}</h3>
                    <p class="text-muted small mb-0 fw-medium">Recommended by VPAF</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2.5">
                            <i class="bi bi-award-fill fs-4 mb-0"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 small fw-semibold">Completed</span>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['total_approved'] }}</h3>
                    <p class="text-muted small mb-0 fw-medium">Approved by President</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2.5">
                            <i class="bi bi-cash-stack fs-4 mb-0"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 small fw-semibold">Total Funds</span>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">₱{{ number_format($stats['total_budget_recommended'], 2) }}</h3>
                    <p class="text-muted small mb-0 fw-medium">Total Recommended Budget</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Pending Recommending Endorsements -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0">Internally-Funded Research Proposals Awaiting VPAF Recommending Approval</h5>
                <p class="text-muted small mb-0">Endorsement dossiers submitted by the Research Director requiring financial and academic recommendation.</p>
            </div>
            <a href="{{ route('internally_funded_endorsements.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold">
                View All Dossiers &rarr;
            </a>
        </div>
        <div class="card-body p-0">
            @if($pendingEndorsements->isEmpty())
                <div class="text-center py-5 px-3">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="bi bi-check2-all fs-3 text-success"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">All Caught Up!</h6>
                    <p class="text-muted small mb-0">There are no pending internally-funded proposal endorsement forms requiring your recommendation at this time.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Endorsement Dossier</th>
                                <th>Fiscal Year</th>
                                <th>Proposals Included</th>
                                <th>Total Budget</th>
                                <th>VPREI Status</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingEndorsements as $endorsement)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $endorsement->title }}</div>
                                        <div class="text-muted small">Prepared by: {{ $endorsement->preparer->name ?? 'Director' }} on {{ $endorsement->report_date->format('M d, Y') }}</div>
                                    </td>
                                    <td>{{ $endorsement->fiscal_year ?? date('Y') }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5">
                                            {{ count($endorsement->items ?? []) }} proposal(s)
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">₱{{ number_format($endorsement->total_budget, 2) }}</td>
                                    <td>
                                        @if($endorsement->isVpreiRecommended())
                                            <span class="badge bg-success bg-opacity-15 text-success rounded-pill px-2.5">
                                                <i class="bi bi-check-circle-fill me-1"></i> Recommended
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-15 text-warning text-dark rounded-pill px-2.5">
                                                <i class="bi bi-clock me-1"></i> Pending VPREI
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('internally_funded_endorsements.show', $endorsement->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold shadow-xs">
                                            Review &amp; Sign &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
