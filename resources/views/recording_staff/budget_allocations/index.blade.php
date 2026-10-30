<x-app-layout>
    <div class="container-fluid px-4 py-4">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-3 text-success"></i>
                <div class="flex-grow-1 fw-medium">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-3 text-danger"></i>
                <div class="flex-grow-1 fw-medium">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
                    <strong>Please check the following errors:</strong>
                </div>
                <ul class="mb-0 ps-4 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small text-muted">
                        <li class="breadcrumb-item"><a href="{{ route('recording_staff.dashboard') }}" class="text-decoration-none text-muted">Recording Staff</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Budget Allocations</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-pie-chart-fill text-primary me-2"></i>Institutional Budget Allocation Matrix
                </h1>
                <p class="text-muted mb-0 small">
                    Fiscal Year {{ $fiscalYear }} allocation per academic program, college, and campus based on GAA and Trust Funds.
                </p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <a href="{{ route('recording_staff.budget_allocations.print', ['fiscal_year' => $fiscalYear, 'campus' => $campus]) }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-printer"></i>
                    <span>Print Official Sheet</span>
                </a>
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#newAllocationModal">
                    <i class="bi bi-plus-circle"></i>
                    <span>Add Program Allocation</span>
                </button>
            </div>
        </div>

        {{-- KPI Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-body p-3">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total GAA</span>
                        <div class="fs-4 fw-bold text-dark">₱{{ number_format($metrics['total_gaa'], 2) }}</div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill mt-2 px-2 py-1 small">General Appropriations</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-body p-3">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Local Travel (TF)</span>
                        <div class="fs-4 fw-bold text-info">₱{{ number_format($metrics['total_local_tf'], 2) }}</div>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill mt-2 px-2 py-1 small">Trust Fund</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-body p-3">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Foreign Travel (TF)</span>
                        <div class="fs-4 fw-bold text-warning">₱{{ number_format($metrics['total_foreign_tf'], 2) }}</div>
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill mt-2 px-2 py-1 small">Trust Fund</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-body p-3">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Implementation (TF)</span>
                        <div class="fs-4 fw-bold text-success">₱{{ number_format($metrics['total_implementation_tf'], 2) }}</div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill mt-2 px-2 py-1 small">Trust Fund</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white">
                    <div class="card-body p-3">
                        <span class="text-white-50 small fw-semibold text-uppercase d-block mb-1">Grand Total Ceiling</span>
                        <div class="fs-4 fw-bold text-white">₱{{ number_format($metrics['grand_total'], 2) }}</div>
                        <span class="badge bg-white text-primary rounded-pill mt-2 px-2 py-1 small">Full Year Ceiling</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-body p-3">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Programs</span>
                        <div class="fs-4 fw-bold text-secondary">{{ $metrics['total_programs'] }}</div>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill mt-2 px-2 py-1 small">Active Units</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-body p-3">
                <form action="{{ route('recording_staff.budget_allocations.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-bold mb-1">Fiscal Year</label>
                        <select name="fiscal_year" class="form-select rounded-pill form-select-sm" onchange="this.form.submit()">
                            @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ $fiscalYear == $yr ? 'selected' : '' }}>FY {{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-bold mb-1">Campus</label>
                        <select name="campus" class="form-select rounded-pill form-select-sm" onchange="this.form.submit()">
                            <option value="all" {{ empty($campus) || $campus === 'all' ? 'selected' : '' }}>All Campuses</option>
                            @foreach($campuses as $camp)
                                <option value="{{ $camp }}" {{ $campus === $camp ? 'selected' : '' }}>{{ $camp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted fw-bold mb-1">Search Program or College</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" value="{{ $search }}" class="form-control rounded-start-pill" placeholder="e.g. Computer Science, Cas...">
                            <button type="submit" class="btn btn-outline-primary rounded-end-pill px-3">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('recording_staff.budget_allocations.index', ['fiscal_year' => $fiscalYear]) }}" class="btn btn-sm btn-light text-muted rounded-pill w-100">
                            Reset Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Allocations Matrix Table --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                    <thead class="bg-light text-muted small" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <tr class="text-uppercase fw-bold border-bottom">
                            <th rowspan="2" class="ps-4 align-middle" style="min-width: 280px;">PROGRAMS</th>
                            <th rowspan="2" class="text-center align-middle" style="width: 50px;">No.</th>
                            <th rowspan="2" class="text-end align-middle" style="width: 95px;">% Implementation</th>
                            <th rowspan="2" class="text-end align-middle" style="width: 95px;">% Traveling</th>
                            <th colspan="2" class="text-center border-bottom pb-1">Traveling-Local</th>
                            <th class="text-center border-bottom pb-1" style="width: 110px;">foreign</th>
                            <th class="text-center border-bottom pb-1" style="width: 130px;">implementation</th>
                            <th rowspan="2" class="text-end align-middle" style="width: 120px;">Total</th>
                            <th rowspan="2" class="text-center align-middle pe-4" style="width: 95px;">Actions</th>
                        </tr>
                        <tr class="text-uppercase fw-semibold bg-white" style="font-size: 0.70rem;">
                            <th class="text-end py-1">
                                GAA
                                <span class="d-block text-primary fw-bold" style="font-size: 0.68rem;">106,000.00</span>
                            </th>
                            <th class="text-end py-1">
                                TF
                                <span class="d-block text-primary fw-bold" style="font-size: 0.68rem;">70,000.00</span>
                            </th>
                            <th class="text-end py-1">
                                TF
                                <span class="d-block text-primary fw-bold" style="font-size: 0.68rem;">70,000.00</span>
                            </th>
                            <th class="text-end py-1">
                                TF
                                <span class="d-block text-primary fw-bold" style="font-size: 0.68rem;">416,000.00</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grouped as $campusName => $collegeGroups)
                            {{-- Campus Banner --}}
                            <tr class="table-primary bg-primary bg-opacity-10 fw-bold border-top border-2 border-primary">
                                <td colspan="10" class="ps-4 py-2 text-primary">
                                    <i class="bi bi-geo-alt-fill me-2"></i>{{ strtoupper($campusName) }}
                                </td>
                            </tr>

                            @foreach($collegeGroups as $collegeName => $allocations)
                                @if($collegeName)
                                    <tr class="table-secondary bg-light fw-bold text-dark border-top">
                                        <td colspan="10" class="ps-4 py-2">
                                            <i class="bi bi-building me-2 text-muted"></i>{{ strtoupper($collegeName) }}
                                        </td>
                                    </tr>
                                @endif

                                @php
                                    $programRows = $allocations->where('is_allotment_row', false);
                                    $allotmentRow = $allocations->where('is_allotment_row', true)->first();
                                    $collegeSlug = \Illuminate\Support\Str::slug($campusName . '-' . ($collegeName ?: 'unit'));
                                @endphp

                                @foreach($programRows as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold text-dark">{{ $item->program_name }}</div>
                                            @if($item->source_of_fund || $item->remarks)
                                                <div class="text-muted small fst-italic">
                                                    {{ $item->source_of_fund }} {{ $item->remarks ? '• ' . $item->remarks : '' }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center text-muted">{{ $item->sequence_no ?? '—' }}</td>
                                        <td class="text-end text-muted">{{ $item->implementation_pct > 0 ? number_format($item->implementation_pct, 5) . '%' : '0.00000%' }}</td>
                                        <td class="text-end text-muted">{{ $item->traveling_pct > 0 ? number_format($item->traveling_pct, 5) . '%' : '—' }}</td>
                                        <td class="text-end text-muted">{{ $item->traveling_local_gaa > 0 ? number_format($item->traveling_local_gaa, 2) : '—' }}</td>
                                        <td class="text-end text-muted">{{ $item->traveling_local_tf > 0 ? number_format($item->traveling_local_tf, 2) : '—' }}</td>
                                        <td class="text-end text-muted">{{ $item->traveling_foreign_tf > 0 ? number_format($item->traveling_foreign_tf, 2) : '—' }}</td>
                                        <td class="text-end text-muted">{{ $item->implementation_tf > 0 ? number_format($item->implementation_tf, 2) : '—' }}</td>
                                        <td class="text-end fw-bold text-dark">
                                            {{ $item->total_allocation > 0 ? '₱' . number_format($item->total_allocation, 2) : '—' }}
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle p-1" 
                                                        style="width: 28px; height: 28px;"
                                                        title="Edit Program"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal{{ $item->id }}">
                                                    <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                                </button>
                                                <form action="{{ route('recording_staff.budget_allocations.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Remove allocation for {{ addslashes($item->program_name) }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 28px; height: 28px;" title="Delete">
                                                        <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Edit Modal for each individual program row --}}
                                    <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <form action="{{ route('recording_staff.budget_allocations.update', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                                                        <h5 class="modal-title fw-bold">Edit Program Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body px-4 py-3">
                                                        <div class="row g-3">
                                                            <div class="col-md-3">
                                                                <label class="form-label small fw-bold">Fiscal Year</label>
                                                                <input type="number" name="fiscal_year" value="{{ $item->fiscal_year }}" class="form-control" required>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label class="form-label small fw-bold">Campus</label>
                                                                <input type="text" name="campus" value="{{ $item->campus }}" class="form-control" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label small fw-bold">College / Unit</label>
                                                                <input type="text" name="college" value="{{ $item->college }}" class="form-control">
                                                            </div>
                                                            <div class="col-md-10">
                                                                <label class="form-label small fw-bold">Program Name</label>
                                                                <input type="text" name="program_name" value="{{ $item->program_name }}" class="form-control" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="form-label small fw-bold">Sequence</label>
                                                                <input type="number" name="sequence_no" value="{{ $item->sequence_no }}" class="form-control">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold">% Implementation</label>
                                                                <input type="number" step="0.00001" name="implementation_pct" value="{{ $item->implementation_pct }}" class="form-control" placeholder="e.g. 1.33333">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold">% Traveling</label>
                                                                <input type="number" step="0.00001" name="traveling_pct" value="{{ $item->traveling_pct }}" class="form-control" placeholder="e.g. 1.88679">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small fw-bold">Local Travel (GAA)</label>
                                                                <input type="number" step="0.01" name="traveling_local_gaa" value="{{ $item->traveling_local_gaa }}" class="form-control edit-calc" data-target="edit-total-{{ $item->id }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small fw-bold">Local Travel (TF)</label>
                                                                <input type="number" step="0.01" name="traveling_local_tf" value="{{ $item->traveling_local_tf }}" class="form-control edit-calc" data-target="edit-total-{{ $item->id }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small fw-bold">Foreign Travel (TF)</label>
                                                                <input type="number" step="0.01" name="traveling_foreign_tf" value="{{ $item->traveling_foreign_tf }}" class="form-control edit-calc" data-target="edit-total-{{ $item->id }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small fw-bold">Implementation (TF)</label>
                                                                <input type="number" step="0.01" name="implementation_tf" value="{{ $item->implementation_tf }}" class="form-control edit-calc" data-target="edit-total-{{ $item->id }}">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold">Source of Fund (Optional)</label>
                                                                <input type="text" name="source_of_fund" value="{{ $item->source_of_fund }}" class="form-control" placeholder="e.g. Source of Fund: GAA">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold">Total Allocation (Computed / Override)</label>
                                                                <input type="number" step="0.01" name="total_allocation" id="edit-total-{{ $item->id }}" value="{{ $item->total_allocation }}" class="form-control fw-bold bg-light">
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label small fw-bold">Remarks</label>
                                                                <textarea name="remarks" class="form-control" rows="2">{{ $item->remarks }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 px-4 pb-4">
                                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- College Total / Allotted Budget Row --}}
                                @if($collegeName || $allotmentRow)
                                    @if($allotmentRow)
                                        @php
                                            $rowLabel = $allotmentRow->program_name ?: ('TOTAL' . ($collegeName ? ' (' . $collegeName . ')' : ''));
                                            $isSubtotalAllotment = str_starts_with(strtoupper($rowLabel), 'SUBTOTAL');
                                            $isGaaOnly = ($allotmentRow->traveling_local_tf == 0 && $allotmentRow->traveling_foreign_tf == 0 && $allotmentRow->implementation_tf == 0 && !empty($allotmentRow->source_of_fund));
                                        @endphp
                                        {{-- Custom Allotted Budget / Subtotal Row directly entered by User --}}
                                        <tr class="fw-bold bg-light border-bottom border-top border-2 border-primary">
                                            <td class="ps-4 text-dark text-uppercase" style="font-size: 0.85rem;">
                                                @if(!$isSubtotalAllotment)
                                                    <span class="badge bg-primary text-white me-2">ALLOTTED</span>
                                                @endif
                                                <span class="fw-bold">{{ $rowLabel }}</span>
                                                @if($allotmentRow->source_of_fund && !$isGaaOnly)
                                                    <span class="text-muted small fst-italic fw-normal">({{ $allotmentRow->source_of_fund }})</span>
                                                @endif
                                            </td>
                                            <td class="text-center text-dark fw-bold">{{ $allotmentRow->sequence_no ?? 1 }}</td>
                                            <td class="text-end text-muted">{{ $allotmentRow->implementation_pct > 0 ? number_format($allotmentRow->implementation_pct, 5) . '%' : '—' }}</td>
                                            <td class="text-end text-muted">{{ $allotmentRow->traveling_pct > 0 ? number_format($allotmentRow->traveling_pct, 5) . '%' : '—' }}</td>
                                            <td class="text-end fw-bold text-dark">₱{{ number_format($allotmentRow->traveling_local_gaa, 2) }}</td>
                                            @if($isGaaOnly)
                                                <td colspan="3" class="text-center text-muted fst-italic fw-normal bg-light bg-opacity-75" style="letter-spacing: 0.3px;">
                                                    ({{ $allotmentRow->source_of_fund }})
                                                </td>
                                            @else
                                                <td class="text-end text-muted">{{ $allotmentRow->traveling_local_tf > 0 ? '₱' . number_format($allotmentRow->traveling_local_tf, 2) : '—' }}</td>
                                                <td class="text-end text-muted">{{ $allotmentRow->traveling_foreign_tf > 0 ? '₱' . number_format($allotmentRow->traveling_foreign_tf, 2) : '—' }}</td>
                                                <td class="text-end text-muted">{{ $allotmentRow->implementation_tf > 0 ? '₱' . number_format($allotmentRow->implementation_tf, 2) : '—' }}</td>
                                            @endif
                                            <td class="text-end text-primary fw-bold">₱{{ number_format($allotmentRow->total_allocation, 2) }}</td>
                                            <td class="text-center pe-4">
                                                <div class="d-flex justify-content-center align-items-center gap-1">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-primary rounded-pill px-2 py-1 shadow-sm d-inline-flex align-items-center gap-1"
                                                            title="Edit Allotted Budget Data directly"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#allotmentModal_{{ $collegeSlug }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                        <span style="font-size: 0.72rem;" class="fw-bold">Edit Allotment</span>
                                                    </button>
                                                    <form action="{{ route('recording_staff.budget_allocations.delete_allotment') }}" method="POST" onsubmit="return confirm('Revert total to program auto-sum?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="fiscal_year" value="{{ $fiscalYear }}">
                                                        <input type="hidden" name="campus" value="{{ $campusName }}">
                                                        <input type="hidden" name="college" value="{{ $collegeName }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle p-1" style="width: 26px; height: 26px;" title="Revert to auto-sum">
                                                            <i class="bi bi-arrow-counterclockwise" style="font-size: 0.7rem;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                        {{-- Automatic Sum of Programs, with button to add manual allotment row --}}
                                        <tr class="fw-bold bg-light border-bottom border-top">
                                            <td class="ps-4 text-muted text-uppercase" style="font-size: 0.8rem;">TOTAL @if($collegeName)({{ $collegeName }})@endif</td>
                                            <td class="text-center text-muted">{{ $programRows->count() }}</td>
                                            <td class="text-end text-muted">{{ number_format($programRows->sum('implementation_pct'), 5) }}%</td>
                                            <td class="text-end text-muted">{{ number_format($programRows->sum('traveling_pct'), 5) }}%</td>
                                            <td class="text-end">₱{{ number_format($programRows->sum('traveling_local_gaa'), 2) }}</td>
                                            <td class="text-end">₱{{ number_format($programRows->sum('traveling_local_tf'), 2) }}</td>
                                            <td class="text-end">₱{{ number_format($programRows->sum('traveling_foreign_tf'), 2) }}</td>
                                            <td class="text-end">₱{{ number_format($programRows->sum('implementation_tf'), 2) }}</td>
                                            <td class="text-end text-primary fw-bold">₱{{ number_format($programRows->sum('total_allocation'), 2) }}</td>
                                            <td class="text-center pe-4">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 shadow-sm d-inline-flex align-items-center gap-1"
                                                        title="Input Allotted Budget Data directly on Total Row"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#allotmentModal_{{ $collegeSlug }}">
                                                    <i class="bi bi-plus-circle"></i>
                                                    <span style="font-size: 0.72rem;" class="fw-bold">Input Allotment</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif

                                    {{-- Allotted Budget Input Modal for College / Total Row --}}
                                    <div class="modal fade" id="allotmentModal_{{ $collegeSlug }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <form action="{{ route('recording_staff.budget_allocations.save_allotment') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="fiscal_year" value="{{ $fiscalYear }}">
                                                    <input type="hidden" name="campus" value="{{ $campusName }}">
                                                    <input type="hidden" name="college" value="{{ $collegeName }}">

                                                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                                                        <div>
                                                            <h5 class="modal-title fw-bold text-dark mb-1">
                                                                <i class="bi bi-cash-coin text-primary me-2"></i>Allotted Budget Input: {{ $allotmentRow ? $allotmentRow->program_name : ($collegeName ?: $campusName) }}
                                                            </h5>
                                                            <p class="text-muted small mb-0">{{ $campusName }} • Enter budget figures directly on this Total/Subtotal row</p>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body px-4 py-3">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold">Row Label / Title</label>
                                                                <input type="text" name="program_name" value="{{ $allotmentRow ? $allotmentRow->program_name : ($campusName === 'Hamtic Campus' ? 'Subtotal' : ($campusName === 'Tibiao Campus' ? 'Subtotal (TLMC)' : 'TOTAL')) }}" class="form-control" placeholder="e.g. Subtotal, Subtotal (TLMC), TOTAL" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold">Count / No. of Programs</label>
                                                                <input type="number" name="sequence_no" value="{{ $allotmentRow ? $allotmentRow->sequence_no : ($programRows->count() ?: 1) }}" class="form-control" placeholder="1" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold">% Implementation</label>
                                                                <input type="number" step="0.00001" name="implementation_pct" value="{{ $allotmentRow ? $allotmentRow->implementation_pct : '0' }}" class="form-control" placeholder="e.g. 1.33333">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold">% Traveling</label>
                                                                <input type="number" step="0.00001" name="traveling_pct" value="{{ $allotmentRow ? $allotmentRow->traveling_pct : '0' }}" class="form-control" placeholder="e.g. 1.88679">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small fw-bold">Local Travel (GAA)</label>
                                                                <input type="number" step="0.01" name="traveling_local_gaa" value="{{ $allotmentRow ? $allotmentRow->traveling_local_gaa : '0.00' }}" class="form-control allot-calc" data-target="allot-total-{{ $collegeSlug }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small fw-bold">Local Travel (TF)</label>
                                                                <input type="number" step="0.01" name="traveling_local_tf" value="{{ $allotmentRow ? $allotmentRow->traveling_local_tf : '0.00' }}" class="form-control allot-calc" data-target="allot-total-{{ $collegeSlug }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small fw-bold">Foreign Travel (TF)</label>
                                                                <input type="number" step="0.01" name="traveling_foreign_tf" value="{{ $allotmentRow ? $allotmentRow->traveling_foreign_tf : '0.00' }}" class="form-control allot-calc" data-target="allot-total-{{ $collegeSlug }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small fw-bold">Implementation (TF)</label>
                                                                <input type="number" step="0.01" name="implementation_tf" value="{{ $allotmentRow ? $allotmentRow->implementation_tf : '0.00' }}" class="form-control allot-calc" data-target="allot-total-{{ $collegeSlug }}">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold">Source of Fund (Optional)</label>
                                                                <input type="text" name="source_of_fund" value="{{ $allotmentRow ? $allotmentRow->source_of_fund : '' }}" class="form-control" placeholder="e.g. Source of Fund: GAA">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold">Total Allocation Amount</label>
                                                                <input type="number" step="0.01" name="total_allocation" id="allot-total-{{ $collegeSlug }}" value="{{ $allotmentRow ? $allotmentRow->total_allocation : '0.00' }}" class="form-control fw-bold bg-light">
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label small fw-bold">Remarks (Optional)</label>
                                                                <textarea name="remarks" class="form-control" rows="2" placeholder="Notes for this college allotment...">{{ $allotmentRow ? $allotmentRow->remarks : '' }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 px-4 pb-4">
                                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Save Allotted Budget</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            {{-- Subtotal row for TF-funded units (Main Campus + Libertad + Caluya) right after Caluya Campus --}}
                            @if($campusName === 'Caluya Campus' && (empty($campus) || $campus === 'all' || $campus === 'Caluya Campus'))
                                <tr class="fw-bold bg-secondary bg-opacity-10 border-top border-bottom border-2 text-dark" style="font-size: 0.90rem;">
                                    <td class="ps-4 text-uppercase fw-bold">
                                        <i class="bi bi-calculator me-1 text-secondary"></i>Subtotal
                                    </td>
                                    <td class="text-center text-muted">—</td>
                                    <td class="text-end text-muted">—</td>
                                    <td class="text-end text-muted">—</td>
                                    <td class="text-end fw-bold">₱{{ number_format($tfPoolMetrics['total_gaa'], 2) }}</td>
                                    <td class="text-end fw-bold">₱{{ number_format($tfPoolMetrics['total_local_tf'], 2) }}</td>
                                    <td class="text-end fw-bold">₱{{ number_format($tfPoolMetrics['total_foreign_tf'], 2) }}</td>
                                    <td class="text-end fw-bold">₱{{ number_format($tfPoolMetrics['total_implementation_tf'], 2) }}</td>
                                    <td class="text-end text-muted">—</td>
                                    <td class="text-center text-muted">—</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted fst-italic">
                                    No budget allocation records found for the selected filter.
                                </td>
                            </tr>
                        @endforelse

                        {{-- Final Grand Total Row matching official report --}}
                        <tr class="fw-bold table-dark border-top border-3 border-dark" style="font-size: 0.95rem;">
                            <td class="ps-4 text-uppercase">Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-end fw-bold">{{ number_format($metrics['total_gaa'], 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($metrics['total_local_tf'], 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($metrics['total_foreign_tf'], 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($metrics['total_implementation_tf'], 2) }}</td>
                            <td class="text-end fw-bold text-nowrap">₱{{ number_format($metrics['grand_total'], 2) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Programs Summary Block matching document footer --}}
        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2 d-flex justify-content-between">
                        <span>No. of Programs</span>
                        <span class="badge bg-primary rounded-pill">75 Total</span>
                    </h6>
                    <div class="d-flex justify-content-between py-1 border-bottom border-light">
                        <span class="text-muted">Main Campus</span>
                        <span class="fw-bold text-dark">53</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom border-light">
                        <span class="text-muted">Tibiao Campus</span>
                        <span class="fw-bold text-dark">14</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom border-light">
                        <span class="text-muted">Hamtic Campus</span>
                        <span class="fw-bold text-dark">8</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2">
                        <span class="fw-bold text-primary">Total Programs</span>
                        <span class="fw-bold text-primary">75</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Allocation Modal --}}
    <div class="modal fade" id="newAllocationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('recording_staff.budget_allocations.store') }}" method="POST" id="newAllocationForm">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-plus-circle-fill text-primary me-2"></i>Add Program Budget Allocation
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Fiscal Year <span class="text-danger">*</span></label>
                                <input type="number" name="fiscal_year" value="{{ $fiscalYear }}" class="form-control" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-bold">Campus <span class="text-danger">*</span></label>
                                <input type="text" name="campus" list="campusSuggestions" class="form-control" placeholder="e.g. Main Campus" value="Main Campus" required>
                                <datalist id="campusSuggestions">
                                    <option value="Main Campus">
                                    <option value="Libertad Campus">
                                    <option value="Caluya Campus">
                                    <option value="Hamtic Campus">
                                    <option value="Tibiao Campus">
                                </datalist>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">College / Academic Unit</label>
                                <input type="text" name="college" class="form-control" placeholder="e.g. COLLEGE OF ARTS AND SCIENCES">
                            </div>
                            <div class="col-md-10">
                                <label class="form-label small fw-bold">Program Name <span class="text-danger">*</span></label>
                                <input type="text" name="program_name" class="form-control" placeholder="e.g. Bachelor of Science in Information Technology" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Sequence</label>
                                <input type="number" name="sequence_no" class="form-control" placeholder="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">% Implementation</label>
                                <input type="number" step="0.00001" name="implementation_pct" class="form-control" value="1.33333">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">% Traveling</label>
                                <input type="number" step="0.00001" name="traveling_pct" class="form-control" value="1.88679">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Local Travel (GAA)</label>
                                <input type="number" step="0.01" name="traveling_local_gaa" id="add_gaa" value="2000.00" class="form-control add-calc">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Local Travel (TF)</label>
                                <input type="number" step="0.01" name="traveling_local_tf" id="add_tf_local" value="1320.75" class="form-control add-calc">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Foreign Travel (TF)</label>
                                <input type="number" step="0.01" name="traveling_foreign_tf" id="add_tf_foreign" value="1320.75" class="form-control add-calc">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Implementation (TF)</label>
                                <input type="number" step="0.01" name="implementation_tf" id="add_tf_impl" value="5546.67" class="form-control add-calc">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Source of Fund (Optional)</label>
                                <input type="text" name="source_of_fund" class="form-control" placeholder="e.g. Source of Fund: GAA">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Total Allocation (Computed)</label>
                                <input type="number" step="0.01" name="total_allocation" id="add_total" value="10188.17" class="form-control fw-bold bg-light">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Create Allocation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script for auto-summing modal fields --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Add Modal Calculation
            const addInputs = document.querySelectorAll('.add-calc');
            const addTotal = document.getElementById('add_total');

            function updateAddTotal() {
                let sum = 0;
                addInputs.forEach(input => {
                    sum += parseFloat(input.value) || 0;
                });
                addTotal.value = sum.toFixed(2);
            }

            addInputs.forEach(input => {
                input.addEventListener('input', updateAddTotal);
            });

            // Edit Modals Calculation
            const editInputs = document.querySelectorAll('.edit-calc');
            editInputs.forEach(input => {
                input.addEventListener('input', function () {
                    const targetId = this.getAttribute('data-target');
                    const modal = this.closest('.modal-body');
                    const relatedInputs = modal.querySelectorAll('.edit-calc');
                    let sum = 0;
                    relatedInputs.forEach(inp => {
                        sum += parseFloat(inp.value) || 0;
                    });
                    const targetInput = document.getElementById(targetId);
                    if (targetInput) {
                        targetInput.value = sum.toFixed(2);
                    }
                });
            });

            // College Total & Allotment Modals Calculation
            const allotInputs = document.querySelectorAll('.allot-calc, .college-calc');
            allotInputs.forEach(input => {
                input.addEventListener('input', function () {
                    const targetId = this.getAttribute('data-target');
                    const modal = this.closest('.modal-body');
                    const relatedInputs = modal.querySelectorAll('.allot-calc, .college-calc');
                    let sum = 0;
                    relatedInputs.forEach(inp => {
                        sum += parseFloat(inp.value) || 0;
                    });
                    const targetInput = document.getElementById(targetId);
                    if (targetInput) {
                        targetInput.value = sum.toFixed(2);
                    }
                });
            });
        });
    </script>
</x-app-layout>
