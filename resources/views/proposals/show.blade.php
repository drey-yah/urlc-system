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

    <!-- Phase 2: Implementation, Monitoring & Evaluation Workspace -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 px-lg-5 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="h5 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-diagram-3-fill text-success"></i> Phase 2: Implementation, Monitoring & Evaluation Workspace
                </h3>
                <p class="text-muted small mb-0">Multi-step document sign-off & submission hub for Activity Designs, Purchase Requests, Monitoring Forms, and Terminal Reports.</p>
            </div>
            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-4 py-2 fw-bold">Phase 2 Active</span>
        </div>

        <div class="card-body p-4 p-lg-5">
            <div class="row g-4">
                <!-- 1. Activity Design & Budget Clearance (Steps 1 & 2) -->
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-4 h-100 shadow-xs">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold">Step 1 & 2</span>
                                <h6 class="fw-bold text-dark mb-0">Activity Design & Budget</h6>
                            </div>
                            <p class="text-muted small mb-3">Submission and sign-off for Activity Design (HRU-FM-021) and Proposed Budgetary Requirement (BU-FM-006).</p>
                            
                            @php
                                $latestActivity = $proposal->activityDesigns->last();
                            @endphp

                            @if($latestActivity)
                                <div class="bg-light p-3 rounded-3 mb-3 border">
                                    <div class="fw-bold text-dark small">{{ $latestActivity->activity_title }}</div>
                                    <div class="text-muted fs-7">Venue: {{ $latestActivity->venue ?? 'N/A' }} | Date: {{ $latestActivity->target_date ? $latestActivity->target_date->format('M d, Y') : 'N/A' }}</div>
                                    <div class="fw-bold text-success fs-7 mt-1">Proposed Budget: ₱{{ number_format($latestActivity->proposed_budget, 2) }}</div>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @if($latestActivity->activity_design_file)
                                            <a href="{{ route('file.serve', ['path' => $latestActivity->activity_design_file]) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill">
                                                <i class="bi bi-file-earmark-pdf"></i> View HRU-FM-021
                                            </a>
                                        @endif
                                        @if($latestActivity->budget_requirement_file)
                                            <a href="{{ route('file.serve', ['path' => $latestActivity->budget_requirement_file]) }}" target="_blank" class="btn btn-xs btn-outline-success rounded-pill">
                                                <i class="bi bi-file-earmark-pdf"></i> View BU-FM-006
                                            </a>
                                        @endif
                                    </div>
                                    <div class="mt-2 pt-2 border-top">
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1 small">
                                            Status: {{ strtoupper(str_replace('_', ' ', $latestActivity->status)) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Buttons for Officials -->
                                @if(auth()->user()->role === 'admin' && !$latestActivity->director_noted)
                                    <form action="{{ route('admin.phase2.noteActivity', $latestActivity->id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill shadow-sm" onclick="return confirm('Note Activity Design as Research Director?');">
                                            <i class="bi bi-check-circle me-1"></i> Director Note (HRU-FM-021)
                                        </button>
                                    </form>
                                @endif

                                @if(auth()->user()->role === 'budget_officer' && $latestActivity->director_noted && !$latestActivity->budget_officer_noted)
                                    <form action="{{ route('budget.phase2.noteActivity', $latestActivity->id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill shadow-sm" onclick="return confirm('Note Budgetary Requirement as Budget Officer?');">
                                            <i class="bi bi-cash-stack me-1"></i> Budget Officer Note (BU-FM-006)
                                        </button>
                                    </form>
                                @endif

                                @if(auth()->user()->role === 'vprei' && $latestActivity->budget_officer_noted && !$latestActivity->vprei_approved)
                                    <form action="{{ route('vprei.phase2.approveActivity', $latestActivity->id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill shadow-sm" onclick="return confirm('Approve Activity Design as VPREI?');">
                                            <i class="bi bi-award me-1"></i> VPREI Approve Activity Design
                                        </button>
                                    </form>
                                @endif
                            @else
                                <p class="text-muted fs-7 italic mb-3">No Activity Design submitted yet.</p>
                            @endif

                            <!-- Researcher Upload Form -->
                            @if(auth()->id() == $proposal->user_id)
                                <button class="btn btn-sm btn-outline-primary w-100 rounded-pill" data-bs-toggle="collapse" data-bs-target="#activityDesignForm">
                                    <i class="bi bi-upload me-1"></i> Submit Activity Design & Budget
                                </button>
                                <div class="collapse mt-3" id="activityDesignForm">
                                    <form action="{{ route('activity_design.store', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-2">
                                            <input type="text" name="activity_title" class="form-control form-control-sm" placeholder="Activity Title" required>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <input type="text" name="venue" class="form-control form-control-sm" placeholder="Venue">
                                            </div>
                                            <div class="col-6">
                                                <input type="date" name="target_date" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <input type="number" step="0.01" min="0" name="proposed_budget" class="form-control form-control-sm" placeholder="Proposed Budget (₱)" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fs-7 fw-bold mb-1">Signed HRU-FM-021 (PDF)</label>
                                            <input type="file" name="activity_design_file" class="form-control form-control-sm" accept=".pdf">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fs-7 fw-bold mb-1">Signed BU-FM-006 (PDF)</label>
                                            <input type="file" name="budget_requirement_file" class="form-control form-control-sm" accept=".pdf">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill">Upload & Submit</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 2. Purchase Request (Steps 3 & 4) -->
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-4 h-100 shadow-xs">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-bold">Step 3 & 4</span>
                                <h6 class="fw-bold text-dark mb-0">Purchase Request (PR)</h6>
                            </div>
                            <p class="text-muted small mb-3">Procurement flow for supplies, materials, and equipment.</p>

                            @php
                                $latestPR = $proposal->purchaseRequests->last();
                            @endphp

                            @if($latestPR)
                                <div class="bg-light p-3 rounded-3 mb-3 border">
                                    <div class="fw-bold text-dark small">PR #{{ $latestPR->id }} {{ $latestPR->pr_number ? '('.$latestPR->pr_number.')' : '' }}</div>
                                    <div class="text-muted fs-7">Purpose: {{ $latestPR->purpose }}</div>
                                    <div class="fw-bold text-success fs-7 mt-1">Total Amount: ₱{{ number_format($latestPR->total_amount, 2) }}</div>
                                    @if($latestPR->document_path)
                                        <div class="mt-2">
                                            <a href="{{ route('file.serve', ['path' => $latestPR->document_path]) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill">
                                                <i class="bi bi-file-earmark-pdf"></i> View Signed PR PDF
                                            </a>
                                        </div>
                                    @endif
                                    <div class="mt-2 pt-2 border-top">
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 small">
                                            Status: {{ strtoupper(str_replace('_', ' ', $latestPR->status)) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Buttons for Officials -->
                                @if(auth()->user()->role === 'admin' && !$latestPR->director_countersigned)
                                    <form action="{{ route('admin.phase2.countersignPR', $latestPR->id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill shadow-sm" onclick="return confirm('Countersign Purchase Request as Director?');">
                                            <i class="bi bi-pencil-square me-1"></i> Director Countersign PR
                                        </button>
                                    </form>
                                @endif

                                @if(auth()->user()->role === 'sao_finance' && $latestPR->director_countersigned && !$latestPR->finance_approved)
                                    <form action="{{ route('finance.pr.approve', $latestPR->id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill shadow-sm" onclick="return confirm('Approve PR for procurement as Finance Officer?');">
                                            <i class="bi bi-check-circle me-1"></i> Approve Procurement PR
                                        </button>
                                    </form>
                                @endif
                            @else
                                <p class="text-muted fs-7 italic mb-3">No Purchase Request created yet.</p>
                            @endif

                            <!-- Researcher Upload Form -->
                            @if(auth()->id() == $proposal->user_id)
                                <button class="btn btn-sm btn-outline-success w-100 rounded-pill" data-bs-toggle="collapse" data-bs-target="#prForm">
                                    <i class="bi bi-upload me-1"></i> Submit Purchase Request
                                </button>
                                <div class="collapse mt-3" id="prForm">
                                    <form action="{{ route('purchase_request.store', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-2">
                                            <input type="text" name="purpose" class="form-control form-control-sm" placeholder="PR Purpose / Materials Description" required>
                                        </div>
                                        <div class="mb-2">
                                            <input type="number" step="0.01" min="0" name="total_amount" class="form-control form-control-sm" placeholder="Total PR Amount (₱)" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fs-7 fw-bold mb-1">Upload Signed PR PDF</label>
                                            <input type="file" name="document_path" class="form-control form-control-sm" accept=".pdf">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill">Upload PR</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 3. Research Project Monitoring Form (Step 7) -->
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-4 h-100 shadow-xs">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 fw-bold">Step 7</span>
                                <h6 class="fw-bold text-dark mb-0">Project Monitoring (RESU-FM-014)</h6>
                            </div>
                            <p class="text-muted small mb-3">Periodic progress monitoring and evaluation by College Research Coordinator.</p>

                            @php
                                $latestMonitoring = $proposal->projectMonitorings->last();
                            @endphp

                            @if($latestMonitoring)
                                <div class="bg-light p-3 rounded-3 mb-3 border">
                                    <div class="fw-bold text-dark small">Period: {{ $latestMonitoring->period_covered }}</div>
                                    <div class="text-muted fs-7">{{ $latestMonitoring->progress_summary }}</div>
                                    @if($latestMonitoring->monitoring_form_path)
                                        <div class="mt-2">
                                            <a href="{{ route('file.serve', ['path' => $latestMonitoring->monitoring_form_path]) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill">
                                                <i class="bi bi-file-earmark-pdf"></i> View RESU-FM-014 PDF
                                            </a>
                                        </div>
                                    @endif
                                    <div class="mt-2 pt-2 border-top">
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 small">
                                            Status: {{ strtoupper($latestMonitoring->status) }}
                                        </span>
                                    </div>
                                </div>

                                @if(auth()->user()->role === 'coordinator' && !$latestMonitoring->coordinator_verified)
                                    <form action="{{ route('coordinator.phase2.verify', $latestMonitoring->id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill shadow-sm" onclick="return confirm('Verify monitoring form as College Coordinator?');">
                                            <i class="bi bi-check-circle me-1"></i> Coordinator Verify (RESU-FM-014)
                                        </button>
                                    </form>
                                @endif
                            @else
                                <p class="text-muted fs-7 italic mb-3">No Monitoring Form submitted yet.</p>
                            @endif

                            @if(auth()->id() == $proposal->user_id)
                                <button class="btn btn-sm btn-outline-warning text-dark w-100 rounded-pill" data-bs-toggle="collapse" data-bs-target="#monitoringForm">
                                    <i class="bi bi-upload me-1"></i> Submit Monitoring Form (RESU-FM-014)
                                </button>
                                <div class="collapse mt-3" id="monitoringForm">
                                    <form action="{{ route('monitoring.store', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-2">
                                            <input type="text" name="period_covered" class="form-control form-control-sm" placeholder="Period Covered (e.g. Q1 2026)" required>
                                        </div>
                                        <div class="mb-2">
                                            <textarea name="progress_summary" class="form-control form-control-sm" rows="2" placeholder="Brief Progress Summary"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fs-7 fw-bold mb-1">Signed RESU-FM-014 (PDF)</label>
                                            <input type="file" name="monitoring_form_path" class="form-control form-control-sm" accept=".pdf">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-warning text-dark w-100 rounded-pill">Submit Form</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 4. Terminal Report & Completion (Steps 8-11) -->
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-4 h-100 shadow-xs">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-purple bg-opacity-10 text-purple rounded-pill px-3 py-1 fw-bold" style="color: #8B5CF6;">Steps 8-11</span>
                                <h6 class="fw-bold text-dark mb-0">Terminal Report & Completion</h6>
                            </div>
                            <p class="text-muted small mb-3">Terminal Report (RESU-FM-017), Panel Evaluation (RESU-FM-001), & Completion Certificate (RESU-FM-028).</p>

                            @php
                                $terminal = $proposal->terminalReport;
                            @endphp

                            @if($terminal)
                                <div class="bg-light p-3 rounded-3 mb-3 border">
                                    <div class="fw-bold text-dark small">Status: {{ strtoupper(str_replace('_', ' ', $terminal->status)) }}</div>
                                    @if($terminal->evaluator_score)
                                        <div class="fw-bold text-success fs-7 mt-1">Evaluator Score: {{ $terminal->evaluator_score }} / 100</div>
                                    @endif
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @if($terminal->terminal_report_file)
                                            <a href="{{ route('file.serve', ['path' => $terminal->terminal_report_file]) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill">
                                                <i class="bi bi-file-earmark-pdf"></i> View RESU-FM-017
                                            </a>
                                        @endif
                                        @if($terminal->full_paper_file)
                                            <a href="{{ route('file.serve', ['path' => $terminal->full_paper_file]) }}" target="_blank" class="btn btn-xs btn-outline-secondary rounded-pill">
                                                <i class="bi bi-file-earmark-text"></i> View Full Paper
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                @if(auth()->user()->role === 'reviewer' && $terminal->status === 'submitted_to_unit')
                                    <button class="btn btn-sm btn-primary w-100 rounded-pill mb-2" data-bs-toggle="collapse" data-bs-target="#evaluateTerminalForm">
                                        <i class="bi bi-pencil-square me-1"></i> Submit Panel Evaluation (RESU-FM-001)
                                    </button>
                                    <div class="collapse mb-3" id="evaluateTerminalForm">
                                        <form action="{{ route('reviewer.phase2.evaluate', $terminal->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-2">
                                                <input type="number" step="0.1" min="0" max="100" name="evaluator_score" class="form-control form-control-sm" placeholder="Evaluation Score (0-100)" required>
                                            </div>
                                            <div class="mb-2">
                                                <textarea name="evaluator_comments" class="form-control form-control-sm" rows="2" placeholder="Evaluator Feedback / Comments"></textarea>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label fs-7 fw-bold mb-1">Upload RESU-FM-001 (PDF)</label>
                                                <input type="file" name="evaluation_form_file" class="form-control form-control-sm" accept=".pdf">
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill">Submit Evaluation</button>
                                        </form>
                                    </div>
                                @endif

                                @if(auth()->user()->role === 'admin' && $terminal->status === 'final_report_submitted')
                                    <form action="{{ route('admin.phase2.issueCompletion', $terminal->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill shadow-sm" onclick="return confirm('Issue Certificate of Research Completion (RESU-FM-028) and complete project?');">
                                            <i class="bi bi-award-fill me-1"></i> Issue Certificate of Completion (RESU-FM-028)
                                        </button>
                                    </form>
                                @endif
                            @else
                                <p class="text-muted fs-7 italic mb-3">No Terminal Report submitted yet.</p>
                            @endif

                            @if(auth()->id() == $proposal->user_id)
                                <button class="btn btn-sm btn-outline-purple text-purple w-100 rounded-pill" style="color: #8B5CF6; border-color: #8B5CF6;" data-bs-toggle="collapse" data-bs-target="#terminalForm">
                                    <i class="bi bi-upload me-1"></i> Submit Terminal Report (RESU-FM-017)
                                </button>
                                <div class="collapse mt-3" id="terminalForm">
                                    <form action="{{ route('terminal_report.store', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-2">
                                            <textarea name="executive_summary" class="form-control form-control-sm" rows="2" placeholder="Executive Summary"></textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fs-7 fw-bold mb-1">Signed RESU-FM-017 (PDF)</label>
                                            <input type="file" name="terminal_report_file" class="form-control form-control-sm" accept=".pdf">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fs-7 fw-bold mb-1">Full Research Manuscript (PDF)</label>
                                            <input type="file" name="full_paper_file" class="form-control form-control-sm" accept=".pdf">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill">Submit Terminal Report</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phase 3: Research Dissemination & Presentation Workspace (Appendix B) -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 px-lg-5 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="h5 fw-bold d-flex align-items-center gap-2 text-dark mb-1">
                    <i class="bi bi-easel-fill" style="color: #8B5CF6;"></i> Phase 3: Research Dissemination & Presentation Workspace (Appendix B)
                </h3>
                <p class="text-muted fs-7 mb-0">Full authorization workflow for Oral & Poster Presentation of Research Outputs, Sponsoring Agency Acceptance, and SUC President Sign-off.</p>
            </div>
            @if($proposal->current_phase >= 2)
                <span class="badge bg-purple bg-opacity-10 rounded-pill px-3 py-2 fw-medium fs-7" style="color: #8B5CF6;">
                    <i class="bi bi-broadcast me-1"></i> Phase 3 Active
                </span>
            @endif
        </div>

        <div class="card-body p-4 p-lg-5">
            @forelse($proposal->researchPresentations as $pres)
                <div class="card border mb-4 rounded-4 shadow-sm overflow-hidden">
                    <div class="card-header bg-light py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="badge {{ $pres->presentation_type === 'oral' ? 'bg-primary' : 'bg-info' }} text-white text-uppercase me-2 fw-bold fs-7">
                                {{ strtoupper($pres->presentation_type) }} PRESENTATION
                            </span>
                            <strong class="text-dark fs-6">{{ $pres->presentation_title }}</strong>
                        </div>
                        <div>
                            @if($pres->status === 'completed')
                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Presented & Completed</span>
                            @elseif($pres->status === 'approved_by_president')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="bi bi-award-fill me-1"></i> Approved by SUC President</span>
                            @elseif($pres->status === 'recommended_to_president')
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><i class="bi bi-hand-thumbs-up me-1"></i> Endorsed to President</span>
                            @elseif($pres->status === 'agency_rejected')
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2"><i class="bi bi-x-circle me-1"></i> Rejected by Agency</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2"><i class="bi bi-hourglass-split me-1"></i> In Progress ({{ strtoupper(str_replace('_', ' ', $pres->status)) }})</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="text-muted fs-7">Sponsoring Organization / Agency</div>
                                <div class="fw-bold text-dark">{{ $pres->sponsoring_agency }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted fs-7">Conference / Event Name</div>
                                <div class="fw-semibold text-dark">{{ $pres->conference_name }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted fs-7">Event Date & Venue</div>
                                <div class="fw-medium text-dark">
                                    {{ $pres->event_date ? $pres->event_date->format('M d, Y') : 'Date TBD' }} 
                                    {{ $pres->venue ? '('.$pres->venue.')' : '' }}
                                </div>
                            </div>
                        </div>

                        <!-- Grid of Steps 1-6 -->
                        <div class="row g-3">
                            <!-- Step 1 & 2: Agency Acceptance -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase">1. Agency Letter of Acceptance</h6>
                                        @if($pres->acceptance_letter_path)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fs-7">Uploaded</span>
                                        @endif
                                    </div>
                                    @if($pres->acceptance_letter_path)
                                        <a href="{{ route('file.serve', ['path' => $pres->acceptance_letter_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill mt-2">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> View Acceptance Letter
                                        </a>
                                    @else
                                        <p class="text-muted fs-7 italic mb-2">No acceptance letter uploaded yet.</p>
                                        @if(auth()->id() == $proposal->user_id)
                                            <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="collapse" data-bs-target="#acceptanceForm{{ $pres->id }}">
                                                <i class="bi bi-upload me-1"></i> Upload Acceptance Result
                                            </button>
                                            <div class="collapse mt-2" id="acceptanceForm{{ $pres->id }}">
                                                <form action="{{ route('presentation.uploadAcceptance', $pres->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <select name="decision" class="form-select form-select-sm" required>
                                                            <option value="accepted">Accepted for Presentation</option>
                                                            <option value="rejected">Rejected by Sponsoring Agency</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label fs-7 fw-bold mb-1">Letter of Acceptance (PDF/Image)</label>
                                                        <input type="file" name="acceptance_letter_file" class="form-control form-control-sm">
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill">Submit Result</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Step 3: Full Paper / Poster Presentation File -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase">2. Presentation Slides / Poster Deck</h6>
                                        @if($pres->presentation_file_path)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fs-7">Uploaded</span>
                                        @endif
                                    </div>
                                    @if($pres->presentation_file_path)
                                        <a href="{{ route('file.serve', ['path' => $pres->presentation_file_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill mt-2">
                                            <i class="bi bi-file-earmark-slides me-1"></i> View Presentation File
                                        </a>
                                    @else
                                        <p class="text-muted fs-7 italic mb-2">No presentation slides/poster uploaded yet.</p>
                                        @if(auth()->id() == $proposal->user_id)
                                            <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="collapse" data-bs-target="#presentationFileForm{{ $pres->id }}">
                                                <i class="bi bi-upload me-1"></i> Upload Slides / Poster
                                            </button>
                                            <div class="collapse mt-2" id="presentationFileForm{{ $pres->id }}">
                                                <form action="{{ route('presentation.uploadPresentation', $pres->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label class="form-label fs-7 fw-bold mb-1">Slides / Poster Deck (PDF/PPT/PPTX)</label>
                                                        <input type="file" name="presentation_file" class="form-control form-control-sm" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill">Upload Slides</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Step 4 & 5: Director Recommendation & SUC President Approval -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <h6 class="fw-bold text-dark mb-2 fs-7 text-uppercase">3. Director & SUC President Sign-Off</h6>
                                    
                                    <!-- Recommendation Status -->
                                    <div class="mb-2">
                                        @if($pres->director_recommended)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fs-7">
                                                <i class="bi bi-check-circle me-1"></i> Recommended by Director on {{ $pres->director_recommended_at ? $pres->director_recommended_at->format('M d, Y') : '' }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 fs-7">
                                                <i class="bi bi-clock me-1"></i> Awaiting Director Recommendation
                                            </span>
                                            @if(in_array(auth()->user()->role, ['admin', 'vprei', 'president']) && $pres->acceptance_letter_path)
                                                <form action="{{ route('presentation.recommend', $pres->id) }}" method="POST" class="mt-2">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100 shadow-sm" onclick="return confirm('Recommend this presentation to the SUC President?');">
                                                        <i class="bi bi-hand-thumbs-up me-1"></i> Recommend to SUC President
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- President Approval Status -->
                                    <div>
                                        @if($pres->president_approved)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fs-7">
                                                <i class="bi bi-award-fill me-1"></i> Approved by SUC President on {{ $pres->president_approved_at ? $pres->president_approved_at->format('M d, Y') : '' }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1 fs-7">
                                                <i class="bi bi-clock me-1"></i> Awaiting SUC President Approval
                                            </span>
                                            @if(in_array(auth()->user()->role, ['admin', 'vprei', 'president']) && $pres->director_recommended)
                                                <form action="{{ route('presentation.approve', $pres->id) }}" method="POST" class="mt-2">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill w-100 shadow-sm" onclick="return confirm('Officially approve this research output presentation as SUC President?');">
                                                        <i class="bi bi-check-circle me-1"></i> Approve for Oral/Poster Presentation
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Step 6: Presentation Execution & Certificate -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase">4. Certificate of Presentation</h6>
                                        @if($pres->certificate_path)
                                            <span class="badge bg-success rounded-pill px-2 py-1 fs-7"><i class="bi bi-check-lg me-1"></i> Completed</span>
                                        @endif
                                    </div>
                                    @if($pres->certificate_path)
                                        <a href="{{ route('file.serve', ['path' => $pres->certificate_path]) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill mt-2">
                                            <i class="bi bi-award me-1"></i> View Presentation Certificate
                                        </a>
                                    @else
                                        <p class="text-muted fs-7 italic mb-2">No certificate uploaded yet.</p>
                                        @if(auth()->id() == $proposal->user_id && $pres->president_approved)
                                            <button class="btn btn-sm btn-outline-success rounded-pill" data-bs-toggle="collapse" data-bs-target="#certForm{{ $pres->id }}">
                                                <i class="bi bi-upload me-1"></i> Upload Certificate of Presentation
                                            </button>
                                            <div class="collapse mt-2" id="certForm{{ $pres->id }}">
                                                <form action="{{ route('presentation.uploadCertificate', $pres->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label class="form-label fs-7 fw-bold mb-1">Certificate of Presentation (PDF/Image)</label>
                                                        <input type="file" name="certificate_file" class="form-control form-control-sm" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill">Upload Certificate</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 bg-light rounded-4 border mb-3">
                    <i class="bi bi-easel fs-2 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-2 fw-medium">No oral or poster presentation details logged for this research output yet.</p>
                </div>
            @endforelse

            <!-- Researcher New Presentation Submission Form -->
            @if(auth()->id() == $proposal->user_id)
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="collapse" data-bs-target="#newPresentationForm">
                    <i class="bi bi-plus-circle me-2"></i> Log Research Output for Oral/Poster Presentation
                </button>
                <div class="collapse mt-3" id="newPresentationForm">
                    <div class="card border rounded-4 p-4 bg-light">
                        <h6 class="fw-bold text-dark mb-3">Log Presentation & Conference Details</h6>
                        <form action="{{ route('presentation.store', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Presentation Title</label>
                                    <input type="text" name="presentation_title" class="form-control" value="{{ $proposal->title }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Presentation Type</label>
                                    <select name="presentation_type" class="form-select" required>
                                        <option value="oral">Oral Presentation</option>
                                        <option value="poster">Poster Presentation</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Sponsoring Organization / Agency</label>
                                    <input type="text" name="sponsoring_agency" class="form-control" placeholder="e.g. DOST, NRCP, PSSN" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Conference / Event Name</label>
                                    <input type="text" name="conference_name" class="form-control" placeholder="e.g. 15th National Research Conference" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Event Date</label>
                                    <input type="date" name="event_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Venue / Location</label>
                                    <input type="text" name="venue" class="form-control" placeholder="e.g. Manila, Philippines / Hybrid">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Letter of Acceptance (Optional at this stage)</label>
                                    <input type="file" name="acceptance_letter_file" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Presentation Slides / Poster Deck (Optional at this stage)</label>
                                    <input type="file" name="presentation_file" class="form-control">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-send me-1"></i> Log Presentation & Start Phase 3 Flow
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Phase 4: Publication of Research Outputs Workspace (Appendix C) -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 px-lg-5 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="h5 fw-bold d-flex align-items-center gap-2 text-dark mb-1">
                    <i class="bi bi-journal-check text-success"></i> Phase 4: Publication of Research Outputs Workspace (Appendix C)
                </h3>
                <p class="text-muted fs-7 mb-0">Letter of intent submission, IP screening/clearance by VPREI & IEDC, refereed journal submission tracking, and final publication archiving.</p>
            </div>
            @if($proposal->current_phase >= 3 || $proposal->researchPublications->count() > 0)
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-medium fs-7">
                    <i class="bi bi-journal-bookmark-fill me-1"></i> Phase 4 Active
                </span>
            @endif
        </div>

        <div class="card-body p-4 p-lg-5">
            @forelse($proposal->researchPublications as $pub)
                <div class="card border mb-4 rounded-4 shadow-sm overflow-hidden">
                    <div class="card-header bg-light py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="badge bg-dark text-white text-uppercase me-2 fw-bold fs-7">
                                {{ $pub->indexing_agency ?? 'REFEREED JOURNAL' }}
                            </span>
                            <strong class="text-dark fs-6">{{ $pub->journal_title }}</strong>
                            @if($pub->issn_number)
                                <span class="text-muted fs-7 ms-2">(ISSN: {{ $pub->issn_number }})</span>
                            @endif
                        </div>
                        <div>
                            @if($pub->status === 'published_and_archived')
                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-patch-check-fill me-1"></i> Published & Archived</span>
                            @elseif($pub->status === 'submitted_to_journal')
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2"><i class="bi bi-send-check me-1"></i> Submitted to Journal</span>
                            @elseif($pub->status === 'approved_for_publication')
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Approved for Publication</span>
                            @elseif($pub->status === 'ip_registration_required')
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2"><i class="bi bi-exclamation-triangle me-1"></i> IP Registration Required</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2"><i class="bi bi-hourglass-split me-1"></i> Intent Submitted (IP Screening)</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Step 1: Letter of Intent to Publish -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase">1. Letter of Intent to Publish</h6>
                                        @if($pub->intent_letter_path)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fs-7">Submitted</span>
                                        @endif
                                    </div>
                                    <p class="text-muted fs-7 mb-2">Official letter of intent to publish in a refereed/indexed journal.</p>
                                    @if($pub->intent_letter_path)
                                        <a href="{{ route('file.serve', ['path' => $pub->intent_letter_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> View Letter of Intent
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Step 2: IP Potential Screening & Clearance -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase">2. IP Potential & VPREI Clearance</h6>
                                        @if($pub->vprei_approved)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fs-7">Approved</span>
                                        @endif
                                    </div>

                                    @if($pub->vprei_approved)
                                        <div class="alert alert-success py-2 px-3 mb-0 fs-7 rounded-3 border-0">
                                            <i class="bi bi-check-circle-fill me-1"></i> <strong>IP Cleared & VPREI Authorized</strong> on {{ $pub->vprei_approved_at ? $pub->vprei_approved_at->format('M d, Y') : '' }}.
                                            @if($pub->has_ip_potential)
                                                <div class="mt-1"><small class="badge bg-warning text-dark">IP Registered</small></div>
                                            @endif
                                        </div>
                                        @if($pub->ip_registration_file_path)
                                            <a href="{{ route('file.serve', ['path' => $pub->ip_registration_file_path]) }}" target="_blank" class="btn btn-sm btn-link text-decoration-none p-0 mt-2 fs-7">
                                                <i class="bi bi-shield-check me-1"></i> View IP Registration Evidence
                                            </a>
                                        @endif
                                    @elseif($pub->status === 'ip_registration_required')
                                        <div class="alert alert-warning py-2 px-3 mb-2 fs-7 rounded-3 border-0">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>IP Potential Detected!</strong> Please submit proof of Intellectual Property (IP) Registration application.
                                        </div>
                                        @if(auth()->id() == $proposal->user_id)
                                            <button class="btn btn-sm btn-warning rounded-pill text-dark fw-bold" data-bs-toggle="collapse" data-bs-target="#ipProofForm{{ $pub->id }}">
                                                <i class="bi bi-upload me-1"></i> Submit IP Registration Proof
                                            </button>
                                            <div class="collapse mt-2" id="ipProofForm{{ $pub->id }}">
                                                <form action="{{ route('publication.uploadIpRegistration', $pub->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label class="form-label fs-7 fw-bold">IP Application File (PDF/Image)</label>
                                                        <input type="file" name="ip_registration_file" class="form-control form-control-sm" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-dark w-100 rounded-pill">Upload Proof & Request Clearance</button>
                                                </form>
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-muted fs-7 mb-2 italic">Awaiting IP Screening review by VPREI, Research Director & IEDC.</p>
                                        @if(in_array(auth()->user()->role, ['admin', 'vprei', 'coordinator']))
                                            <button class="btn btn-sm btn-outline-dark rounded-pill" data-bs-toggle="collapse" data-bs-target="#ipScreenForm{{ $pub->id }}">
                                                <i class="bi bi-shield-lock me-1"></i> Perform IP Screening
                                            </button>
                                            <div class="collapse mt-2" id="ipScreenForm{{ $pub->id }}">
                                                <form action="{{ route('publication.screenIp', $pub->id) }}" method="POST" class="p-3 bg-white border rounded-3">
                                                    @csrf
                                                    <label class="form-label fs-7 fw-bold mb-2 text-dark">Does this output have Intellectual Property (IP) Potential?</label>
                                                    <div class="d-flex gap-3 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="has_ip_potential" value="1" id="ipYes{{ $pub->id }}" required>
                                                            <label class="form-check-label fs-7 fw-semibold text-warning" for="ipYes{{ $pub->id }}">
                                                                YES (Require IP Registration)
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="has_ip_potential" value="0" id="ipNo{{ $pub->id }}" required>
                                                            <label class="form-check-label fs-7 fw-semibold text-success" for="ipNo{{ $pub->id }}">
                                                                NO (Approve for Journal)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <input type="text" name="ip_notes" class="form-control form-control-sm" placeholder="Review notes / comments (optional)">
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill">Save Screening Decision</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Step 3: Journal Submission Proof -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase">3. Journal Submission Proof</h6>
                                        @if($pub->submission_proof_path)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fs-7">Submitted</span>
                                        @endif
                                    </div>
                                    @if($pub->submission_proof_path)
                                        <a href="{{ route('file.serve', ['path' => $pub->submission_proof_path]) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill mt-1">
                                            <i class="bi bi-file-earmark-check me-1"></i> View Submission Receipt
                                        </a>
                                    @else
                                        <p class="text-muted fs-7 italic mb-2">No journal submission proof logged yet.</p>
                                        @if(auth()->id() == $proposal->user_id && $pub->vprei_approved)
                                            <button class="btn btn-sm btn-outline-info rounded-pill" data-bs-toggle="collapse" data-bs-target="#journalSubForm{{ $pub->id }}">
                                                <i class="bi bi-upload me-1"></i> Log Proof of Journal Submission
                                            </button>
                                            <div class="collapse mt-2" id="journalSubForm{{ $pub->id }}">
                                                <form action="{{ route('publication.logJournalSubmission', $pub->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label class="form-label fs-7 fw-bold">Submission Proof / Receipt (PDF/Image)</label>
                                                        <input type="file" name="submission_proof" class="form-control form-control-sm" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-info text-white w-100 rounded-pill">Log Journal Submission</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Step 4: Final Published Copy & Archival -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase">4. Refereed Published Journal Copy</h6>
                                        @if($pub->published_copy_path)
                                            <span class="badge bg-success rounded-pill px-2 py-1 fs-7"><i class="bi bi-archive-fill me-1"></i> Archived</span>
                                        @endif
                                    </div>
                                    @if($pub->published_copy_path)
                                        <div class="d-flex flex-column gap-2 mt-1">
                                            <a href="{{ route('file.serve', ['path' => $pub->published_copy_path]) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill">
                                                <i class="bi bi-journal-text me-1"></i> View Published Journal PDF
                                            </a>
                                            @if($pub->doi_link)
                                                <a href="{{ $pub->doi_link }}" target="_blank" class="btn btn-sm btn-link text-decoration-none p-0 fs-7">
                                                    <i class="bi bi-link-45deg me-1"></i> DOI Link: {{ $pub->doi_link }}
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-muted fs-7 italic mb-2">No final published copy uploaded to Research Center yet.</p>
                                        @if(auth()->id() == $proposal->user_id && $pub->vprei_approved)
                                            <button class="btn btn-sm btn-outline-success rounded-pill" data-bs-toggle="collapse" data-bs-target="#pubCopyForm{{ $pub->id }}">
                                                <i class="bi bi-upload me-1"></i> Submit Published Copy to Research Center
                                            </button>
                                            <div class="collapse mt-2" id="pubCopyForm{{ $pub->id }}">
                                                <form action="{{ route('publication.archivePublishedCopy', $pub->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label class="form-label fs-7 fw-bold">Published Journal Copy (PDF)</label>
                                                        <input type="file" name="published_copy" class="form-control form-control-sm" accept=".pdf" required>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label fs-7 fw-bold">DOI / Journal Article URL (Optional)</label>
                                                        <input type="url" name="doi_link" class="form-control form-control-sm" placeholder="https://doi.org/10.xxxx/xxxx">
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill">Archive Published Copy</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 bg-light rounded-4 border mb-3">
                    <i class="bi bi-journal-bookmark fs-2 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-2 fw-medium">No publication intents logged for this research output yet.</p>
                </div>
            @endforelse

            <!-- Researcher New Intent to Publish Form -->
            @if(auth()->id() == $proposal->user_id)
                <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="collapse" data-bs-target="#newIntentForm">
                    <i class="bi bi-plus-circle me-2"></i> Submit Letter of Intent to Publish (Appendix C)
                </button>
                <div class="collapse mt-3" id="newIntentForm">
                    <div class="card border rounded-4 p-4 bg-light">
                        <h6 class="fw-bold text-dark mb-3">Submit Letter of Intent to Publish in Refereed Journal</h6>
                        <form action="{{ route('publication.storeIntent', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Target Refereed / Indexed Journal Title</label>
                                    <input type="text" name="journal_title" class="form-control" placeholder="e.g. Philippine Journal of Science" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">ISSN / e-ISSN Number (Optional)</label>
                                    <input type="text" name="issn_number" class="form-control" placeholder="e.g. 0031-7683">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Indexing / Accreditation Category</label>
                                    <select name="indexing_agency" class="form-select" required>
                                        <option value="CHED Accredited Journal">CHED Accredited Journal</option>
                                        <option value="Scopus Indexed">Scopus Indexed</option>
                                        <option value="Web of Science (WoS)">Web of Science (WoS)</option>
                                        <option value="ASEAN Citation Index (ACI)">ASEAN Citation Index (ACI)</option>
                                        <option value="International Refereed Journal">International Refereed Journal</option>
                                        <option value="National Refereed Journal">National Refereed Journal</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Letter of Intent to Publish (PDF/Doc)</label>
                                    <input type="file" name="intent_letter" class="form-control" accept=".pdf,.doc,.docx" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-send me-1"></i> Submit Intent & Start Appendix C Workflow
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Appendix D: Conduct of Local Research Forum Workspace -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 px-lg-5 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="h5 fw-bold d-flex align-items-center gap-2 text-dark mb-1">
                    <i class="bi bi-people-fill text-primary"></i> Phase 5: Conduct of Local Research Forum Workspace (Appendix D)
                </h3>
                <p class="text-muted fs-7 mb-0">Internal University In-House Colloquium workflow: Call for papers, College Coordinator endorsement, Notice of Acceptance, and presentation certificates.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(in_array(auth()->user()->role, ['admin', 'vprei', 'coordinator', 'super_admin']))
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" data-bs-toggle="collapse" data-bs-target="#createForumModal">
                        <i class="bi bi-megaphone me-1"></i> Launch Call for Papers
                    </button>
                @endif
                @if($proposal->localForumSubmissions->count() > 0)
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-medium fs-7">
                        <i class="bi bi-calendar-event-fill me-1"></i> Forum Active
                    </span>
                @endif
            </div>
        </div>

        <!-- Launch Call for Papers Form (Collapse) -->
        <div class="collapse px-4 px-lg-5 pt-3" id="createForumModal">
            <div class="card border rounded-4 p-4 bg-light">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-megaphone-fill me-2 text-primary"></i> Launch New Local Research Forum Event (Research Director)</h6>
                <form action="{{ route('local_forum.create') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-bold">Forum Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. 2026 Annual SUC In-House Research Forum" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-bold">Forum Theme (Optional)</label>
                            <input type="text" name="theme" class="form-control" placeholder="e.g. Innovating for Sustainable Regional Development">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-7 fw-bold">Event Date</label>
                            <input type="date" name="event_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-7 fw-bold">Venue / Location</label>
                            <input type="text" name="venue" class="form-control" placeholder="e.g. University Convention Center / Online">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-7 fw-bold">Submission Deadline</label>
                            <input type="date" name="submission_deadline" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fs-7 fw-bold">Guidelines & Mechanics</label>
                            <textarea name="guidelines" class="form-control" rows="2" placeholder="Enter submission guidelines or instructions for faculty researchers..."></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-broadcast me-1"></i> Launch & Disseminate Call for Papers
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body p-4 p-lg-5">
            @forelse($proposal->localForumSubmissions as $sub)
                <div class="card border mb-4 rounded-4 shadow-sm overflow-hidden">
                    <div class="card-header bg-light py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="badge bg-primary text-white text-uppercase me-2 fw-bold fs-7">
                                LOCAL FORUM SUBMISSION
                            </span>
                            <strong class="text-dark fs-6">{{ $sub->paper_title }}</strong>
                            <div class="fs-7 text-muted">Forum Event: <strong>{{ $sub->forum->title ?? 'University Research Forum' }}</strong></div>
                        </div>
                        <div>
                            @if($sub->status === 'presented_and_completed')
                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-award-fill me-1"></i> Presented & Certified</span>
                            @elseif($sub->status === 'accepted_by_director')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Notice of Acceptance Issued</span>
                            @elseif($sub->status === 'endorsed_by_coordinator')
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2"><i class="bi bi-hand-thumbs-up me-1"></i> Endorsed by College Coordinator</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2"><i class="bi bi-hourglass-split me-1"></i> Submitted to College Coordinator</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Step 1 & 2: College Research Coordinator Endorsement -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase">1. College Coordinator Endorsement</h6>
                                        @if($sub->coordinator_endorsed)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fs-7">Endorsed</span>
                                        @endif
                                    </div>

                                    @if($sub->coordinator_endorsed)
                                        <div class="alert alert-success py-2 px-3 mb-0 fs-7 rounded-3 border-0">
                                            <i class="bi bi-check-circle-fill me-1"></i> Endorsed to Research Director on {{ $sub->coordinator_endorsed_at ? $sub->coordinator_endorsed_at->format('M d, Y') : '' }}
                                            @if($sub->coordinator)
                                                by <strong>{{ $sub->coordinator->name }}</strong>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-muted fs-7 mb-2 italic">Awaiting review & dissemination endorsement by College Research Coordinator.</p>
                                        @if(in_array(auth()->user()->role, ['coordinator', 'admin', 'vprei', 'super_admin']))
                                            <form action="{{ route('local_forum.endorse', $sub->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100 shadow-sm" onclick="return confirm('Endorse this research output for presentation in the Local Forum?');">
                                                    <i class="bi bi-hand-thumbs-up me-1"></i> Endorse Output to Research Director
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Step 3 & 4: Research Director Notice of Acceptance -->
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase">2. Notice of Acceptance</h6>
                                        @if($sub->is_accepted)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fs-7">Issued</span>
                                        @endif
                                    </div>

                                    @if($sub->is_accepted)
                                        <div class="alert alert-success py-2 px-3 mb-2 fs-7 rounded-3 border-0">
                                            <i class="bi bi-check-circle-fill me-1"></i> <strong>Notice of Acceptance Issued</strong> on {{ $sub->accepted_at ? $sub->accepted_at->format('M d, Y') : '' }}!
                                        </div>
                                        @if($sub->notice_of_acceptance_path)
                                            <a href="{{ route('file.serve', ['path' => $sub->notice_of_acceptance_path]) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill">
                                                <i class="bi bi-file-earmark-pdf me-1"></i> View Notice of Acceptance
                                            </a>
                                        @endif
                                    @else
                                        <p class="text-muted fs-7 mb-2 italic">Awaiting Research Director's Notice of Acceptance.</p>
                                        @if(in_array(auth()->user()->role, ['admin', 'vprei', 'super_admin']) && $sub->coordinator_endorsed)
                                            <button class="btn btn-sm btn-success rounded-pill w-100 shadow-sm" data-bs-toggle="collapse" data-bs-target="#noticeForm{{ $sub->id }}">
                                                <i class="bi bi-award me-1"></i> Issue Notice of Acceptance
                                            </button>
                                            <div class="collapse mt-2" id="noticeForm{{ $sub->id }}">
                                                <form action="{{ route('local_forum.accept', $sub->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label class="form-label fs-7 fw-bold">Attach Signed Notice of Acceptance (Optional)</label>
                                                        <input type="file" name="notice_file" class="form-control form-control-sm" accept=".pdf">
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill">Issue Notice to Presenter</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Step 5: Presentation Files & Certificate -->
                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-7 text-uppercase">3. Presentation Files & Certificate of Local Forum Presentation</h6>
                                        @if($sub->certificate_path)
                                            <span class="badge bg-success rounded-pill px-2 py-1 fs-7"><i class="bi bi-check-lg me-1"></i> Forum Completed</span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        @if($sub->extended_abstract_path)
                                            <a href="{{ route('file.serve', ['path' => $sub->extended_abstract_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="bi bi-file-earmark-text me-1"></i> Extended Abstract
                                            </a>
                                        @endif
                                        @if($sub->presentation_file_path)
                                            <a href="{{ route('file.serve', ['path' => $sub->presentation_file_path]) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill">
                                                <i class="bi bi-file-earmark-slides me-1"></i> Presentation Slides
                                            </a>
                                        @endif
                                        @if($sub->certificate_path)
                                            <a href="{{ route('file.serve', ['path' => $sub->certificate_path]) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill">
                                                <i class="bi bi-award-fill me-1"></i> View Presentation Certificate
                                            </a>
                                        @endif
                                    </div>
                                    @if(!$sub->certificate_path && auth()->id() == $proposal->user_id && $sub->is_accepted)
                                        <button class="btn btn-sm btn-outline-success rounded-pill mt-1" data-bs-toggle="collapse" data-bs-target="#forumCertForm{{ $sub->id }}">
                                            <i class="bi bi-upload me-1"></i> Upload Certificate of Local Presentation
                                        </button>
                                        <div class="collapse mt-2" id="forumCertForm{{ $sub->id }}">
                                            <form action="{{ route('local_forum.certificate', $sub->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-2">
                                                    <label class="form-label fs-7 fw-bold">Certificate File (PDF/Image)</label>
                                                    <input type="file" name="certificate_file" class="form-control form-control-sm" required>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill">Upload Certificate</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 bg-light rounded-4 border mb-3">
                    <i class="bi bi-easel2 fs-2 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-2 fw-medium">No local research forum submissions logged for this research output yet.</p>
                </div>
            @endforelse

            <!-- Researcher Submit Paper for Local Forum Form -->
            @if(auth()->id() == $proposal->user_id)
                @if(isset($openForums) && $openForums->count() > 0)
                    <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="collapse" data-bs-target="#newForumSubForm">
                        <i class="bi bi-plus-circle me-2"></i> Submit Paper to Local Research Forum (Appendix D)
                    </button>
                    <div class="collapse mt-3" id="newForumSubForm">
                        <div class="card border rounded-4 p-4 bg-light">
                            <h6 class="fw-bold text-dark mb-3">Submit Output to College Coordinator for Local Forum</h6>
                            <form action="{{ route('local_forum.submit', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fs-7 fw-bold">Select Active Local Research Forum Event</label>
                                        <select name="local_research_forum_id" class="form-select" required>
                                            @foreach($openForums as $of)
                                                <option value="{{ $of->id }}">{{ $of->title }} ({{ $of->event_date ? $of->event_date->format('M d, Y') : 'TBD' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fs-7 fw-bold">Paper / Presentation Title</label>
                                        <input type="text" name="paper_title" class="form-control" value="{{ $proposal->title }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fs-7 fw-bold">Executive Abstract</label>
                                        <textarea name="abstract" class="form-control" rows="3" required>{{ $proposal->abstract }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fs-7 fw-bold">Extended Abstract File (PDF/Doc)</label>
                                        <input type="file" name="extended_abstract_file" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fs-7 fw-bold">Presentation Slides / Poster Deck (PDF/PPT/PPTX)</label>
                                        <input type="file" name="presentation_file" class="form-control">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    <i class="bi bi-send me-1"></i> Submit to College Coordinator (Appendix D)
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info py-3 px-4 rounded-4 mb-0 fs-7 border-0">
                        <i class="bi bi-info-circle-fill me-2"></i> No active Call for Papers for Local Research Forums currently open. When the Research Director posts a call, you will be able to submit your paper here.
                    </div>
                @endif
            @endif
        </div>
    </div>

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

    <!-- Line Item Budget (LIB) Section -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 px-lg-5 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="h5 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-calculator-fill text-success"></i> Line Item Budget (LIB)
                </h3>
                <p class="text-muted small mb-0">Detailed breakdown of Maintenance & Operating Expenses (MOOE) and Capital Outlay (CO).</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success px-4 py-2 rounded-pill fw-bold fs-6">
                    Grand Total: ₱{{ number_format($proposal->total_budget ?? $proposal->budgetItems->sum('amount'), 2) }}
                </div>
                @if(auth()->id() == $proposal->user_id)
                    <button type="button" class="btn btn-success btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addBudgetItemModal">
                        <i class="bi bi-plus-circle-fill"></i> Add Budget Entry
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body p-4 p-lg-5">
            @php
                $mooeItems = $proposal->budgetItems->where('category_type', 'mooe');
                $coItems = $proposal->budgetItems->where('category_type', 'co');
                $subtotalMOOE = $mooeItems->sum('amount');
                $subtotalCO = $coItems->sum('amount');
                $grandTotal = $subtotalMOOE + $subtotalCO;
            @endphp

            <div class="table-responsive border rounded-4 bg-white shadow-sm mb-3">
                <table class="table table-bordered align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th rowspan="2" style="width: 240px;" class="py-3">Particulars / Category</th>
                            <th rowspan="2" class="py-3">Details</th>
                            <th rowspan="2" style="width: 140px;" class="py-3">Funding Agency / Org</th>
                            <th colspan="3" class="py-2 bg-primary bg-opacity-10 text-primary fw-bold">University of Antique</th>
                            <th rowspan="2" style="width: 130px;" class="py-3 bg-light">Total (₱)</th>
                            @if(auth()->id() == $proposal->user_id)
                                <th rowspan="2" style="width: 60px;" class="py-3">Action</th>
                            @endif
                        </tr>
                        <tr>
                            <th style="width: 120px;" class="small py-2">Equivalent Teaching Unit</th>
                            <th style="width: 120px;" class="small py-2">Existing Resources</th>
                            <th style="width: 140px;" class="small py-2 fw-bold text-success">Proposed Expenditures</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Section I: MOOE -->
                        <tr class="table-secondary fw-bold">
                            <td colspan="{{ auth()->id() == $proposal->user_id ? 8 : 7 }}" class="py-2.5 ps-3 text-dark">
                                I. Maintenance and Other Operating Expenses (MOOE)
                            </td>
                        </tr>

                        @php
                            $groups = [
                                'supplies' => 'A. Supplies and Materials Expenses',
                                'semi_expandable' => 'B. Semi-Expandable Expenses',
                                'travel' => 'C. Travelling Expenses (Local)',
                                'transportation' => 'D. Transportation',
                                'professional_services' => 'E. Other Professional Services',
                                'other_mooe' => 'F. Other MOOE',
                            ];
                        @endphp

                        @foreach($groups as $groupKey => $groupLabel)
                            @php
                                $groupItems = $mooeItems->where('category_group', $groupKey);
                            @endphp
                            @if($groupItems->isNotEmpty())
                                <tr>
                                    <td colspan="{{ auth()->id() == $proposal->user_id ? 8 : 7 }}" class="bg-light fw-semibold text-muted py-2 ps-4">
                                        {{ $groupLabel }}
                                    </td>
                                </tr>
                                @foreach($groupItems as $item)
                                    <tr>
                                        <td class="ps-5 text-dark fw-medium">{{ $item->item_name }}</td>
                                        <td class="text-muted">{{ $item->item_name }}</td>
                                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $item->funding_agency ?? '—' }}</span></td>
                                        <td class="text-center text-muted">{{ $item->equivalent_teaching_unit ?? '—' }}</td>
                                        <td class="text-center text-muted">{{ $item->existing_resources ?? '—' }}</td>
                                        <td class="text-end fw-bold text-dark">₱{{ number_format($item->amount, 2) }}</td>
                                        <td class="text-end fw-bold text-success bg-light">₱{{ number_format($item->amount, 2) }}</td>
                                        @if(auth()->id() == $proposal->user_id)
                                            <td class="text-center">
                                                <form action="{{ route('budget_items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this budget entry?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger border-0 p-1" title="Delete Entry">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach

                        @if($mooeItems->isEmpty())
                            <tr>
                                <td colspan="{{ auth()->id() == $proposal->user_id ? 8 : 7 }}" class="text-center py-3 text-muted italic small">No MOOE items listed yet.</td>
                            </tr>
                        @endif

                        <!-- Sub-total for MOOE -->
                        <tr class="table-warning fw-bold">
                            <td colspan="5" class="py-2.5 text-end text-dark">Sub-total for MOOE:</td>
                            <td class="text-end text-dark">₱{{ number_format($subtotalMOOE, 2) }}</td>
                            <td class="text-end text-dark">₱{{ number_format($subtotalMOOE, 2) }}</td>
                            @if(auth()->id() == $proposal->user_id) <td></td> @endif
                        </tr>

                        <!-- Section II: Capital Outlay -->
                        <tr class="table-secondary fw-bold">
                            <td colspan="{{ auth()->id() == $proposal->user_id ? 8 : 7 }}" class="py-2.5 ps-3 text-dark">
                                II. Capital Outlay (CO)
                            </td>
                        </tr>

                        @if($coItems->isNotEmpty())
                            @foreach($coItems as $item)
                                <tr>
                                    <td class="ps-4 text-dark fw-medium">{{ $item->item_name }}</td>
                                    <td class="text-muted">{{ $item->item_name }}</td>
                                    <td class="text-center"><span class="badge bg-light text-dark border">{{ $item->funding_agency ?? '—' }}</span></td>
                                    <td class="text-center text-muted">{{ $item->equivalent_teaching_unit ?? '—' }}</td>
                                    <td class="text-center text-muted">{{ $item->existing_resources ?? '—' }}</td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($item->amount, 2) }}</td>
                                    <td class="text-end fw-bold text-success bg-light">₱{{ number_format($item->amount, 2) }}</td>
                                    @if(auth()->id() == $proposal->user_id)
                                        <td class="text-center">
                                            <form action="{{ route('budget_items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this budget entry?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger border-0 p-1" title="Delete Entry">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="{{ auth()->id() == $proposal->user_id ? 8 : 7 }}" class="text-center py-3 text-muted italic small">No Capital Outlay items listed yet.</td>
                            </tr>
                        @endif

                        <!-- Sub-total for Capital Outlay -->
                        <tr class="table-warning fw-bold">
                            <td colspan="5" class="py-2.5 text-end text-dark">Sub-total for CO:</td>
                            <td class="text-end text-dark">₱{{ number_format($subtotalCO, 2) }}</td>
                            <td class="text-end text-dark">₱{{ number_format($subtotalCO, 2) }}</td>
                            @if(auth()->id() == $proposal->user_id) <td></td> @endif
                        </tr>

                        <!-- GRAND TOTAL -->
                        <tr class="table-success fw-bold fs-6">
                            <td colspan="5" class="py-3 text-end text-uppercase text-dark">GRAND TOTAL:</td>
                            <td class="text-end text-success fs-5">₱{{ number_format($grandTotal, 2) }}</td>
                            <td class="text-end text-success fs-5">₱{{ number_format($grandTotal, 2) }}</td>
                            @if(auth()->id() == $proposal->user_id) <td></td> @endif
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal: Add Budget Entry -->
    @if(auth()->id() == $proposal->user_id)
    <div class="modal fade" id="addBudgetItemModal" tabindex="-1" aria-labelledby="addBudgetItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-success" id="addBudgetItemModalLabel">
                        <i class="bi bi-plus-circle me-2"></i> Add Line Item Budget Entry
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('budget_items.store', $proposal->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted uppercase">Expenditure Type</label>
                            <select name="category_type" class="form-select bg-light border-0 py-2 px-3 rounded-3" required>
                                <option value="mooe">Section I: Maintenance & Other Operating Expenses (MOOE)</option>
                                <option value="co">Section II: Capital Outlay (CO)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted uppercase">Budget Category</label>
                            <select name="category_group" class="form-select bg-light border-0 py-2 px-3 rounded-3" required>
                                <option value="supplies">A. Supplies and Materials Expenses</option>
                                <option value="semi_expandable">B. Semi-Expandable Expenses</option>
                                <option value="travel">C. Travelling Expenses (Local)</option>
                                <option value="transportation">D. Transportation</option>
                                <option value="professional_services">E. Other Professional Services</option>
                                <option value="other_mooe">F. Other MOOE</option>
                                <option value="capital_outlay">II. Capital Outlay (CO - Equipment/Hardware)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted uppercase">Item Description / Details</label>
                            <textarea name="item_name" class="form-control bg-light border-0 py-2 px-3 rounded-3" rows="2" placeholder="e.g. Bookpaper [A4;80gsm] @ Php 315 x 2 reams" required></textarea>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted uppercase">Funding Agency / Org</label>
                                <input type="text" name="funding_agency" class="form-control bg-light border-0 py-2 px-3 rounded-3" placeholder="e.g. RESU">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted uppercase">Proposed Amount (₱)</label>
                                <input type="number" step="0.01" min="0" name="amount" class="form-control bg-light border-0 py-2 px-3 rounded-3" placeholder="e.g. 630.00" required>
                            </div>
                        </div>
                        <div class="row g-3 mb-0">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted uppercase">Equivalent Teaching Unit</label>
                                <input type="text" name="equivalent_teaching_unit" class="form-control bg-light border-0 py-2 px-3 rounded-3" placeholder="Optional">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted uppercase">Existing Resources</label>
                                <input type="text" name="existing_resources" class="form-control bg-light border-0 py-2 px-3 rounded-3" placeholder="Optional">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 rounded-pill fw-bold shadow-sm">Save Budget Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Work Plan & Gantt Chart Matrix Section -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 px-lg-5 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="h5 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-bar-chart-line-fill text-primary"></i> 12-Month Research Work Plan (Gantt Chart)
                </h3>
                <p class="text-muted small mb-0">Visual Gantt chart timeline mapping project phases from inception to final submission.</p>
            </div>
            @if(auth()->id() == $proposal->user_id)
                <button type="button" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                    <i class="bi bi-plus-circle-fill"></i> Add Work Plan Phase
                </button>
            @endif
        </div>

        <div class="card-body p-4 p-lg-5">
            @php
                $months = [
                    ['num' => 'M1', 'label' => 'Jan'],
                    ['num' => 'M2', 'label' => 'Feb'],
                    ['num' => 'M3', 'label' => 'Mar'],
                    ['num' => 'M4', 'label' => 'Apr'],
                    ['num' => 'M5', 'label' => 'May'],
                    ['num' => 'M6', 'label' => 'Jun'],
                    ['num' => 'M7', 'label' => 'Jul'],
                    ['num' => 'M8', 'label' => 'Aug'],
                    ['num' => 'M9', 'label' => 'Sep'],
                    ['num' => 'M10', 'label' => 'Oct'],
                    ['num' => 'M11', 'label' => 'Nov'],
                    ['num' => 'M12', 'label' => 'Dec'],
                ];
            @endphp

            @if($proposal->milestones && $proposal->milestones->count() > 0)
                <div class="gantt-matrix-container border rounded-4 p-4 bg-white shadow-sm overflow-x-auto">
                    <!-- Gantt Header (Timeline Month Columns) -->
                    <div class="gantt-header d-flex align-items-center border-bottom pb-3 mb-2" style="min-width: 950px;">
                        <div class="gantt-phase-col fw-bold text-uppercase text-secondary small" style="width: 260px; flex-shrink: 0;">
                            Phase / Activity Name
                        </div>
                        <div class="gantt-timeline-header d-flex flex-grow-1 text-center">
                            @foreach($months as $m)
                                <div style="width: 8.333%;" class="border-start px-1">
                                    <span class="d-block fw-bold text-dark small">{{ $m['num'] }}</span>
                                    <span class="text-muted fw-semibold" style="font-size: 0.72rem;">{{ $m['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Gantt Body Rows -->
                    <div class="gantt-body" style="min-width: 950px;">
                        @foreach($proposal->milestones as $milestone)
                            @php
                                $mCount = $proposal->milestones->count();
                                if ($milestone->start_date && $milestone->target_date) {
                                    $startDate = \Carbon\Carbon::parse($milestone->start_date);
                                    $targetDate = \Carbon\Carbon::parse($milestone->target_date);
                                    
                                    $mStartIdx = (int)$startDate->format('n');
                                    $mEndIdx = (int)$targetDate->format('n');
                                    if ($mEndIdx < $mStartIdx) {
                                        $mEndIdx = $mStartIdx;
                                    }
                                } elseif ($milestone->start_date) {
                                    $startDate = \Carbon\Carbon::parse($milestone->start_date);
                                    $mStartIdx = (int)$startDate->format('n');
                                    $mEndIdx = min(12, $mStartIdx + 1);
                                } else {
                                    $createdMonth = (int)($proposal->created_at ? $proposal->created_at->format('n') : 1);
                                    $mStartIdx = min(12, max(1, $createdMonth + $loop->index));
                                    $mEndIdx = min(12, $mStartIdx);
                                }
                                
                                $leftPercent = (($mStartIdx - 1) / 12) * 100;
                                $widthPercent = max(8.333, (($mEndIdx - $mStartIdx + 1) / 12) * 100);
                                if ($leftPercent + $widthPercent > 100) {
                                    $widthPercent = 100 - $leftPercent;
                                }

                                $barGradient = match($milestone->status) {
                                    'approved' => 'linear-gradient(135deg, #10B981, #059669)',
                                    'rejected' => 'linear-gradient(135deg, #EF4444, #DC2626)',
                                    default => 'linear-gradient(135deg, #3B82F6, #1D4ED8)'
                                };

                                $statusBadge = match($milestone->status) {
                                    'approved' => 'bg-success text-white',
                                    'rejected' => 'bg-danger text-white',
                                    default => 'bg-warning text-dark'
                                };
                            @endphp

                            <div class="gantt-row d-flex align-items-center py-3 border-bottom position-relative">
                                <!-- Left Column: Phase Title & Description -->
                                <div class="gantt-phase-col pe-3" style="width: 260px; flex-shrink: 0;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-bold text-dark text-truncate" title="{{ $milestone->title }}">{{ $milestone->title }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $statusBadge }} px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">{{ ucfirst($milestone->status) }}</span>
                                        @if($milestone->document_path)
                                            <a href="{{ route('file.serve', ['path' => $milestone->document_path]) }}" target="_blank" class="text-primary small" style="font-size: 0.75rem;" title="View Attachment">
                                                <i class="bi bi-paperclip"></i> File
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Timeline Track with Horizontal Gantt Bar -->
                                <div class="gantt-track-col flex-grow-1 position-relative d-flex align-items-center" 
                                     style="height: 42px; background-size: 8.333% 100%; background-image: linear-gradient(to right, #f1f5f9 1px, transparent 1px);">
                                    
                                    <!-- Horizontal Bar -->
                                    <div class="gantt-bar rounded-pill shadow-sm d-flex align-items-center justify-content-between px-3 text-white position-absolute"
                                         style="left: {{ $leftPercent }}%; width: {{ $widthPercent }}%; height: 32px; background: {{ $barGradient }}; transition: all 0.3s ease;"
                                         data-bs-toggle="tooltip" data-bs-html="true" 
                                         title="<strong>{{ $milestone->title }}</strong><br>{{ $milestone->description }}">
                                        
                                        <span class="small fw-bold text-truncate me-2" style="font-size: 0.75rem;">
                                            {{ $milestone->title }}
                                        </span>
                                        <span class="badge bg-white text-dark rounded-pill shadow-xs" style="font-size: 0.65rem;">
                                            {{ $months[$mStartIdx-1]['num'] }}@if($mEndIdx > $mStartIdx)-{{ $months[$mEndIdx-1]['num'] }}@endif
                                        </span>
                                    </div>
                                </div>

                                <!-- Reviewer/Admin Actions inline -->
                                @if(in_array(auth()->user()->role, ['admin', 'super_admin', 'reviewer']) && $milestone->status == 'pending')
                                    <div class="ms-3 d-flex gap-1 flex-shrink-0">
                                        <form action="{{ route('admin.milestones.updateStatus', $milestone->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-xs btn-success py-1 px-2 rounded-pill fw-bold" style="font-size: 0.7rem;" title="Approve Phase">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.milestones.updateStatus', $milestone->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2 rounded-pill fw-bold" style="font-size: 0.7rem;" title="Reject Phase">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Gantt Legend -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-4 pt-3 border-top small text-muted">
                        <div class="d-flex align-items-center gap-4">
                            <span class="fw-bold text-uppercase" style="font-size: 0.75rem;">Legend:</span>
                            <span class="d-flex align-items-center gap-2"><span class="rounded-pill d-inline-block" style="width: 12px; height: 12px; background: linear-gradient(135deg, #10B981, #059669);"></span> Approved / Completed</span>
                            <span class="d-flex align-items-center gap-2"><span class="rounded-pill d-inline-block" style="width: 12px; height: 12px; background: linear-gradient(135deg, #3B82F6, #1D4ED8);"></span> Pending Review</span>
                            <span class="d-flex align-items-center gap-2"><span class="rounded-pill d-inline-block" style="width: 12px; height: 12px; background: linear-gradient(135deg, #EF4444, #DC2626);"></span> Revision Required</span>
                        </div>
                        <div class="text-muted italic" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle me-1"></i> M1 to M12 represent Month 1 through Month 12 of the research duration.
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5 bg-light rounded-4 border">
                    <i class="bi bi-kanban text-muted display-5 d-block mb-3"></i>
                    <h6 class="fw-bold text-muted mb-1">No Work Plan Phases Created Yet</h6>
                    <p class="text-muted small mb-3">Add research phases (e.g., Literature review, Proposal, Ethics, Data collection, Analysis, Final submission) to populate the Gantt Chart.</p>
                    @if(auth()->id() == $proposal->user_id)
                        <button type="button" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                            <i class="bi bi-plus-circle me-1"></i> Add First Work Plan Phase
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Modal: Add Work Plan Item -->
    @if(auth()->id() == $proposal->user_id)
    <div class="modal fade" id="addMilestoneModal" tabindex="-1" aria-labelledby="addMilestoneModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-primary" id="addMilestoneModalLabel">
                        <i class="bi bi-calendar-plus me-2"></i> Add Work Plan Phase / Milestone
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('milestones.store', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted uppercase">Phase / Activity Title</label>
                            <input type="text" name="title" class="form-control bg-light border-0 py-2 px-3 rounded-3" placeholder="e.g. Data Collection & Field Work" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted uppercase">Start Date</label>
                                <input type="date" name="start_date" class="form-control bg-light border-0 py-2 px-3 rounded-3">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted uppercase">Target Date</label>
                                <input type="date" name="target_date" class="form-control bg-light border-0 py-2 px-3 rounded-3">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted uppercase">Phase Description & Deliverables</label>
                            <textarea name="description" class="form-control bg-light border-0 py-2 px-3 rounded-3" rows="3" placeholder="Describe key activities, methodology, and deliverables..." required></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted uppercase">Supporting Document (Optional PDF)</label>
                            <input type="file" name="document" class="form-control bg-light border-0 py-2 px-3 rounded-3" accept=".pdf,.doc,.docx">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">Save Work Plan Phase</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
