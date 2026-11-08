<x-app-layout>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('completed_researches_summary.index') }}" class="text-decoration-none">Completed Summary Forms</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Prepare Summary Form</li>
                </ol>
            </nav>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-file-earmark-ruled-fill text-primary"></i> Prepare Completed Researches Summary Form
            </h1>
            <p class="text-muted small mb-0">Compile completed college research projects with project durations, timeline milestones, approved budgets, and actual expenditures for Dean noting.</p>
        </div>
        <div>
            <a href="{{ route('completed_researches_summary.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Back to Summary Forms
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

    <form action="{{ route('completed_researches_summary.store') }}" method="POST" id="prepareSummaryForm">
        @csrf

        <div class="row g-4">
            <!-- Left: Metadata Card -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 85px; z-index: 10;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                        <h5 class="fw-bold text-dark mb-1">Form Details</h5>
                        <p class="text-muted small mb-0">Official institutional college-level completed research reporting format.</p>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Form Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3 @error('title') is-invalid @enderror" 
                                   value="{{ old('title', 'COMPLETED RESEARCHES SUMMARY FORM') }}" 
                                   placeholder="COMPLETED RESEARCHES SUMMARY FORM" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">College / Department <span class="text-danger">*</span></label>
                            @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                                <select name="college" class="form-select rounded-3" id="collegeSelect" onchange="window.location.href = '{{ route('completed_researches_summary.create') }}?college=' + encodeURIComponent(this.value);">
                                    @foreach($colleges as $c)
                                        <option value="{{ $c }}" {{ $college === $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" name="college" class="form-control bg-light rounded-3 text-muted fw-bold" 
                                       value="{{ $coordinatorCollege }}" readonly>
                                <small class="text-muted" style="font-size: 0.75rem;">Summary form is compiled for your assigned college.</small>
                            @endif
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
                                   value="{{ auth()->user()->name }} (College Research Coordinator)" readonly>
                            <small class="text-muted" style="font-size: 0.75rem;">Your account is recorded as the preparing Coordinator.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Forwarded to for Noting</label>
                            <input type="text" class="form-control bg-light rounded-3 text-muted" 
                                   value="Dean, {{ $college }}" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-dark">General Remarks / Notes</label>
                            <textarea name="remarks" class="form-control rounded-3" rows="3" 
                                      placeholder="Optional remarks regarding this summary of completed college researches...">{{ old('remarks') }}</textarea>
                        </div>

                        <!-- Selected Counter & Submit Button -->
                        <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-center justify-content-between">
                            <span class="small fw-semibold text-muted">Selected Projects:</span>
                            <span class="badge bg-primary px-3 py-1.5 rounded-pill fs-6 fw-bold" id="selectedCountBadge">0</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-send-fill"></i> Transmit to College Dean for Noting
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
                            <div class="col-12 col-md-8">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                                    <input type="text" id="tableSearch" class="form-control bg-light border-0 py-2" placeholder="Search title or proponent...">
                                </div>
                            </div>
                            <div class="col-12 col-md-4 text-end">
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
                            <h5 class="fw-bold mb-0 text-dark">Completed Researches ({{ $college }})</h5>
                            <small class="text-muted">Proposals completed in this college with recorded budgets and expenditures.</small>
                        </div>
                        <span class="badge bg-light text-muted px-3 py-1.5 rounded-pill">{{ $eligibleProposals->count() }} Available</span>
                    </div>

                    <div class="card-body p-0">
                        @if($eligibleProposals->isEmpty())
                            <div class="p-5 text-center">
                                <div class="p-3 bg-secondary bg-opacity-10 text-secondary rounded-circle d-inline-flex mb-3">
                                    <i class="bi bi-folder-x fs-1"></i>
                                </div>
                                <h6 class="fw-bold text-dark">No Completed Researches Found for {{ $college }}</h6>
                                <p class="text-muted small mx-auto mb-0" style="max-width: 440px;">
                                    There are currently no completed research projects recorded for this college. Researches with completed terminal reports will appear here automatically.
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
                                            <th>Project Title & Proponent/s</th>
                                            <th>Duration & Dates</th>
                                            <th class="pe-4">Budget & Expenditure</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($eligibleProposals as $proposal)
                                            @php
                                                $leadName = $proposal->user ? $proposal->user->name : 'N/A';
                                                $collabNames = $proposal->collaborators ? $proposal->collaborators->pluck('name')->toArray() : [];
                                                $allProponents = implode(', ', array_merge([$leadName], $collabNames));

                                                // Duration
                                                $duration = '12 Months';
                                                if ($proposal->milestones && $proposal->milestones->count() > 0) {
                                                    $minD = $proposal->milestones->min('start_date');
                                                    $maxD = $proposal->milestones->max('target_date');
                                                    if ($minD && $maxD) {
                                                        $diffM = \Carbon\Carbon::parse($minD)->diffInMonths(\Carbon\Carbon::parse($maxD));
                                                        if ($diffM > 0) $duration = $diffM . ' Months';
                                                    }
                                                }

                                                // Dates
                                                $dateStarted = $proposal->created_at ? $proposal->created_at->format('M d, Y') : date('M d, Y');
                                                if ($proposal->milestones && $proposal->milestones->min('start_date')) {
                                                    $dateStarted = \Carbon\Carbon::parse($proposal->milestones->min('start_date'))->format('M d, Y');
                                                }

                                                $dateCompleted = date('M d, Y');
                                                if ($proposal->terminalReport && $proposal->terminalReport->updated_at) {
                                                    $dateCompleted = $proposal->terminalReport->updated_at->format('M d, Y');
                                                } elseif ($proposal->status === 'completed' && $proposal->updated_at) {
                                                    $dateCompleted = $proposal->updated_at->format('M d, Y');
                                                }

                                                // Budget & Expenditure
                                                $budgetStr = $proposal->total_budget ? number_format($proposal->total_budget, 2) : '50,000.00';
                                                $expStr = $budgetStr;
                                                if ($proposal->purchaseRequests && $proposal->purchaseRequests->count() > 0) {
                                                    $sumPR = $proposal->purchaseRequests->sum('total_amount');
                                                    if ($sumPR > 0) {
                                                        $expStr = number_format($sumPR, 2);
                                                    }
                                                }
                                            @endphp
                                            <tr class="proposal-row">
                                                <td class="ps-4 align-top pt-3">
                                                    <input class="form-check-input proposal-checkbox" type="checkbox" name="proposal_ids[]" 
                                                           value="{{ $proposal->id }}" id="chk_{{ $proposal->id }}"
                                                           {{ in_array($proposal->id, old('proposal_ids', [])) ? 'checked' : '' }}>
                                                </td>
                                                <td class="align-top pt-3" style="min-width: 240px;">
                                                    <label for="chk_{{ $proposal->id }}" class="mb-0 cursor-pointer d-block">
                                                        <div class="fw-bold text-dark proposal-title-text">{{ $proposal->title }}</div>
                                                        <div class="small text-muted mt-1">
                                                            <i class="bi bi-people me-1"></i><strong>Proponent/s:</strong> {{ $allProponents }}
                                                        </div>
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
                                                <td class="align-top pt-2" style="min-width: 170px;">
                                                    <div class="mb-1">
                                                        <input type="text" name="items[{{ $proposal->id }}][duration]" 
                                                               class="form-control form-control-sm rounded-2" 
                                                               value="{{ old("items.{$proposal->id}.duration", $duration) }}" 
                                                               placeholder="Duration (e.g. 12 Months)">
                                                        <small class="text-muted" style="font-size: 0.7rem;">Duration</small>
                                                    </div>
                                                    <div class="row g-1">
                                                        <div class="col-6">
                                                            <input type="text" name="items[{{ $proposal->id }}][date_started]" 
                                                                   class="form-control form-control-sm rounded-2" 
                                                                   value="{{ old("items.{$proposal->id}.date_started", $dateStarted) }}" 
                                                                   placeholder="Date Started">
                                                            <small class="text-muted" style="font-size: 0.65rem;">Started</small>
                                                        </div>
                                                        <div class="col-6">
                                                            <input type="text" name="items[{{ $proposal->id }}][date_completed]" 
                                                                   class="form-control form-control-sm rounded-2" 
                                                                   value="{{ old("items.{$proposal->id}.date_completed", $dateCompleted) }}" 
                                                                   placeholder="Date Completed">
                                                            <small class="text-muted" style="font-size: 0.65rem;">Completed</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-top pt-2 pe-4" style="min-width: 170px;">
                                                    <div class="mb-1">
                                                        <input type="text" name="items[{{ $proposal->id }}][budget]" 
                                                               class="form-control form-control-sm rounded-2 text-end" 
                                                               value="{{ old("items.{$proposal->id}.budget", $budgetStr) }}" 
                                                               placeholder="Approved Budget">
                                                        <small class="text-muted" style="font-size: 0.7rem;">Budget (PhP)</small>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="items[{{ $proposal->id }}][expenditure]" 
                                                               class="form-control form-control-sm rounded-2 text-end" 
                                                               value="{{ old("items.{$proposal->id}.expenditure", $expStr) }}" 
                                                               placeholder="Actual Expenditure">
                                                        <small class="text-muted" style="font-size: 0.7rem;">Expenditure (PhP)</small>
                                                    </div>
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
            const proposalCheckboxes = document.querySelectorAll('.proposal-checkbox');
            const selectedCountBadge = document.getElementById('selectedCountBadge');
            const tableSearch = document.getElementById('tableSearch');
            const rows = document.querySelectorAll('.proposal-row');

            function updateSelectedCount() {
                const checked = document.querySelectorAll('.proposal-checkbox:checked').length;
                selectedCountBadge.textContent = checked;
                if (masterCheckbox) {
                    const totalVisible = document.querySelectorAll('.proposal-row:not([style*="display: none"]) .proposal-checkbox').length;
                    const checkedVisible = document.querySelectorAll('.proposal-row:not([style*="display: none"]) .proposal-checkbox:checked').length;
                    masterCheckbox.checked = totalVisible > 0 && totalVisible === checkedVisible;
                }
            }

            if (masterCheckbox) {
                masterCheckbox.addEventListener('change', function () {
                    const isChecked = this.checked;
                    rows.forEach(row => {
                        if (row.style.display !== 'none') {
                            const chk = row.querySelector('.proposal-checkbox');
                            if (chk) chk.checked = isChecked;
                        }
                    });
                    updateSelectedCount();
                });
            }

            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function () {
                    const anyUnchecked = Array.from(rows).some(row => {
                        if (row.style.display !== 'none') {
                            const chk = row.querySelector('.proposal-checkbox');
                            return chk && !chk.checked;
                        }
                        return false;
                    });

                    rows.forEach(row => {
                        if (row.style.display !== 'none') {
                            const chk = row.querySelector('.proposal-checkbox');
                            if (chk) chk.checked = anyUnchecked;
                        }
                    });
                    if (masterCheckbox) masterCheckbox.checked = anyUnchecked;
                    updateSelectedCount();
                });
            }

            proposalCheckboxes.forEach(chk => {
                chk.addEventListener('change', updateSelectedCount);
            });

            // Filter logic
            function filterTable() {
                const searchVal = (tableSearch ? tableSearch.value.toLowerCase().trim() : '');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (!searchVal || text.includes(searchVal)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                updateSelectedCount();
            }

            if (tableSearch) tableSearch.addEventListener('input', filterTable);

            updateSelectedCount();
        });
    </script>
</x-app-layout>
