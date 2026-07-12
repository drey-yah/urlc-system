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
                        'funds_certified' => '#8B5CF6',
                        'final_copy_noted_by_dean' => '#6366F1',
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

    <!-- Workflow Actions Card -->
    @php
        $showActionsCard = false;
        $userRole = auth()->user()->role;
        $userId = auth()->id();
        
        if ($userRole === 'coordinator' && ($proposal->status === 'pending_coordinator_endorsement' || $proposal->status === 'noted_by_dean')) {
            $showActionsCard = true;
        } elseif ($userRole === 'dean' && ($proposal->status === 'pending_dean_noting' || $proposal->status === 'final_copy_submitted')) {
            $showActionsCard = true;
        } elseif ($userRole === 'staff' && $proposal->status === 'submitted_to_research_unit') {
            $showActionsCard = true;
        } elseif (($userRole === 'admin' || auth()->user()->isSuperAdmin()) && ($proposal->status === 'pending_director_review' || $proposal->status === 'funds_certified')) {
            $showActionsCard = true;
        } elseif ($userRole === 'vprei' && $proposal->status === 'endorsed_to_vprei') {
            $showActionsCard = true;
        } elseif ($userRole === 'budget_officer' && $proposal->status === 'final_copy_noted_by_dean') {
            $showActionsCard = true;
        } elseif ($userId === $proposal->user_id && $proposal->status === 'approved') {
            $showActionsCard = true;
        }
    @endphp

    @if($showActionsCard)
    <div class="card border-0 shadow-sm mb-4 border-start border-primary border-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="h5 fw-bold d-flex align-items-center gap-3 mb-3 text-primary">
                <i class="bi bi-gear-wide-connected fs-4"></i> Workflow Actions Required
            </h3>
            <p class="text-muted small mb-4">Please review the proposal details below and perform the required action for the next step in the workflow.</p>

            <!-- Coordinator Actions -->
            @if($userRole === 'coordinator')
                @if($proposal->status === 'pending_coordinator_endorsement')
                    <form action="{{ route('coordinator.proposals.endorse', $proposal->id) }}" method="POST" class="d-flex gap-3">
                        @csrf
                        <button type="submit" name="action" value="return" class="btn btn-outline-danger px-4 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Are you sure you want to return this proposal for revision?');">
                            <i class="bi bi-arrow-return-left fs-6"></i> Return for Revision
                        </button>
                        <button type="submit" name="action" value="endorse" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Are you sure you want to endorse this proposal?');">
                            <i class="bi bi-check2-circle fs-6"></i> Endorse & Forward to Dean
                        </button>
                    </form>
                @elseif($proposal->status === 'noted_by_dean')
                    <form action="{{ route('coordinator.proposals.submitToUnit', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Submit this noted proposal list to the Research Unit support staff?');">
                            <i class="bi bi-send-fill fs-6"></i> Submit to Research Unit
                        </button>
                    </form>
                @endif
            @endif

            <!-- Dean Actions -->
            @if($userRole === 'dean')
                @if($proposal->status === 'pending_dean_noting')
                    <form action="{{ route('dean.noteEndorsement', $proposal->id) }}" method="POST" class="d-flex gap-3">
                        @csrf
                        <button type="submit" name="action" value="return" class="btn btn-outline-danger px-4 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Are you sure you want to return this endorsement?');">
                            <i class="bi bi-arrow-return-left fs-6"></i> Return to Coordinator
                        </button>
                        <button type="submit" name="action" value="note" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Are you sure you want to note this endorsement?');">
                            <i class="bi bi-journal-check fs-6"></i> Note Endorsement
                        </button>
                    </form>
                @elseif($proposal->status === 'final_copy_submitted')
                    <form action="{{ route('dean.noteFinal', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Note the final copy submitted for this proposal?');">
                            <i class="bi bi-check-circle-fill fs-6"></i> Note Final Copy
                        </button>
                    </form>
                @endif
            @endif

            <!-- Support Staff Actions -->
            @if($userRole === 'staff' && $proposal->status === 'submitted_to_research_unit')
                <form action="{{ route('staff.proposals.forward', $proposal->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Forward this proposal to the Research Director for review?');">
                        <i class="bi bi-box-arrow-in-right fs-6"></i> Forward to Research Director
                    </button>
                </form>
            @endif

            <!-- Research Director (Admin) Actions -->
            @if($userRole === 'admin' || auth()->user()->isSuperAdmin())
                @if($proposal->status === 'pending_director_review')
                    <form action="{{ route('admin.proposals.acceptInHouse', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Accept this proposal and issue the In-House Review Acceptance Form?');">
                            <i class="bi bi-card-checklist fs-6"></i> Accept for In-House Review
                        </button>
                    </form>
                @elseif($proposal->status === 'funds_certified')
                    <form action="{{ route('admin.proposals.endorseVprei', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Endorse this revised proposal to the VPREI for final approval?');">
                            <i class="bi bi-award-fill fs-6"></i> Endorse to VPREI
                        </button>
                    </form>
                @endif
            @endif

            <!-- Budget Officer Actions -->
            @if($userRole === 'budget_officer' && $proposal->status === 'final_copy_noted_by_dean')
                <form action="{{ route('budget.certify', $proposal->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Certify this research proposal for availability of funds?');">
                        <i class="bi bi-cash-stack fs-6"></i> Certify Availability of Funds
                    </button>
                </form>
            @endif

            <!-- VPREI Actions -->
            @if($userRole === 'vprei' && $proposal->status === 'endorsed_to_vprei')
                <form action="{{ route('vprei.approve', $proposal->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Approve this proposal and issue the Notice to Proceed?');">
                        <i class="bi bi-check-circle-fill fs-6"></i> Grant Final Approval & Issue NTP
                    </button>
                </form>
            @endif

            <!-- Researcher Submit Final Copy Action -->
            @if($userId === $proposal->user_id && $proposal->status === 'approved')
                <form action="{{ route('proposal.submitFinalCopy', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload Final Copy of Research Proposal (PDF)</label>
                        <p class="text-muted small">Please submit the final version of your research proposal incorporating all reviewer suggestions. This is required before the Dean and Director can endorse it to VPREI.</p>
                        <input type="file" name="final_copy" class="form-control" accept=".pdf" required>
                    </div>
                    <button type="submit" class="btn btn-success px-5 py-2.5 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-cloud-upload-fill fs-6"></i> Submit Final Copy
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endif

    {{-- Collaborators section: visible to staff/admin roles only --}}
    @if(!in_array(auth()->user()->role, ['researcher']))
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="h5 fw-bold d-flex align-items-center gap-3 mb-4">
                <i class="bi bi-people text-info"></i> Collaborators
            </h3>

            @if($proposal->collaborators->isNotEmpty())
                <div class="d-flex flex-wrap gap-3">
                    @foreach($proposal->collaborators as $collab)
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 border bg-light" style="min-width: 220px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                             style="width: 42px; height: 42px; font-size: 1rem; background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                            {{ strtoupper(substr($collab->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-semibold text-dark small">{{ $collab->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                {{ $collab->department ? strtoupper($collab->department) : 'No Department' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0 small fst-italic">No collaborators listed for this proposal.</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Reviewer Feedback Section -->
    @if(auth()->user()->role !== 'coordinator')
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
    @endif

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
                                    <a href="{{ route('file.serve', ['path' => $doc->file_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
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
                    <a href="{{ route('file.serve', ['path' => $proposal->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary px-4">
                        View PDF
                    </a>
                </div>
            @else
                <p class="text-muted mb-0">No documents uploaded.</p>
            @endif
        </div>
    </div>

    <!-- Official Documents (Notice & Certificate) -->
    @if(in_array($proposal->status, ['accepted_for_in_house_review', 'revision_required', 'approved', 'final_copy_submitted', 'final_copy_noted_by_dean', 'endorsed_to_vprei', 'final_approved', 'ongoing', 'completed', 'archived']))
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-award text-warning"></i> Official Documents</h5>
            <div class="d-flex gap-3">
                <a href="{{ route('proposal.downloadNotice', $proposal->id) }}" class="btn btn-primary">
                    <i class="bi bi-download"></i> In-House Review Acceptance Form
                </a>
                
                @if(in_array($proposal->status, ['final_approved', 'ongoing', 'completed', 'archived']))
                <a href="{{ route('proposal.downloadNTP', $proposal->id) }}" class="btn btn-info text-white">
                    <i class="bi bi-download"></i> Notice to Proceed
                </a>
                @endif
                
                @if($proposal->current_phase >= 5)
                <a href="{{ route('proposal.downloadCertificate', $proposal->id) }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Certificate of Completion
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Phase 6: Final Manuscript Submission -->
    @if(auth()->id() == $proposal->user_id && $proposal->current_phase >= 5 && $proposal->status !== 'archived')
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">
                <h3 class="h5 fw-bold d-flex align-items-center gap-3 mb-4">
                    <i class="bi bi-journal-check text-primary"></i> Phase 6: Final Manuscript Submission
                </h3>
                <form action="{{ route('proposal.submitFinal', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Upload Final Manuscript (PDF)</label>
                        <input type="file" name="final_manuscript" class="form-control" accept=".pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Final Manuscript</button>
                </form>
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
                                <a href="{{ route('file.serve', ['path' => $milestone->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">View Attachment</a>
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
