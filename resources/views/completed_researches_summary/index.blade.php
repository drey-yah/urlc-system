<x-app-layout>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-file-earmark-ruled-fill text-primary"></i> Completed Researches Summary Forms
            </h1>
            <p class="text-muted small mb-0">Official institutional college-level summary forms of completed research projects prepared by the College Research Coordinator and noted by the College Dean.</p>
        </div>
        @if(in_array(auth()->user()->role, ['coordinator', 'admin', 'super_admin']))
            <div>
                <a href="{{ route('completed_researches_summary.create') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill"></i> Prepare Summary Form
                </a>
            </div>
        @endif
    </div>

    <!-- Stats Counter Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-folder-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Summary Forms</div>
                        <div class="fs-4 fw-bold text-dark">{{ $lists->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Pending Dean Noting</div>
                        <div class="fs-4 fw-bold text-warning">{{ $pendingCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-patch-check-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Officially Noted</div>
                        <div class="fs-4 fw-bold text-success">{{ $notedCount }}</div>
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

    <!-- Lists Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">Completed Researches Summary Forms</h5>
            <span class="badge bg-light text-muted px-3 py-1.5 rounded-pill">{{ $lists->total() }} Record(s)</span>
        </div>
        <div class="card-body p-0">
            @if($lists->isEmpty())
                <div class="p-5 text-center">
                    <div class="p-3 bg-secondary bg-opacity-10 text-secondary rounded-circle d-inline-flex mb-3">
                        <i class="bi bi-file-earmark-ruled fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No Completed Researches Summary Forms Prepared Yet</h5>
                    <p class="text-muted small mx-auto" style="max-width: 480px;">
                        The College Research Coordinator has not compiled any Completed Researches Summary Form for this college yet. Click "Prepare Summary Form" to compile completed projects with budget and expenditure records.
                    </p>
                    @if(in_array(auth()->user()->role, ['coordinator', 'admin', 'super_admin']))
                        <a href="{{ route('completed_researches_summary.create') }}" class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-bold mt-2">
                            <i class="bi bi-plus-circle-fill me-1"></i> Prepare First Summary Form
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Summary Form / College</th>
                                <th>Report Date</th>
                                <th>Completed Projects</th>
                                <th>Prepared By</th>
                                <th>Dean Noting</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lists as $summary)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">
                                            <a href="{{ route('completed_researches_summary.show', $summary->id) }}" class="text-decoration-none text-dark hover-primary">
                                                {{ $summary->title }}
                                            </a>
                                        </div>
                                        <div class="small text-primary fw-semibold">
                                            <i class="bi bi-bank2 me-1"></i> {{ $summary->college }}
                                        </div>
                                        <small class="text-muted">Form #{{ str_pad($summary->id, 5, '0', STR_PAD_LEFT) }} &bull; FY {{ $summary->fiscal_year ?? date('Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $summary->report_date ? $summary->report_date->format('M d, Y') : 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 rounded-pill fw-bold">
                                            {{ count($summary->proposal_ids ?? []) }} Projects
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ $summary->preparer?->name ?? 'Coordinator' }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">College Research Coordinator</div>
                                    </td>
                                    <td>
                                        @if($summary->status === 'noted' && $summary->noter)
                                            <div class="small fw-semibold text-success">
                                                <i class="bi bi-check-circle-fill me-1"></i> {{ $summary->noter->name }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Dean &bull; {{ $summary->noted_at ? $summary->noted_at->format('M d, Y') : 'Noted' }}</div>
                                        @else
                                            <span class="badge bg-warning bg-opacity-15 text-warning fw-semibold px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-hourglass-split me-1"></i> Awaiting Dean
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($summary->status === 'noted')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded-pill fw-bold">
                                                <i class="bi bi-check2-all me-1"></i> Officially Noted
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded-pill fw-bold">
                                                <i class="bi bi-clock-history me-1"></i> Pending Dean Noting
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('completed_researches_summary.show', $summary->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" title="View Summary Form">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <a href="{{ route('completed_researches_summary.pdf', $summary->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" title="Download Official Portrait PDF">
                                                <i class="bi bi-file-earmark-pdf"></i> PDF
                                            </a>
                                            @if(in_array(auth()->user()->role, ['coordinator', 'admin', 'super_admin']) && $summary->status !== 'noted')
                                                <form action="{{ route('completed_researches_summary.destroy', $summary->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this summary form?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 30px; height: 30px;" title="Delete Form">
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
                @if($lists->hasPages())
                    <div class="p-3 border-top">
                        {{ $lists->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
