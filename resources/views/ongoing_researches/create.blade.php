<x-app-layout>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('ongoing_researches.index') }}" class="text-decoration-none">On-going Researches</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Prepare New List</li>
                </ol>
            </nav>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-play-circle-fill text-primary"></i> Prepare List of On-going Researches
            </h1>
            <p class="text-muted small mb-0">Compile active, funded, and on-going institutional research projects for VPREI formal noting.</p>
        </div>
        <div>
            <a href="{{ route('ongoing_researches.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Back to Dossiers
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please check the form for errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('ongoing_researches.store') }}" method="POST" id="prepareOngoingForm">
        @csrf

        <div class="row g-4">
            <!-- Left: Dossier Metadata Details Card -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 85px; z-index: 10;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                        <h5 class="fw-bold text-dark mb-1">Dossier Details</h5>
                        <p class="text-muted small mb-0">Official institutional metadata matching UA Research Unit standards.</p>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Dossier Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3 @error('title') is-invalid @enderror" 
                                   value="{{ old('title', 'List of On-going Researches - FY ' . date('Y')) }}" 
                                   placeholder="e.g. List of On-going Researches - FY 2026" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold small text-dark">Fiscal Year <span class="text-danger">*</span></label>
                                <input type="number" name="fiscal_year" class="form-control rounded-3 @error('fiscal_year') is-invalid @enderror" 
                                       value="{{ old('fiscal_year', date('Y')) }}" min="2020" max="2050" required>
                                @error('fiscal_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold small text-dark">Date</label>
                                <input type="date" name="report_date" class="form-control rounded-3" 
                                       value="{{ old('report_date', date('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Prepared By (Signatory)</label>
                            <input type="text" class="form-control bg-light rounded-3 text-muted" 
                                   value="{{ auth()->user()->name }} (Director, Research Unit)" readonly>
                            <small class="text-muted" style="font-size: 0.75rem;">Your account is automatically registered as the preparer.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Forwarded to for Noting</label>
                            <input type="text" class="form-control bg-light rounded-3 text-muted" 
                                   value="Office of the Vice President for Research, Extension & Innovation (VPREI)" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-dark">Remarks / Implementation Notes</label>
                            <textarea name="remarks" class="form-control rounded-3" rows="3" 
                                      placeholder="Optional remarks regarding this batch of on-going studies...">{{ old('remarks') }}</textarea>
                        </div>

                        <!-- Selected Counter & Submit Button -->
                        <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-center justify-content-between">
                            <span class="small fw-semibold text-muted">Selected Projects:</span>
                            <span class="badge bg-primary px-3 py-1.5 rounded-pill fs-6 fw-bold" id="selectedCountBadge">0</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-send-fill"></i> Transmit to VPREI for Noting
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right: Eligible Proposals Selection List -->
            <div class="col-12 col-lg-8">
                <!-- Filters Bar -->
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-5">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                                    <input type="text" id="tableSearch" class="form-control bg-light border-0 py-2" placeholder="Search by title or proponent...">
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <select id="collegeFilter" class="form-select form-select-sm bg-light border-0 py-2">
                                    <option value="">All Colleges / Units</option>
                                    @foreach($colleges as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5" id="selectAllBtn">
                                    <i class="bi bi-check-all me-1"></i> Select All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Proposals Table Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Active & On-going Researches</h5>
                            <small class="text-muted">Proposals currently undergoing implementation, monitoring, or approved execution.</small>
                        </div>
                        <span class="badge bg-light text-muted px-3 py-1.5 rounded-pill">{{ $ongoingProposals->count() }} Available</span>
                    </div>

                    <div class="card-body p-0">
                        @if($ongoingProposals->isEmpty())
                            <div class="p-5 text-center">
                                <div class="p-3 bg-secondary bg-opacity-10 text-secondary rounded-circle d-inline-flex mb-3">
                                    <i class="bi bi-journal-x fs-1"></i>
                                </div>
                                <h6 class="fw-bold text-dark">No Active On-going Researches Found</h6>
                                <p class="text-muted small mx-auto mb-0" style="max-width: 440px;">
                                    There are currently no research proposals in active implementation status. Once proposals pass approval and receive Notice to Proceed, they will appear here.
                                </p>
                            </div>
                        @else
                            <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0" id="proposalsTable">
                                    <thead class="table-light text-muted small text-uppercase sticky-top" style="top: 0; z-index: 5;">
                                        <tr>
                                            <th class="ps-4" style="width: 45px;">
                                                <input class="form-check-input" type="checkbox" id="masterCheckbox" title="Select / Deselect All">
                                            </th>
                                            <th>Research Title</th>
                                            <th>Proponent/s</th>
                                            <th>College</th>
                                            <th class="pe-4">Time Frame</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ongoingProposals as $proposal)
                                            @php
                                                $leadName = $proposal->user ? $proposal->user->name : 'N/A';
                                                $collabNames = $proposal->collaborators ? $proposal->collaborators->pluck('name')->toArray() : [];
                                                $allProponents = implode(', ', array_merge([$leadName], $collabNames));

                                                $timeFrame = '12 Months';
                                                if ($proposal->milestones && $proposal->milestones->count() > 0) {
                                                    $minDate = $proposal->milestones->min('start_date');
                                                    $maxDate = $proposal->milestones->max('target_date');
                                                    if ($minDate && $maxDate) {
                                                        $timeFrame = \Carbon\Carbon::parse($minDate)->format('M Y') . ' - ' . \Carbon\Carbon::parse($maxDate)->format('M Y');
                                                    } elseif ($maxDate) {
                                                        $timeFrame = 'Until ' . \Carbon\Carbon::parse($maxDate)->format('M Y');
                                                    }
                                                } elseif ($proposal->created_at) {
                                                    $timeFrame = 'FY ' . $proposal->created_at->format('Y');
                                                }

                                                $college = $proposal->user?->department ?? 'General';
                                            @endphp
                                            <tr class="proposal-row" data-college="{{ $proposal->user?->department ?? '' }}" data-campus="{{ $proposal->user?->campus ?? '' }}">
                                                <td class="ps-4 align-middle">
                                                    <input class="form-check-input proposal-checkbox" type="checkbox" name="proposal_ids[]" 
                                                           value="{{ $proposal->id }}" id="chk_{{ $proposal->id }}"
                                                           {{ in_array($proposal->id, old('proposal_ids', [])) ? 'checked' : '' }}>
                                                </td>
                                                <td class="align-middle">
                                                    <label for="chk_{{ $proposal->id }}" class="mb-0 cursor-pointer d-block">
                                                        <div class="fw-bold text-dark proposal-title-text">{{ $proposal->title }}</div>
                                                        <div class="d-flex align-items-center gap-2 mt-1">
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.7rem;">
                                                                {{ $proposal->proposal_code ?? 'PROPOSAL-#' . $proposal->id }}
                                                            </span>
                                                            <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 0.7rem;">
                                                                {{ ucfirst(str_replace('_', ' ', $proposal->status)) }}
                                                            </span>
                                                        </div>
                                                    </label>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="small fw-semibold text-dark">{{ $allProponents }}</div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill" style="font-size: 0.78rem;">
                                                        {{ $college }}
                                                    </span>
                                                    @if($proposal->user?->campus)
                                                        <small class="text-muted d-block mt-0.5" style="font-size: 0.72rem;">{{ $proposal->user->campus }}</small>
                                                    @endif
                                                </td>
                                                <td class="align-middle pe-4">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2.5 py-1 rounded-pill" style="font-size: 0.78rem;">
                                                        <i class="bi bi-calendar3 me-1"></i> {{ $timeFrame }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const masterCheckbox = document.getElementById('masterCheckbox');
            const selectAllBtn = document.getElementById('selectAllBtn');
            const checkboxes = document.querySelectorAll('.proposal-checkbox');
            const counterBadge = document.getElementById('selectedCountBadge');
            const searchInput = document.getElementById('tableSearch');
            const collegeFilter = document.getElementById('collegeFilter');
            const rows = document.querySelectorAll('.proposal-row');

            function updateCounter() {
                const checked = document.querySelectorAll('.proposal-checkbox:checked').length;
                if (counterBadge) {
                    counterBadge.textContent = checked;
                }
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateCounter);
            });

            if (masterCheckbox) {
                masterCheckbox.addEventListener('change', function () {
                    const visibleRows = Array.from(rows).filter(r => r.style.display !== 'none');
                    visibleRows.forEach(r => {
                        const cb = r.querySelector('.proposal-checkbox');
                        if (cb) cb.checked = masterCheckbox.checked;
                    });
                    updateCounter();
                });
            }

            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function () {
                    const visibleRows = Array.from(rows).filter(r => r.style.display !== 'none');
                    const allChecked = visibleRows.every(r => r.querySelector('.proposal-checkbox')?.checked);
                    visibleRows.forEach(r => {
                        const cb = r.querySelector('.proposal-checkbox');
                        if (cb) cb.checked = !allChecked;
                    });
                    if (masterCheckbox) masterCheckbox.checked = !allChecked;
                    selectAllBtn.innerHTML = !allChecked 
                        ? '<i class="bi bi-x-circle me-1"></i> Deselect All' 
                        : '<i class="bi bi-check-all me-1"></i> Select All';
                    updateCounter();
                });
            }

            function filterRows() {
                const query = (searchInput?.value || '').toLowerCase().trim();
                const selectedCollege = collegeFilter?.value || '';

                rows.forEach(r => {
                    const titleText = (r.querySelector('.proposal-title-text')?.textContent || '').toLowerCase();
                    const collegeAttr = r.getAttribute('data-college') || '';

                    const matchesSearch = !query || titleText.includes(query);
                    const matchesCollege = !selectedCollege || collegeAttr === selectedCollege;

                    if (matchesSearch && matchesCollege) {
                        r.style.display = '';
                    } else {
                        r.style.display = 'none';
                    }
                });
            }

            if (searchInput) searchInput.addEventListener('input', filterRows);
            if (collegeFilter) collegeFilter.addEventListener('change', filterRows);

            updateCounter();
        });
    </script>
</x-app-layout>
