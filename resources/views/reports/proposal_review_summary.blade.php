<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In-House Review Summary Sheet - {{ $proposal->proposal_code ?? 'Proposal' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f1f3f5;
            color: #111;
            line-height: 1.5;
        }
        .page-container {
            max-width: 920px;
            margin: 30px auto;
            background: #fff;
            padding: 50px 60px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #ddd;
        }
        .header-logo {
            width: 75px;
            height: auto;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        .report-table th, .report-table td {
            border: 1px solid #333;
            padding: 8px 10px;
            font-size: 0.88rem;
            vertical-align: top;
        }
        .report-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.78rem;
            letter-spacing: 0.03em;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .meta-table td {
            padding: 4px 6px;
            font-size: 0.92rem;
            vertical-align: top;
        }
        .signature-box {
            margin-top: 45px;
        }
        .signature-line {
            border-bottom: 1.5px solid #000;
            width: 250px;
            margin-bottom: 4px;
        }
        @media print {
            body {
                background: #fff;
            }
            .page-container {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Print / Action Toolbar -->
    <div class="no-print bg-dark text-white py-2 px-4 shadow-sm position-sticky top-0" style="z-index: 1000;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="small fw-semibold">
                <i class="bi bi-file-earmark-text text-warning me-1"></i> In-House Review Summary Sheet &bull; {{ $proposal->proposal_code ?? 'Proposal #'.$proposal->id }}
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-light fw-bold" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print / Save as PDF
                </button>
                <a href="{{ route('proposal.reviewWorkspace', $proposal->id) }}" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-arrow-left me-1"></i> Back to Review Workspace
                </a>
            </div>
        </div>
    </div>

    <div class="page-container">
        <!-- University Letterhead -->
        <div class="text-center mb-4 pb-3 border-bottom border-dark">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                <img src="https://upload.wikimedia.org/wikipedia/en/e/eb/University_of_Antique_logo.png" alt="UA Logo" class="header-logo" onerror="this.style.display='none'">
                <div>
                    <h6 class="mb-0 fw-normal" style="font-size: 0.95rem;">Republic of the Philippines</h6>
                    <h5 class="mb-0 fw-bold" style="letter-spacing: 0.05em; font-size: 1.15rem;">UNIVERSITY OF ANTIQUE</h5>
                    <div class="small text-muted" style="font-size: 0.85rem;">Sibalom, Antique, Philippines &bull; www.antiquespride.edu.ph</div>
                    <div class="fw-bold mt-1" style="font-size: 0.9rem; text-transform: uppercase;">University Research &amp; Development Center (URDC / URLC)</div>
                </div>
            </div>
            <h4 class="fw-bold mt-3 mb-1" style="font-size: 1.25rem; letter-spacing: 0.04em;">IN-HOUSE REVIEW SUMMARY SHEET &amp; REVISION NOTES</h4>
            <div class="small text-muted fst-italic">Official Peer Evaluation Document &bull; Document Tracking Form</div>
        </div>

        <!-- Proposal Metadata -->
        <table class="meta-table">
            <tr>
                <td style="width: 25%; font-weight: bold;">Proposal Code</td>
                <td style="width: 2%;">:</td>
                <td style="width: 73%;" class="fw-bold">{{ $proposal->proposal_code ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Research Title</td>
                <td>:</td>
                <td class="fw-bold">{{ $proposal->title }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Lead Proponent</td>
                <td>:</td>
                <td>{{ $proposal->user->name }} ({{ $proposal->user->email }})</td>
            </tr>
            @if($proposal->collaborators && $proposal->collaborators->count() > 0)
            <tr>
                <td style="font-weight: bold;">Collaborator(s)</td>
                <td>:</td>
                <td>{{ $proposal->collaborators->pluck('name')->implode(', ') }}</td>
            </tr>
            @endif
            <tr>
                <td style="font-weight: bold;">College / Department</td>
                <td>:</td>
                <td>{{ $proposal->user->department ?? 'General Academic Department' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Research Field &amp; Funding</td>
                <td>:</td>
                <td>{{ $proposal->research_field ?? 'N/A' }} &bull; {{ ucwords(str_replace('_', ' ', $proposal->funding_type ?? 'Institutional')) }} (&#8369;{{ number_format($proposal->total_budget ?? 0, 2) }})</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Evaluation Status</td>
                <td>:</td>
                <td>
                    <span class="badge text-uppercase border border-dark text-dark px-2 py-1" style="font-size: 0.78rem;">
                        {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                    </span>
                    &bull; Current Stage: <strong>Phase {{ $proposal->current_phase }} ({{ min(100, max(0, round(($proposal->current_phase / 5) * 100))) }}%)</strong>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Date Generated</td>
                <td>:</td>
                <td>{{ now()->format('F d, Y - h:i A') }}</td>
            </tr>
        </table>

        <!-- Statistics Summary Banner -->
        @php
            $totalAnnotations = $annotations->count();
            $resolvedCount = $annotations->where('is_resolved', true)->count();
            $pendingCount = $totalAnnotations - $resolvedCount;
        @endphp
        <div class="row g-2 mb-3 text-center">
            <div class="col-4">
                <div class="p-2 border border-dark bg-light">
                    <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Total Review Notes</small>
                    <strong class="fs-5">{{ $totalAnnotations }}</strong>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 border border-dark bg-light">
                    <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Resolved by Proponent</small>
                    <strong class="fs-5 text-success">{{ $resolvedCount }}</strong>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 border border-dark bg-light">
                    <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Pending Revision</small>
                    <strong class="fs-5 text-danger">{{ $pendingCount }}</strong>
                </div>
            </div>
        </div>

        <!-- General Feedback from Overall Form -->
        @if($proposal->review_comments || $proposal->review_suggestions)
        <div class="mb-4 p-3 border border-dark bg-light">
            <h6 class="fw-bold text-uppercase mb-2" style="font-size: 0.85rem; letter-spacing: 0.03em;">General Evaluator Remarks:</h6>
            @if($proposal->review_comments)
                <p class="mb-2 small"><strong>General Comments:</strong> {{ $proposal->review_comments }}</p>
            @endif
            @if($proposal->review_suggestions)
                <p class="mb-0 small"><strong>Recommendations:</strong> {{ $proposal->review_suggestions }}</p>
            @endif
        </div>
        @endif

        <!-- In-Line Contextual Annotations Table -->
        <h6 class="fw-bold text-uppercase mb-2" style="font-size: 0.9rem; letter-spacing: 0.04em;">Detailed In-Line Document Comments &amp; Required Revisions:</h6>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th style="width: 22%;">Chapter / Section</th>
                    <th style="width: 33%;">Referenced Passage / Excerpt</th>
                    <th style="width: 25%;">Reviewer Suggestion &amp; Tag</th>
                    <th style="width: 15%; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($annotations as $idx => $item)
                    <tr>
                        <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item->chapter_section }}</strong>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                By: {{ $item->user->name ?? 'Reviewer' }}
                                <br>{{ $item->created_at->format('M d, Y') }}
                            </div>
                        </td>
                        <td>
                            @if($item->highlighted_text)
                                <div style="font-style: italic; background-color: #fff9db; padding: 4px 6px; border-left: 3px solid #f59e0b; font-size: 0.82rem;">
                                    &ldquo;{{ $item->highlighted_text }}&rdquo;
                                </div>
                            @else
                                <span class="text-muted fst-italic" style="font-size: 0.8rem;">[General Section Note]</span>
                            @endif
                        </td>
                        <td>
                            <div class="mb-1" style="font-size: 0.86rem;">{{ $item->comment }}</div>
                            @if($item->taggedUser)
                                <div class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                                    <i class="bi bi-at"></i>{{ $item->taggedUser->name }}
                                </div>
                            @endif
                            @if($item->replies && $item->replies->count() > 0)
                                <div class="mt-2 pt-1 border-top" style="font-size: 0.78rem;">
                                    <strong class="d-block text-muted">Author Replies:</strong>
                                    @foreach($item->replies as $reply)
                                        <div class="mt-1">
                                            <em>{{ $reply->user->name }}:</em> &ldquo;{{ $reply->comment }}&rdquo;
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->is_resolved)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 fw-bold" style="font-size: 0.72rem;">
                                    RESOLVED
                                </span>
                                @if($item->resolvedByUser)
                                    <div class="text-muted mt-1" style="font-size: 0.68rem;">By: {{ $item->resolvedByUser->name }}</div>
                                @endif
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1 fw-bold" style="font-size: 0.72rem;">
                                    PENDING
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted fst-italic">
                            No inline annotations or revision notes recorded for this proposal yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Evaluator Sign-off Blocks -->
        <div class="signature-box">
            <div class="row">
                <div class="col-6">
                    <p class="mb-5 small">Evaluated &amp; Reviewed by:</p>
                    <div class="signature-line"></div>
                    <div class="fw-bold">{{ $proposal->reviewer->name ?? 'ASSIGNED PEER REVIEWER' }}</div>
                    <div class="small text-muted">Technical Reviewer / Evaluator</div>
                    <div class="small text-muted">Date: ________________________</div>
                </div>
                <div class="col-6 text-end">
                    <p class="mb-5 small">Noted &amp; Certified by:</p>
                    <div class="signature-line ms-auto"></div>
                    <div class="fw-bold">DIRECTOR, URDC</div>
                    <div class="small text-muted">Research and Development Services</div>
                    <div class="small text-muted">Date: ________________________</div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
