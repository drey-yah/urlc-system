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
                                            Phase {{ $proposal->current_phase }}
                                        </span>
                                    </div>

                                    <div class="d-flex gap-2 pt-2 border-top">
                                        <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill flex-fill text-center py-1.5" style="font-size: 0.8rem;">
                                            View Details
                                        </a>
                                        <button class="btn btn-sm btn-primary rounded-pill flex-fill text-center py-1.5 shadow-xs select-proposal" 
                                                data-id="{{ $proposal->id }}" 
                                                data-title="{{ $proposal->title }}"
                                                data-status="{{ $proposal->status }}"
                                                data-comments="{{ $proposal->review_comments }}"
                                                data-suggestions="{{ $proposal->review_suggestions }}"
                                                style="font-size: 0.8rem;">
                                            <i class="bi bi-pencil-square me-1"></i> Review
                                        </button>
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
                                    <th class="py-3 fw-semibold border-0 text-center">Phase</th>
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
                                            <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 small fw-medium">Phase {{ $proposal->current_phase }}</span>
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                                    View
                                                </a>
                                                <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm select-proposal" 
                                                        data-id="{{ $proposal->id }}" 
                                                        data-title="{{ $proposal->title }}"
                                                        data-status="{{ $proposal->status }}"
                                                        data-comments="{{ $proposal->review_comments }}"
                                                        data-suggestions="{{ $proposal->review_suggestions }}">
                                                    <i class="bi bi-pencil-square me-1"></i> Review
                                                </button>
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
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 80px;" id="feedback-card">
                <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                    <h5 class="mb-0 fw-bold text-dark">Review Feedback</h5>
                </div>
                <div class="card-body p-3 p-md-4" id="feedback-container">
                    <div id="no-proposal-selected" class="text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3 text-muted">
                            <i class="bi bi-file-earmark-text fs-2"></i>
                        </div>
                        <p class="text-muted small mb-0 fw-medium">Select a proposal from the list to begin evaluating</p>
                    </div>

                    <form id="review-form" method="POST" action="" enctype="multipart/form-data" class="d-none">
                        @csrf
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 mb-3 border border-primary border-opacity-10">
                            <small class="text-primary fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Selected Proposal:</small>
                            <p class="fw-bold mb-0 text-dark small" id="selected-proposal-title"></p>
                        </div>
                        
                        <div class="mb-3 d-flex justify-content-between align-items-center bg-light p-2.5 rounded-3 border">
                            <div class="small fw-semibold text-muted" style="font-size: 0.78rem;">Evaluation Form</div>
                            <a href="javascript:alert('Template download will be provided by Admin.')" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                                <i class="bi bi-download me-1"></i> Template
                            </a>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small text-uppercase" style="font-size: 0.72rem;">Upload Completed Evaluation</label>
                            <input type="file" name="evaluation_document" class="form-control bg-light border py-1.5 px-2" style="font-size: 0.8rem;" accept=".pdf,.doc,.docx">
                            <small class="text-muted" style="font-size: 0.68rem;">Optional. PDF, DOC, DOCX up to 10MB.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small text-uppercase" style="font-size: 0.72rem;">Decision Status</label>
                            <select name="status" id="status-select" class="form-select bg-light border py-2" style="font-size: 0.85rem;">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="revision_required">Revision Required</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small text-uppercase" style="font-size: 0.72rem;">Comments</label>
                            <textarea name="review_comments" id="comments-text" class="form-control bg-light border py-2" rows="3" placeholder="Enter your comments about the proposal" style="font-size: 0.85rem;"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small text-uppercase" style="font-size: 0.72rem;">Suggestions</label>
                            <textarea name="review_suggestions" id="suggestions-text" class="form-control bg-light border py-2" rows="3" placeholder="Enter suggestions for improvement" style="font-size: 0.85rem;"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 rounded-pill shadow-sm">
                            <i class="bi bi-check-circle"></i> Submit Review
                        </button>
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

            selectButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    const status = this.getAttribute('data-status');
                    const comments = this.getAttribute('data-comments');
                    const suggestions = this.getAttribute('data-suggestions');

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
                    // Ensure comments and suggestions start completely blank for reviewer input
                    commentsText.value = '';
                    suggestionsText.value = '';

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