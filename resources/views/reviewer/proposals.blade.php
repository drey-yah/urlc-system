<x-app-layout>
    <div class="mb-5">
        <h1 class="h3 fw-bold mb-1">Reviewer Dashboard</h1>
        <p class="text-muted">Welcome, {{ Auth::user()->name }}</p>
    </div>

    <div class="row g-4">
        <!-- Proposals List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Proposals for Review</h5>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Proposal Title</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Phase</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                            @forelse($proposals as $proposal)
                            <tr id="proposal-row-{{ $proposal->id }}" class="proposal-row">
                                <td class="ps-4 py-3">
                                    <div class="fw-semibold text-dark">{{ $proposal->title }}</div>
                                    <small class="text-muted">By {{ $proposal->user->name }}</small>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = match($proposal->status) {
                                            'approved', 'final_approved' => 'badge-approved',
                                            'pending' => 'badge-pending',
                                            'rejected', 'final_rejected' => 'badge-rejected',
                                            'revision_required' => 'badge-in-review',
                                            default => 'badge-pending'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="text-muted small fw-medium">Phase {{ $proposal->current_phase }}</span>
                                </td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-outline-primary btn-sm px-3 d-inline-flex align-items-center gap-2 select-proposal" 
                                            data-id="{{ $proposal->id }}" 
                                            data-title="{{ $proposal->title }}"
                                            data-status="{{ $proposal->status }}"
                                            data-comments="{{ $proposal->review_comments }}"
                                            data-suggestions="{{ $proposal->review_suggestions }}">
                                        <i class="bi bi-pencil-square"></i> Review
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted small italic">No proposals have been assigned to you for review yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Feedback Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Review Feedback</h5>
                </div>
                <div class="card-body p-4" id="feedback-container">
                    <div id="no-proposal-selected" class="text-center py-5">
                        <i class="bi bi-file-earmark-text text-muted display-4 mb-3 d-block"></i>
                        <p class="text-muted">Select a proposal to begin reviewing</p>
                    </div>

                    <form id="review-form" method="POST" action="" class="d-none">
                        @csrf
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 mb-4">
                            <small class="text-primary fw-bold text-uppercase d-block mb-1">Selected Proposal:</small>
                            <p class="fw-bold mb-0 text-dark" id="selected-proposal-title"></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small uppercase">Decision Status</label>
                            <select name="status" id="status-select" class="form-select bg-light border-0 py-2">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="revision_required">Revision Required</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small uppercase">Comments</label>
                            <textarea name="review_comments" id="comments-text" class="form-control bg-light border-0 py-2" rows="4" placeholder="Enter your comments about the proposal"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted small uppercase">Suggestions</label>
                            <textarea name="review_suggestions" id="suggestions-text" class="form-control bg-light border-0 py-2" rows="4" placeholder="Enter suggestions for improvement"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2">
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

            selectButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    const status = this.getAttribute('data-status');
                    const comments = this.getAttribute('data-comments');
                    const suggestions = this.getAttribute('data-suggestions');

                    // Update UI
                    document.querySelectorAll('.proposal-row').forEach(row => row.classList.remove('table-primary'));
                    document.getElementById('proposal-row-' + id).classList.add('table-primary');

                    // Fill Form
                    form.action = `/reviewer/proposals/${id}/update-status`;
                    titleDisplay.textContent = title;
                    statusSelect.value = status;
                    commentsText.value = comments === 'null' ? '' : comments;
                    suggestionsText.value = suggestions === 'null' ? '' : suggestions;

                    // Show Form
                    form.classList.remove('d-none');
                    noSelection.classList.add('d-none');
                });
            });
        });
    </script>
</x-app-layout>