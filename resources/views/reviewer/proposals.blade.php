<x-app-layout>
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Review Evaluation Queue</h1>
            <p class="text-muted small mb-0">Welcome, <strong>Reviewer {{ Auth::user()->name }}</strong> | Peer Evaluation & Scoring</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reviewer.dashboard') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2 shadow-sm rounded-3 py-2 px-3 fw-semibold">
                <i class="bi bi-arrow-left"></i> <span>Back to Dashboard</span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Proposals List (Left 8 Columns) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-0 rounded-top-4">
                    <h5 class="mb-0 fw-bold text-dark">Proposals for Review</h5>
                </div>
                <div class="card-body p-0">
                    <!-- Mobile View: Cards (d-md-none) -->
                    <div class="d-md-none p-3 bg-light bg-opacity-25">
                        @forelse($proposals as $proposal)
                            <div class="card border shadow-none rounded-3 mb-2.5 bg-white proposal-card" id="proposal-card-{{ $proposal->id }}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                        <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.72rem;">
                                            {{ $proposal->proposal_code ?? 'P-'.$proposal->id }}
                                        </span>
                                        @php
                                            $statusClass = match($proposal->status) {
                                                'approved', 'final_approved' => 'bg-success bg-opacity-10 text-success',
                                                'pending', 'submitted', 'under_review' => 'bg-warning bg-opacity-10 text-warning',
                                                'rejected', 'final_rejected' => 'bg-danger bg-opacity-10 text-danger',
                                                'revision_required' => 'bg-primary bg-opacity-10 text-primary',
                                                default => 'bg-secondary bg-opacity-10 text-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }} rounded-pill px-2.5 py-1 fw-semibold text-capitalize" style="font-size: 0.72rem;">
                                            {{ str_replace('_', ' ', $proposal->status) }}
                                        </span>
                                    </div>

                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                                        {{ $proposal->title }}
                                    </h6>
                                    
                                    <div class="d-flex align-items-center justify-content-between small text-muted mb-2.5" style="font-size: 0.78rem;">
                                        <div class="d-flex align-items-center gap-1.5">
                                            <i class="bi bi-person text-muted"></i>
                                            <span class="text-truncate" style="max-width: 140px;">{{ $proposal->user->name ?? 'Unknown' }}</span>
                                        </div>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                            {{ min(100, max(0, round(($proposal->current_phase / 5) * 100))) }}% &bull; Phase {{ $proposal->current_phase }}
                                        </span>
                                    </div>

                                    @php
                                        $myEval = $proposal->evaluations->first();
                                        $isFinalDecision = in_array($proposal->status, ['final_approved', 'final_rejected']);
                                    @endphp
                                    <div class="d-flex gap-1.5 pt-2 border-top flex-wrap align-items-center">
                                        <a href="{{ route('proposal.reviewWorkspace', $proposal->id) }}" class="btn btn-sm btn-outline-primary rounded-pill flex-fill text-center py-1.5 fw-bold" style="font-size: 0.78rem;">
                                            <i class="bi bi-chat-quote-fill text-warning me-0.5"></i> Annotate
                                        </a>
                                        <a href="{{ route('proposal.evaluationPdf', $proposal->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill flex-fill text-center py-1.5 fw-semibold" style="font-size: 0.78rem;" title="Official Form RESU-FM-001">
                                            <i class="bi bi-file-earmark-pdf-fill"></i> FM-001
                                        </a>
                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill flex-fill text-center py-1.5" style="font-size: 0.8rem;">
                                            Details
                                        </a>
                                        @if($isFinalDecision)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1.5 flex-fill text-center small fw-semibold" title="Evaluation closed - final executive decision already rendered">
                                                <i class="bi bi-lock-fill me-1"></i> Finalized
                                            </span>
                                        @else
                                            <button class="btn btn-sm btn-primary rounded-pill flex-fill text-center py-1.5 shadow-xs select-proposal" 
                                                    data-id="{{ $proposal->id }}" 
                                                    data-title="{{ $proposal->title }}"
                                                    data-status="{{ $proposal->status }}"
                                                    data-comments="{{ $myEval->comments ?? $proposal->review_comments }}"
                                                    data-suggestions="{{ $myEval->suggestions ?? $proposal->review_suggestions }}"
                                                    data-score-a="{{ $myEval->score_a ?? '' }}"
                                                    data-score-b="{{ $myEval->score_b ?? '' }}"
                                                    data-score-c="{{ $myEval->score_c ?? '' }}"
                                                    data-score-d="{{ $myEval->score_d ?? '' }}"
                                                    data-score-e="{{ $myEval->score_e ?? '' }}"
                                                    data-total-score="{{ $myEval->total_score ?? '' }}"
                                                    style="font-size: 0.8rem;">
                                                <i class="bi bi-star-fill text-warning me-1"></i> Evaluate
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-journal-x fs-2 d-block mb-2 text-secondary"></i>
                                <p class="mb-0 fw-medium small">No proposals assigned to you for review yet.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Desktop View: Table (d-none d-md-block) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0 border-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3 fw-semibold border-0">Proposal Title</th>
                                    <th class="py-3 fw-semibold border-0 text-center">Status</th>
                                    <th class="py-3 fw-semibold border-0 text-center">Progress</th>
                                    <th class="pe-4 py-3 fw-semibold border-0 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($proposals as $proposal)
                                    <tr id="proposal-row-{{ $proposal->id }}" class="proposal-row">
                                        <td class="ps-4 py-3">
                                            <div class="fw-semibold text-dark">{{ $proposal->title }}</div>
                                            <small class="text-muted">By {{ $proposal->user->name ?? 'Unknown' }}</small>
                                        </td>
                                        <td class="py-3 text-center">
                                            @php
                                                $statusClass = match($proposal->status) {
                                                    'approved', 'final_approved' => 'bg-success bg-opacity-10 text-success',
                                                    'pending', 'submitted', 'under_review' => 'bg-warning bg-opacity-10 text-warning',
                                                    'rejected', 'final_rejected' => 'bg-danger bg-opacity-10 text-danger',
                                                    'revision_required' => 'bg-primary bg-opacity-10 text-primary',
                                                    default => 'bg-secondary bg-opacity-10 text-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill px-3 py-1 fw-medium text-capitalize">
                                                {{ str_replace('_', ' ', $proposal->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 small fw-medium">{{ min(100, max(0, round(($proposal->current_phase / 5) * 100))) }}% (Phase {{ $proposal->current_phase }})</span>
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            @php
                                                $myEval = $proposal->evaluations->first();
                                                $isFinalDecision = in_array($proposal->status, ['final_approved', 'final_rejected']);
                                            @endphp
                                            <div class="d-flex justify-content-end align-items-center gap-1.5 flex-wrap">
                                                <a href="{{ route('proposal.reviewWorkspace', $proposal->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 fw-bold shadow-xs d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                                    <i class="bi bi-chat-quote-fill text-warning"></i> Annotate
                                                </a>
                                                <a href="{{ route('proposal.evaluationPdf', $proposal->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Official Form RESU-FM-001 PDF">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i> FM-001
                                                </a>
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1" style="font-size: 0.8rem;">
                                                    View
                                                </a>
                                                @if($isFinalDecision)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1 small fw-semibold" title="Evaluation closed - final executive decision already rendered">
                                                        <i class="bi bi-lock-fill"></i> Finalized
                                                    </span>
                                                @else
                                                    <button class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-sm select-proposal d-inline-flex align-items-center gap-1" 
                                                            data-id="{{ $proposal->id }}" 
                                                            data-title="{{ $proposal->title }}"
                                                            data-status="{{ $proposal->status }}"
                                                            data-comments="{{ $myEval->comments ?? $proposal->review_comments }}"
                                                            data-suggestions="{{ $myEval->suggestions ?? $proposal->review_suggestions }}"
                                                            data-score-a="{{ $myEval->score_a ?? '' }}"
                                                            data-score-b="{{ $myEval->score_b ?? '' }}"
                                                            data-score-c="{{ $myEval->score_c ?? '' }}"
                                                            data-score-d="{{ $myEval->score_d ?? '' }}"
                                                            data-score-e="{{ $myEval->score_e ?? '' }}"
                                                            data-total-score="{{ $myEval->total_score ?? '' }}"
                                                            style="font-size: 0.8rem;">
                                                        <i class="bi bi-star-fill text-warning"></i> Evaluate
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted small">No proposals have been assigned to you for review yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Form (Right 4 Columns) -->
        <div class="col-lg-5 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 80px;" id="feedback-card">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1rem;">
                        <i class="bi bi-clipboard2-check-fill text-primary"></i> Proposal Evaluation
                    </h5>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-0.5 rounded-pill font-monospace" style="font-size: 0.68rem;">RESU-FM-001</span>
                </div>
                <div class="card-body p-3 p-md-4" id="feedback-container">
                    <div id="no-proposal-selected" class="text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3 text-muted">
                            <i class="bi bi-file-earmark-check fs-2 text-primary opacity-75"></i>
                        </div>
                        <p class="text-dark fw-bold mb-1 small">Select a proposal to evaluate</p>
                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">Click "Evaluate" on any proposal to input criteria ratings and export the official RESU-FM-001 sheet.</p>
                    </div>

                    <form id="review-form" method="POST" action="" enctype="multipart/form-data" class="d-none">
                        @csrf
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 mb-3 border border-primary border-opacity-10">
                            <small class="text-primary fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Selected Proposal:</small>
                            <p class="fw-bold mb-2 text-dark small" id="selected-proposal-title"></p>
                            <div class="d-flex gap-1.5">
                                <a href="#" id="selected-workspace-link" class="btn btn-warning bg-opacity-25 text-dark border border-warning border-opacity-50 btn-xs flex-fill rounded-pill py-1 fw-bold d-inline-flex align-items-center justify-content-center gap-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-chat-quote-fill text-warning"></i> Annotator
                                </a>
                                <a href="#" id="eval-pdf-link" target="_blank" class="btn btn-danger bg-opacity-25 text-danger border border-danger border-opacity-50 btn-xs flex-fill rounded-pill py-1 fw-bold d-inline-flex align-items-center justify-content-center gap-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> FM-001 PDF
                                </a>
                            </div>
                        </div>

                        <!-- Form RESU-FM-001 Criteria Rubric -->
                        <div class="card border rounded-3 mb-3 p-2.5 bg-light bg-opacity-50 shadow-2xs">
                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                <span class="fw-bold text-dark small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                                    <i class="bi bi-list-check text-primary me-1"></i> Criteria on Approval
                                </span>
                                <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.65rem;">Max 100%</span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered bg-white small mb-1 align-middle" style="font-size: 0.75rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Criteria &amp; Weight</th>
                                            <th style="width: 78px;" class="text-center">Score (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong class="text-dark">A.</strong> Innovativeness / Novelty / Creativity
                                                <small class="text-muted d-block" style="font-size: 0.67rem;">Max weight: 15%</small>
                                            </td>
                                            <td>
                                                <input type="number" step="0.5" min="0" max="15" name="score_a" id="eval-score-a" class="form-control form-control-sm text-center fw-bold rubric-input py-1 px-1" placeholder="0" style="font-size: 0.8rem;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong class="text-dark">B.</strong> Time Schedule and Cost
                                                <small class="text-muted d-block" style="font-size: 0.67rem;">Max weight: 5%</small>
                                            </td>
                                            <td>
                                                <input type="number" step="0.5" min="0" max="5" name="score_b" id="eval-score-b" class="form-control form-control-sm text-center fw-bold rubric-input py-1 px-1" placeholder="0" style="font-size: 0.8rem;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong class="text-dark">C.</strong> Applicability (CPE, ext., IPSs)
                                                <small class="text-muted d-block" style="font-size: 0.67rem;">Max weight: 25%</small>
                                            </td>
                                            <td>
                                                <input type="number" step="0.5" min="0" max="25" name="score_c" id="eval-score-c" class="form-control form-control-sm text-center fw-bold rubric-input py-1 px-1" placeholder="0" style="font-size: 0.8rem;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong class="text-dark">D.</strong> Relevance (S&amp;T Advancement)
                                                <small class="text-muted d-block" style="font-size: 0.67rem;">Max weight: 25%</small>
                                            </td>
                                            <td>
                                                <input type="number" step="0.5" min="0" max="25" name="score_d" id="eval-score-d" class="form-control form-control-sm text-center fw-bold rubric-input py-1 px-1" placeholder="0" style="font-size: 0.8rem;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong class="text-dark">E.</strong> Effectiveness (Stakeholders)
                                                <small class="text-muted d-block" style="font-size: 0.67rem;">Max weight: 30%</small>
                                            </td>
                                            <td>
                                                <input type="number" step="0.5" min="0" max="30" name="score_e" id="eval-score-e" class="form-control form-control-sm text-center fw-bold rubric-input py-1 px-1" placeholder="0" style="font-size: 0.8rem;">
                                            </td>
                                        </tr>
                                        <tr class="table-light">
                                            <td class="fw-bold text-uppercase text-dark" style="font-size: 0.72rem;">TOTAL RATING</td>
                                            <td class="text-center fw-bold fs-6" id="eval-total-display">0.00%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Live Passing Indicator Banner -->
                            <div id="eval-threshold-badge" class="alert py-1 px-2 mb-0 rounded-2 text-center small fw-semibold alert-secondary" style="font-size: 0.72rem;">
                                <i class="bi bi-info-circle me-1"></i> A rating of at least <strong>80%</strong> is required for approval.
                            </div>
                        </div>

                        <!-- Decision Status -->
                        <div class="mb-2.5">
                            <label class="form-label fw-semibold text-muted small text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.04em;">Decision Status</label>
                            <select name="status" id="status-select" class="form-select form-select-sm bg-white border py-1.5" style="font-size: 0.82rem;">
                                <option value="pending">Pending Review</option>
                                <option value="approved">Approved</option>
                                <option value="approved_with_revisions">Approved with Revisions</option>
                                <option value="revision_required">Revision Required</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- Comments -->
                        <div class="mb-2.5">
                            <label class="form-label fw-semibold text-muted small text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.04em;">Evaluator Comments / Remarks</label>
                            <textarea name="review_comments" id="comments-text" class="form-control bg-white border py-1.5 px-2.5" rows="3" placeholder="Enter evaluation comments for Form RESU-FM-001" style="font-size: 0.82rem;"></textarea>
                        </div>

                        <!-- Suggestions -->
                        <div class="mb-2.5">
                            <label class="form-label fw-semibold text-muted small text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.04em;">Suggestions for Improvement</label>
                            <textarea name="review_suggestions" id="suggestions-text" class="form-control bg-white border py-1.5 px-2.5" rows="2" placeholder="Specific actionable suggestions" style="font-size: 0.82rem;"></textarea>
                        </div>

                        <!-- Optional file -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.04em;">Supplementary Document (Optional)</label>
                            <input type="file" name="evaluation_document" class="form-control form-control-sm bg-white border py-1 px-2" style="font-size: 0.78rem;" accept=".pdf,.doc,.docx">
                            <small class="text-muted d-block mt-0.5" style="font-size: 0.67rem;">Upload signed scan or supplementary review file (PDF/DOC up to 10MB).</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold d-flex align-items-center justify-content-center gap-2 rounded-pill shadow-xs" style="font-size: 0.84rem;">
                                <i class="bi bi-check-circle-fill"></i> Save Evaluation &amp; Decision
                            </button>
                            <a href="#" id="eval-print-bottom-btn" target="_blank" class="btn btn-outline-danger py-1.5 fw-semibold d-flex align-items-center justify-content-center gap-1.5 rounded-pill" style="font-size: 0.8rem;">
                                <i class="bi bi-printer-fill"></i> Print / Download RESU-FM-001 Form
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectButtons = document.querySelectorAll('.select-proposal');
            const form = document.getElementById('review-form');
            const noSelection = document.getElementById('no-proposal-selected');
            const titleDisplay = document.getElementById('selected-proposal-title');
            const statusSelect = document.getElementById('status-select');
            const commentsText = document.getElementById('comments-text');
            const suggestionsText = document.getElementById('suggestions-text');
            const feedbackCard = document.getElementById('feedback-card');

            const scoreA = document.getElementById('eval-score-a');
            const scoreB = document.getElementById('eval-score-b');
            const scoreC = document.getElementById('eval-score-c');
            const scoreD = document.getElementById('eval-score-d');
            const scoreE = document.getElementById('eval-score-e');
            const totalDisplay = document.getElementById('eval-total-display');
            const badge = document.getElementById('eval-threshold-badge');
            const pdfTopLink = document.getElementById('eval-pdf-link');
            const pdfBottomLink = document.getElementById('eval-print-bottom-btn');

            function recalculateTotal() {
                const a = parseFloat(scoreA.value || 0);
                const b = parseFloat(scoreB.value || 0);
                const c = parseFloat(scoreC.value || 0);
                const d = parseFloat(scoreD.value || 0);
                const e = parseFloat(scoreE.value || 0);

                const total = Math.min(100, Math.max(0, a + b + c + d + e));
                totalDisplay.textContent = total.toFixed(2) + '%';

                if (total >= 80) {
                    totalDisplay.className = 'text-center fw-bold fs-6 text-success';
                    badge.className = 'alert py-1 px-2 mb-0 rounded-2 text-center small fw-semibold alert-success text-success border-success border-opacity-25';
                    badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Total score: <strong>' + total.toFixed(2) + '%</strong>. Meets the <strong>80%</strong> approval threshold.';
                } else if (total > 0) {
                    totalDisplay.className = 'text-center fw-bold fs-6 text-danger';
                    badge.className = 'alert py-1 px-2 mb-0 rounded-2 text-center small fw-semibold alert-warning text-dark border-warning border-opacity-50';
                    badge.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Total score: <strong>' + total.toFixed(2) + '%</strong>. Below the 80% passing threshold.';
                } else {
                    totalDisplay.className = 'text-center fw-bold fs-6 text-primary';
                    badge.className = 'alert py-1 px-2 mb-0 rounded-2 text-center small fw-semibold alert-secondary text-muted';
                    badge.innerHTML = '<i class="bi bi-info-circle me-1"></i> A rating of at least <strong>80%</strong> is required for approval.';
                }
            }

            [scoreA, scoreB, scoreC, scoreD, scoreE].forEach(input => {
                if (input) {
                    input.addEventListener('input', recalculateTotal);
                }
            });

            selectButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    const status = this.getAttribute('data-status');
                    const comments = this.getAttribute('data-comments') || '';
                    const suggestions = this.getAttribute('data-suggestions') || '';

                    const valA = this.getAttribute('data-score-a');
                    const valB = this.getAttribute('data-score-b');
                    const valC = this.getAttribute('data-score-c');
                    const valD = this.getAttribute('data-score-d');
                    const valE = this.getAttribute('data-score-e');

                    // Highlight desktop row
                    document.querySelectorAll('.proposal-row').forEach(row => row.classList.remove('table-primary'));
                    const targetRow = document.getElementById('proposal-row-' + id);
                    if (targetRow) targetRow.classList.add('table-primary');

                    // Highlight mobile card
                    document.querySelectorAll('.proposal-card').forEach(card => card.classList.remove('border-primary', 'bg-light'));
                    const targetCard = document.getElementById('proposal-card-' + id);
                    if (targetCard) targetCard.classList.add('border-primary');

                    // Fill Form
                    form.action = `/reviewer/proposals/${id}/update-status`;
                    titleDisplay.textContent = title;
                    statusSelect.value = status;

                    const workspaceLink = document.getElementById('selected-workspace-link');
                    if (workspaceLink) workspaceLink.href = `/proposal/${id}/review-workspace`;

                    const pdfUrl = `/proposal/${id}/evaluation-sheet`;
                    if (pdfTopLink) pdfTopLink.href = pdfUrl;
                    if (pdfBottomLink) pdfBottomLink.href = pdfUrl;

                    scoreA.value = valA !== '' ? valA : '';
                    scoreB.value = valB !== '' ? valB : '';
                    scoreC.value = valC !== '' ? valC : '';
                    scoreD.value = valD !== '' ? valD : '';
                    scoreE.value = valE !== '' ? valE : '';

                    commentsText.value = comments;
                    suggestionsText.value = suggestions;

                    recalculateTotal();

                    // Show Form
                    form.classList.remove('d-none');
                    noSelection.classList.add('d-none');

                    // Smooth scroll to form on mobile devices
                    if (window.innerWidth < 992 && feedbackCard) {
                        feedbackCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        });
    </script>
</x-app-layout>