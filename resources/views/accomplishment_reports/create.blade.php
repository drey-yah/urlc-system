<x-app-layout>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('accomplishment_reports.index') }}" class="text-decoration-none">Accomplishment Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Prepare Report</li>
                </ol>
            </nav>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-file-earmark-bar-graph-fill text-primary"></i> Prepare Research Accomplishment Report
            </h1>
            <p class="text-muted small mb-0">Official University Form (RESU - FM-004) &bull; Quarterly accomplishments across all institutional research targets.</p>
        </div>
        <div>
            <a href="{{ route('accomplishment_reports.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Back to Reports
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

    <form action="{{ route('accomplishment_reports.store') }}" method="POST" id="prepareReportForm">
        @csrf

        <!-- Metadata Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h5 class="fw-bold text-dark mb-1">Institutional Header & Period</h5>
                <p class="text-muted small mb-0">Specify the reporting college, quarter, school year, and submission date.</p>
            </div>
            <div class="card-body px-4 py-3">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold small text-dark">College / Department <span class="text-danger">*</span></label>
                        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                            <select name="college" class="form-select rounded-3" onchange="window.location.href = '{{ route('accomplishment_reports.create') }}?college=' + encodeURIComponent(this.value);">
                                @foreach($colleges as $c)
                                    <option value="{{ $c }}" {{ $college === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" name="college" class="form-control bg-light rounded-3 text-muted fw-bold" 
                                   value="{{ $coordinatorCollege }}" readonly>
                        @endif
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-bold small text-dark">Quarter <span class="text-danger">*</span></label>
                        <select name="quarter" class="form-select rounded-3">
                            <option value="1st Quarter" {{ old('quarter') === '1st Quarter' ? 'selected' : '' }}>1st Quarter</option>
                            <option value="2nd Quarter" {{ old('quarter') === '2nd Quarter' ? 'selected' : '' }}>2nd Quarter</option>
                            <option value="3rd Quarter" {{ old('quarter') === '3rd Quarter' ? 'selected' : '' }}>3rd Quarter</option>
                            <option value="4th Quarter" {{ old('quarter') === '4th Quarter' ? 'selected' : '' }}>4th Quarter</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-bold small text-dark">School Year <span class="text-danger">*</span></label>
                        <input type="text" name="school_year" class="form-control rounded-3" 
                               value="{{ old('school_year', 'SY ' . date('Y') . '-' . (date('Y') + 1)) }}" placeholder="e.g. SY 2026-2027" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-bold small text-dark">Report Date <span class="text-danger">*</span></label>
                        <input type="date" name="report_date" class="form-control rounded-3" 
                               value="{{ old('report_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-bold small text-dark">Fiscal Year</label>
                        <input type="number" name="fiscal_year" class="form-control rounded-3" 
                               value="{{ old('fiscal_year', date('Y')) }}" min="2020" max="2050">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section A: Targets vs. Accomplishment (Page 1) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold text-dark mb-0">A. Targets vs. Accomplishment</h5>
                    <small class="text-muted">Standard institutional research performance indicators matching RESU - FM-004.</small>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold">Page 1</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light text-center">
                            <tr>
                                <th rowspan="2" class="align-middle text-start ps-3" style="width: 38%;">PERFORMANCE INDICATORS</th>
                                <th colspan="4" style="background-color: #f8fafc;">TARGETS</th>
                                <th colspan="4" style="background-color: #f1f5f9;">ACCOMPLISHMENTS</th>
                                <th rowspan="2" class="align-middle" style="width: 20%;">REMARKS</th>
                            </tr>
                            <tr>
                                <th style="width: 5%;">Q1</th>
                                <th style="width: 5%;">Q2</th>
                                <th style="width: 5%;">Q3</th>
                                <th style="width: 5%;">Q4</th>
                                <th style="width: 5%;">Q1</th>
                                <th style="width: 5%;">Q2</th>
                                <th style="width: 5%;">Q3</th>
                                <th style="width: 5%;">Q4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($indicators as $idx => $desc)
                                <tr>
                                    <td class="ps-3 py-2 fw-medium text-dark">
                                        <input type="hidden" name="section_a[{{ $idx }}][indicator]" value="{{ $desc }}">
                                        {{ $idx }}. {{ $desc }}
                                    </td>
                                    <td><input type="text" name="section_a[{{ $idx }}][t_q1]" class="form-control form-control-sm text-center p-1" value="{{ old("section_a.{$idx}.t_q1", '0') }}"></td>
                                    <td><input type="text" name="section_a[{{ $idx }}][t_q2]" class="form-control form-control-sm text-center p-1" value="{{ old("section_a.{$idx}.t_q2", '0') }}"></td>
                                    <td><input type="text" name="section_a[{{ $idx }}][t_q3]" class="form-control form-control-sm text-center p-1" value="{{ old("section_a.{$idx}.t_q3", '0') }}"></td>
                                    <td><input type="text" name="section_a[{{ $idx }}][t_q4]" class="form-control form-control-sm text-center p-1" value="{{ old("section_a.{$idx}.t_q4", '0') }}"></td>

                                    <td><input type="text" name="section_a[{{ $idx }}][a_q1]" class="form-control form-control-sm text-center p-1" value="{{ old("section_a.{$idx}.a_q1", $counts[$idx] ?? '0') }}"></td>
                                    <td><input type="text" name="section_a[{{ $idx }}][a_q2]" class="form-control form-control-sm text-center p-1" value="{{ old("section_a.{$idx}.a_q2", '0') }}"></td>
                                    <td><input type="text" name="section_a[{{ $idx }}][a_q3]" class="form-control form-control-sm text-center p-1" value="{{ old("section_a.{$idx}.a_q3", '0') }}"></td>
                                    <td><input type="text" name="section_a[{{ $idx }}][a_q4]" class="form-control form-control-sm text-center p-1" value="{{ old("section_a.{$idx}.a_q4", '0') }}"></td>

                                    <td><input type="text" name="section_a[{{ $idx }}][remarks]" class="form-control form-control-sm p-1" value="{{ old("section_a.{$idx}.remarks") }}" placeholder="Remarks"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Section B & C & D (Page 2) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold text-dark mb-0">B. Research Activities & C/D Researches</h5>
                    <small class="text-muted">Activities conducted, activities attended, ongoing researches, and completed researches.</small>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold">Page 2</span>
            </div>
            <div class="card-body px-4 py-3">
                <!-- B.1 Activities Conducted/Sponsored -->
                <h6 class="fw-bold text-dark mb-2">B.1 Activities Conducted/Sponsored</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle" id="tableB1">
                        <thead class="table-light">
                            <tr>
                                <th>Title of Activity/ies</th>
                                <th style="width: 18%;">Date</th>
                                <th style="width: 25%;">Venue</th>
                                <th style="width: 25%;">Persons Involved</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="section_b[b1][0][title]" class="form-control form-control-sm" placeholder="e.g. College In-House Research Review 2026"></td>
                                <td><input type="text" name="section_b[b1][0][date]" class="form-control form-control-sm" placeholder="e.g. October 15, 2026"></td>
                                <td><input type="text" name="section_b[b1][0][venue]" class="form-control form-control-sm" placeholder="e.g. CTE Audio-Visual Hall"></td>
                                <td><input type="text" name="section_b[b1][0][persons]" class="form-control form-control-sm" placeholder="e.g. Faculty Researchers & Evaluators"></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addRowB1()">
                        <i class="bi bi-plus-circle me-1"></i> Add Activity Conducted
                    </button>
                </div>

                <!-- B.2 Activities Attended -->
                <h6 class="fw-bold text-dark mb-2">B.2 Activities Attended</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle" id="tableB2">
                        <thead class="table-light">
                            <tr>
                                <th>Title of Activity/ies</th>
                                <th style="width: 15%;">Type of Activity</th>
                                <th style="width: 14%;">Date</th>
                                <th style="width: 18%;">Venue</th>
                                <th style="width: 18%;">Name of Person Involved</th>
                                <th style="width: 16%;">Sponsoring Agency</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="section_b[b2][0][title]" class="form-control form-control-sm" placeholder="e.g. Regional Research Methodology Training"></td>
                                <td>
                                    <select name="section_b[b2][0][type]" class="form-select form-select-sm">
                                        <option value="Training">Training</option>
                                        <option value="Meeting">Meeting</option>
                                        <option value="Conferences/Fora">Conferences/Fora</option>
                                    </select>
                                </td>
                                <td><input type="text" name="section_b[b2][0][date]" class="form-control form-control-sm" placeholder="e.g. September 10, 2026"></td>
                                <td><input type="text" name="section_b[b2][0][venue]" class="form-control form-control-sm" placeholder="e.g. Iloilo City"></td>
                                <td><input type="text" name="section_b[b2][0][person]" class="form-control form-control-sm" placeholder="e.g. Prof. Ernesto Cruz"></td>
                                <td><input type="text" name="section_b[b2][0][sponsor]" class="form-control form-control-sm" placeholder="e.g. CHED / PASUC VI"></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addRowB2()">
                        <i class="bi bi-plus-circle me-1"></i> Add Activity Attended
                    </button>
                </div>

                <!-- C. On-Going Researches -->
                <h6 class="fw-bold text-dark mb-2">C. On-Going Researches</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle" id="tableC">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th style="width: 22%;">Proponent</th>
                                <th style="width: 16%;">College</th>
                                <th style="width: 14%;">Time Frame</th>
                                <th style="width: 16%;">Date of In-house Review</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $cIdx = 0; @endphp
                            @forelse($ongoingProposals as $prop)
                                <tr>
                                    <td><input type="text" name="section_c[{{ $cIdx }}][title]" class="form-control form-control-sm" value="{{ $prop->title }}"></td>
                                    <td><input type="text" name="section_c[{{ $cIdx }}][proponent]" class="form-control form-control-sm" value="{{ $prop->user?->name }}"></td>
                                    <td><input type="text" name="section_c[{{ $cIdx }}][college]" class="form-control form-control-sm" value="{{ $college }}"></td>
                                    <td><input type="text" name="section_c[{{ $cIdx }}][time_frame]" class="form-control form-control-sm" value="12 Months"></td>
                                    <td><input type="text" name="section_c[{{ $cIdx }}][review_date]" class="form-control form-control-sm" value="{{ $prop->created_at ? $prop->created_at->format('M d, Y') : date('M d, Y') }}"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
                                </tr>
                                @php $cIdx++; @endphp
                            @empty
                                <tr>
                                    <td><input type="text" name="section_c[0][title]" class="form-control form-control-sm" placeholder="Title of On-going Research"></td>
                                    <td><input type="text" name="section_c[0][proponent]" class="form-control form-control-sm" placeholder="Proponent Name"></td>
                                    <td><input type="text" name="section_c[0][college]" class="form-control form-control-sm" value="{{ $college }}"></td>
                                    <td><input type="text" name="section_c[0][time_frame]" class="form-control form-control-sm" placeholder="e.g. 12 Months"></td>
                                    <td><input type="text" name="section_c[0][review_date]" class="form-control form-control-sm" placeholder="Date of Review"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addRowC()">
                        <i class="bi bi-plus-circle me-1"></i> Add Ongoing Research Row
                    </button>
                </div>

                <!-- D. Completed Researches -->
                <h6 class="fw-bold text-dark mb-2">D. Completed Researches</h6>
                <div class="table-responsive mb-2">
                    <table class="table table-bordered table-sm align-middle" id="tableD">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th style="width: 22%;">Proponent</th>
                                <th style="width: 16%;">College</th>
                                <th style="width: 14%;">Date Started</th>
                                <th style="width: 16%;">Date Completed</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $dIdx = 0; @endphp
                            @forelse($completedProposals as $cProp)
                                <tr>
                                    <td><input type="text" name="section_d[{{ $dIdx }}][title]" class="form-control form-control-sm" value="{{ $cProp->title }}"></td>
                                    <td><input type="text" name="section_d[{{ $dIdx }}][proponent]" class="form-control form-control-sm" value="{{ $cProp->user?->name }}"></td>
                                    <td><input type="text" name="section_d[{{ $dIdx }}][college]" class="form-control form-control-sm" value="{{ $college }}"></td>
                                    <td><input type="text" name="section_d[{{ $dIdx }}][date_started]" class="form-control form-control-sm" value="{{ $cProp->created_at ? $cProp->created_at->format('M d, Y') : date('M d, Y') }}"></td>
                                    <td><input type="text" name="section_d[{{ $dIdx }}][date_completed]" class="form-control form-control-sm" value="{{ $cProp->terminalReport?->updated_at ? $cProp->terminalReport->updated_at->format('M d, Y') : date('M d, Y') }}"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
                                </tr>
                                @php $dIdx++; @endphp
                            @empty
                                <tr>
                                    <td><input type="text" name="section_d[0][title]" class="form-control form-control-sm" placeholder="Title of Completed Research"></td>
                                    <td><input type="text" name="section_d[0][proponent]" class="form-control form-control-sm" placeholder="Proponent Name"></td>
                                    <td><input type="text" name="section_d[0][college]" class="form-control form-control-sm" value="{{ $college }}"></td>
                                    <td><input type="text" name="section_d[0][date_started]" class="form-control form-control-sm" placeholder="Date Started"></td>
                                    <td><input type="text" name="section_d[0][date_completed]" class="form-control form-control-sm" placeholder="Date Completed"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addRowD()">
                        <i class="bi bi-plus-circle me-1"></i> Add Completed Research Row
                    </button>
                </div>
                <small class="text-muted fst-italic">*Note: Ensure Terminal Reports and Completion Certificates are verified.</small>
            </div>
        </div>

        <!-- Section E & F: Presentation & Publication (Page 3) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold text-dark mb-0">E. Research Presentation & F. Publication</h5>
                    <small class="text-muted">Conferences disseminated, journal publications, volume numbers, and indexing details.</small>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold">Page 3</span>
            </div>
            <div class="card-body px-4 py-3">
                <!-- E. Research Presentation -->
                <h6 class="fw-bold text-dark mb-2">E. Research Presentation</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle" id="tableE" style="font-size: 0.8rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Delivery Unit</th>
                                <th>Type of Research</th>
                                <th>Title of Research</th>
                                <th>Duration</th>
                                <th>Started/Completed</th>
                                <th>Date Presented</th>
                                <th>Conf Type</th>
                                <th>Venue</th>
                                <th>Conference Name</th>
                                <th>Author/s</th>
                                <th>Remarks</th>
                                <th style="width: 35px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $eIdx = 0; @endphp
                            @forelse($presentations as $pres)
                                <tr>
                                    <td><input type="text" name="section_e[{{ $eIdx }}][unit]" class="form-control form-control-sm" value="{{ $college }}"></td>
                                    <td><input type="text" name="section_e[{{ $eIdx }}][type]" class="form-control form-control-sm" value="Study"></td>
                                    <td><input type="text" name="section_e[{{ $eIdx }}][title]" class="form-control form-control-sm" value="{{ $pres->presentation_title ?? $pres->proposal?->title }}"></td>
                                    <td><input type="text" name="section_e[{{ $eIdx }}][duration]" class="form-control form-control-sm" value="12 Mos"></td>
                                    <td><input type="text" name="section_e[{{ $eIdx }}][dates]" class="form-control form-control-sm" value="2026"></td>
                                    <td><input type="text" name="section_e[{{ $eIdx }}][date_presented]" class="form-control form-control-sm" value="{{ $pres->event_date ? $pres->event_date->format('M d, Y') : date('M d, Y') }}"></td>
                                    <td>
                                        <select name="section_e[{{ $eIdx }}][conf_type]" class="form-select form-select-sm">
                                            <option value="1-Institutional">1-Institutional</option>
                                            <option value="2-Local" {{ strtolower($pres->presentation_type ?? '') === 'local' ? 'selected' : '' }}>2-Local</option>
                                            <option value="3-National" {{ strtolower($pres->presentation_type ?? '') === 'national' ? 'selected' : '' }}>3-National</option>
                                            <option value="4-International" {{ strtolower($pres->presentation_type ?? '') === 'international' ? 'selected' : '' }}>4-International</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="section_e[{{ $eIdx }}][venue]" class="form-control form-control-sm" value="{{ $pres->venue ?? 'Iloilo City' }}"></td>
                                    <td><input type="text" name="section_e[{{ $eIdx }}][conf_title]" class="form-control form-control-sm" value="{{ $pres->conference_name ?? 'Research Colloquium' }}"></td>
                                    <td><input type="text" name="section_e[{{ $eIdx }}][authors]" class="form-control form-control-sm" value="{{ $pres->user?->name ?? 'N/A' }}"></td>
                                    <td><input type="text" name="section_e[{{ $eIdx }}][remarks]" class="form-control form-control-sm" placeholder="Remarks"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
                                </tr>
                                @php $eIdx++; @endphp
                            @empty
                                <tr>
                                    <td><input type="text" name="section_e[0][unit]" class="form-control form-control-sm" value="{{ $college }}"></td>
                                    <td><input type="text" name="section_e[0][type]" class="form-control form-control-sm" placeholder="Project/Study/Article"></td>
                                    <td><input type="text" name="section_e[0][title]" class="form-control form-control-sm" placeholder="Research Title"></td>
                                    <td><input type="text" name="section_e[0][duration]" class="form-control form-control-sm" placeholder="Duration"></td>
                                    <td><input type="text" name="section_e[0][dates]" class="form-control form-control-sm" placeholder="Mo & Yr Started/Completed"></td>
                                    <td><input type="text" name="section_e[0][date_presented]" class="form-control form-control-sm" placeholder="Date Presented"></td>
                                    <td>
                                        <select name="section_e[0][conf_type]" class="form-select form-select-sm">
                                            <option value="1-Institutional">1-Institutional</option>
                                            <option value="2-Local">2-Local</option>
                                            <option value="3-National">3-National</option>
                                            <option value="4-International">4-International</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="section_e[0][venue]" class="form-control form-control-sm" placeholder="Venue"></td>
                                    <td><input type="text" name="section_e[0][conf_title]" class="form-control form-control-sm" placeholder="Title of Conference"></td>
                                    <td><input type="text" name="section_e[0][authors]" class="form-control form-control-sm" placeholder="Author/s"></td>
                                    <td><input type="text" name="section_e[0][remarks]" class="form-control form-control-sm" placeholder="Remarks"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addRowE()">
                        <i class="bi bi-plus-circle me-1"></i> Add Presentation Row
                    </button>
                </div>

                <!-- F. Publication -->
                <h6 class="fw-bold text-dark mb-2">F. Publication</h6>
                <div class="table-responsive mb-2">
                    <table class="table table-bordered table-sm align-middle" id="tableF" style="font-size: 0.8rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Delivery Unit</th>
                                <th>Research Title</th>
                                <th>Duration</th>
                                <th>Date Started</th>
                                <th>Date Completed</th>
                                <th>Date Published</th>
                                <th>Type of Journal</th>
                                <th>Name of Publication</th>
                                <th>Vol. No.</th>
                                <th>Author/s</th>
                                <th>Remarks</th>
                                <th style="width: 35px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $fIdx = 0; @endphp
                            @forelse($publications as $pub)
                                <tr>
                                    <td><input type="text" name="section_f[{{ $fIdx }}][unit]" class="form-control form-control-sm" value="{{ $college }}"></td>
                                    <td><input type="text" name="section_f[{{ $fIdx }}][title]" class="form-control form-control-sm" value="{{ $pub->paper_title ?? $pub->proposal?->title }}"></td>
                                    <td><input type="text" name="section_f[{{ $fIdx }}][duration]" class="form-control form-control-sm" value="12 Mos"></td>
                                    <td><input type="text" name="section_f[{{ $fIdx }}][date_started]" class="form-control form-control-sm" value="2026"></td>
                                    <td><input type="text" name="section_f[{{ $fIdx }}][date_completed]" class="form-control form-control-sm" value="2026"></td>
                                    <td><input type="text" name="section_f[{{ $fIdx }}][date_published]" class="form-control form-control-sm" value="{{ $pub->publication_date ? $pub->publication_date->format('M d, Y') : date('M d, Y') }}"></td>
                                    <td><input type="text" name="section_f[{{ $fIdx }}][journal_type]" class="form-control form-control-sm" value="{{ $pub->indexing ?? 'CHED Recognized' }}"></td>
                                    <td><input type="text" name="section_f[{{ $fIdx }}][pub_name]" class="form-control form-control-sm" value="{{ $pub->journal_name ?? 'International Journal' }}"></td>
                                    <td><input type="text" name="section_f[{{ $fIdx }}][vol_no]" class="form-control form-control-sm" value="{{ $pub->volume_number ?? 'Vol. 1 No. 2' }}"></td>
                                    <td><input type="text" name="section_f[{{ $fIdx }}][authors]" class="form-control form-control-sm" value="{{ $pub->authors ?? ($pub->user?->name ?? 'N/A') }}"></td>
                                    <td><input type="text" name="section_f[{{ $fIdx }}][remarks]" class="form-control form-control-sm" placeholder="Remarks"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
                                </tr>
                                @php $fIdx++; @endphp
                            @empty
                                <tr>
                                    <td><input type="text" name="section_f[0][unit]" class="form-control form-control-sm" value="{{ $college }}"></td>
                                    <td><input type="text" name="section_f[0][title]" class="form-control form-control-sm" placeholder="Research Title"></td>
                                    <td><input type="text" name="section_f[0][duration]" class="form-control form-control-sm" placeholder="Duration"></td>
                                    <td><input type="text" name="section_f[0][date_started]" class="form-control form-control-sm" placeholder="Started"></td>
                                    <td><input type="text" name="section_f[0][date_completed]" class="form-control form-control-sm" placeholder="Completed"></td>
                                    <td><input type="text" name="section_f[0][date_published]" class="form-control form-control-sm" placeholder="Published"></td>
                                    <td><input type="text" name="section_f[0][journal_type]" class="form-control form-control-sm" placeholder="Type"></td>
                                    <td><input type="text" name="section_f[0][pub_name]" class="form-control form-control-sm" placeholder="Journal Name"></td>
                                    <td><input type="text" name="section_f[0][vol_no]" class="form-control form-control-sm" placeholder="Vol No."></td>
                                    <td><input type="text" name="section_f[0][authors]" class="form-control form-control-sm" placeholder="Author/s"></td>
                                    <td><input type="text" name="section_f[0][remarks]" class="form-control form-control-sm" placeholder="Remarks"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addRowF()">
                        <i class="bi bi-plus-circle me-1"></i> Add Publication Row
                    </button>
                </div>
            </div>
        </div>

        <!-- Section G & H & Signatures (Page 4) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold text-dark mb-0">G. Utilization & H. Evidences / Signatures</h5>
                    <small class="text-muted">Technology adoption, patents, documentation pictures, and official signatory flow.</small>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold">Page 4</span>
            </div>
            <div class="card-body px-4 py-3">
                <!-- G. Utilization -->
                <h6 class="fw-bold text-dark mb-2">G. Utilization</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle" id="tableG">
                        <thead class="table-light">
                            <tr>
                                <th>Product Name/Methods/Process/Technology</th>
                                <th style="width: 25%;">Patent Number/Product Description</th>
                                <th style="width: 25%;">Benefiting Industry/Community</th>
                                <th style="width: 20%;">Date Started and Completed</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="section_g[0][product]" class="form-control form-control-sm" placeholder="e.g. Bamboo Processing Technology"></td>
                                <td><input type="text" name="section_g[0][patent]" class="form-control form-control-sm" placeholder="e.g. Utility Model #2026-0012"></td>
                                <td><input type="text" name="section_g[0][beneficiary]" class="form-control form-control-sm" placeholder="e.g. Sibalom Bamboo Crafters Cooperative"></td>
                                <td><input type="text" name="section_g[0][dates]" class="form-control form-control-sm" placeholder="e.g. Jan 2025 - Dec 2025"></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addRowG()">
                        <i class="bi bi-plus-circle me-1"></i> Add Utilization Row
                    </button>
                </div>

                <!-- H. Evidences/Picture of Activities -->
                <h6 class="fw-bold text-dark mb-2">H. Evidences/Picture of Activities</h6>
                <div class="row g-2 mb-4">
                    <div class="col-12 col-md-4">
                        <input type="text" name="section_h[title]" class="form-control form-control-sm" placeholder="Activity Title (e.g. Research Colloquium)">
                    </div>
                    <div class="col-12 col-md-4">
                        <input type="text" name="section_h[venue]" class="form-control form-control-sm" placeholder="Venue">
                    </div>
                    <div class="col-12 col-md-4">
                        <input type="text" name="section_h[date]" class="form-control form-control-sm" placeholder="Date">
                    </div>
                    <div class="col-12 mt-2">
                        <textarea name="section_h[pictures_note]" class="form-control rounded-3" rows="2" placeholder="Documentation / Photo Documentation Notes / References..."></textarea>
                    </div>
                </div>

                <!-- Signatories Overview Banner -->
                <div class="p-3 bg-light rounded-4 border mb-4">
                    <h6 class="fw-bold text-dark mb-2">Official Signatory Hierarchy (RESU - FM-004)</h6>
                    <div class="row g-3 small">
                        <div class="col-12 col-md-4">
                            <span class="text-muted d-block">Submitted by:</span>
                            <strong class="text-dark">{{ auth()->user()->name }}</strong>
                            <div class="text-muted">College Research Coordinator</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <span class="text-muted d-block">Noted by:</span>
                            <strong class="text-dark">College Dean ({{ $college }})</strong>
                            <div class="text-muted">Forwards to Director upon noting</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <span class="text-muted d-block">Submitted to:</span>
                            <strong class="text-dark">Director, Research Unit</strong>
                            <div class="text-muted">Receives official institutional report</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2">
                    <a href="{{ route('accomplishment_reports.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-send-fill"></i> Submit Accomplishment Report to Dean
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        function addRowB1() {
            const table = document.getElementById('tableB1').getElementsByTagName('tbody')[0];
            const idx = table.rows.length;
            const row = table.insertRow();
            row.innerHTML = `
                <td><input type="text" name="section_b[b1][${idx}][title]" class="form-control form-control-sm" placeholder="Title of Activity"></td>
                <td><input type="text" name="section_b[b1][${idx}][date]" class="form-control form-control-sm" placeholder="Date"></td>
                <td><input type="text" name="section_b[b1][${idx}][venue]" class="form-control form-control-sm" placeholder="Venue"></td>
                <td><input type="text" name="section_b[b1][${idx}][persons]" class="form-control form-control-sm" placeholder="Persons Involved"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
            `;
        }

        function addRowB2() {
            const table = document.getElementById('tableB2').getElementsByTagName('tbody')[0];
            const idx = table.rows.length;
            const row = table.insertRow();
            row.innerHTML = `
                <td><input type="text" name="section_b[b2][${idx}][title]" class="form-control form-control-sm" placeholder="Title of Activity"></td>
                <td>
                    <select name="section_b[b2][${idx}][type]" class="form-select form-select-sm">
                        <option value="Training">Training</option>
                        <option value="Meeting">Meeting</option>
                        <option value="Conferences/Fora">Conferences/Fora</option>
                    </select>
                </td>
                <td><input type="text" name="section_b[b2][${idx}][date]" class="form-control form-control-sm" placeholder="Date"></td>
                <td><input type="text" name="section_b[b2][${idx}][venue]" class="form-control form-control-sm" placeholder="Venue"></td>
                <td><input type="text" name="section_b[b2][${idx}][person]" class="form-control form-control-sm" placeholder="Person Involved"></td>
                <td><input type="text" name="section_b[b2][${idx}][sponsor]" class="form-control form-control-sm" placeholder="Sponsor"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
            `;
        }

        function addRowC() {
            const table = document.getElementById('tableC').getElementsByTagName('tbody')[0];
            const idx = table.rows.length;
            const row = table.insertRow();
            row.innerHTML = `
                <td><input type="text" name="section_c[${idx}][title]" class="form-control form-control-sm" placeholder="Title"></td>
                <td><input type="text" name="section_c[${idx}][proponent]" class="form-control form-control-sm" placeholder="Proponent"></td>
                <td><input type="text" name="section_c[${idx}][college]" class="form-control form-control-sm" value="{{ $college }}"></td>
                <td><input type="text" name="section_c[${idx}][time_frame]" class="form-control form-control-sm" placeholder="Time Frame"></td>
                <td><input type="text" name="section_c[${idx}][review_date]" class="form-control form-control-sm" placeholder="Review Date"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
            `;
        }

        function addRowD() {
            const table = document.getElementById('tableD').getElementsByTagName('tbody')[0];
            const idx = table.rows.length;
            const row = table.insertRow();
            row.innerHTML = `
                <td><input type="text" name="section_d[${idx}][title]" class="form-control form-control-sm" placeholder="Title"></td>
                <td><input type="text" name="section_d[${idx}][proponent]" class="form-control form-control-sm" placeholder="Proponent"></td>
                <td><input type="text" name="section_d[${idx}][college]" class="form-control form-control-sm" value="{{ $college }}"></td>
                <td><input type="text" name="section_d[${idx}][date_started]" class="form-control form-control-sm" placeholder="Date Started"></td>
                <td><input type="text" name="section_d[${idx}][date_completed]" class="form-control form-control-sm" placeholder="Date Completed"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
            `;
        }

        function addRowE() {
            const table = document.getElementById('tableE').getElementsByTagName('tbody')[0];
            const idx = table.rows.length;
            const row = table.insertRow();
            row.innerHTML = `
                <td><input type="text" name="section_e[${idx}][unit]" class="form-control form-control-sm" value="{{ $college }}"></td>
                <td><input type="text" name="section_e[${idx}][type]" class="form-control form-control-sm" placeholder="Project/Study/Article"></td>
                <td><input type="text" name="section_e[${idx}][title]" class="form-control form-control-sm" placeholder="Title"></td>
                <td><input type="text" name="section_e[${idx}][duration]" class="form-control form-control-sm" placeholder="Duration"></td>
                <td><input type="text" name="section_e[${idx}][dates]" class="form-control form-control-sm" placeholder="Started/Completed"></td>
                <td><input type="text" name="section_e[${idx}][date_presented]" class="form-control form-control-sm" placeholder="Date Presented"></td>
                <td>
                    <select name="section_e[${idx}][conf_type]" class="form-select form-select-sm">
                        <option value="1-Institutional">1-Institutional</option>
                        <option value="2-Local">2-Local</option>
                        <option value="3-National">3-National</option>
                        <option value="4-International">4-International</option>
                    </select>
                </td>
                <td><input type="text" name="section_e[${idx}][venue]" class="form-control form-control-sm" placeholder="Venue"></td>
                <td><input type="text" name="section_e[${idx}][conf_title]" class="form-control form-control-sm" placeholder="Conference Title"></td>
                <td><input type="text" name="section_e[${idx}][authors]" class="form-control form-control-sm" placeholder="Author/s"></td>
                <td><input type="text" name="section_e[${idx}][remarks]" class="form-control form-control-sm" placeholder="Remarks"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
            `;
        }

        function addRowF() {
            const table = document.getElementById('tableF').getElementsByTagName('tbody')[0];
            const idx = table.rows.length;
            const row = table.insertRow();
            row.innerHTML = `
                <td><input type="text" name="section_f[${idx}][unit]" class="form-control form-control-sm" value="{{ $college }}"></td>
                <td><input type="text" name="section_f[${idx}][title]" class="form-control form-control-sm" placeholder="Research Title"></td>
                <td><input type="text" name="section_f[${idx}][duration]" class="form-control form-control-sm" placeholder="Duration"></td>
                <td><input type="text" name="section_f[${idx}][date_started]" class="form-control form-control-sm" placeholder="Started"></td>
                <td><input type="text" name="section_f[${idx}][date_completed]" class="form-control form-control-sm" placeholder="Completed"></td>
                <td><input type="text" name="section_f[${idx}][date_published]" class="form-control form-control-sm" placeholder="Published"></td>
                <td><input type="text" name="section_f[${idx}][journal_type]" class="form-control form-control-sm" placeholder="Type"></td>
                <td><input type="text" name="section_f[${idx}][pub_name]" class="form-control form-control-sm" placeholder="Journal Name"></td>
                <td><input type="text" name="section_f[${idx}][vol_no]" class="form-control form-control-sm" placeholder="Vol No."></td>
                <td><input type="text" name="section_f[${idx}][authors]" class="form-control form-control-sm" placeholder="Author/s"></td>
                <td><input type="text" name="section_f[${idx}][remarks]" class="form-control form-control-sm" placeholder="Remarks"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
            `;
        }

        function addRowG() {
            const table = document.getElementById('tableG').getElementsByTagName('tbody')[0];
            const idx = table.rows.length;
            const row = table.insertRow();
            row.innerHTML = `
                <td><input type="text" name="section_g[${idx}][product]" class="form-control form-control-sm" placeholder="Product/Method/Technology"></td>
                <td><input type="text" name="section_g[${idx}][patent]" class="form-control form-control-sm" placeholder="Patent/Description"></td>
                <td><input type="text" name="section_g[${idx}][beneficiary]" class="form-control form-control-sm" placeholder="Beneficiary Community"></td>
                <td><input type="text" name="section_g[${idx}][dates]" class="form-control form-control-sm" placeholder="Date Started & Completed"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="this.closest('tr').remove();"><i class="bi bi-trash"></i></button></td>
            `;
        }
    </script>
</x-app-layout>
