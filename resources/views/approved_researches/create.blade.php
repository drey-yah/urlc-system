<x-app-layout>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('approved_researches.index') }}" class="text-decoration-none">Approved Researches</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Prepare New List</li>
                </ol>
            </nav>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-pencil-square text-primary"></i> Prepare List of Approved Researches
            </h1>
            <p class="text-muted small mb-0">Compile approved research proposals into an official institutional dossier for VPREI formal noting.</p>
        </div>
        <div>
            <a href="{{ route('approved_researches.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold">
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

    <form action="{{ route('approved_researches.store') }}" method="POST" id="prepareListForm">
        @csrf

        <div class="row g-4">
            <!-- Left: Document Details Card -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 85px; z-index: 10;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                        <h5 class="fw-bold text-dark mb-1">Dossier Details</h5>
                        <p class="text-muted small mb-0">Official institutional metadata matching Form RESU standards.</p>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Dossier / List Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3 @error('title') is-invalid @enderror" 
                                   value="{{ old('title', 'List of Approved Researches - FY ' . date('Y')) }}" 
                                   placeholder="e.g. List of Approved Researches - 1st Cycle 2026" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold small text-dark">Date <span class="text-danger">*</span></label>
                                <input type="date" name="report_date" class="form-control rounded-3 @error('report_date') is-invalid @enderror" 
                                       value="{{ old('report_date', date('Y-m-d')) }}" required>
                                @error('report_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold small text-dark">Fiscal Year</label>
                                <input type="number" name="fiscal_year" class="form-control rounded-3" 
                                       value="{{ old('fiscal_year', date('Y')) }}" min="2020" max="2050">
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
                            <label class="form-label fw-bold small text-dark">Remarks / Endorsement Notes</label>
                            <textarea name="remarks" class="form-control rounded-3" rows="3" 
                                      placeholder="Optional remarks regarding this batch of approved studies...">{{ old('remarks') }}</textarea>
                        </div>

                        <!-- Selected Counter & Submit Button -->
                        <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-center justify-content-between">
                            <span class="small fw-semibold text-muted">Selected Researches:</span>
                            <span class="badge bg-primary px-3 py-1.5 rounded-pill fs-6 fw-bold" id="selectedCountBadge">0</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-send-fill"></i> Transmit to VPREI for Noting
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right: Approved Proposals Selection List -->
            <div class="col-12 col-lg-8">
                <!-- Filters Bar -->
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-5">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                                    <input type="text" id="tableSearch" class="form-control bg-light border-0 py-2" placeholder="Filter proposals on page...">
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
                            <h5 class="fw-bold mb-0 text-dark">Eligible Approved Researches</h5>
                            <small class="text-muted">Proposals that have passed college endorsement and institutional approval.</small>
                        </div>
                        <span class="badge bg-light text-muted px-3 py-1.5 rounded-pill">{{ $approvedProposals->count() }} Available</span>
                    </div>

                    <div class="card-body p-0">
                        @if($approvedProposals->isEmpty())
                            <div class="p-5 text-center">
                                <div class="p-3 bg-secondary bg-opacity-10 text-secondary rounded-circle d-inline-flex mb-3">
                                    <i class="bi bi-journal-x fs-1"></i>
                                </div>
                                <h6 class="fw-bold text-dark">No Eligible Approved Researches Found</h6>
                                <p class="text-muted small mx-auto mb-0" style="max-width: 420px;">
                                    There are currently no research proposals in approved or ongoing status. Once research proposals are approved through the review workflow, they will appear here.
                                </p>
                            </div>
                        @else
                            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0" id="proposalsTable">
                                    <thead class="table-light text-muted small text-uppercase sticky-top" style="top: 0; z-index: 5;">
                                        <tr>
                                            <th class="ps-4" style="width: 45px;">
                                                <input class="form-check-input" type="checkbox" id="masterCheckbox" title="Select / Deselect All">
                                            </th>
                                            <th>Proposal Title & Details</th>
                                            <th>Proponent / College</th>
                                            <th>Time Frame</th>
                                            <th class="text-end pe-4">Budget</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($approvedProposals as $proposal)
                                            @php
                                                $leadName = $proposal->user ? $proposal->user->name : 'N/A';
                                                $collabNames = $proposal->collaborators ? $proposal->collaborators->pluck('name')->toArray() : [];
                                                $allProponents = array_merge([$leadName], $collabNames);

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

                                                $budget = $proposal->total_budget ?? 0;
                                                if ((!$budget || $budget == 0) && $proposal->budgetItems && $proposal->budgetItems->count() > 0) {
                                                    $budget = $proposal->budgetItems->sum('amount');
                                                }
                                            @endphp
                                            <tr class="proposal-row" data-college="{{ $proposal->user?->department ?? '' }}" data-campus="{{ $proposal->user?->campus ?? '' }}">
                                                <td class="ps-4">
                                                    <input class="form-check-input proposal-checkbox" type="checkbox" name="proposal_ids[]" 
                                                           value="{{ $proposal->id }}" id="chk_{{ $proposal->id }}"
                                                           {{ in_array($proposal->id, old('proposal_ids', [])) ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <label for="chk_{{ $proposal->id }}" class="mb-0 cursor-pointer d-block">
                                                        <div class="fw-bold text-dark proposal-title-text">{{ $proposal->title }}</div>
                                                        <div class="d-flex align-items-center gap-2 mt-1">
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.72rem;">
                                                                {{ $proposal->proposal_code ?? 'PROPOSAL-#' . $proposal->id }}
                                                            </span>
                                                            <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 0.72rem;">
                                                                {{ ucfirst(str_replace('_', ' ', $proposal->status)) }}
                                                            </span>
                                                        </div>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div class="small fw-semibold text-dark">{{ implode(', ', $allProponents) }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">
                                                        {{ $proposal->user?->department ?? 'General' }}
                                                        @if($proposal->user?->campus)
                                                            &bull; {{ $proposal->user->campus }}
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark fw-semibold px-2.5 py-1 rounded-pill" style="font-size: 0.78rem;">
                                                        <i class="bi bi-calendar3 me-1"></i> {{ $timeFrame }}
                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="fw-bold text-dark">PHP {{ number_format((float)$budget, 2) }}</div>
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
                cb.addEventListener('change', () => {
                    updateCounter();
                });
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
