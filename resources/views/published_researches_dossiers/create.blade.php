<x-app-layout>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('published_researches_dossiers.index') }}" class="text-decoration-none">Published Dossiers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Prepare New Dossier</li>
                </ol>
            </nav>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-journal-bookmark-fill text-primary"></i> Prepare List of Published Researches
            </h1>
            <p class="text-muted small mb-0">Compile published articles, refereed papers, and indexed journal outputs into an official landscape dossier for VPREI formal noting.</p>
        </div>
        <div>
            <a href="{{ route('published_researches_dossiers.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold">
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

    <form action="{{ route('published_researches_dossiers.store') }}" method="POST" id="preparePublishedForm">
        @csrf

        <div class="row g-4">
            <!-- Left: Metadata Card -->
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
                                   value="{{ old('title', 'LIST OF PUBLISHED RESEARCHES - FY ' . date('Y')) }}" 
                                   placeholder="e.g. LIST OF PUBLISHED RESEARCHES - FY 2026" required>
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
                            <label class="form-label fw-bold small text-dark">General Remarks / Notes</label>
                            <textarea name="remarks" class="form-control rounded-3" rows="3" 
                                      placeholder="Optional remarks regarding this batch of published research outputs...">{{ old('remarks') }}</textarea>
                        </div>

                        <!-- Selected Counter & Submit Button -->
                        <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-center justify-content-between">
                            <span class="small fw-semibold text-muted">Selected Outputs:</span>
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
                                    <input type="text" id="tableSearch" class="form-control bg-light border-0 py-2" placeholder="Search by title, author, or journal...">
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
                            <h5 class="fw-bold mb-0 text-dark">Eligible Published Researches</h5>
                            <small class="text-muted">Proposals with logged publications or completed institutional dissemination.</small>
                        </div>
                        <span class="badge bg-light text-muted px-3 py-1.5 rounded-pill">{{ $eligibleProposals->count() }} Available</span>
                    </div>

                    <div class="card-body p-0">
                        @if($eligibleProposals->isEmpty())
                            <div class="p-5 text-center">
                                <div class="p-3 bg-secondary bg-opacity-10 text-secondary rounded-circle d-inline-flex mb-3">
                                    <i class="bi bi-journal-x fs-1"></i>
                                </div>
                                <h6 class="fw-bold text-dark">No Published Researches Found</h6>
                                <p class="text-muted small mx-auto mb-0" style="max-width: 440px;">
                                    There are currently no research proposals in publication stage. Once research publications are recorded under Phase 4, they will appear here.
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
                                            <th>Research Title & Authors</th>
                                            <th>Date Published</th>
                                            <th>Type of Journal / Publication</th>
                                            <th>Name of Publication & Vol.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($eligibleProposals as $proposal)
                                            @php
                                                $leadName = $proposal->user ? $proposal->user->name : 'N/A';
                                                $collabNames = $proposal->collaborators ? $proposal->collaborators->pluck('name')->toArray() : [];
                                                $allAuthors = implode(', ', array_merge([$leadName], $collabNames));

                                                $latestPub = $proposal->researchPublications ? $proposal->researchPublications->last() : null;

                                                $pubDate = date('F Y');
                                                if ($latestPub && $latestPub->updated_at) {
                                                    $pubDate = $latestPub->updated_at->format('F Y');
                                                } elseif ($proposal->updated_at) {
                                                    $pubDate = $proposal->updated_at->format('F Y');
                                                }

                                                $pubType = 'Refereed Journal';
                                                if ($latestPub && !empty($latestPub->indexing_agency)) {
                                                    $pubType = $latestPub->indexing_agency;
                                                }

                                                $pubName = 'University of Antique Research Journal';
                                                if ($latestPub && !empty($latestPub->journal_title)) {
                                                    $pubName = $latestPub->journal_title;
                                                }

                                                $volNo = 'Vol. ' . date('y') . ' No. 1';
                                            @endphp
                                            <tr class="proposal-row" data-college="{{ $proposal->user?->department ?? '' }}" data-campus="{{ $proposal->user?->campus ?? '' }}">
                                                <td class="ps-4 align-top pt-3">
                                                    <input class="form-check-input proposal-checkbox" type="checkbox" name="proposal_ids[]" 
                                                           value="{{ $proposal->id }}" id="chk_{{ $proposal->id }}"
                                                           {{ in_array($proposal->id, old('proposal_ids', [])) ? 'checked' : '' }}>
                                                </td>
                                                <td class="align-top pt-3" style="min-width: 200px;">
                                                    <label for="chk_{{ $proposal->id }}" class="mb-0 cursor-pointer d-block">
                                                        <div class="fw-bold text-dark proposal-title-text">{{ $proposal->title }}</div>
                                                        <div class="small text-muted mt-1">
                                                            <i class="bi bi-people me-1"></i><strong>Author/s:</strong> {{ $allAuthors }}
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
                                                <td class="align-top pt-2" style="width: 140px;">
                                                    <input type="text" name="items[{{ $proposal->id }}][date_published]" 
                                                           class="form-control form-control-sm rounded-2 date-field" 
                                                           value="{{ old("items.{$proposal->id}.date_published", $pubDate) }}" 
                                                           placeholder="e.g. November 2026">
                                                    <small class="text-muted" style="font-size: 0.7rem;">Date Published</small>
                                                </td>
                                                <td class="align-top pt-2" style="min-width: 150px;">
                                                    <input type="text" name="items[{{ $proposal->id }}][type_of_publication]" 
                                                           class="form-control form-control-sm rounded-2 type-field" 
                                                           value="{{ old("items.{$proposal->id}.type_of_publication", $pubType) }}" 
                                                           placeholder="e.g. Scopus / Refereed">
                                                    <small class="text-muted" style="font-size: 0.7rem;">Type of Journal</small>
                                                </td>
                                                <td class="align-top pt-2 pe-4" style="min-width: 180px;">
                                                    <div class="mb-1">
                                                        <input type="text" name="items[{{ $proposal->id }}][name_of_publication]" 
                                                               class="form-control form-control-sm rounded-2 name-field" 
                                                               value="{{ old("items.{$proposal->id}.name_of_publication", $pubName) }}" 
                                                               placeholder="Name of Publication">
                                                    </div>
                                                    <div>
                                                        <input type="text" name="items[{{ $proposal->id }}][volume_no]" 
                                                               class="form-control form-control-sm rounded-2 vol-field" 
                                                               value="{{ old("items.{$proposal->id}.volume_no", $volNo) }}" 
                                                               placeholder="e.g. Vol. 12 No. 2">
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
                    const nameText = (r.querySelector('.name-field')?.value || '').toLowerCase();
                    const collegeAttr = r.getAttribute('data-college') || '';

                    const matchesSearch = !query || titleText.includes(query) || nameText.includes(query);
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
