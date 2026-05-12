<x-app-layout>
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-link text-muted p-0 text-decoration-none d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <!-- Main Header Banner -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="gradient-banner p-4 p-lg-5">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="display-6 fw-bold mb-3">{{ $proposal->title }}</h1>
                    <div class="d-flex align-items-center gap-4 opacity-90">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-person"></i> {{ $proposal->user->name }}
                        </span>
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar3"></i> Submitted: {{ $proposal->created_at->format('Y-m-d') }}
                        </span>
                    </div>
                </div>
                @php
                    $statusColor = match($proposal->status) {
                        'approved', 'final_approved' => '#10B981',
                        'pending' => '#F59E0B',
                        'rejected', 'final_rejected' => '#EF4444',
                        'revision_required' => '#3B82F6',
                        default => '#6B7280'
                    };
                @endphp
                <span class="badge py-2 px-4" style="background-color: rgba(255,255,255,0.2); backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.3); color: white;">
                    {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4">
                <small class="text-muted fw-bold text-uppercase mb-2 d-block">Phase</small>
                <h2 class="h3 fw-bold mb-0">Phase {{ $proposal->current_phase }}</h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4">
                <small class="text-muted fw-bold text-uppercase mb-2 d-block">Status</small>
                <h2 class="h3 fw-bold mb-0 text-capitalize">{{ str_replace('_', ' ', $proposal->status) }}</h2>
            </div>
        </div>
    </div>

    <!-- Reviewer Feedback Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="h5 fw-bold d-flex align-items-center gap-3 mb-4">
                <i class="bi bi-chat-left-text text-primary"></i> Reviewer Feedback
            </h3>

            <p class="text-muted mb-4 small fw-medium">Reviewed by: {{ $proposal->reviewer->name ?? 'Under Review' }}</p>

            <div class="mb-4">
                <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Comments:</label>
                <div class="feedback-box">
                    {{ $proposal->review_comments ?? 'No comments available yet.' }}
                </div>
            </div>

            <div>
                <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Suggestions:</label>
                <div class="feedback-box">
                    {{ $proposal->review_suggestions ?? 'No suggestions available yet.' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Document Preview Link -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center p-4">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-file-earmark-pdf text-danger h3 mb-0"></i>
                <div>
                    <h5 class="mb-0 fw-bold">Full Proposal Document</h5>
                    <small class="text-muted">PDF Format</small>
                </div>
            </div>
            <a href="{{ asset('storage/' . $proposal->document_path) }}" target="_blank" class="btn btn-outline-primary px-4">
                View PDF
            </a>
        </div>
    </div>
</x-app-layout>
