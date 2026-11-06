<x-app-layout>
    <div class="mb-3 mb-md-4">
        <a href="{{ route('dashboard') }}" class="btn btn-link text-muted p-0 text-decoration-none d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row justify-content-center pb-5">
        <div class="col-12 col-xl-11 col-xxl-10">

            <!-- Reviewer / Return Notice Alerts -->
            @if($proposal->status === 'returned_for_revision' && $proposal->return_reason)
                <div class="alert alert-danger border-0 rounded-4 mb-4 p-3 p-md-4 shadow-sm" style="background-color: #fef2f2; border-left: 5px solid #ef4444 !important;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-danger fs-3 flex-shrink-0"><i class="bi bi-exclamation-octagon-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                <h5 class="fw-bold mb-0 text-danger">
                                    @if($proposal->returned_by_role === 'staff')
                                        Research Unit Receiving Staff - Administrative Completeness Remarks
                                    @elseif($proposal->returned_by_role === 'dean')
                                        College Dean Revision Remarks
                                    @else
                                        {{ ucfirst($proposal->returned_by_role ?? 'Coordinator') }} Revision Remarks
                                    @endif
                                </h5>
                                @if($proposal->returned_at)
                                    <span class="text-muted small">
                                        {{ \Carbon\Carbon::parse($proposal->returned_at)->format('M d, Y h:i A') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-muted small mb-2">
                                Please revise your proposal according to the remarks below. Once resubmitted, it will automatically update in the pipeline.
                            </p>
                            <div class="p-3 bg-white rounded-3 border border-danger border-opacity-25 small text-dark fw-medium" style="white-space: pre-wrap; font-size: 0.88rem;">{{ $proposal->return_reason }}</div>
                        </div>
                    </div>
                </div>
            @elseif($proposal->status === 'revision_required' || $proposal->status === 'approved_with_revisions')
                <div class="alert alert-warning border-0 rounded-4 mb-4 p-3 p-md-4 shadow-sm" style="background-color: #fffbeb; border-left: 5px solid #f59e0b !important;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-warning fs-3 flex-shrink-0"><i class="bi bi-chat-left-text-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <h5 class="fw-bold mb-0 text-dark">Reviewer Feedback &amp; Revision Required</h5>
                                <a href="{{ route('proposal.reviewWorkspace', $proposal->id) }}" target="_blank" class="btn btn-warning rounded-pill px-3 py-1.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1.5 text-dark" style="font-size: 0.82rem;">
                                    <i class="bi bi-layout-split"></i> Open In-Browser Review Workspace
                                </a>
                            </div>
                            @if($proposal->review_comments)
                                <div class="small text-secondary mb-1"><strong>Reviewer Comments:</strong> {{ $proposal->review_comments }}</div>
                            @endif
                            @if($proposal->review_suggestions)
                                <div class="small text-secondary mb-2"><strong>Suggestions:</strong> {{ $proposal->review_suggestions }}</div>
                            @endif
                            <div class="p-2.5 bg-white bg-opacity-75 rounded-3 border border-warning border-opacity-25 mt-2 small text-dark">
                                <i class="bi bi-info-circle-fill text-warning me-1"></i>
                                Evaluators may have highlighted specific sentences and sections in your proposal. Click <strong>"Open In-Browser Review Workspace"</strong> to view highlighted quotes and tagged revision notes side-by-side with your manuscript.
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Header Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="p-4 p-md-5 text-white" style="background: linear-gradient(135deg, #1f080c 0%, #3e1218 50%, #791d1d 100%);">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-15 p-3 rounded-4 flex-shrink-0 text-warning">
                                <i class="bi bi-pencil-square fs-2"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <span class="badge bg-warning text-dark fw-bold rounded-pill px-2.5 py-1 font-monospace" style="font-size: 0.72rem;">{{ $proposal->proposal_code }}</span>
                                    <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">Form RESU-FM-015 Template</span>
                                </div>
                                <h3 class="h4 fw-bold mb-1 text-white">Edit &amp; Revise Research Proposal</h3>
                                <p class="text-white text-opacity-75 small mb-0">Update your manuscript sections, adjust the Work Plan matrix and Line-Item Budget directly in the system.</p>
                            </div>
                        </div>

                        <div class="text-md-end d-flex flex-column flex-sm-row align-items-stretch align-items-md-center gap-2">
                            <a href="{{ route('template.download.proposal') }}" class="btn btn-warning text-dark rounded-pill px-3 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5" style="font-size: 0.82rem;" title="Download 15-RESEARCH-PROPOSAL-TEMPLATE-REVISED (RESU-FM-015).docx">
                                <i class="bi bi-file-earmark-word-fill text-primary fs-6"></i> Download Word Template
                            </a>
                            <span class="badge bg-white text-dark rounded-pill px-3 py-2 fw-bold shadow-sm align-self-center text-nowrap" id="stepCounterBadge">
                                Step 1 of 5: General Info
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Step Tabs Navigation -->
                <div class="card-header bg-white border-bottom p-2 p-md-3">
                    <ul class="nav nav-pills nav-fill gap-1 flex-nowrap overflow-auto" id="proposalFormTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-3 py-2 fw-bold small text-nowrap" id="tab-step-1" data-bs-toggle="pill" data-bs-target="#pane-step-1" type="button" role="tab">
                                <i class="bi bi-person-lines-fill me-1"></i> 1. General Info &amp; Proponents
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-3 py-2 fw-bold small text-nowrap" id="tab-step-2" data-bs-toggle="pill" data-bs-target="#pane-step-2" type="button" role="tab">
                                <i class="bi bi-journal-text me-1"></i> 2. Manuscript Sections
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-3 py-2 fw-bold small text-nowrap" id="tab-step-3" data-bs-toggle="pill" data-bs-target="#pane-step-3" type="button" role="tab">
                                <i class="bi bi-calendar3-range me-1"></i> 3. Work Plan (Gantt)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-3 py-2 fw-bold small text-nowrap" id="tab-step-4" data-bs-toggle="pill" data-bs-target="#pane-step-4" type="button" role="tab">
                                <i class="bi bi-calculator me-1"></i> 4. Line-Item Budget
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-3 py-2 fw-bold small text-nowrap" id="tab-step-5" data-bs-toggle="pill" data-bs-target="#pane-step-5" type="button" role="tab">
                                <i class="bi bi-check2-circle me-1"></i> 5. Review &amp; Resubmit
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('proposal.update', $proposal->id) }}" enctype="multipart/form-data" id="proposalEditForm">
                @csrf
                @method('PUT')

                @if (session('error'))
                    <div class="alert alert-warning border-0 rounded-4 p-3 p-md-4 mb-4 shadow-sm" style="border-left: 5px solid #f59e0b !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">{{ session('error') }}</h6>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 p-3 p-md-4 mb-4 shadow-sm">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                            <h6 class="fw-bold mb-0">Please correct the following errors:</h6>
                        </div>
                        <ul class="mb-0 small ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="tab-content" id="proposalFormContent">

                    <!-- ======================================================== -->
                    <!-- TAB 1: General Info & Proponents -->
                    <!-- ======================================================== -->
                    <div class="tab-pane fade show active" id="pane-step-1" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-12 col-lg-7">
                                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                                    <h5 class="fw-bold mb-3 border-start border-4 border-primary ps-3 text-dark">
                                        Proposal Overview &amp; Proponent Information
                                    </h5>
                                    <p class="text-muted small mb-4">Official fields corresponding to UA Form RESU-FM-015.</p>

                                    <!-- Title -->
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                                            Proposal Title <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="title" id="editProposalTitle" class="form-control border-0 bg-light py-2.5 py-md-3 px-3 px-md-4 rounded-4 fw-bold" value="{{ old('title', $proposal->title) }}" required>
                                    </div>

                                    <!-- Research Field & College -->
                                    <div class="row g-3 mb-3 mb-md-4">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                                                Research Field / Discipline
                                            </label>
                                            <input type="text" name="research_field" id="editProposalField" class="form-control border-0 bg-light py-2.5 px-3 rounded-4" value="{{ old('research_field', $proposal->research_field) }}" placeholder="e.g. Information Technology, Agriculture">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                                                College Affiliation
                                            </label>
                                            <input type="text" name="college_affiliation" id="editProposalCollege" class="form-control border-0 bg-light py-2.5 px-3 rounded-4" value="{{ old('college_affiliation', $proposal->college_affiliation ?: $proposal->user->department) }}">
                                        </div>
                                    </div>

                                    <!-- Credentials of Key Proponent/s -->
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                                            Credentials of Key Proponent/s
                                        </label>
                                        <textarea name="credentials" id="editCredentials" rows="3" class="form-control border-0 bg-light py-2.5 px-3 rounded-4" placeholder="Specify highest educational attainment, academic rank, specialization...">{{ old('credentials', $proposal->credentials) }}</textarea>
                                    </div>

                                    <!-- SDGs Addressed -->
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                                            Sustainable Development Goals (SDGs) &amp; Thrusts
                                        </label>
                                        <input type="text" name="sdgs_addressed" id="editSdgs" class="form-control border-0 bg-light py-2.5 px-3 rounded-4" value="{{ old('sdgs_addressed', $proposal->sdgs_addressed) }}" placeholder="e.g. SDG 4, SDG 9, Institutional Priority 1">
                                    </div>

                                    <!-- Abstract -->
                                    <div class="mb-0">
                                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                                            Executive Abstract / Summary <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="abstract" class="form-control border-0 bg-light py-2.5 px-3 rounded-4" rows="4" required>{{ old('abstract', $proposal->abstract) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Collaborators -->
                            <div class="col-12 col-lg-5">
                                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                                    <h5 class="fw-bold mb-2 border-start border-4 border-info ps-3 text-dark">Co-Proponents &amp; Collaborators</h5>
                                    <p class="text-muted small mb-3">Check faculty members collaborating on this research study.</p>

                                    @php
                                        $selectedCollabIds = old('collaborators', $proposal->collaborators ? $proposal->collaborators->pluck('id')->toArray() : []);
                                    @endphp

                                    <div class="collaborator-picker card border-0 bg-light rounded-4 overflow-hidden mb-3">
                                        <div class="p-2.5 p-md-3 border-bottom bg-primary bg-opacity-10 d-flex justify-content-between align-items-center">
                                            <span class="small fw-bold text-primary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                                <i class="bi bi-people-fill me-1"></i> Available Researchers
                                            </span>
                                            <span class="badge bg-white text-dark border rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                                {{ $researchers->count() }} Available
                                            </span>
                                        </div>

                                        <div class="overflow-auto p-2 p-md-2.5" style="max-height: 380px;">
                                            @forelse($researchers as $researcher)
                                                <label class="collaborator-item d-flex align-items-center justify-content-between p-2.5 mb-2 rounded-3 bg-white border shadow-xs user-select-none transition-all" style="cursor: pointer;">
                                                    <div class="d-flex align-items-center gap-2.5 me-2 min-w-0" style="min-width: 0;">
                                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                                            {{ strtoupper(substr($researcher->name, 0, 1)) }}
                                                        </div>
                                                        <div class="min-w-0" style="min-width: 0;">
                                                            <div class="fw-bold text-dark small text-truncate">{{ $researcher->name }}</div>
                                                            <div class="text-muted d-flex align-items-center gap-1 flex-wrap" style="font-size: 0.72rem;">
                                                                <span class="text-truncate">{{ $researcher->department ? strtoupper($researcher->department) : 'No Dept' }}</span>
                                                                @if($researcher->campus)
                                                                    <span>&bull;</span>
                                                                    <span class="text-truncate">{{ $researcher->campus }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex-shrink-0 ms-2">
                                                        <input class="form-check-input border-primary rounded" type="checkbox" name="collaborators[]" value="{{ $researcher->id }}" {{ in_array($researcher->id, $selectedCollabIds) ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                                                    </div>
                                                </label>
                                            @empty
                                                <div class="text-center py-4 text-muted small">No other researchers available.</div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="alert alert-info border-0 rounded-4 d-flex align-items-center gap-3 py-3 mb-0">
                                        <i class="bi bi-shield-check fs-4 flex-shrink-0 text-info"></i>
                                        <p class="mb-0 small" style="font-size: 0.8rem; line-height: 1.4;">
                                            Selected co-proponents are credited in official Form RESU-FM-015 and have co-ownership tracking.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 1 Footer Nav -->
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mt-4 d-flex flex-row justify-content-between align-items-center">
                            <span class="text-muted small">Step 1 of 5</span>
                            <button type="button" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" onclick="navigateToStep('tab-step-2')">
                                Next: Manuscript Sections <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 2: Manuscript Narrative Sections -->
                    <!-- ======================================================== -->
                    <div class="tab-pane fade" id="pane-step-2" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">
                                        <i class="bi bi-journal-richtext text-primary me-2"></i> Manuscript Narrative Sections
                                    </h5>
                                    <p class="text-muted small mb-0">Edit or refine the core research chapters directly. Changes will appear live in the in-browser review workspace.</p>
                                </div>
                                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill small d-none d-md-inline-block">
                                    <i class="bi bi-markdown me-1"></i> Form RESU-FM-015 Sections
                                </span>
                            </div>

                            <div class="row g-4">
                                <!-- Background of the Study -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-1">
                                        <label class="form-label fw-bold text-dark mb-0">
                                            Background of the Study <span class="text-danger">*</span>
                                        </label>
                                        <div class="btn-group btn-group-sm bg-white border rounded-pill p-0.5 shadow-2xs">
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fw-bold" onclick="insertFormat('editBackgroundOfStudy', '**', '**')" title="Bold"><strong>B</strong></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fst-italic" onclick="insertFormat('editBackgroundOfStudy', '*', '*')" title="Italic (scientific names / et al.)"><em>I</em></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('editBackgroundOfStudy', '\n• ', '')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('editBackgroundOfStudy', '\n1. ', '')" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('editBackgroundOfStudy', '\n### ', '')" title="Subheading"><i class="bi bi-type-h3"></i></button>
                                        </div>
                                    </div>
                                    <div class="text-muted small mb-1" style="font-size: 0.72rem;">Statement of the Problem, Theoretical / Conceptual Framework</div>
                                    <textarea name="background_of_study" id="editBackgroundOfStudy" class="form-control border-0 bg-light p-3 rounded-4" rows="6" placeholder="Present the context, current baseline problem, statement of specific research objectives/questions, and the theoretical or conceptual framework...">{{ old('background_of_study', $proposal->background_of_study ?: $proposal->rationale) }}</textarea>
                                </div>

                                <!-- Significance of the Study -->
                                <div class="col-12 col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-bold text-dark mb-0">
                                            Significance of the Study
                                        </label>
                                        <div class="btn-group btn-group-sm bg-white border rounded-pill p-0.5 shadow-2xs">
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-1.5 py-0.5 text-muted fw-bold" onclick="insertFormat('editSignificanceOfStudy', '**', '**')" title="Bold">B</button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-1.5 py-0.5 text-muted fst-italic" onclick="insertFormat('editSignificanceOfStudy', '*', '*')" title="Italic"><em>I</em></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-1.5 py-0.5 text-muted" onclick="insertFormat('editSignificanceOfStudy', '\n• ', '')" title="Bullet"><i class="bi bi-list-ul"></i></button>
                                        </div>
                                    </div>
                                    <textarea name="significance_of_study" id="editSignificanceOfStudy" class="form-control border-0 bg-light p-3 rounded-4" rows="5" placeholder="Detail who will benefit from this study...">{{ old('significance_of_study', $proposal->significance_of_study) }}</textarea>
                                </div>

                                <!-- Definition of Terms -->
                                <div class="col-12 col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-bold text-dark mb-0">
                                            Definition of Terms
                                        </label>
                                        <div class="btn-group btn-group-sm bg-white border rounded-pill p-0.5 shadow-2xs">
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-1.5 py-0.5 text-muted fw-bold" onclick="insertFormat('editDefinitionOfTerms', '**', '**')" title="Bold">B</button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-1.5 py-0.5 text-muted fst-italic" onclick="insertFormat('editDefinitionOfTerms', '*', '*')" title="Italic"><em>I</em></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-1.5 py-0.5 text-muted" onclick="insertFormat('editDefinitionOfTerms', '\n• ', '')" title="Bullet"><i class="bi bi-list-ul"></i></button>
                                        </div>
                                    </div>
                                    <textarea name="definition_of_terms" id="editDefinitionOfTerms" class="form-control border-0 bg-light p-3 rounded-4" rows="5" placeholder="Define key terms, technical jargon, and operational definitions...">{{ old('definition_of_terms', $proposal->definition_of_terms) }}</textarea>
                                </div>

                                <!-- Review of Related Literature -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-1">
                                        <label class="form-label fw-bold text-dark mb-0">
                                            Review of Related Literature (RRL)
                                        </label>
                                        <div class="btn-group btn-group-sm bg-white border rounded-pill p-0.5 shadow-2xs">
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fw-bold" onclick="insertFormat('editLiteratureReview', '**', '**')" title="Bold"><strong>B</strong></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fst-italic" onclick="insertFormat('editLiteratureReview', '*', '*')" title="Italic"><em>I</em></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('editLiteratureReview', '\n• ', '')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('editLiteratureReview', '\n1. ', '')" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                                        </div>
                                    </div>
                                    <textarea name="literature_review" id="editLiteratureReview" class="form-control border-0 bg-light p-3 rounded-4" rows="6" placeholder="Synthesize local and international literature, recent journal publications, and clarify the research gap...">{{ old('literature_review', $proposal->literature_review) }}</textarea>
                                </div>

                                <!-- Methodology -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-1">
                                        <label class="form-label fw-bold text-dark mb-0">
                                            Methodology &amp; Research Design <span class="text-danger">*</span>
                                        </label>
                                        <div class="btn-group btn-group-sm bg-white border rounded-pill p-0.5 shadow-2xs">
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fw-bold" onclick="insertFormat('editMethodologyField', '**', '**')" title="Bold"><strong>B</strong></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fst-italic" onclick="insertFormat('editMethodologyField', '*', '*')" title="Italic"><em>I</em></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('editMethodologyField', '\n• ', '')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('editMethodologyField', '\n1. ', '')" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('editMethodologyField', '\n### ', '')" title="Subheading"><i class="bi bi-type-h3"></i></button>
                                        </div>
                                    </div>
                                    <div class="text-muted small mb-1" style="font-size: 0.72rem;">Design, Participants, Instruments, Ethics, Data Analysis</div>
                                    <textarea name="methodology" id="editMethodologyField" class="form-control border-0 bg-light p-3 rounded-4" rows="6" placeholder="Describe the research design, participants, sampling, instruments, ethical considerations, and analysis tools...">{{ old('methodology', $proposal->methodology) }}</textarea>
                                </div>

                                <!-- References -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-1">
                                        <label class="form-label fw-bold text-dark mb-0">
                                            References &amp; Citations
                                        </label>
                                        <div class="btn-group btn-group-sm bg-white border rounded-pill p-0.5 shadow-2xs">
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fst-italic" onclick="insertFormat('editReferencesField', '*', '*')" title="Italic (Journal titles)"><em>I</em></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('editReferencesField', '\n', '')" title="New line"><i class="bi bi-arrow-return-left"></i></button>
                                        </div>
                                    </div>
                                    <textarea name="references" id="editReferencesField" class="form-control border-0 bg-light p-3 rounded-4 font-monospace small" rows="5" placeholder="List bibliographic references in APA 7th Edition format...">{{ old('references', $proposal->references) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 Footer Nav -->
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-bold" onclick="navigateToStep('tab-step-1')">
                                <i class="bi bi-arrow-left me-1"></i> Previous Step
                            </button>
                            <button type="button" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" onclick="navigateToStep('tab-step-3')">
                                Next: Work Plan (Gantt Chart) <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 3: Work Plan (Gantt Chart Matrix) -->
                    <!-- ======================================================== -->
                    <div class="tab-pane fade" id="pane-step-3" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-2 border-bottom">
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">
                                        <i class="bi bi-calendar3-range text-primary me-2"></i> Work Plan (Gantt Chart Matrix)
                                    </h5>
                                    <p class="text-muted small mb-0">Present a matrix of activities indicating the corresponding schedule / active months for each (Form RESU-FM-015 Standard).</p>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" onclick="addWorkPlanRow()">
                                    <i class="bi bi-plus-circle-fill"></i> Add Activity Row
                                </button>
                            </div>

                            @php
                                $workPlanData = $proposal->work_plan_matrix;
                                if (empty($workPlanData) && $proposal->milestones && $proposal->milestones->count() > 0) {
                                    $workPlanData = [];
                                    foreach ($proposal->milestones as $ms) {
                                        $workPlanData[] = [
                                            'activity' => $ms->title,
                                            'start_date' => $ms->start_date ? \Carbon\Carbon::parse($ms->start_date)->format('Y-m-d') : null,
                                            'target_date' => $ms->target_date ? \Carbon\Carbon::parse($ms->target_date)->format('Y-m-d') : null,
                                            'months' => [1, 2],
                                        ];
                                    }
                                }
                                if (empty($workPlanData)) {
                                    $workPlanData = [
                                        ['activity' => 'Submission of Revised Research Proposal', 'months' => [1, 2]],
                                        ['activity' => 'Data Collection and Gathering', 'months' => [2, 3, 4]],
                                        ['activity' => 'Data Processing and Analysis', 'months' => [4, 5]],
                                        ['activity' => 'Interpretation of Results and Discussion', 'months' => [5, 6]],
                                        ['activity' => 'Writing of Final Report / Terminal Report', 'months' => [6, 7]],
                                        ['activity' => 'Submission and Presentation of Terminal Report', 'months' => [7]],
                                        ['activity' => 'Final Revision and Archiving', 'months' => [8]],
                                    ];
                                }
                            @endphp

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center small mb-3" id="workPlanTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-start" style="min-width: 280px;">Activities / Milestones</th>
                                            <th style="min-width: 140px;">Timeline</th>
                                            <th colspan="12" class="text-center bg-primary bg-opacity-10 text-primary fw-bold">Active Implementation Months (M1 - M12)</th>
                                            <th style="width: 60px;">Action</th>
                                        </tr>
                                        <tr class="x-small text-muted" style="font-size: 0.72rem;">
                                            <th class="text-start">Task Description</th>
                                            <th>Start &bull; End</th>
                                            @for($m = 1; $m <= 12; $m++)
                                                <th style="width: 38px;">M{{ $m }}</th>
                                            @endfor
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="workPlanTableBody">
                                        @foreach($workPlanData as $index => $row)
                                            @php
                                                $actName = is_array($row) ? ($row['activity'] ?? '') : ($row->activity ?? '');
                                                $selectedMonths = is_array($row) ? ($row['months'] ?? []) : ($row->months ?? []);
                                                $startDate = is_array($row) ? ($row['start_date'] ?? '') : '';
                                                $targetDate = is_array($row) ? ($row['target_date'] ?? '') : '';
                                            @endphp
                                            <tr class="workplan-row" data-index="{{ $index }}">
                                                <td class="text-start p-2">
                                                    <input type="text" name="work_plan_matrix[{{ $index }}][activity]" class="form-control form-control-sm border-0 bg-light rounded-3 fw-medium" value="{{ $actName }}" required>
                                                    <input type="hidden" name="milestones[{{ $index }}][title]" value="{{ $actName }}">
                                                    <input type="hidden" name="milestones[{{ $index }}][description]" value="Work plan milestone: {{ $actName }}">
                                                </td>
                                                <td class="p-2">
                                                    <div class="d-flex gap-1">
                                                        <input type="date" name="milestones[{{ $index }}][start_date]" class="form-control form-control-sm border-0 bg-light rounded-2 px-1" value="{{ $startDate }}" title="Start Date">
                                                        <input type="date" name="milestones[{{ $index }}][target_date]" class="form-control form-control-sm border-0 bg-light rounded-2 px-1" value="{{ $targetDate }}" title="Target Date">
                                                    </div>
                                                </td>
                                                @for($m = 1; $m <= 12; $m++)
                                                    <td class="p-1">
                                                        <input class="form-check-input gantt-check" type="checkbox" name="work_plan_matrix[{{ $index }}][months][]" value="{{ $m }}" {{ in_array($m, $selectedMonths) ? 'checked' : '' }} style="cursor: pointer;">
                                                    </td>
                                                @endfor
                                                <td class="p-1">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1" onclick="removeWorkPlanRow(this)" title="Remove Activity">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Check the months during which each milestone will be executed.</small>
                        </div>

                        <!-- Step 3 Footer Nav -->
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-bold" onclick="navigateToStep('tab-step-2')">
                                <i class="bi bi-arrow-left me-1"></i> Previous Step
                            </button>
                            <button type="button" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" onclick="navigateToStep('tab-step-4')">
                                Next: Line-Item Budget <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 4: Line-Item Budget (LIB) -->
                    <!-- ======================================================== -->
                    <div class="tab-pane fade" id="pane-step-4" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-2 border-bottom">
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">
                                        <i class="bi bi-calculator text-primary me-2"></i> Line-Item Budget (LIB)
                                    </h5>
                                    <p class="text-muted small mb-0">Itemize Maintenance and Other Operating Expenses (MOOE) and Capital Outlay (CO) according to RESU-FM-015 format.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" onclick="addBudgetRow('supplies', 'mooe')">
                                        <i class="bi bi-plus-circle-fill"></i> Add MOOE Item
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" onclick="addBudgetRow('capital_outlay', 'co')">
                                        <i class="bi bi-plus-circle-fill"></i> Add Capital Outlay
                                    </button>
                                </div>
                            </div>

                            @php
                                $budgetList = $proposal->budgetItems;
                            @endphp

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle small mb-3" id="budgetItemsTable">
                                    <thead class="table-light">
                                        <tr class="text-uppercase text-muted" style="font-size: 0.73rem;">
                                            <th style="width: 220px;">Category</th>
                                            <th>Item Description &amp; Specifications</th>
                                            <th style="width: 150px;">Existing Resources</th>
                                            <th style="width: 180px;" class="text-end">Proposed Amount (₱)</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="budgetItemsBody">
                                        @forelse($budgetList as $bIndex => $bItem)
                                            @php
                                                $catType = $bItem->category_type ?? ($bItem->category_group === 'capital_outlay' ? 'co' : 'mooe');
                                                $catGroup = $bItem->category_group ?? 'supplies';
                                            @endphp
                                            <tr class="budget-item-row" data-type="{{ $catType }}">
                                                <td class="p-2">
                                                    <select name="budget_items[{{ $bIndex }}][category_group]" class="form-select form-select-sm border-0 bg-light rounded-3 fw-bold {{ $catType === 'co' ? 'text-dark' : 'text-primary' }}" onchange="updateBudgetItemType(this)">
                                                        <option value="supplies" {{ $catGroup === 'supplies' ? 'selected' : '' }}>Supplies &amp; Materials</option>
                                                        <option value="travel" {{ $catGroup === 'travel' ? 'selected' : '' }}>Travelling Expenses (Local)</option>
                                                        <option value="professional_services" {{ $catGroup === 'professional_services' ? 'selected' : '' }}>Other Professional Services</option>
                                                        <option value="other_mooe" {{ $catGroup === 'other_mooe' ? 'selected' : '' }}>Other MOOE</option>
                                                        <option value="capital_outlay" {{ $catGroup === 'capital_outlay' ? 'selected' : '' }}>Capital Outlay (CO)</option>
                                                    </select>
                                                    <input type="hidden" name="budget_items[{{ $bIndex }}][category_type]" value="{{ $catType }}" class="category-type-input">
                                                </td>
                                                <td class="p-2">
                                                    <input type="text" name="budget_items[{{ $bIndex }}][item_name]" class="form-control form-control-sm border-0 bg-light rounded-3" value="{{ $bItem->item_name }}" placeholder="Item description" required>
                                                </td>
                                                <td class="p-2">
                                                    <input type="text" name="budget_items[{{ $bIndex }}][existing_resources]" class="form-control form-control-sm border-0 bg-light rounded-3" value="{{ $bItem->existing_resources ?? 'RESU' }}" placeholder="RESU / College">
                                                </td>
                                                <td class="p-2">
                                                    <input type="number" step="0.01" name="budget_items[{{ $bIndex }}][amount]" class="form-control form-control-sm border-0 bg-light rounded-3 text-end fw-bold budget-amount-input" value="{{ number_format($bItem->amount, 2, '.', '') }}" oninput="calculateBudgetTotals()" required>
                                                </td>
                                                <td class="p-2 text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1" onclick="removeBudgetRow(this)"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <!-- Fallback row if no budget items existed yet -->
                                            <tr class="budget-item-row" data-type="mooe">
                                                <td class="p-2">
                                                    <select name="budget_items[0][category_group]" class="form-select form-select-sm border-0 bg-light rounded-3 fw-bold text-primary" onchange="updateBudgetItemType(this)">
                                                        <option value="supplies" selected>Supplies &amp; Materials</option>
                                                        <option value="travel">Travelling Expenses (Local)</option>
                                                        <option value="professional_services">Other Professional Services</option>
                                                        <option value="other_mooe">Other MOOE</option>
                                                        <option value="capital_outlay">Capital Outlay (CO)</option>
                                                    </select>
                                                    <input type="hidden" name="budget_items[0][category_type]" value="mooe" class="category-type-input">
                                                </td>
                                                <td class="p-2">
                                                    <input type="text" name="budget_items[0][item_name]" class="form-control form-control-sm border-0 bg-light rounded-3" value="Research Consumables &amp; Supplies" placeholder="Item description" required>
                                                </td>
                                                <td class="p-2">
                                                    <input type="text" name="budget_items[0][existing_resources]" class="form-control form-control-sm border-0 bg-light rounded-3" value="RESU" placeholder="RESU / College">
                                                </td>
                                                <td class="p-2">
                                                    <input type="number" step="0.01" name="budget_items[0][amount]" class="form-control form-control-sm border-0 bg-light rounded-3 text-end fw-bold budget-amount-input" value="1000.00" oninput="calculateBudgetTotals()" required>
                                                </td>
                                                <td class="p-2 text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1" onclick="removeBudgetRow(this)"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                    <!-- Summary Calculation Footer -->
                                    <tfoot class="table-light fw-bold">
                                        <tr>
                                            <td colspan="3" class="text-end text-primary">Subtotal for MOOE:</td>
                                            <td class="text-end text-primary" id="subtotalMooeDisplay">₱ 0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end text-secondary">Subtotal for Capital Outlay (CO):</td>
                                            <td class="text-end text-secondary" id="subtotalCoDisplay">₱ 0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr class="table-warning" style="font-size: 1.05rem;">
                                            <td colspan="3" class="text-end text-dark fw-bold">GRAND TOTAL ESTIMATED BUDGET:</td>
                                            <td class="text-end text-dark fw-bold" id="grandTotalBudgetDisplay">₱ 0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Step 4 Footer Nav -->
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-bold" onclick="navigateToStep('tab-step-3')">
                                <i class="bi bi-arrow-left me-1"></i> Previous Step
                            </button>
                            <button type="button" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" onclick="navigateToStep('tab-step-5')">
                                Next: Review &amp; Resubmit <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 5: Document & Resubmission Actions -->
                    <!-- ======================================================== -->
                    <div class="tab-pane fade" id="pane-step-5" role="tabpanel">
                        <div class="row g-4">
                            <!-- Left: PDF Upload & Current Manuscript -->
                            <div class="col-12 col-lg-6">
                                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                                    <h5 class="fw-bold mb-3 border-start border-4 border-primary ps-3 text-dark">
                                        Document Manuscript Attachment
                                    </h5>
                                    
                                    @if($proposal->document_path)
                                        <div class="mb-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Current Attached File</label>
                                            <div class="bg-light p-3 rounded-4 d-flex justify-content-between align-items-center border">
                                                <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                                                    <span class="small fw-semibold text-dark text-truncate">{{ basename($proposal->document_path) }}</span>
                                                </div>
                                                <a href="{{ route('file.serve', ['path' => $proposal->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                                    View File
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mb-0">
                                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                                            Upload Revised PDF Document <span class="badge bg-light text-muted border fw-normal ms-1">Optional</span>
                                        </label>
                                        <input type="file" name="document" class="form-control bg-light border-0 py-2.5 px-3 rounded-4" accept="application/pdf">
                                        <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Leave empty to retain the current manuscript file. Uploading a new PDF creates a new version while preserving complete audit history.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Resubmission Guidance -->
                            <div class="col-12 col-lg-6">
                                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                                    <h5 class="fw-bold mb-3 border-start border-4 border-success ps-3 text-dark">
                                        Resubmission Routing
                                    </h5>
                                    
                                    @if($proposal->status === 'returned_for_revision' && $proposal->returned_by_role === 'staff')
                                        <div class="p-3 bg-info bg-opacity-10 text-info rounded-4 border border-info border-opacity-25 mb-4 small">
                                            <div class="fw-bold mb-1 text-dark"><i class="bi bi-fast-forward-fill text-info me-1"></i> Fast-Track Administrative Routing</div>
                                            Once updated, this proposal will route directly back to the <strong>Research Unit Receiving Staff</strong> for verification without having to restart department endorsements.
                                        </div>
                                    @elseif($proposal->status === 'revision_required' || $proposal->status === 'approved_with_revisions')
                                        <div class="p-3 bg-warning bg-opacity-10 text-dark rounded-4 border border-warning border-opacity-25 mb-4 small">
                                            <div class="fw-bold mb-1"><i class="bi bi-arrow-repeat text-warning me-1"></i> Evaluator Re-evaluation</div>
                                            Once submitted, your assigned peer evaluators will be notified to review your revised manuscript and verify addressed revision tags.
                                        </div>
                                    @else
                                        <div class="p-3 bg-light rounded-4 border mb-4 small text-secondary">
                                            <div class="fw-bold mb-1 text-dark"><i class="bi bi-check2-circle text-primary me-1"></i> Draft Saving or Routing</div>
                                            You can save your current edits as a draft or submit to your College Coordinator for formal college endorsement.
                                        </div>
                                    @endif

                                    <div class="vstack gap-2 small text-muted">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                            <span>All 5 template sections are automatically preserved.</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                            <span>Work Plan Matrix &amp; Line-Item Budget totals are recalculated.</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                            <span>Full manuscript text stream syncs with In-Browser Review Workspace.</span>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-3 border-top vstack gap-2">
                                        <a href="{{ route('template.download.proposal') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-bold w-100 shadow-xs d-inline-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-file-earmark-word-fill text-primary fs-5"></i> Download Word (.docx) Template
                                        </a>
                                        <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2.5 fw-bold w-100 shadow-xs" onclick="openLivePreviewModal()">
                                            <i class="bi bi-eye-fill me-1.5"></i> Live Preview Form RESU-FM-015
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5 Footer & Submission Actions -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mt-4">
                            <div class="d-flex flex-column-reverse flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                                <button type="button" class="btn btn-outline-secondary px-4 py-2.5 rounded-pill fw-bold" onclick="navigateToStep('tab-step-4')">
                                    <i class="bi bi-arrow-left me-1"></i> Back to Line-Item Budget
                                </button>
                                
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <button type="submit" name="action" value="draft" class="btn btn-outline-secondary px-4 py-2.5 rounded-pill fw-bold shadow-xs">
                                        <i class="bi bi-save me-1"></i> SAVE AS DRAFT
                                    </button>
                                    <button type="submit" name="action" value="submit" class="btn btn-primary px-5 py-2.5 rounded-pill fw-bold shadow-sm">
                                        <i class="bi bi-send-check-fill me-1"></i>
                                        @if($proposal->status === 'returned_for_revision' && $proposal->returned_by_role === 'staff')
                                            UPDATE &amp; RESUBMIT TO RESEARCH UNIT
                                        @elseif($proposal->status === 'revision_required' || $proposal->status === 'approved_with_revisions')
                                            UPDATE &amp; RESUBMIT TO EVALUATOR
                                        @else
                                            UPDATE PROPOSAL
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Live Preview Modal (Form RESU-FM-015 Simulation) -->
    <div class="modal fade" id="livePreviewModal" tabindex="-1" aria-labelledby="livePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 bg-light px-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-text-fill text-primary fs-5"></i>
                        <h5 class="modal-title fw-bold text-dark fs-6" id="livePreviewModalLabel">Form RESU-FM-015 Official Manuscript Preview</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 p-md-5" style="background-color: #faf9f6;">
                    <div class="bg-white p-4 p-md-5 rounded-3 shadow-sm border mx-auto" style="max-width: 820px; line-height: 1.7; color: #222;" id="previewContentArea">
                        <!-- UA Header -->
                        <div class="text-center pb-3 mb-4 border-bottom">
                            <div class="small text-muted text-uppercase" style="font-size: 0.8rem;">Republic of the Philippines</div>
                            <h4 class="fw-bold text-uppercase mb-0 text-dark">UNIVERSITY OF ANTIQUE</h4>
                            <div class="small text-muted" style="font-size: 0.82rem;">Sibalom, Antique</div>
                            <div class="badge bg-dark text-white rounded-pill px-3 py-1 mt-2 text-uppercase font-monospace" style="font-size: 0.75rem;">RESEARCH PROPOSAL TEMPLATE (RESU-FM-015)</div>
                        </div>

                        <!-- Metadata Table -->
                        <table class="table table-borderless small mb-4">
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-muted text-uppercase" style="width: 220px; font-size: 0.75rem;">Title:</td>
                                    <td class="fw-bold text-dark" id="prevTitle" style="font-size: 1.05rem;">—</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Name of Proponent/s:</td>
                                    <td class="text-dark" id="prevProponent">{{ $proposal->user->name }} (Lead Researcher)</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Credentials of Key:</td>
                                    <td class="text-dark" id="prevCredentials">—</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">College Affiliation:</td>
                                    <td class="text-dark" id="prevCollege">—</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">SDGs &amp; Thrusts:</td>
                                    <td class="text-dark" id="prevSdgs">—</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Sections Stream -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">Background of the Study</h6>
                            <div class="small text-secondary lh-lg" style="white-space: pre-wrap;" id="prevBackground">—</div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">Significance of the Study</h6>
                            <div class="small text-secondary lh-lg" style="white-space: pre-wrap;" id="prevSignificance">—</div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">Definition of Terms</h6>
                            <div class="small text-secondary lh-lg" style="white-space: pre-wrap;" id="prevTerms">—</div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">Review of Related Literature</h6>
                            <div class="small text-secondary lh-lg" style="white-space: pre-wrap;" id="prevLiterature">—</div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">Methodology</h6>
                            <div class="small text-secondary lh-lg" style="white-space: pre-wrap;" id="prevMethodology">—</div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">Work Plan (Gantt Chart Matrix)</h6>
                            <div class="table-responsive" id="prevWorkPlanContainer"></div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">Line-Item Budget (LIB) Summary</h6>
                            <div class="table-responsive" id="prevBudgetContainer"></div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">References</h6>
                            <div class="small text-secondary lh-lg font-monospace" style="white-space: pre-wrap;" id="prevReferences">—</div>
                        </div>

                        <div class="mt-5 pt-3 border-top text-center text-muted small">
                            Prepared by: <strong>{{ $proposal->user->name }}</strong> &bull; University of Antique Research Services Unit
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close Preview</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="bootstrap.Modal.getInstance(document.getElementById('livePreviewModal')).hide(); navigateToStep('tab-step-5');">
                        Proceed to Resubmission
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Rich Text Formatting Helper
        function insertFormat(textareaId, prefix, suffix = '') {
            const textarea = document.getElementById(textareaId);
            if (!textarea) return;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const selected = text.substring(start, end) || 'text';
            const replacement = prefix + selected + suffix;
            textarea.value = text.substring(0, start) + replacement + text.substring(end);
            textarea.focus();
            textarea.selectionStart = start + prefix.length;
            textarea.selectionEnd = start + prefix.length + selected.length;
        }

        // Live Preview Modal Populator
        function openLivePreviewModal() {
            document.getElementById('prevTitle').innerText = document.getElementById('editProposalTitle').value || 'Untitled Research';
            document.getElementById('prevCredentials').innerText = document.getElementById('editCredentials').value || 'Not specified';
            document.getElementById('prevCollege').innerText = document.getElementById('editProposalCollege').value || '{{ $proposal->college_affiliation ?: ($proposal->user->department ?? "College of Computer Studies") }}';
            document.getElementById('prevSdgs').innerText = document.getElementById('editSdgs').value || 'None specified';

            document.getElementById('prevBackground').innerText = document.getElementById('editBackgroundOfStudy').value || 'No background provided yet.';
            document.getElementById('prevSignificance').innerText = document.getElementById('editSignificanceOfStudy').value || 'No significance provided yet.';
            document.getElementById('prevTerms').innerText = document.getElementById('editDefinitionOfTerms').value || 'No terms provided yet.';
            document.getElementById('prevLiterature').innerText = document.getElementById('editLiteratureReview').value || 'No literature review provided yet.';
            document.getElementById('prevMethodology').innerText = document.getElementById('editMethodologyField').value || 'No methodology provided yet.';
            document.getElementById('prevReferences').innerText = document.getElementById('editReferencesField').value || 'No references provided yet.';

            const workPlanTable = document.getElementById('workPlanTable');
            if (workPlanTable) {
                document.getElementById('prevWorkPlanContainer').innerHTML = workPlanTable.outerHTML;
                document.querySelectorAll('#prevWorkPlanContainer th:last-child, #prevWorkPlanContainer td:last-child').forEach(el => el.remove());
            }

            const budgetTable = document.getElementById('budgetItemsTable');
            if (budgetTable) {
                document.getElementById('prevBudgetContainer').innerHTML = budgetTable.outerHTML;
                document.querySelectorAll('#prevBudgetContainer th:last-child, #prevBudgetContainer td:last-child').forEach(el => el.remove());
            }

            const previewModal = new bootstrap.Modal(document.getElementById('livePreviewModal'));
            previewModal.show();
        }
        function navigateToStep(tabId) {
            const triggerEl = document.getElementById(tabId);
            if (triggerEl) {
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
                window.scrollTo({ top: 120, behavior: 'smooth' });
            }
        }

        const tabList = [
            { id: 'tab-step-1', label: 'Step 1 of 5: General Info' },
            { id: 'tab-step-2', label: 'Step 2 of 5: Manuscript Sections' },
            { id: 'tab-step-3', label: 'Step 3 of 5: Work Plan Matrix' },
            { id: 'tab-step-4', label: 'Step 4 of 5: Line-Item Budget' },
            { id: 'tab-step-5', label: 'Step 5 of 5: Review & Resubmit' }
        ];

        document.querySelectorAll('#proposalFormTabs button[data-bs-toggle="pill"]').forEach((tabEl) => {
            tabEl.addEventListener('shown.bs.tab', function (event) {
                const target = tabList.find(t => t.id === event.target.id);
                if (target) {
                    const badge = document.getElementById('stepCounterBadge');
                    if (badge) badge.innerText = target.label;
                }
            });
        });

        // Dynamic Work Plan Rows
        let workPlanCounter = {{ count($workPlanData) + 5 }};

        function addWorkPlanRow() {
            const tbody = document.getElementById('workPlanTableBody');
            const newIndex = workPlanCounter++;
            
            let monthsHtml = '';
            for (let m = 1; m <= 12; m++) {
                monthsHtml += `
                    <td class="p-1">
                        <input class="form-check-input gantt-check" type="checkbox" name="work_plan_matrix[${newIndex}][months][]" value="${m}" style="cursor: pointer;">
                    </td>
                `;
            }

            const tr = document.createElement('tr');
            tr.className = 'workplan-row';
            tr.setAttribute('data-index', newIndex);
            tr.innerHTML = `
                <td class="text-start p-2">
                    <input type="text" name="work_plan_matrix[${newIndex}][activity]" class="form-control form-control-sm border-0 bg-light rounded-3 fw-medium" placeholder="Enter activity name..." required>
                    <input type="hidden" name="milestones[${newIndex}][title]" value="New Activity">
                    <input type="hidden" name="milestones[${newIndex}][description]" value="Work plan milestone">
                </td>
                <td class="p-2">
                    <div class="d-flex gap-1">
                        <input type="date" name="milestones[${newIndex}][start_date]" class="form-control form-control-sm border-0 bg-light rounded-2 px-1">
                        <input type="date" name="milestones[${newIndex}][target_date]" class="form-control form-control-sm border-0 bg-light rounded-2 px-1">
                    </div>
                </td>
                ${monthsHtml}
                <td class="p-1">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1" onclick="removeWorkPlanRow(this)" title="Remove Activity">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);

            const textInput = tr.querySelector('input[type="text"]');
            const hiddenTitle = tr.querySelector('input[name^="milestones"][name$="[title]"]');
            textInput.addEventListener('input', function() {
                hiddenTitle.value = this.value;
            });
        }

        function removeWorkPlanRow(btn) {
            const row = btn.closest('tr');
            if (document.querySelectorAll('#workPlanTableBody tr').length > 1) {
                row.remove();
            } else {
                alert('At least one activity is required in the Work Plan.');
            }
        }

        document.querySelectorAll('#workPlanTableBody tr').forEach(row => {
            const textInput = row.querySelector('input[type="text"]');
            const hiddenTitle = row.querySelector('input[name^="milestones"][name$="[title]"]');
            if (textInput && hiddenTitle) {
                textInput.addEventListener('input', function() {
                    hiddenTitle.value = this.value;
                });
            }
        });

        // Dynamic Line-Item Budget Rows
        let budgetCounter = {{ count($budgetList) + 5 }};

        function addBudgetRow(catGroup = 'supplies', catType = 'mooe') {
            const tbody = document.getElementById('budgetItemsBody');
            const newIndex = budgetCounter++;

            const tr = document.createElement('tr');
            tr.className = 'budget-item-row';
            tr.setAttribute('data-type', catType);

            tr.innerHTML = `
                <td class="p-2">
                    <select name="budget_items[${newIndex}][category_group]" class="form-select form-select-sm border-0 bg-light rounded-3 fw-bold ${catType === 'co' ? 'text-dark' : 'text-primary'}" onchange="updateBudgetItemType(this)">
                        <option value="supplies" ${catGroup === 'supplies' ? 'selected' : ''}>Supplies &amp; Materials</option>
                        <option value="travel" ${catGroup === 'travel' ? 'selected' : ''}>Travelling Expenses (Local)</option>
                        <option value="professional_services" ${catGroup === 'professional_services' ? 'selected' : ''}>Other Professional Services</option>
                        <option value="other_mooe" ${catGroup === 'other_mooe' ? 'selected' : ''}>Other MOOE</option>
                        <option value="capital_outlay" ${catGroup === 'capital_outlay' ? 'selected' : ''}>Capital Outlay (CO)</option>
                    </select>
                    <input type="hidden" name="budget_items[${newIndex}][category_type]" value="${catType}" class="category-type-input">
                </td>
                <td class="p-2">
                    <input type="text" name="budget_items[${newIndex}][item_name]" class="form-control form-control-sm border-0 bg-light rounded-3" placeholder="Description of expenditure..." required>
                </td>
                <td class="p-2">
                    <input type="text" name="budget_items[${newIndex}][existing_resources]" class="form-control form-control-sm border-0 bg-light rounded-3" value="RESU" placeholder="RESU / College">
                </td>
                <td class="p-2">
                    <input type="number" step="0.01" name="budget_items[${newIndex}][amount]" class="form-control form-control-sm border-0 bg-light rounded-3 text-end fw-bold budget-amount-input" value="0.00" oninput="calculateBudgetTotals()" required>
                </td>
                <td class="p-2 text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1" onclick="removeBudgetRow(this)"><i class="bi bi-trash"></i></button>
                </td>
            `;

            tbody.appendChild(tr);
            calculateBudgetTotals();
        }

        function removeBudgetRow(btn) {
            const row = btn.closest('tr');
            if (document.querySelectorAll('#budgetItemsBody tr').length > 1) {
                row.remove();
                calculateBudgetTotals();
            } else {
                alert('At least one budget line-item is required.');
            }
        }

        function updateBudgetItemType(select) {
            const row = select.closest('tr');
            const typeInput = row.querySelector('.category-type-input');
            if (select.value === 'capital_outlay') {
                typeInput.value = 'co';
                row.setAttribute('data-type', 'co');
                select.className = 'form-select form-select-sm border-0 bg-light rounded-3 fw-bold text-dark';
            } else {
                typeInput.value = 'mooe';
                row.setAttribute('data-type', 'mooe');
                select.className = 'form-select form-select-sm border-0 bg-light rounded-3 fw-bold text-primary';
            }
            calculateBudgetTotals();
        }

        function calculateBudgetTotals() {
            let mooeTotal = 0;
            let coTotal = 0;

            document.querySelectorAll('#budgetItemsBody tr.budget-item-row').forEach(row => {
                const type = row.getAttribute('data-type');
                const amtInput = row.querySelector('.budget-amount-input');
                const val = parseFloat(amtInput ? amtInput.value : 0) || 0;

                if (type === 'co') {
                    coTotal += val;
                } else {
                    mooeTotal += val;
                }
            });

            const grandTotal = mooeTotal + coTotal;
            const formatPhp = (num) => '₱ ' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const mooeEl = document.getElementById('subtotalMooeDisplay');
            const coEl = document.getElementById('subtotalCoDisplay');
            const grandEl = document.getElementById('grandTotalBudgetDisplay');

            if (mooeEl) mooeEl.innerText = formatPhp(mooeTotal);
            if (coEl) coEl.innerText = formatPhp(coTotal);
            if (grandEl) grandEl.innerText = formatPhp(grandTotal);
        }

        calculateBudgetTotals();

        // ==========================================
        // SESSION KEEP-ALIVE & CSRF REFRESH
        // ==========================================
        setInterval(() => {
            fetch("{{ url('/ping-session') }}", {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data && data.csrf_token) {
                    document.querySelectorAll('input[name="_token"]').forEach(input => {
                        input.value = data.csrf_token;
                    });
                }
            })
            .catch(() => {});
        }, 5 * 60 * 1000); // Heartbeat every 5 minutes

        // Intercept form submit to ensure fresh CSRF token and prevent 419 Page Expired
        let isEditSubmitting = false;
        const proposalEditForm = document.getElementById('proposalEditForm');
        if (proposalEditForm) {
            proposalEditForm.addEventListener('submit', async function(e) {
                if (isEditSubmitting) return;
                e.preventDefault();
                isEditSubmitting = true;

                const activeBtn = document.activeElement && document.activeElement.type === 'submit' ? document.activeElement : null;
                if (activeBtn) {
                    let hidden = document.getElementById('submittedEditActionValue');
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = activeBtn.name || 'action';
                        hidden.id = 'submittedEditActionValue';
                        proposalEditForm.appendChild(hidden);
                    }
                    hidden.value = activeBtn.value;
                    activeBtn.disabled = true;
                    activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Updating...';
                }

                try {
                    const resp = await fetch("{{ url('/ping-session') }}", {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await resp.json();
                    if (data && data.csrf_token) {
                        document.querySelectorAll('input[name="_token"]').forEach(input => {
                            input.value = data.csrf_token;
                        });
                    }
                } catch (err) {
                    console.debug('Session check bypassed:', err);
                }

                proposalEditForm.submit();
            });
        }
    </script>
</x-app-layout>
