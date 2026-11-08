<x-app-layout>
    <!-- Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('accomplishment_reports.index') }}" class="text-decoration-none">Accomplishment Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Report #{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h1 class="h4 h-md-3 fw-bold text-dark mb-0">{{ $report->college }} &bull; {{ $report->quarter }} ({{ $report->school_year }})</h1>
                @if($report->status === 'acknowledged')
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-patch-check-fill me-1"></i> Acknowledged by Research Director
                    </span>
                @elseif($report->status === 'submitted_to_director')
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-send-check me-1"></i> Noted by Dean &bull; Transmitted to Director
                    </span>
                @else
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-clock-history me-1"></i> Awaiting Dean Noting
                    </span>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('accomplishment_reports.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Back to Reports
            </a>

            <a href="{{ route('accomplishment_reports.pdf', $report->id) }}" class="btn btn-primary rounded-pill px-3 py-2 small fw-bold shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download Official 4-Page Landscape PDF
            </a>

            <!-- College Dean Action: Note & Submit to Director -->
            @if(in_array(auth()->user()->role, ['dean', 'super_admin']) && $report->status === 'pending_dean_noting')
                <button type="button" class="btn btn-success rounded-pill px-3 py-2 small fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#deanNoteModal">
                    <i class="bi bi-send-check-fill me-1"></i> Note & Submit to Director
                </button>
            @endif

            <!-- Research Director Action: Acknowledge & Receive -->
            @if(in_array(auth()->user()->role, ['admin', 'super_admin']) && $report->status === 'submitted_to_director')
                <button type="button" class="btn btn-success rounded-pill px-3 py-2 small fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#directorAckModal">
                    <i class="bi bi-check2-circle me-1"></i> Acknowledge & Receive Report
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Document Paper Preview Container (Landscape proportions) -->
    <div class="mx-auto mb-5" style="max-width: 1100px;">
        
        <!-- PAGE 1 OF 4 PREVIEW -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4 p-md-5 text-dark" style="font-family: Arial, sans-serif;">
            <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
                <span>{{ $report->form_code }}</span>
                <span class="fw-bold">Page | 1</span>
            </div>

            <!-- Institutional Header -->
            <div class="text-center mb-3">
                <img src="{{ asset('logo.png') }}" alt="UA Seal" style="width: 70px; height: 70px; object-fit: contain;" class="mb-2">
                <div class="small text-muted mb-0">Republic of the Philippines</div>
                <div class="fw-bold text-uppercase fs-5" style="letter-spacing: 0.5px;">UNIVERSITY OF ANTIQUE</div>
                <div class="small text-muted">Sibalom, Antique</div>
                <div class="fw-bold text-dark mt-2" style="font-size: 0.95rem;">College (<u>{{ $report->college }}</u>)</div>
                <h5 class="fw-bold text-dark text-uppercase mt-1 mb-0" style="letter-spacing: 0.3px;">RESEARCH ACCOMPLISHMENT REPORT</h5>
                <div class="text-dark small fw-semibold mt-1"><u>{{ $report->quarter }}</u>, <u>{{ $report->school_year }}</u></div>
            </div>

            <!-- Section A Table -->
            <div class="mt-4">
                <div class="fw-bold mb-2 small text-dark">A. Targets vs. Accomplishment</div>
                <div class="table-responsive">
                    <table class="table table-bordered border-dark table-sm align-middle mb-0" style="font-size: 0.78rem;">
                        <thead class="bg-light text-center fw-bold">
                            <tr>
                                <th rowspan="2" class="text-start ps-2" style="width: 38%; vertical-align: middle;">PERFORMANCE INDICATORS</th>
                                <th colspan="4">TARGETS</th>
                                <th colspan="4">ACCOMPLISHMENTS</th>
                                <th rowspan="2" style="width: 18%; vertical-align: middle;">REMARKS</th>
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
                            @php
                                $secA = $report->section_a ?? [];
                            @endphp
                            @foreach($secA as $idx => $row)
                                <tr>
                                    <td class="ps-2 py-1 fw-medium">{{ $idx }}. {{ $row['indicator'] ?? '' }}</td>
                                    <td class="text-center">{{ $row['t_q1'] ?? '0' }}</td>
                                    <td class="text-center">{{ $row['t_q2'] ?? '0' }}</td>
                                    <td class="text-center">{{ $row['t_q3'] ?? '0' }}</td>
                                    <td class="text-center">{{ $row['t_q4'] ?? '0' }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $row['a_q1'] ?? '0' }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $row['a_q2'] ?? '0' }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $row['a_q3'] ?? '0' }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $row['a_q4'] ?? '0' }}</td>
                                    <td class="small">{{ $row['remarks'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center small text-muted mt-4 pt-3 border-top">
                <span>{{ $report->form_code }}</span>
                <span>{{ $report->revision_no }}</span>
            </div>
        </div>

        <!-- PAGE 2 OF 4 PREVIEW -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4 p-md-5 text-dark" style="font-family: Arial, sans-serif;">
            <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
                <span>{{ $report->form_code }}</span>
                <span class="fw-bold">Page | 2</span>
            </div>

            <!-- B. Research Activities -->
            <div class="fw-bold small text-dark mb-2">B. RESEARCH ACTIVITIES</div>
            <div class="small fw-semibold mb-1">B.1 Activities Conducted/Sponsored</div>
            <div class="table-responsive mb-3">
                <table class="table table-bordered border-dark table-sm align-middle mb-0" style="font-size: 0.78rem;">
                    <thead class="bg-light text-center fw-bold">
                        <tr>
                            <th style="width: 35%;">Title of Activity/ies</th>
                            <th style="width: 18%;">Date</th>
                            <th style="width: 25%;">Venue</th>
                            <th style="width: 22%;">Persons Involved</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report->section_b['b1'] ?? [] as $b1)
                            <tr>
                                <td>{{ $b1['title'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $b1['date'] ?? 'N/A' }}</td>
                                <td>{{ $b1['venue'] ?? 'N/A' }}</td>
                                <td>{{ $b1['persons'] ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-2">No activities conducted recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="small fw-semibold mb-1">B.2 Activities Attended</div>
            <div class="table-responsive mb-4">
                <table class="table table-bordered border-dark table-sm align-middle mb-0" style="font-size: 0.78rem;">
                    <thead class="bg-light text-center fw-bold">
                        <tr>
                            <th style="width: 30%;">Title of Activity/ies</th>
                            <th style="width: 15%;">Type of Activity</th>
                            <th style="width: 13%;">Date</th>
                            <th style="width: 15%;">Venue</th>
                            <th style="width: 14%;">Name of Person Involved</th>
                            <th style="width: 13%;">Sponsoring Agency</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report->section_b['b2'] ?? [] as $b2)
                            <tr>
                                <td>{{ $b2['title'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $b2['type'] ?? 'Training' }}</td>
                                <td class="text-center">{{ $b2['date'] ?? 'N/A' }}</td>
                                <td>{{ $b2['venue'] ?? 'N/A' }}</td>
                                <td>{{ $b2['person'] ?? 'N/A' }}</td>
                                <td>{{ $b2['sponsor'] ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-2">No activities attended recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- C. On-Going Researches -->
            <div class="fw-bold small text-dark mb-1">C. On-Going Researches</div>
            <div class="table-responsive mb-4">
                <table class="table table-bordered border-dark table-sm align-middle mb-0" style="font-size: 0.78rem;">
                    <thead class="bg-light text-center fw-bold">
                        <tr>
                            <th style="width: 35%;">Title</th>
                            <th style="width: 22%;">Proponent</th>
                            <th style="width: 15%;">College</th>
                            <th style="width: 13%;">Time Frame</th>
                            <th style="width: 15%;">Date of In-house Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report->section_c ?? [] as $cRow)
                            <tr>
                                <td>{{ $cRow['title'] ?? 'N/A' }}</td>
                                <td>{{ $cRow['proponent'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $cRow['college'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $cRow['time_frame'] ?? '12 Months' }}</td>
                                <td class="text-center">{{ $cRow['review_date'] ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-2">No ongoing researches recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- D. Completed Researches -->
            <div class="fw-bold small text-dark mb-1">D. Completed Researches</div>
            <div class="table-responsive mb-2">
                <table class="table table-bordered border-dark table-sm align-middle mb-0" style="font-size: 0.78rem;">
                    <thead class="bg-light text-center fw-bold">
                        <tr>
                            <th style="width: 35%;">Title</th>
                            <th style="width: 25%;">Proponent</th>
                            <th style="width: 16%;">College</th>
                            <th style="width: 12%;">Date Started</th>
                            <th style="width: 12%;">Date Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report->section_d ?? [] as $dRow)
                            <tr>
                                <td>{{ $dRow['title'] ?? 'N/A' }}</td>
                                <td>{{ $dRow['proponent'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $dRow['college'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $dRow['date_started'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $dRow['date_completed'] ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-2">No completed researches recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-muted fst-italic" style="font-size: 0.72rem;">
                *NOTE: Completed &bull; Terminal Report/Abstracts of research outputs &bull; Certificate of completion signed by Research Director/VP for REI (c/o RESU)
            </div>

            <div class="d-flex justify-content-between align-items-center small text-muted mt-4 pt-3 border-top">
                <span>{{ $report->form_code }}</span>
                <span>{{ $report->revision_no }}</span>
            </div>
        </div>

        <!-- PAGE 3 OF 4 PREVIEW -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4 p-md-5 text-dark" style="font-family: Arial, sans-serif;">
            <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
                <span>{{ $report->form_code }}</span>
                <span class="fw-bold">Page | 3</span>
            </div>

            <!-- E. Research Presentation -->
            <div class="fw-bold small text-dark mb-1">E. Research Presentation</div>
            <div class="table-responsive mb-2">
                <table class="table table-bordered border-dark table-sm align-middle mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light text-center fw-bold">
                        <tr>
                            <th>Campus/Unit</th>
                            <th>Type</th>
                            <th>#</th>
                            <th>Research Title</th>
                            <th>Duration</th>
                            <th>Started/Completed</th>
                            <th>Date Presented</th>
                            <th>Conf Type</th>
                            <th>Venue</th>
                            <th>Title of Conference</th>
                            <th>Author/s</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $eNum = 1; @endphp
                        @forelse($report->section_e ?? [] as $eRow)
                            <tr>
                                <td>{{ $eRow['unit'] ?? '' }}</td>
                                <td>{{ $eRow['type'] ?? 'Study' }}</td>
                                <td class="text-center">{{ $eNum++ }}</td>
                                <td>{{ $eRow['title'] ?? '' }}</td>
                                <td class="text-center">{{ $eRow['duration'] ?? '' }}</td>
                                <td class="text-center">{{ $eRow['dates'] ?? '' }}</td>
                                <td class="text-center">{{ $eRow['date_presented'] ?? '' }}</td>
                                <td class="text-center">{{ $eRow['conf_type'] ?? '' }}</td>
                                <td>{{ $eRow['venue'] ?? '' }}</td>
                                <td>{{ $eRow['conf_title'] ?? '' }}</td>
                                <td>{{ $eRow['authors'] ?? '' }}</td>
                                <td>{{ $eRow['remarks'] ?? '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="12" class="text-center text-muted py-2">No research presentations recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-muted fst-italic mb-4" style="font-size: 0.72rem;">
                *NOTE: Presented Researches &bull; Call for Paper/Invitation &bull; Notice of Acceptance &bull; Conference Proceeding &bull; Certificate of Participation/Recognition
            </div>

            <!-- F. Publication -->
            <div class="fw-bold small text-dark mb-1">F. Publication</div>
            <div class="table-responsive mb-2">
                <table class="table table-bordered border-dark table-sm align-middle mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light text-center fw-bold">
                        <tr>
                            <th>Campus/Unit</th>
                            <th>#</th>
                            <th>Research Title</th>
                            <th>Duration</th>
                            <th>Date Started</th>
                            <th>Date Completed</th>
                            <th>Date Published</th>
                            <th>Type of Journal</th>
                            <th>Name of Publication</th>
                            <th>Vol No.</th>
                            <th>Author/s</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $fNum = 1; @endphp
                        @forelse($report->section_f ?? [] as $fRow)
                            <tr>
                                <td>{{ $fRow['unit'] ?? '' }}</td>
                                <td class="text-center">{{ $fNum++ }}</td>
                                <td>{{ $fRow['title'] ?? '' }}</td>
                                <td class="text-center">{{ $fRow['duration'] ?? '' }}</td>
                                <td class="text-center">{{ $fRow['date_started'] ?? '' }}</td>
                                <td class="text-center">{{ $fRow['date_completed'] ?? '' }}</td>
                                <td class="text-center">{{ $fRow['date_published'] ?? '' }}</td>
                                <td class="text-center">{{ $fRow['journal_type'] ?? '' }}</td>
                                <td>{{ $fRow['pub_name'] ?? '' }}</td>
                                <td class="text-center">{{ $fRow['vol_no'] ?? '' }}</td>
                                <td>{{ $fRow['authors'] ?? '' }}</td>
                                <td>{{ $fRow['remarks'] ?? '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="12" class="text-center text-muted py-2">No publications recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-muted fst-italic" style="font-size: 0.72rem;">
                *NOTE: Published Researches &bull; Full copy of journal title page and 1st page of article &bull; Complete manuscript
            </div>

            <div class="d-flex justify-content-between align-items-center small text-muted mt-4 pt-3 border-top">
                <span>{{ $report->form_code }}</span>
                <span>{{ $report->revision_no }}</span>
            </div>
        </div>

        <!-- PAGE 4 OF 4 PREVIEW -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4 p-md-5 text-dark" style="font-family: Arial, sans-serif;">
            <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
                <span>{{ $report->form_code }}</span>
                <span class="fw-bold">Page | 4</span>
            </div>

            <!-- G. Utilization -->
            <div class="fw-bold small text-dark mb-1">G. Utilization</div>
            <div class="table-responsive mb-2">
                <table class="table table-bordered border-dark table-sm align-middle mb-0" style="font-size: 0.78rem;">
                    <thead class="bg-light text-center fw-bold">
                        <tr>
                            <th style="width: 30%;">Product Name/Methods/Process/Technology</th>
                            <th style="width: 25%;">Patent Number/Product Description</th>
                            <th style="width: 25%;">Benefiting Industry/Community</th>
                            <th style="width: 20%;">Date Started and Date Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report->section_g ?? [] as $gRow)
                            <tr>
                                <td>{{ $gRow['product'] ?? 'N/A' }}</td>
                                <td>{{ $gRow['patent'] ?? 'N/A' }}</td>
                                <td>{{ $gRow['beneficiary'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $gRow['dates'] ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-2">No utilization entries recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-muted fst-italic mb-4" style="font-size: 0.72rem;">
                *NOTE: Utilized outputs &bull; Proof of utilization by stakeholders &bull; MOAs/MOUs &bull; Reports with testimonies &bull; Certifications
            </div>

            <!-- H. Evidences/Picture of Activities -->
            <div class="fw-bold small text-dark mb-1">H. Evidences/Picture of Activities</div>
            <div class="border border-dark p-3 mb-4 rounded-2" style="font-size: 0.8rem;">
                <div class="row g-2 mb-2">
                    <div class="col-4"><strong>Title:</strong> {{ $report->section_h['title'] ?? 'N/A' }}</div>
                    <div class="col-4"><strong>Venue:</strong> {{ $report->section_h['venue'] ?? 'N/A' }}</div>
                    <div class="col-4"><strong>Date:</strong> {{ $report->section_h['date'] ?? 'N/A' }}</div>
                </div>
                <div><strong>Pictures / Documentation:</strong></div>
                <div class="text-muted p-2 bg-light rounded mt-1">{{ $report->section_h['pictures_note'] ?? 'Photo documentation submitted on file with College Research Unit.' }}</div>
            </div>

            <!-- Signatures Section (3 Signatories matching template) -->
            <div class="row mt-4 pt-2">
                <div class="col-12 col-md-4 mb-4 mb-md-0">
                    <div class="small text-muted mb-4">Submitted by:</div>
                    <div class="d-inline-block">
                        <div class="fw-bold text-uppercase border-bottom border-dark pb-1" style="min-width: 220px;">
                            {{ $report->preparer?->name ?? '____________________________________' }}
                        </div>
                        <div class="small text-dark mt-1">College Research Coordinator</div>
                    </div>
                </div>

                <div class="col-12 col-md-4 mb-4 mb-md-0">
                    <div class="small text-muted mb-4">Noted:</div>
                    <div class="d-inline-block">
                        <div class="fw-bold text-uppercase border-bottom border-dark pb-1" style="min-width: 220px;">
                            @if($report->noted_by && $report->noter)
                                {{ $report->noter->name }}
                            @else
                                <span class="text-muted fw-normal">____________________________________</span>
                            @endif
                        </div>
                        <div class="small text-dark mt-1">College Dean</div>
                        @if($report->noted_at)
                            <div class="text-success small mt-1" style="font-size: 0.72rem;">
                                <i class="bi bi-patch-check-fill me-1"></i> Noted on {{ $report->noted_at->format('M d, Y') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="small text-muted mb-4">Submitted to:</div>
                    <div class="d-inline-block">
                        <div class="fw-bold text-uppercase border-bottom border-dark pb-1" style="min-width: 220px;">
                            @if($report->status === 'acknowledged' && $report->receiver)
                                {{ $report->receiver->name }}
                            @else
                                <span class="text-muted fw-normal">____________________________________</span>
                            @endif
                        </div>
                        <div class="small text-dark mt-1">Director, Research Unit</div>
                        @if($report->received_at)
                            <div class="text-success small mt-1" style="font-size: 0.72rem;">
                                <i class="bi bi-patch-check-fill me-1"></i> Received on {{ $report->received_at->format('M d, Y') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center small text-muted mt-5 pt-3 border-top">
                <span>{{ $report->form_code }}</span>
                <span>{{ $report->revision_no }}</span>
            </div>
        </div>

    </div>

    <!-- Dean Modal: Note & Submit to Director -->
    @if(in_array(auth()->user()->role, ['dean', 'super_admin']) && $report->status === 'pending_dean_noting')
        <div class="modal fade" id="deanNoteModal" tabindex="-1" aria-labelledby="deanNoteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0 bg-success bg-opacity-10 p-4 pb-2">
                        <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2" id="deanNoteModalLabel">
                            <i class="bi bi-send-check-fill fs-4"></i> Note & Submit Accomplishment Report
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('accomplishment_reports.note_and_submit', $report->id) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <p class="text-muted small mb-3">
                                As the <strong>College Dean</strong> of <strong>{{ $report->college }}</strong>, your action will officially note this quarterly accomplishment report and transmit it directly to the <strong>Director of the Research Unit</strong>.
                            </p>
                            <div class="p-3 bg-light rounded-3 small">
                                <div><strong>College:</strong> {{ $report->college }}</div>
                                <div><strong>Period:</strong> {{ $report->quarter }} &bull; {{ $report->school_year }}</div>
                                <div><strong>Prepared by:</strong> {{ $report->preparer?->name ?? 'Coordinator' }}</div>
                                <div><strong>Date of Noting:</strong> {{ date('F d, Y') }}</div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 small" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success rounded-pill px-4 py-2 small fw-bold">
                                <i class="bi bi-send-fill me-1"></i> Confirm, Note & Submit to Director
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Director Modal: Acknowledge & Receive -->
    @if(in_array(auth()->user()->role, ['admin', 'super_admin']) && $report->status === 'submitted_to_director')
        <div class="modal fade" id="directorAckModal" tabindex="-1" aria-labelledby="directorAckModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0 bg-success bg-opacity-10 p-4 pb-2">
                        <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2" id="directorAckModalLabel">
                            <i class="bi bi-check2-circle fs-4"></i> Acknowledge & Receive Report
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('accomplishment_reports.acknowledge', $report->id) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <p class="text-muted small mb-3">
                                As the <strong>Director of the Research Unit</strong>, confirming this action marks the official receipt of the <strong>{{ $report->quarter }} Accomplishment Report</strong> submitted by the Dean of <strong>{{ $report->college }}</strong>.
                            </p>
                            <div class="p-3 bg-light rounded-3 small">
                                <div><strong>College:</strong> {{ $report->college }}</div>
                                <div><strong>Noted & Submitted by:</strong> {{ $report->noter?->name ?? 'College Dean' }}</div>
                                <div><strong>Date Received:</strong> {{ date('F d, Y') }}</div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 small" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success rounded-pill px-4 py-2 small fw-bold">
                                <i class="bi bi-check2-all me-1"></i> Confirm Official Receipt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
