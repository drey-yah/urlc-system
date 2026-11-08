<x-app-layout>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('service_credits.index') }}" class="text-decoration-none">Service Credits</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Prepare Qualification List</li>
                </ol>
            </nav>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-award-fill text-primary"></i> Prepare List of Researchers Qualified to Avail Service Credits
            </h1>
            <p class="text-muted small mb-0">Compile completed institutional research projects with attached Terminal Reports and Certificates of Completion for VPREI executive approval.</p>
        </div>
        <div>
            <a href="{{ route('service_credits.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold">
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

    <form action="{{ route('service_credits.store') }}" method="POST" id="prepareServiceCreditsForm">
        @csrf

        <div class="row g-4">
            <!-- Left: Metadata Card -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 85px; z-index: 10;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                        <h5 class="fw-bold text-dark mb-1">Dossier Details</h5>
                        <p class="text-muted small mb-0">Official institutional roster format matching UA Research Unit standards.</p>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Dossier Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3 @error('title') is-invalid @enderror" 
                                   value="{{ old('title', 'List of Researchers Qualified to Avail Service Credits') }}" 
                                   placeholder="e.g. List of Researchers Qualified to Avail Service Credits" required>
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
                            <small class="text-muted" style="font-size: 0.75rem;">Your account is recorded as the preparing Research Director.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Approved By (Approving Authority)</label>
                            <input type="text" class="form-control bg-light rounded-3 text-muted" 
                                   value="VP for Research, Extension, and Innovation" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Required Attachments</label>
                            <div class="p-2.5 bg-light rounded-3 small text-muted">
                                <i class="bi bi-paperclip me-1 text-primary"></i> <strong>Mandatory Enclosures:</strong><br>
                                &bull; Terminal Report (including Full Manuscript)<br>
                                &bull; Certificate of Research Completion
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-dark">General Remarks / Notes</label>
                            <textarea name="remarks" class="form-control rounded-3" rows="3" 
                                      placeholder="Optional remarks regarding this batch of qualified faculty researchers...">{{ old('remarks') }}</textarea>
                        </div>

                        <!-- Selected Counter & Submit Button -->
                        <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-center justify-content-between">
                            <span class="small fw-semibold text-muted">Selected Researches:</span>
                            <span class="badge bg-primary px-3 py-1.5 rounded-pill fs-6 fw-bold" id="selectedCountBadge">0</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-send-fill"></i> Transmit to VPREI for Approval
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
                                    <input type="text" id="tableSearch" class="form-control bg-light border-0 py-2" placeholder="Search title, researcher, or college...">
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
                            <h5 class="fw-bold mb-0 text-dark">Eligible Researches for Service Credits</h5>
                            <small class="text-muted">Faculty with completed research projects, terminal reports, or completion certificates.</small>
                        </div>
                        <span class="badge bg-light text-muted px-3 py-1.5 rounded-pill">{{ $eligibleProposals->count() }} Available</span>
                    </div>

                    <div class="card-body p-0">
                        @if($eligibleProposals->isEmpty())
                            <div class="p-5 text-center">
                                <div class="p-3 bg-secondary bg-opacity-10 text-secondary rounded-circle d-inline-flex mb-3">
                                    <i class="bi bi-award fs-1"></i>
                                </div>
                                <h6 class="fw-bold text-dark">No Completed Researches Found</h6>
                                <p class="text-muted small mx-auto mb-0" style="max-width: 440px;">
                                    There are currently no research projects recorded as completed with terminal reports. Once terminal reports are submitted and completion certificates are issued, they will appear here.
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
                                            <th>Researcher/s & Research Title</th>
                                            <th>College</th>
                                            <th>Date Completed</th>
                                            <th class="pe-4">Service Credits</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($eligibleProposals as $proposal)
                                            @php
                                                $leadName = $proposal->user ? $proposal->user->name : 'N/A';
                                                $collabNames = $proposal->collaborators ? $proposal->collaborators->pluck('name')->toArray() : [];
                                                $allResearchers = implode(', ', array_merge([$leadName], $collabNames));
                                                $collegeName = $proposal->college_affiliation ?? ($proposal->user ? $proposal->user->department : 'College of Arts and Sciences');

                                                // Date completed
                                                $dateCompleted = date('F d, Y');
                                                if ($proposal->terminalReport && $proposal->terminalReport->updated_at) {
                                                    $dateCompleted = $proposal->terminalReport->updated_at->format('F d, Y');
                                                } elseif ($proposal->status === 'completed' && $proposal->updated_at) {
                                                    $dateCompleted = $proposal->updated_at->format('F d, Y');
                                                }
                                            @endphp
                                            <tr class="proposal-row" data-college="{{ $collegeName }}" data-campus="{{ $proposal->user?->campus ?? '' }}">
                                                <td class="ps-4 align-top pt-3">
                                                    <input class="form-check-input proposal-checkbox" type="checkbox" name="proposal_ids[]" 
                                                           value="{{ $proposal->id }}" id="chk_{{ $proposal->id }}"
                                                           {{ in_array($proposal->id, old('proposal_ids', [])) ? 'checked' : '' }}>
                                                </td>
                                                <td class="align-top pt-3" style="min-width: 230px;">
                                                    <label for="chk_{{ $proposal->id }}" class="mb-0 cursor-pointer d-block">
                                                        <div class="fw-bold text-dark proposal-title-text">{{ $proposal->title }}</div>
                                                        <div class="small text-muted mt-1">
                                                            <i class="bi bi-person-badge me-1"></i><strong>Researcher/s:</strong> {{ $allResearchers }}
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 mt-1">
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.7rem;">
                                                                {{ $proposal->proposal_code ?? 'PROPOSAL-#' . $proposal->id }}
                                                            </span>
                                                            <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 0.7rem;">
                                                                {{ ucfirst(str_replace('_', ' ', $proposal->status)) }}
                                                            </span>
                                                            @if($proposal->terminalReport)
                                                                <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.7rem;">
                                                                    <i class="bi bi-file-earmark-check"></i> Terminal Report Attached
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </label>
                                                </td>
                                                <td class="align-top pt-2" style="min-width: 140px;">
                                                    <input type="text" name="items[{{ $proposal->id }}][college]" 
                                                           class="form-control form-control-sm rounded-2 college-field" 
                                                           value="{{ old("items.{$proposal->id}.college", $collegeName) }}" 
                                                           placeholder="College / Unit">
                                                    <small class="text-muted" style="font-size: 0.7rem;">College</small>
                                                </td>
                                                <td class="align-top pt-2" style="width: 150px;">
                                                    <input type="text" name="items[{{ $proposal->id }}][date_completed]" 
                                                           class="form-control form-control-sm rounded-2 date-field" 
                                                           value="{{ old("items.{$proposal->id}.date_completed", $dateCompleted) }}" 
                                                           placeholder="e.g. October 20, 2026">
                                                    <small class="text-muted" style="font-size: 0.7rem;">Date Completed</small>
                                                </td>
                                                <td class="align-top pt-2 pe-4" style="width: 140px;">
                                                    <input type="text" name="items[{{ $proposal->id }}][service_credits]" 
                                                           class="form-control form-control-sm rounded-2 text-center credit-field" 
                                                           value="{{ old("items.{$proposal->id}.service_credits", '15 Days') }}" 
                                                           placeholder="e.g. 15 Days">
                                                    <small class="text-muted" style="font-size: 0.7rem;">Credits Granted</small>
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
            const collegeFilter = document.getElementById('collegeFilter');
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
                const collegeVal = (collegeFilter ? collegeFilter.value.toLowerCase().trim() : '');

                rows.forEach(row => {
                    const rowCollege = (row.dataset.college || '').toLowerCase();
                    const text = row.textContent.toLowerCase();

                    const matchesSearch = !searchVal || text.includes(searchVal);
                    const matchesCollege = !collegeVal || rowCollege.includes(collegeVal);

                    if (matchesSearch && matchesCollege) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                updateSelectedCount();
            }

            if (tableSearch) tableSearch.addEventListener('input', filterTable);
            if (collegeFilter) collegeFilter.addEventListener('change', filterTable);

            updateSelectedCount();
        });
    </script>
</x-app-layout>
