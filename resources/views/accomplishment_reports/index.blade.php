<x-app-layout>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-file-earmark-bar-graph-fill text-primary"></i> Research Accomplishment Reports
            </h1>
            <p class="text-muted small mb-0">Official institutional quarterly accomplishment reports (RESU - FM-004) prepared by College Coordinators, noted and submitted by College Deans, and received by the Research Director.</p>
        </div>
        @if(in_array(auth()->user()->role, ['coordinator', 'admin', 'super_admin']))
            <div>
                <a href="{{ route('accomplishment_reports.create') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill"></i> Prepare Accomplishment Report
                </a>
            </div>
        @endif
    </div>

    <!-- Stats Counter Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-folder-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Reports</div>
                        <div class="fs-4 fw-bold text-dark">{{ $reports->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Pending Dean Noting</div>
                        <div class="fs-4 fw-bold text-warning">{{ $pendingDeanCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-send-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Submitted to Director</div>
                        <div class="fs-4 fw-bold text-info">{{ $submittedDirectorCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-patch-check-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Acknowledged</div>
                        <div class="fs-4 fw-bold text-success">{{ $acknowledgedCount }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Reports Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">Research Accomplishment Reports</h5>
            <span class="badge bg-light text-muted px-3 py-1.5 rounded-pill">{{ $reports->total() }} Record(s)</span>
        </div>
        <div class="card-body p-0">
            @if($reports->isEmpty())
                <div class="p-5 text-center">
                    <div class="p-3 bg-secondary bg-opacity-10 text-secondary rounded-circle d-inline-flex mb-3">
                        <i class="bi bi-file-earmark-bar-graph fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No Accomplishment Reports Prepared Yet</h5>
                    <p class="text-muted small mx-auto" style="max-width: 480px;">
                        The College Research Coordinator has not prepared any Quarterly Research Accomplishment Report yet. Click "Prepare Accomplishment Report" to compile research accomplishments across targets, presentations, publications, and activities.
                    </p>
                    @if(in_array(auth()->user()->role, ['coordinator', 'admin', 'super_admin']))
                        <a href="{{ route('accomplishment_reports.create') }}" class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-bold mt-2">
                            <i class="bi bi-plus-circle-fill me-1"></i> Prepare First Report
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Report / College</th>
                                <th>Quarter & SY</th>
                                <th>Report Date</th>
                                <th>Prepared By</th>
                                <th>Dean Noting</th>
                                <th>Director Status</th>
                                <th>Workflow Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">
                                            <a href="{{ route('accomplishment_reports.show', $report->id) }}" class="text-decoration-none text-dark hover-primary">
                                                {{ $report->college }}
                                            </a>
                                        </div>
                                        <small class="text-muted">{{ $report->form_code }} &bull; {{ $report->revision_no }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary">{{ $report->quarter }}</div>
                                        <small class="text-muted">{{ $report->school_year }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $report->report_date ? $report->report_date->format('M d, Y') : 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ $report->preparer?->name ?? 'Coordinator' }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">College Coordinator</div>
                                    </td>
                                    <td>
                                        @if($report->noted_by && $report->noter)
                                            <div class="small fw-semibold text-success">
                                                <i class="bi bi-check-circle-fill me-1"></i> {{ $report->noter->name }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Dean &bull; {{ $report->noted_at ? $report->noted_at->format('M d, Y') : 'Noted' }}</div>
                                        @else
                                            <span class="badge bg-warning bg-opacity-15 text-warning fw-semibold px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-hourglass-split me-1"></i> Awaiting Dean
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->status === 'acknowledged' && $report->receiver)
                                            <div class="small fw-semibold text-success">
                                                <i class="bi bi-patch-check-fill me-1"></i> Received
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $report->receiver->name }} &bull; {{ $report->received_at ? $report->received_at->format('M d, Y') : '' }}</div>
                                        @elseif($report->status === 'submitted_to_director')
                                            <span class="badge bg-primary bg-opacity-15 text-primary fw-semibold px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-inbox-fill me-1"></i> Awaiting Director
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold px-2.5 py-1 rounded-pill">
                                                Not Yet Transmitted
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->status === 'acknowledged')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded-pill fw-bold">
                                                <i class="bi bi-check2-all me-1"></i> Acknowledged
                                            </span>
                                        @elseif($report->status === 'submitted_to_director')
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1.5 rounded-pill fw-bold">
                                                <i class="bi bi-send me-1"></i> Submitted to Director
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded-pill fw-bold">
                                                <i class="bi bi-clock-history me-1"></i> Pending Dean Noting
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('accomplishment_reports.show', $report->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" title="View Report">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <a href="{{ route('accomplishment_reports.pdf', $report->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" title="Download Official Landscape PDF">
                                                <i class="bi bi-file-earmark-pdf"></i> PDF
                                            </a>
                                            @if(in_array(auth()->user()->role, ['coordinator', 'admin', 'super_admin']) && $report->status === 'pending_dean_noting')
                                                <form action="{{ route('accomplishment_reports.destroy', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this report?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 30px; height: 30px;" title="Delete Report">
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
                @if($reports->hasPages())
                    <div class="p-3 border-top">
                        {{ $reports->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
