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
                            <i class="bi bi-tag"></i> {{ $proposal->proposal_code ?? 'No Tag Yet' }}
                        </span>
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar3"></i> Submitted: {{ $proposal->created_at->format('Y-m-d') }}
                        </span>
                    </div>
                </div>
                @php
                    $statusColor = match($proposal->status) {
                        'approved', 'final_approved' => '#10B981',
                        'pending', 'pending_coordinator_endorsement' => '#F59E0B',
                        'endorsed_by_coordinator', 'revision_required' => '#3B82F6',
                        'rejected', 'final_rejected' => '#EF4444',
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

    <!-- Document History Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="h5 fw-bold d-flex align-items-center gap-3 mb-4">
                <i class="bi bi-file-earmark-pdf text-danger"></i> Document History
            </h3>
            
            @if($proposal->documents && $proposal->documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Document Tag</th>
                                <th>Version</th>
                                <th>Phase</th>
                                <th>Date Uploaded</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposal->documents->sortByDesc('version') as $doc)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $doc->document_tag }}</span></td>
                                <td>V{{ $doc->version }}</td>
                                <td>Phase {{ $doc->phase }}</td>
                                <td>{{ $doc->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> View PDF
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif($proposal->document_path)
                <!-- Fallback for old proposals -->
                <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                    <div>
                        <h6 class="mb-0 fw-bold">Original Proposal Document</h6>
                        <small class="text-muted">Legacy Upload</small>
                    </div>
                    <a href="{{ asset('storage/' . $proposal->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary px-4">
                        View PDF
                    </a>
                </div>
            @else
                <p class="text-muted mb-0">No documents uploaded.</p>
            @endif
        </div>
    </div>

    <!-- Official Documents (Notice & Certificate) -->
    @if(in_array($proposal->status, ['approved', 'final_approved']))
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-award text-warning"></i> Official Documents</h5>
            <div class="d-flex gap-3">
                <a href="{{ route('proposal.downloadNotice', $proposal->id) }}" class="btn btn-primary">
                    <i class="bi bi-download"></i> Notice of Acceptance
                </a>
                
                @if($proposal->current_phase == 5)
                <a href="{{ route('proposal.downloadCertificate', $proposal->id) }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Certificate of Completion
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Implementation Milestones -->
    @if($proposal->current_phase >= 4)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="h5 fw-bold d-flex align-items-center gap-3 mb-4">
                <i class="bi bi-bar-chart-steps text-success"></i> Implementation Progress
            </h3>

            @if($proposal->milestones && $proposal->milestones->count() > 0)
                <div class="mb-4">
                    @foreach($proposal->milestones as $milestone)
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $milestone->title }}</strong>
                                <span class="badge bg-{{ $milestone->status == 'approved' ? 'success' : ($milestone->status == 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($milestone->status) }}
                                </span>
                            </div>
                            <p class="text-muted small mt-2">{{ $milestone->description }}</p>
                            @if($milestone->document_path)
                                <a href="{{ asset('storage/' . $milestone->document_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">View Attachment</a>
                            @endif

                            @if(in_array(auth()->user()->role, ['admin', 'super_admin', 'reviewer']) && $milestone->status == 'pending')
                                <form action="{{ route('admin.milestones.updateStatus', $milestone->id) }}" method="POST" class="mt-3 d-inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <form action="{{ route('admin.milestones.updateStatus', $milestone->id) }}" method="POST" class="mt-3 d-inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted small">No milestones reported yet.</p>
            @endif

            <!-- Add New Milestone Form (Only for Researcher) -->
            @if(auth()->id() == $proposal->user_id && $proposal->current_phase < 5)
                <hr>
                <h5 class="fw-bold mt-4 mb-3">Submit Progress Report</h5>
                <form action="{{ route('milestones.store', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Title/Milestone Name</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description of Progress</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supporting Document (Optional)</label>
                        <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </form>
            @endif
        </div>
    </div>
    @endif
</x-app-layout>
