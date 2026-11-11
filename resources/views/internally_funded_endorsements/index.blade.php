<x-app-layout>
    <!-- Page Header -->
    <div class="mb-3 mb-md-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-file-earmark-ruled-fill" style="color: var(--ua-maroon, #8C1D2C);"></i> Internally-Funded Proposal Endorsements
            </h1>
            <p class="text-muted small mb-0">Official university dossier endorsed by the Research Director, recommended by VPREI &amp; VPAF, and approved by the President.</p>
        </div>
        @if($isDirector)
            <a href="{{ route('internally_funded_endorsements.create') }}" class="btn btn-primary px-3 px-md-4 py-2 rounded-pill fw-bold d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-plus-circle-fill"></i> <span>Prepare Endorsement Form</span>
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-xs border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Metrics Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Endorsements</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 44px; height: 44px;">
                        <i class="bi bi-folder-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Awaiting Recommendation</span>
                        <h3 class="fw-bold text-warning mb-0 mt-1">{{ $stats['pending_recommending'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 44px; height: 44px;">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Awaiting President</span>
                        <h3 class="fw-bold text-info mb-0 mt-1">{{ $stats['pending_president'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info" style="width: 44px; height: 44px;">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Approved by President</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">{{ $stats['approved'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 44px; height: 44px;">
                        <i class="bi bi-award-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dossiers Listing -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-bold text-dark mb-0">Official Institutional Endorsement Forms</h5>
            <span class="text-muted small">Showing {{ $endorsements->count() }} of {{ $endorsements->total() }} dossiers</span>
        </div>
        <div class="card-body p-0">
            @if($endorsements->isEmpty())
                <div class="text-center py-5 px-3">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="bi bi-file-earmark-excel fs-3 text-muted"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No Endorsement Forms Yet</h6>
                    <p class="text-muted small mb-3">The Research Director has not prepared any internally-funded proposal endorsement forms yet.</p>
                    @if($isDirector)
                        <a href="{{ route('internally_funded_endorsements.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold shadow-xs">
                            <i class="bi bi-plus-circle me-1"></i> Prepare First Endorsement Form
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Endorsement Form Details</th>
                                <th>Fiscal Year</th>
                                <th>Total Budget</th>
                                <th>Signatory Progression</th>
                                <th>Overall Status</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($endorsements as $endorsement)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark">
                                            <a href="{{ route('internally_funded_endorsements.show', $endorsement->id) }}" class="text-dark text-decoration-none hover-primary">
                                                {{ $endorsement->title }}
                                            </a>
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.76rem;">
                                            <span><i class="bi bi-person-circle"></i> Prepared by {{ $endorsement->preparer->name ?? 'Director' }}</span>
                                            <span class="mx-1">&bull;</span>
                                            <span><i class="bi bi-calendar3"></i> {{ $endorsement->report_date->format('M d, Y') }}</span>
                                            <span class="mx-1">&bull;</span>
                                            <span><strong>{{ count($endorsement->items ?? []) }}</strong> proposals</span>
                                        </div>
                                    </td>
                                    <td>{{ $endorsement->fiscal_year ?? date('Y') }}</td>
                                    <td class="fw-bold text-dark">₱{{ number_format((float)$endorsement->total_budget, 2) }}</td>
                                    <td>
                                        <!-- Signatory Progress Pills -->
                                        <div class="d-flex flex-wrap gap-1" style="font-size: 0.72rem;">
                                            <span class="badge bg-success bg-opacity-15 text-success rounded-pill px-2" title="Prepared by Research Director">
                                                <i class="bi bi-check-lg"></i> Director
                                            </span>
                                            <span class="badge {{ $endorsement->isVpreiRecommended() ? 'bg-success bg-opacity-15 text-success' : 'bg-light text-muted border' }} rounded-pill px-2" title="Recommended by VP for REI">
                                                <i class="bi {{ $endorsement->isVpreiRecommended() ? 'bi-check-lg' : 'bi-clock' }}"></i> VPREI
                                            </span>
                                            <span class="badge {{ $endorsement->isVpafRecommended() ? 'bg-success bg-opacity-15 text-success' : 'bg-light text-muted border' }} rounded-pill px-2" title="Recommended by VPAF">
                                                <i class="bi {{ $endorsement->isVpafRecommended() ? 'bi-check-lg' : 'bi-clock' }}"></i> VPAF
                                            </span>
                                            <span class="badge {{ $endorsement->isPresidentApproved() ? 'bg-success bg-opacity-15 text-success' : 'bg-light text-muted border' }} rounded-pill px-2" title="Approved by President">
                                                <i class="bi {{ $endorsement->isPresidentApproved() ? 'bi-check-lg' : 'bi-clock' }}"></i> President
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill {{ $endorsement->statusBadgeClass() }} px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                            {{ $endorsement->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1.5">
                                            <a href="{{ route('internally_funded_endorsements.show', $endorsement->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold shadow-xs">
                                                View Form &rarr;
                                            </a>
                                            <a href="{{ route('internally_funded_endorsements.pdf', $endorsement->id) }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-muted fw-semibold" title="Download Official PDF">
                                                <i class="bi bi-download"></i> PDF
                                            </a>
                                            @if($isDirector)
                                                <form action="{{ route('internally_funded_endorsements.destroy', $endorsement->id) }}" method="POST" class="m-0 d-inline" onsubmit="return confirm('Delete this endorsement form?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light text-danger border rounded-circle p-1.5" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top">
                    {{ $endorsements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
