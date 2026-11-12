<x-app-layout>
    <div class="mb-3 mb-md-4">
        <a href="{{ route('dashboard') }}" class="btn btn-link text-muted p-0 text-decoration-none d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row justify-content-center pb-5">
        <div class="col-12 col-xl-11 col-xxl-10">

            <!-- Header Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="p-4 p-md-5 text-white" style="background: linear-gradient(135deg, #1f080c 0%, #3e1218 50%, #791d1d 100%);">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-15 p-3 rounded-4 flex-shrink-0 text-warning">
                                <i class="bi bi-file-earmark-text-fill fs-2"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <span class="badge bg-warning text-dark fw-bold rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">FORM RESU-FM-015</span>
                                    <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">University of Antique Research Proposal Template</span>
                                </div>
                                <h3 class="h4 fw-bold mb-1 text-white">Create Research Proposal</h3>
                                <p class="text-white text-opacity-75 small mb-0">Fill in the official template sections directly, build your Work Plan matrix and Line-Item Budget, or optionally attach a manuscript PDF.</p>
                            </div>
                        </div>

                        <!-- Step indicator & Template download -->
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
                                <i class="bi bi-cloud-arrow-up me-1"></i> 5. Attachment &amp; Submit
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Form Form -->
            <form method="POST" action="{{ route('proposal.store') }}" enctype="multipart/form-data" id="proposalCreateForm">
                @csrf

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

                <!-- Auto-saved Local Draft Recovery Banner -->
                <div id="localDraftBanner" class="alert alert-info border-0 rounded-4 p-3 p-md-4 mb-4 shadow-sm d-none" style="background-color: #f0fdf4; border-left: 5px solid #22c55e !important;">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div class="d-flex align-items-center gap-2.5">
                            <i class="bi bi-cloud-arrow-down-fill fs-4 text-success"></i>
                            <div>
                                <div class="fw-bold text-dark small" id="localDraftBannerTime">Unsaved Proposal Draft Found</div>
                                <div class="text-muted" style="font-size: 0.75rem;">You have draft content automatically saved from a previous writing session.</div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold shadow-xs text-nowrap" onclick="restoreLocalDraft()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Restore My Work
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 text-nowrap" onclick="dismissLocalDraft()">
                                Discard
                            </button>
                        </div>
                    </div>
                </div>

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

                        <!-- ========================================== -->
                        <!-- SUBMISSION MODE SELECTOR (Option A vs Option B) -->
                        <!-- ========================================== -->
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                            <div class="p-3 p-md-4 border-bottom bg-light bg-opacity-60 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                <div>
                                    <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-magic text-warning"></i> Choose How to Create Your Proposal
                                    </h5>
                                    <p class="text-muted small mb-0">Select your preferred submission path. You can auto-fill everything from an existing PDF manuscript or compose directly section-by-section.</p>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold rounded-pill px-3 py-1.5 small border border-primary border-opacity-25 text-nowrap">
                                    <i class="bi bi-shield-check me-1"></i> Form RESU-FM-015 Compliant
                                </span>
                            </div>

                            <div class="p-3 p-md-4">
                                <div class="row g-3">
                                    <!-- OPTION A: Smart Upload & Auto-Fill (Fast Track) -->
                                    <div class="col-12 col-lg-7">
                                        <div class="card h-100 border-2 rounded-4 p-3 p-md-4 position-relative border-primary bg-primary bg-opacity-10 shadow-xs" id="modeCardAutoFill" style="border-style: solid !important;">
                                            <div class="position-absolute top-0 end-0 m-3 d-none d-sm-block">
                                                <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 small fw-bold shadow-xs">
                                                    <i class="bi bi-star-fill text-warning me-1"></i> RECOMMENDED (FAST TRACK)
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center gap-2.5 mb-2">
                                                <div class="rounded-circle bg-primary text-white p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                                    <i class="bi bi-cloud-arrow-up-fill fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0 text-dark">Option A: Upload &amp; Auto-Fill from PDF</h6>
                                                    <span class="text-muted" style="font-size: 0.75rem;">Already have your proposal drafted in Word / PDF?</span>
                                                </div>
                                            </div>

                                            <p class="text-secondary small mb-3" style="line-height: 1.5;">
                                                Drop your PDF manuscript here. Our smart reader will instantly extract your <strong>Title, Abstract, Proponent Info, Narrative Chapters, and Line-Item Budget (LIB)</strong> and auto-populate all form sections in seconds.
                                            </p>

                                            <!-- Dropzone Input Box -->
                                            <div class="border-2 border-primary border-opacity-50 rounded-4 p-4 text-center bg-white position-relative shadow-2xs hover-border-primary transition-all" id="smartDropzoneBox" style="border-style: dashed !important; cursor: pointer;">
                                                <input type="file" id="smartProposalDoc" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept="application/pdf" style="cursor: pointer; z-index: 5;">
                                                <i class="bi bi-file-earmark-pdf-fill fs-1 text-primary mb-2 d-block"></i>
                                                <h6 class="fw-bold mb-1 text-dark" id="smartDropzoneTitle">Click to attach external PDF or drag &amp; drop</h6>
                                                <span class="text-muted small d-block" id="smartDropzoneSub" style="font-size: 0.75rem;">PDF FORMAT &bull; MAXIMUM 20MB &bull; Official Form RESU-FM-015</span>
                                            </div>

                                            <!-- Extraction Status Box -->
                                            <div id="smartExtractStatus" class="mt-3 p-3 rounded-4 bg-white border border-primary border-opacity-25 d-none shadow-xs">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="spinner-border spinner-border-sm text-primary d-none" id="smartSpinner" role="status"></div>
                                                        <i class="bi bi-check-circle-fill text-success fs-5 d-none" id="smartSuccessIcon"></i>
                                                        <span class="fw-bold small text-dark" id="smartStatusTitle">Analyzing PDF layers...</span>
                                                    </div>
                                                    <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 small fw-bold" id="smartBadge">Processing</span>
                                                </div>
                                                <div class="small text-muted mb-2" id="smartStatusMessage" style="font-size: 0.8rem;">
                                                    Extracting document text, manuscript chapters, and line-item budget table...
                                                </div>
                                                <!-- Action Buttons after extraction -->
                                                <div class="d-flex align-items-center gap-2 flex-wrap pt-2.5 border-top d-none" id="smartActionRow">
                                                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1.5" onclick="openLivePreviewModal()">
                                                        <i class="bi bi-eye-fill"></i> View Form RESU-FM-015
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" onclick="document.getElementById('proposalTitle').scrollIntoView({ behavior: 'smooth', block: 'center' });">
                                                        <i class="bi bi-pencil-square"></i> Review Extracted Info
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold ms-auto shadow-xs d-inline-flex align-items-center gap-1" onclick="navigateToStep('tab-step-5')">
                                                        Proceed to Submit <i class="bi bi-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- OPTION B: Guided Manual Entry -->
                                    <div class="col-12 col-lg-5">
                                        <div class="card h-100 border rounded-4 p-3 p-md-4 bg-light bg-opacity-40 shadow-xs" id="modeCardManual">
                                            <div class="d-flex align-items-center gap-2.5 mb-2">
                                                <div class="rounded-circle bg-secondary bg-opacity-15 text-secondary p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                                    <i class="bi bi-pencil-fill fs-6"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0 text-dark">Option B: Draft Directly Online</h6>
                                                    <span class="text-muted" style="font-size: 0.75rem;">Drafting from scratch in the system?</span>
                                                </div>
                                            </div>

                                            <p class="text-secondary small mb-3" style="line-height: 1.5;">
                                                Compose your proposal directly section-by-section. Use our structured 5-step guided wizard to draft official chapters, build your Gantt schedule, and calculate your Line-Item Budget directly.
                                            </p>

                                            <div class="bg-white rounded-4 p-3 border mb-3 small shadow-2xs">
                                                <div class="d-flex align-items-center gap-2 mb-1.5 text-secondary">
                                                    <i class="bi bi-check2-circle text-primary"></i> <span>Step 1: General Info &amp; Co-Proponents</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 mb-1.5 text-secondary">
                                                    <i class="bi bi-check2-circle text-primary"></i> <span>Step 2: Narrative Sections (RRL, Methodology)</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 mb-1.5 text-secondary">
                                                    <i class="bi bi-check2-circle text-primary"></i> <span>Step 3: Work Plan Matrix (Gantt)</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 text-secondary">
                                                    <i class="bi bi-check2-circle text-primary"></i> <span>Step 4: Line-Item Budget (MOOE &amp; CO)</span>
                                                </div>
                                            </div>

                                            <button type="button" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold w-100 mt-auto d-inline-flex align-items-center justify-content-center gap-2 shadow-xs" onclick="document.getElementById('proposalTitle').scrollIntoView({ behavior: 'smooth', block: 'center' });">
                                                <i class="bi bi-input-cursor-text"></i> Start Manual Guided Entry
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                        <input type="text" name="title" id="proposalTitle" class="form-control border-0 bg-light py-2.5 py-md-3 px-3 px-md-4 rounded-4 transition-all" placeholder="e.g. Design and Implementation of University Research Life-Cycle Platform" value="{{ old('title') }}" required>
                                        <div id="titleSuggestionsContainer" class="mt-2 d-none">
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                <small class="text-muted fw-bold d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                                    <i class="bi bi-lightbulb-fill text-warning"></i> Alternative Suggestions:
                                                </small>
                                                <div id="titleSuggestionsList" class="d-inline-flex flex-wrap gap-1"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Research Field & College -->
                                    <div class="row g-3 mb-3 mb-md-4">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                                                Research Field / Discipline
                                            </label>
                                            <input type="text" name="research_field" id="proposalField" class="form-control border-0 bg-light py-2.5 px-3 rounded-4" placeholder="e.g. Information Technology, Agriculture, Education" value="{{ old('research_field') }}">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                                                College Affiliation
                                            </label>
                                            <input type="text" name="college_affiliation" id="proposalCollege" class="form-control border-0 bg-light py-2.5 px-3 rounded-4" placeholder="e.g. College of Computer Studies (CCS)" value="{{ old('college_affiliation', auth()->user()->department) }}">
                                        </div>
                                    </div>

                                    <!-- Credentials of Key Proponent/s -->
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                                            Credentials of Key Proponent/s
                                        </label>
                                        <textarea name="credentials" rows="3" class="form-control border-0 bg-light py-2.5 px-3 rounded-4" placeholder="Specify highest educational attainment, academic rank, specialization, and past research experience...">{{ old('credentials') }}</textarea>
                                        <small class="text-muted" style="font-size: 0.72rem;">Example: MS in Information Technology, Assistant Professor II, Specialization in Cloud Systems &amp; Web Applications.</small>
                                    </div>

                                    <!-- SDGs Addressed & Institutional Thrusts -->
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                                            Sustainable Development Goals (SDGs) &amp; Thrusts
                                        </label>
                                        <input type="text" name="sdgs_addressed" class="form-control border-0 bg-light py-2.5 px-3 rounded-4" placeholder="e.g. SDG 4: Quality Education, SDG 9: Industry & Innovation, UA Institutional Priority 1" value="{{ old('sdgs_addressed') }}">
                                    </div>

                                    <!-- Executive Abstract -->
                                    <div class="mb-0">
                                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                                            Executive Abstract / Summary <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="abstract" id="proposalAbstract" class="form-control border-0 bg-light py-2.5 px-3 rounded-4" rows="4" placeholder="Concise overview summarizing the rationale, objectives, methodology, and expected results..." required>{{ old('abstract') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Collaborators Picker -->
                            <div class="col-12 col-lg-5">
                                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                                    <h5 class="fw-bold mb-2 border-start border-4 border-info ps-3 text-dark">Co-Proponents &amp; Collaborators</h5>
                                    <p class="text-muted small mb-3">Check faculty members and co-authors participating in this research proposal.</p>

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
                                                        <input class="form-check-input border-primary rounded" type="checkbox" name="collaborators[]" value="{{ $researcher->id }}" {{ is_array(old('collaborators')) && in_array($researcher->id, old('collaborators')) ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
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
                                            Tagging faculty members will send them a collaboration invitation with an <strong>Accept or Decline</strong> option. Only researchers who have set their profile to <strong>Public (Allow Tagging)</strong> appear here.
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
                                    <p class="text-muted small mb-0">Write the core research chapters directly. Reviewers will be able to read and place inline comments on these exact sections.</p>
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
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fw-bold" onclick="insertFormat('backgroundOfStudy', '**', '**')" title="Bold"><strong>B</strong></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fst-italic" onclick="insertFormat('backgroundOfStudy', '*', '*')" title="Italic (scientific names / et al.)"><em>I</em></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('backgroundOfStudy', '\n• ', '')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('backgroundOfStudy', '\n1. ', '')" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('backgroundOfStudy', '\n### ', '')" title="Subheading"><i class="bi bi-type-h3"></i></button>
                                        </div>
                                    </div>
                                    <div class="text-muted small mb-1" style="font-size: 0.72rem;">Statement of the Problem, Theoretical / Conceptual Framework</div>
                                    <textarea name="background_of_study" id="backgroundOfStudy" class="form-control border-0 bg-light p-3 rounded-4" rows="6" placeholder="Present the context, current baseline problem, statement of specific research objectives/questions, and the theoretical or conceptual framework guiding the investigation...">{{ old('background_of_study') }}</textarea>
                                </div>

                                <!-- Significance of the Study -->
                                <div class="col-12 col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-bold text-dark mb-0">
                                            Significance of the Study
                                        </label>
                                        <div class="btn-group btn-group-sm bg-white border rounded-pill p-0.5 shadow-2xs">
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-1.5 py-0.5 text-muted fw-bold" onclick="insertFormat('significanceOfStudy', '**', '**')" title="Bold">B</button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-1.5 py-0.5 text-muted fst-italic" onclick="insertFormat('significanceOfStudy', '*', '*')" title="Italic"><em>I</em></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-1.5 py-0.5 text-muted" onclick="insertFormat('significanceOfStudy', '\n• ', '')" title="Bullet"><i class="bi bi-list-ul"></i></button>
                                        </div>
                                    </div>
                                    <textarea name="significance_of_study" id="significanceOfStudy" class="form-control border-0 bg-light p-3 rounded-4" rows="5" placeholder="Detail who will benefit from this study (e.g. students, faculty, industry partners, policymakers, community) and how the results will be applied...">{{ old('significance_of_study') }}</textarea>
                                </div>

                                <!-- Definition of Terms -->
                                <div class="col-12 col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-bold text-dark mb-0">
                                            Definition of Terms
                                        </label>
                                        <div class="btn-group btn-group-sm bg-white border rounded-pill p-0.5 shadow-2xs">
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-1.5 py-0.5 text-muted fw-bold" onclick="insertFormat('definitionOfTerms', '**', '**')" title="Bold">B</button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-1.5 py-0.5 text-muted fst-italic" onclick="insertFormat('definitionOfTerms', '*', '*')" title="Italic"><em>I</em></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-1.5 py-0.5 text-muted" onclick="insertFormat('definitionOfTerms', '\n• ', '')" title="Bullet"><i class="bi bi-list-ul"></i></button>
                                        </div>
                                    </div>
                                    <textarea name="definition_of_terms" id="definitionOfTerms" class="form-control border-0 bg-light p-3 rounded-4" rows="5" placeholder="Define key terms, technical jargon, and operational definitions used across the research...">{{ old('definition_of_terms') }}</textarea>
                                </div>

                                <!-- Review of Related Literature -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-1">
                                        <label class="form-label fw-bold text-dark mb-0">
                                            Review of Related Literature (RRL)
                                        </label>
                                        <div class="btn-group btn-group-sm bg-white border rounded-pill p-0.5 shadow-2xs">
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fw-bold" onclick="insertFormat('literatureReview', '**', '**')" title="Bold"><strong>B</strong></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fst-italic" onclick="insertFormat('literatureReview', '*', '*')" title="Italic"><em>I</em></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('literatureReview', '\n• ', '')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('literatureReview', '\n1. ', '')" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                                        </div>
                                    </div>
                                    <textarea name="literature_review" id="literatureReview" class="form-control border-0 bg-light p-3 rounded-4" rows="6" placeholder="Synthesize local and international literature, recent journal publications, related technological innovations, and clarify the research gap...">{{ old('literature_review') }}</textarea>
                                </div>

                                <!-- Methodology -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-1">
                                        <label class="form-label fw-bold text-dark mb-0">
                                            Methodology &amp; Research Design <span class="text-danger">*</span>
                                        </label>
                                        <div class="btn-group btn-group-sm bg-white border rounded-pill p-0.5 shadow-2xs">
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fw-bold" onclick="insertFormat('methodologyField', '**', '**')" title="Bold"><strong>B</strong></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fst-italic" onclick="insertFormat('methodologyField', '*', '*')" title="Italic"><em>I</em></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('methodologyField', '\n• ', '')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('methodologyField', '\n1. ', '')" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('methodologyField', '\n### ', '')" title="Subheading"><i class="bi bi-type-h3"></i></button>
                                        </div>
                                    </div>
                                    <div class="text-muted small mb-1" style="font-size: 0.72rem;">Design, Participants, Instruments, Ethics, Data Analysis</div>
                                    <textarea name="methodology" id="methodologyField" class="form-control border-0 bg-light p-3 rounded-4" rows="6" placeholder="Describe the research design, target population and sampling method, data collection instruments, ethical clearance considerations, and statistical / qualitative analysis tools...">{{ old('methodology') }}</textarea>
                                </div>

                                <!-- References -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-1">
                                        <label class="form-label fw-bold text-dark mb-0">
                                            References &amp; Citations
                                        </label>
                                        <div class="btn-group btn-group-sm bg-white border rounded-pill p-0.5 shadow-2xs">
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted fst-italic" onclick="insertFormat('referencesField', '*', '*')" title="Italic (Journal titles)"><em>I</em></button>
                                            <button type="button" class="btn btn-sm btn-light border-0 rounded-pill px-2 py-0.5 text-muted" onclick="insertFormat('referencesField', '\n', '')" title="New entry"><i class="bi bi-arrow-return-left"></i></button>
                                        </div>
                                    </div>
                                    <textarea name="references" id="referencesField" class="form-control border-0 bg-light p-3 rounded-4 font-monospace small" rows="5" placeholder="List bibliographic references in APA 7th Edition format:
Author, A. A. (Year). Title of work. Publisher / Journal, Vol(Issue), pages. https://doi.org/... ">{{ old('references') }}</textarea>
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
                                        <!-- Pre-populated standard UA activities from template -->
                                        @php
                                            $defaultActivities = [
                                                'Submission of Revised Research Proposal',
                                                'Data Collection and Gathering',
                                                'Data Processing and Analysis',
                                                'Interpretation of Results and Discussion',
                                                'Writing of Final Report / Terminal Report',
                                                'Submission and Presentation of Terminal Report',
                                                'Final Revision and Archiving'
                                            ];
                                        @endphp

                                        @foreach($defaultActivities as $index => $act)
                                            <tr class="workplan-row" data-index="{{ $index }}">
                                                <td class="text-start p-2">
                                                    <input type="text" name="work_plan_matrix[{{ $index }}][activity]" class="form-control form-control-sm border-0 bg-light rounded-3 fw-medium" value="{{ $act }}" required>
                                                    <input type="hidden" name="milestones[{{ $index }}][title]" value="{{ $act }}">
                                                    <input type="hidden" name="milestones[{{ $index }}][description]" value="Work plan milestone: {{ $act }}">
                                                </td>
                                                <td class="p-2">
                                                    <div class="d-flex gap-1">
                                                        <input type="date" name="milestones[{{ $index }}][start_date]" class="form-control form-control-sm border-0 bg-light rounded-2 px-1" title="Start Date">
                                                        <input type="date" name="milestones[{{ $index }}][target_date]" class="form-control form-control-sm border-0 bg-light rounded-2 px-1" title="Target Date">
                                                    </div>
                                                </td>
                                                @for($m = 1; $m <= 12; $m++)
                                                    <td class="p-1">
                                                        <input class="form-check-input gantt-check" type="checkbox" name="work_plan_matrix[{{ $index }}][months][]" value="{{ $m }}" {{ ($index == 0 && $m <= 2) || ($index == 1 && in_array($m, [2,3,4])) || ($index == 2 && in_array($m, [4,5])) || ($index == 3 && in_array($m, [5,6])) || ($index == 4 && in_array($m, [6,7])) || ($index >= 5 && $m >= 7) ? 'checked' : '' }} style="cursor: pointer;">
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
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Check the months during which each research milestone will be executed. These milestones will also populate your research progress tracking timeline.</small>
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

                            <!-- PDF Auto-fill Notification Alert -->
                            <div id="libExtractedAlert" class="alert alert-success border border-success border-opacity-25 rounded-4 d-none mb-3 py-2.5 px-3 d-flex align-items-center justify-content-between shadow-xs">
                                <div class="d-flex align-items-center gap-2 small">
                                    <i class="bi bi-magic fs-5 text-success"></i>
                                    <span><strong>Auto-filled from PDF:</strong> <span id="libExtractedCount" class="fw-bold">0</span> Line-Item Budget entries were automatically extracted and populated from your uploaded proposal. You can freely review, edit, or adjust amounts below.</span>
                                </div>
                                <button type="button" class="btn-close btn-close-sm" onclick="document.getElementById('libExtractedAlert').classList.add('d-none')"></button>
                            </div>

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
                                        <!-- Sample default items from University of Antique template -->
                                        <!-- MOOE Item 1 -->
                                        <tr class="budget-item-row" data-type="mooe">
                                            <td class="p-2">
                                                <select name="budget_items[0][category_group]" class="form-select form-select-sm border-0 bg-light rounded-3 fw-bold text-primary">
                                                    <option value="supplies" selected>Supplies &amp; Materials</option>
                                                    <option value="travel">Travelling Expenses (Local)</option>
                                                    <option value="professional_services">Other Professional Services</option>
                                                    <option value="other_mooe">Other MOOE</option>
                                                    <option value="capital_outlay">Capital Outlay (CO)</option>
                                                </select>
                                                <input type="hidden" name="budget_items[0][category_type]" value="mooe" class="category-type-input">
                                            </td>
                                            <td class="p-2">
                                                <input type="text" name="budget_items[0][item_name]" class="form-control form-control-sm border-0 bg-light rounded-3" value="Bookpaper [A4; 80gsm] @ Php 315 x 2 reams" placeholder="Item description" required>
                                            </td>
                                            <td class="p-2">
                                                <input type="text" name="budget_items[0][existing_resources]" class="form-control form-control-sm border-0 bg-light rounded-3" value="RESU" placeholder="RESU / College">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" step="0.01" name="budget_items[0][amount]" class="form-control form-control-sm border-0 bg-light rounded-3 text-end fw-bold budget-amount-input" value="630.00" oninput="calculateBudgetTotals()" required>
                                            </td>
                                            <td class="p-2 text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1" onclick="removeBudgetRow(this)"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>

                                        <!-- MOOE Item 2 -->
                                        <tr class="budget-item-row" data-type="mooe">
                                            <td class="p-2">
                                                <select name="budget_items[1][category_group]" class="form-select form-select-sm border-0 bg-light rounded-3 fw-bold text-primary">
                                                    <option value="supplies" selected>Supplies &amp; Materials</option>
                                                    <option value="travel">Travelling Expenses (Local)</option>
                                                    <option value="professional_services">Other Professional Services</option>
                                                    <option value="other_mooe">Other MOOE</option>
                                                    <option value="capital_outlay">Capital Outlay (CO)</option>
                                                </select>
                                                <input type="hidden" name="budget_items[1][category_type]" value="mooe" class="category-type-input">
                                            </td>
                                            <td class="p-2">
                                                <input type="text" name="budget_items[1][item_name]" class="form-control form-control-sm border-0 bg-light rounded-3" value="Refill Ink [Epson CISS printer, black, 127ml] @ Php 630 x 1 bottle" placeholder="Item description" required>
                                            </td>
                                            <td class="p-2">
                                                <input type="text" name="budget_items[1][existing_resources]" class="form-control form-control-sm border-0 bg-light rounded-3" value="RESU" placeholder="RESU / College">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" step="0.01" name="budget_items[1][amount]" class="form-control form-control-sm border-0 bg-light rounded-3 text-end fw-bold budget-amount-input" value="630.00" oninput="calculateBudgetTotals()" required>
                                            </td>
                                            <td class="p-2 text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1" onclick="removeBudgetRow(this)"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>

                                        <!-- MOOE Item 3: Travel -->
                                        <tr class="budget-item-row" data-type="mooe">
                                            <td class="p-2">
                                                <select name="budget_items[2][category_group]" class="form-select form-select-sm border-0 bg-light rounded-3 fw-bold text-primary">
                                                    <option value="supplies">Supplies &amp; Materials</option>
                                                    <option value="travel" selected>Travelling Expenses (Local)</option>
                                                    <option value="professional_services">Other Professional Services</option>
                                                    <option value="other_mooe">Other MOOE</option>
                                                    <option value="capital_outlay">Capital Outlay (CO)</option>
                                                </select>
                                                <input type="hidden" name="budget_items[2][category_type]" value="mooe" class="category-type-input">
                                            </td>
                                            <td class="p-2">
                                                <input type="text" name="budget_items[2][item_name]" class="form-control form-control-sm border-0 bg-light rounded-3" value="Local Fieldwork &amp; Data Gathering Transportation" placeholder="Item description" required>
                                            </td>
                                            <td class="p-2">
                                                <input type="text" name="budget_items[2][existing_resources]" class="form-control form-control-sm border-0 bg-light rounded-3" value="RESU" placeholder="RESU / College">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" step="0.01" name="budget_items[2][amount]" class="form-control form-control-sm border-0 bg-light rounded-3 text-end fw-bold budget-amount-input" value="10000.00" oninput="calculateBudgetTotals()" required>
                                            </td>
                                            <td class="p-2 text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1" onclick="removeBudgetRow(this)"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>

                                        <!-- MOOE Item 4: Professional Services -->
                                        <tr class="budget-item-row" data-type="mooe">
                                            <td class="p-2">
                                                <select name="budget_items[3][category_group]" class="form-select form-select-sm border-0 bg-light rounded-3 fw-bold text-primary">
                                                    <option value="supplies">Supplies &amp; Materials</option>
                                                    <option value="travel">Travelling Expenses (Local)</option>
                                                    <option value="professional_services" selected>Other Professional Services</option>
                                                    <option value="other_mooe">Other MOOE</option>
                                                    <option value="capital_outlay">Capital Outlay (CO)</option>
                                                </select>
                                                <input type="hidden" name="budget_items[3][category_type]" value="mooe" class="category-type-input">
                                            </td>
                                            <td class="p-2">
                                                <input type="text" name="budget_items[3][item_name]" class="form-control form-control-sm border-0 bg-light rounded-3" value="Statistician / Data Analyst Honorarium" placeholder="Item description" required>
                                            </td>
                                            <td class="p-2">
                                                <input type="text" name="budget_items[3][existing_resources]" class="form-control form-control-sm border-0 bg-light rounded-3" value="RESU" placeholder="RESU / College">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" step="0.01" name="budget_items[3][amount]" class="form-control form-control-sm border-0 bg-light rounded-3 text-end fw-bold budget-amount-input" value="2500.00" oninput="calculateBudgetTotals()" required>
                                            </td>
                                            <td class="p-2 text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1" onclick="removeBudgetRow(this)"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>

                                        <!-- Capital Outlay Item 1 -->
                                        <tr class="budget-item-row" data-type="co">
                                            <td class="p-2">
                                                <select name="budget_items[4][category_group]" class="form-select form-select-sm border-0 bg-light rounded-3 fw-bold text-dark">
                                                    <option value="supplies">Supplies &amp; Materials</option>
                                                    <option value="travel">Travelling Expenses (Local)</option>
                                                    <option value="professional_services">Other Professional Services</option>
                                                    <option value="other_mooe">Other MOOE</option>
                                                    <option value="capital_outlay" selected>Capital Outlay (CO)</option>
                                                </select>
                                                <input type="hidden" name="budget_items[4][category_type]" value="co" class="category-type-input">
                                            </td>
                                            <td class="p-2">
                                                <input type="text" name="budget_items[4][item_name]" class="form-control form-control-sm border-0 bg-light rounded-3" value="Laptop Computer for Simulation &amp; System Hosting [Intel Core i5, 16GB RAM, 512GB SSD]" placeholder="Equipment specification" required>
                                            </td>
                                            <td class="p-2">
                                                <input type="text" name="budget_items[4][existing_resources]" class="form-control form-control-sm border-0 bg-light rounded-3" value="RESU" placeholder="RESU / College">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" step="0.01" name="budget_items[4][amount]" class="form-control form-control-sm border-0 bg-light rounded-3 text-end fw-bold budget-amount-input" value="45000.00" oninput="calculateBudgetTotals()" required>
                                            </td>
                                            <td class="p-2 text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1" onclick="removeBudgetRow(this)"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>

                                    <!-- Summary Calculation Footer -->
                                    <tfoot class="table-light fw-bold">
                                        <tr>
                                            <td colspan="3" class="text-end text-primary">Subtotal for MOOE:</td>
                                            <td class="text-end text-primary" id="subtotalMooeDisplay">₱ 13,760.00</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end text-secondary">Subtotal for Capital Outlay (CO):</td>
                                            <td class="text-end text-secondary" id="subtotalCoDisplay">₱ 45,000.00</td>
                                            <td></td>
                                        </tr>
                                        <tr class="table-warning" style="font-size: 1.05rem;">
                                            <td colspan="3" class="text-end text-dark fw-bold">GRAND TOTAL ESTIMATED BUDGET:</td>
                                            <td class="text-end text-dark fw-bold" id="grandTotalBudgetDisplay">₱ 58,760.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Per University policy, do not merge or alter Line-Item Budget standard classifications. Leave blank or delete rows that are not applicable.</small>
                        </div>

                        <!-- Step 4 Footer Nav -->
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-bold" onclick="navigateToStep('tab-step-3')">
                                <i class="bi bi-arrow-left me-1"></i> Previous Step
                            </button>
                            <button type="button" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" onclick="navigateToStep('tab-step-5')">
                                Next: Review &amp; Submit <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 5: Attachment & Submission -->
                    <!-- ======================================================== -->
                    <div class="tab-pane fade" id="pane-step-5" role="tabpanel">
                        <div class="row g-4">
                            <!-- Left: PDF Upload & Text Parsing Option -->
                            <div class="col-12 col-lg-6">
                                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                                    <h5 class="fw-bold mb-3 border-start border-4 border-primary ps-3 text-dark">
                                        Attach Formatted Manuscript PDF <span class="badge bg-light text-muted border fw-normal small ms-2">Optional</span>
                                    </h5>
                                    <p class="text-muted small mb-4">
                                        Since you are creating your proposal directly via this template, uploading a PDF file is optional. If you already have a prepared PDF, you can attach it here.
                                    </p>

                                    <div class="p-4 bg-light rounded-4 text-center border-2 border-primary position-relative" style="border-style: dashed !important;">
                                        <i class="bi bi-cloud-arrow-up text-primary display-5 d-block mb-1"></i>
                                        <p class="mb-0 small fw-bold text-primary">Click to attach external PDF or drag &amp; drop</p>
                                        <p class="text-muted x-small mb-0" style="font-size: 0.72rem;">PDF FORMAT &bull; MAXIMUM 20MB</p>
                                        <input type="file" name="document" id="proposalDoc" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept="application/pdf" style="cursor: pointer;">
                                    </div>
                                    <div id="file-name" class="mt-2 small text-success fw-bold text-center d-none p-2 bg-success bg-opacity-10 rounded-3"></div>

                                    <!-- Word Template Download Card -->
                                    <div class="mt-3 p-3 bg-white rounded-4 border d-flex justify-content-between align-items-center shadow-xs">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                                <i class="bi bi-file-earmark-word-fill fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold small text-dark">Official Word (.docx) Template</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">Download Form RESU-FM-015 to draft offline with co-authors.</div>
                                            </div>
                                        </div>
                                        <a href="{{ route('template.download.proposal') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-bold text-nowrap">
                                            <i class="bi bi-download me-1"></i> Download
                                        </a>
                                    </div>

                                    <!-- OCR Status Card -->
                                    <div id="ocrStatusCard" class="mt-3 p-3 rounded-4 border d-none shadow-xs">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status" id="ocrSpinner"></div>
                                                <span class="fw-bold small" id="ocrStatusTitle">Analyzing PDF Document...</span>
                                            </div>
                                            <span class="badge rounded-pill px-2 py-1 small fw-bold" id="ocrBadge" style="display: none;">OCR Active</span>
                                        </div>
                                        <p class="small text-muted mb-0 mt-1" id="ocrStatusMessage" style="font-size: 0.78rem;">Extracting document text to sync with review annotator.</p>
                                    </div>
                                    <input type="hidden" name="extracted_text" id="extractedText">
                                </div>
                            </div>

                            <!-- Right: Proposal Summary Checklist -->
                            <div class="col-12 col-lg-6">
                                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                                    <h5 class="fw-bold mb-3 border-start border-4 border-success ps-3 text-dark">
                                        Readiness Checklist
                                    </h5>
                                    <p class="text-muted small mb-4">Ensure your proposal adheres to University of Antique guidelines before submission.</p>

                                    <div class="vstack gap-3 mb-4">
                                        <div class="d-flex align-items-start gap-3 p-2.5 rounded-3 bg-light">
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            <div>
                                                <div class="fw-bold small text-dark">Form RESU-FM-015 Standard Structure</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">Sections are synchronized with the official Research Proposal Template.</div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-start gap-3 p-2.5 rounded-3 bg-light">
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            <div>
                                                <div class="fw-bold small text-dark">In-Browser Review Workspace Ready</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">Assigned evaluators can highlight quotes, tag you with revisions, and leave suggestions in real-time.</div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-start gap-3 p-2.5 rounded-3 bg-light">
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            <div>
                                                <div class="fw-bold small text-dark">Automatic Budget Calculation</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">Line items are summed and synchronized for Vice President for Research &amp; Extension certification.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2.5 fw-bold w-100 mb-3 shadow-xs" onclick="openLivePreviewModal()">
                                        <i class="bi bi-eye-fill me-1.5"></i> Live Preview Form RESU-FM-015
                                    </button>

                                    <div class="alert alert-warning border-0 rounded-4 py-2.5 px-3 mb-0 small">
                                        <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                                        Tip: You can save this proposal as a <strong>Draft</strong> anytime and continue editing later, or click <strong>Submit Proposal</strong> to route it directly to your College Coordinator.
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
                                        <i class="bi bi-send-fill me-1"></i> SUBMIT PROPOSAL FOR ENDORSEMENT
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
                        <h5 class="modal-title fw-bold text-dark fs-6" id="livePreviewModalLabel">Official Form RESU-FM-015 Live Manuscript Preview</h5>
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
                                    <td class="text-dark" id="prevProponent">{{ auth()->user()->name }} (Lead Researcher)</td>
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
                            Prepared by: <strong>{{ auth()->user()->name }}</strong> &bull; University of Antique Research Services Unit
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close Preview</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="bootstrap.Modal.getInstance(document.getElementById('livePreviewModal')).hide(); navigateToStep('tab-step-5');">
                        Proceed to Submission
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Dynamic Row Scripts & PDF Extractor -->
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
            document.getElementById('prevTitle').innerText = document.getElementById('proposalTitle').value || 'Untitled Research';
            document.getElementById('prevCredentials').innerText = document.querySelector('textarea[name="credentials"]').value || 'Not specified';
            document.getElementById('prevCollege').innerText = document.getElementById('proposalCollege').value || '{{ auth()->user()->department ?? "College of Computer Studies" }}';
            document.getElementById('prevSdgs').innerText = document.querySelector('input[name="sdgs_addressed"]').value || 'None specified';

            document.getElementById('prevBackground').innerText = document.getElementById('backgroundOfStudy').value || 'No background provided yet.';
            document.getElementById('prevSignificance').innerText = document.getElementById('significanceOfStudy').value || 'No significance provided yet.';
            document.getElementById('prevTerms').innerText = document.getElementById('definitionOfTerms').value || 'No terms provided yet.';
            document.getElementById('prevLiterature').innerText = document.getElementById('literatureReview').value || 'No literature review provided yet.';
            document.getElementById('prevMethodology').innerText = document.getElementById('methodologyField').value || 'No methodology provided yet.';
            document.getElementById('prevReferences').innerText = document.getElementById('referencesField').value || 'No references provided yet.';

            // Clone Work Plan Matrix
            const workPlanTable = document.getElementById('workPlanTable');
            if (workPlanTable) {
                document.getElementById('prevWorkPlanContainer').innerHTML = workPlanTable.outerHTML;
                // remove action column from preview
                document.querySelectorAll('#prevWorkPlanContainer th:last-child, #prevWorkPlanContainer td:last-child').forEach(el => el.remove());
            }

            // Clone Budget Table
            const budgetTable = document.getElementById('budgetItemsTable');
            if (budgetTable) {
                document.getElementById('prevBudgetContainer').innerHTML = budgetTable.outerHTML;
                document.querySelectorAll('#prevBudgetContainer th:last-child, #prevBudgetContainer td:last-child').forEach(el => el.remove());
            }

            const previewModal = new bootstrap.Modal(document.getElementById('livePreviewModal'));
            previewModal.show();
        }
        // Tab Stepper Control
        function navigateToStep(tabId) {
            const triggerEl = document.getElementById(tabId);
            if (triggerEl) {
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
                window.scrollTo({ top: 120, behavior: 'smooth' });
            }
        }

        // Update step counter badge on tab switch
        const tabList = [
            { id: 'tab-step-1', label: 'Step 1 of 5: General Info' },
            { id: 'tab-step-2', label: 'Step 2 of 5: Manuscript Sections' },
            { id: 'tab-step-3', label: 'Step 3 of 5: Work Plan Matrix' },
            { id: 'tab-step-4', label: 'Step 4 of 5: Line-Item Budget' },
            { id: 'tab-step-5', label: 'Step 5 of 5: Review & Submit' }
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

        // ==========================================
        // DYNAMIC WORK PLAN (GANTT MATRIX) FUNCTIONS
        // ==========================================
        let workPlanCounter = 7; // starts after default rows

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

            // Keep hidden milestone title in sync with activity input
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

        // Keep existing default milestone titles synced
        document.querySelectorAll('#workPlanTableBody tr').forEach(row => {
            const textInput = row.querySelector('input[type="text"]');
            const hiddenTitle = row.querySelector('input[name^="milestones"][name$="[title]"]');
            if (textInput && hiddenTitle) {
                textInput.addEventListener('input', function() {
                    hiddenTitle.value = this.value;
                });
            }
        });

        // ==========================================
        // DYNAMIC LINE-ITEM BUDGET (LIB) FUNCTIONS
        // ==========================================
        let budgetCounter = 5; // starts after default rows

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

            document.getElementById('subtotalMooeDisplay').innerText = formatPhp(mooeTotal);
            document.getElementById('subtotalCoDisplay').innerText = formatPhp(coTotal);
            document.getElementById('grandTotalBudgetDisplay').innerText = formatPhp(grandTotal);
        }

        // Auto-populate Line-Item Budget (LIB) table from PDF smart extraction
        function populateExtractedBudgetItems(items) {
            if (!items || !items.length) return;
            const tbody = document.getElementById('budgetItemsBody');
            if (!tbody) return;

            // Clear default sample rows
            tbody.innerHTML = '';
            budgetCounter = 0;

            items.forEach((item) => {
                const newIndex = budgetCounter++;
                const catType = item.category_type || (item.category_group === 'capital_outlay' ? 'co' : 'mooe');
                const catGroup = item.category_group || 'supplies';
                const itemName = item.item_name || '';
                const existingRes = item.existing_resources || 'RESU';
                const amount = parseFloat(item.amount) || 0;

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
                        <input type="text" name="budget_items[${newIndex}][item_name]" class="form-control form-control-sm border-0 bg-light rounded-3" value="${escapeHtml(itemName)}" placeholder="Item description" required>
                    </td>
                    <td class="p-2">
                        <input type="text" name="budget_items[${newIndex}][existing_resources]" class="form-control form-control-sm border-0 bg-light rounded-3" value="${escapeHtml(existingRes)}" placeholder="RESU / College">
                    </td>
                    <td class="p-2">
                        <input type="number" step="0.01" name="budget_items[${newIndex}][amount]" class="form-control form-control-sm border-0 bg-light rounded-3 text-end fw-bold budget-amount-input" value="${amount.toFixed(2)}" oninput="calculateBudgetTotals()" required>
                    </td>
                    <td class="p-2 text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1" onclick="removeBudgetRow(this)"><i class="bi bi-trash"></i></button>
                    </td>
                `;

                tbody.appendChild(tr);
            });

            calculateBudgetTotals();

            // Show notification banner inside Tab 4
            const alertBox = document.getElementById('libExtractedAlert');
            const alertCount = document.getElementById('libExtractedCount');
            if (alertBox && alertCount) {
                alertCount.innerText = items.length;
                alertBox.classList.remove('d-none');
            }

            // Add visual pill badge on Tab 4 button to guide user
            const tab4Btn = document.getElementById('tab-step-4');
            if (tab4Btn) {
                const existingBadge = tab4Btn.querySelector('.lib-extracted-badge');
                if (existingBadge) existingBadge.remove();

                const badge = document.createElement('span');
                badge.className = 'badge bg-success rounded-pill ms-1.5 lib-extracted-badge shadow-xs';
                badge.style.fontSize = '0.68rem';
                badge.innerHTML = `<i class="bi bi-check-circle-fill me-0.5"></i> ${items.length} auto-filled`;
                tab4Btn.appendChild(badge);
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Initialize calculations on load
        calculateBudgetTotals();

        // ==========================================
        // SMART PDF EXTRACTOR & AUTO-FILL (DUAL MODES)
        // ==========================================
        const proposalDoc = document.getElementById('proposalDoc');
        const smartProposalDoc = document.getElementById('smartProposalDoc');
        const titleInput = document.getElementById('proposalTitle');
        const fieldInput = document.getElementById('proposalField');
        const abstractInput = document.getElementById('proposalAbstract');
        const extractedTextInput = document.getElementById('extractedText');
        const fileNameDisplay = document.getElementById('file-name');
        const ocrCard = document.getElementById('ocrStatusCard');
        const ocrSpinner = document.getElementById('ocrSpinner');
        const ocrTitle = document.getElementById('ocrStatusTitle');
        const ocrMsg = document.getElementById('ocrStatusMessage');
        const ocrBadge = document.getElementById('ocrBadge');
        const titleSuggestionsBox = document.getElementById('titleSuggestionsContainer');
        const titleSuggestionsList = document.getElementById('titleSuggestionsList');

        // Option A Smart Dropzone UI Elements
        const smartDropzoneTitle = document.getElementById('smartDropzoneTitle');
        const smartDropzoneSub = document.getElementById('smartDropzoneSub');
        const smartExtractStatus = document.getElementById('smartExtractStatus');
        const smartSpinner = document.getElementById('smartSpinner');
        const smartSuccessIcon = document.getElementById('smartSuccessIcon');
        const smartStatusTitle = document.getElementById('smartStatusTitle');
        const smartStatusMessage = document.getElementById('smartStatusMessage');
        const smartBadge = document.getElementById('smartBadge');
        const smartActionRow = document.getElementById('smartActionRow');

        async function processUploadedProposal(file, sourceInput) {
            if (!file) return;

            // Sync files across both inputs so the form submits it properly
            if (sourceInput === smartProposalDoc && proposalDoc) {
                try {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    proposalDoc.files = dt.files;
                } catch (e) {}
            } else if (sourceInput === proposalDoc && smartProposalDoc) {
                try {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    smartProposalDoc.files = dt.files;
                } catch (e) {}
            }

            const fileSizeMb = (file.size / 1024 / 1024).toFixed(2);

            // Update Option A Dropzone Label
            if (smartDropzoneTitle) {
                smartDropzoneTitle.innerHTML = `<i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> ${escapeHtml(file.name)}`;
            }
            if (smartDropzoneSub) {
                smartDropzoneSub.innerText = `${fileSizeMb} MB • Manuscript attached & ready`;
            }

            // Update Step 5 File Display
            if (fileNameDisplay) {
                fileNameDisplay.innerHTML = `<i class="bi bi-file-earmark-pdf me-1"></i> Attached: <strong>${escapeHtml(file.name)}</strong> (${fileSizeMb} MB)`;
                fileNameDisplay.classList.remove('d-none');
            }

            // Show Processing Status in Option A
            if (smartExtractStatus) {
                smartExtractStatus.classList.remove('d-none');
                smartSpinner.classList.remove('d-none');
                smartSuccessIcon.classList.add('d-none');
                smartBadge.className = 'badge bg-primary text-white rounded-pill px-2.5 py-1 small fw-bold';
                smartBadge.innerText = 'Analyzing Manuscript';
                smartStatusTitle.innerText = 'Reading proposal PDF layers...';
                smartStatusMessage.innerText = 'Parsing Title, Abstract, Manuscript Chapters, and Line-Item Budget table...';
                if (smartActionRow) smartActionRow.classList.add('d-none');
            }

            // Show Scanning Status in Step 5
            if (ocrCard) {
                ocrCard.className = 'mt-3 p-3 rounded-4 border border-primary bg-primary bg-opacity-10 d-block shadow-xs';
                ocrSpinner.classList.remove('d-none');
                ocrTitle.className = 'fw-bold small text-primary';
                ocrTitle.innerText = 'Smart Document Analyzer Running...';
                ocrMsg.innerText = 'Extracting document text layers and cross-referencing fields...';
                ocrBadge.style.display = 'none';
            }

            const formData = new FormData();
            formData.append('document', file);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch("{{ route('proposal.extractPdf') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success && data.has_text) {
                    let filledList = [];

                    // 1. General Info & Title
                    if (data.title && (!titleInput.value || titleInput.value.trim() === '')) {
                        titleInput.value = data.title;
                        filledList.push('Title');
                    }

                    if (titleSuggestionsList && data.title_suggestions && data.title_suggestions.length > 1) {
                        titleSuggestionsList.innerHTML = '';
                        data.title_suggestions.forEach(cand => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'btn btn-outline-primary border-opacity-50 py-0.5 px-2.5 rounded-pill shadow-xs text-truncate text-start';
                            btn.style.fontSize = '0.73rem';
                            btn.style.maxWidth = '280px';
                            btn.innerText = cand;
                            btn.title = 'Click to use: ' + cand;
                            btn.onclick = () => {
                                titleInput.value = cand;
                                navigateToStep('tab-step-1');
                            };
                            titleSuggestionsList.appendChild(btn);
                        });
                        titleSuggestionsBox.classList.remove('d-none');
                    }

                    if (data.abstract && (!abstractInput.value || abstractInput.value.trim() === '')) {
                        abstractInput.value = data.abstract;
                        filledList.push('Abstract');
                    }
                    if (data.research_field && (!fieldInput.value || fieldInput.value.trim() === '')) {
                        fieldInput.value = data.research_field;
                        filledList.push('Research Discipline');
                    }
                    const credInput = document.querySelector('textarea[name="credentials"]');
                    if (data.sections && data.sections.credentials && credInput && (!credInput.value || credInput.value.trim() === '')) {
                        credInput.value = data.sections.credentials;
                        filledList.push('Credentials');
                    }

                    // 2. Manuscript Narrative Sections (Step 2)
                    let narrativeExtractedCount = 0;
                    if (data.sections) {
                        const narrativeMap = {
                            'background_of_study': 'backgroundOfStudy',
                            'significance_of_study': 'significanceOfStudy',
                            'definition_of_terms': 'definitionOfTerms',
                            'literature_review': 'literatureReview',
                            'methodology': 'methodologyField',
                            'references': 'referencesField'
                        };

                        for (const [secKey, elId] of Object.entries(narrativeMap)) {
                            const textarea = document.getElementById(elId);
                            if (textarea && data.sections[secKey] && (!textarea.value || textarea.value.trim() === '')) {
                                textarea.value = data.sections[secKey];
                                narrativeExtractedCount++;
                            }
                        }

                        if (narrativeExtractedCount > 0) {
                            filledList.push(`${narrativeExtractedCount} Manuscript Chapters`);

                            // Visual badge on Step 2 tab
                            const tab2Btn = document.getElementById('tab-step-2');
                            if (tab2Btn) {
                                const oldBadge = tab2Btn.querySelector('.narrative-extracted-badge');
                                if (oldBadge) oldBadge.remove();
                                const badge = document.createElement('span');
                                badge.className = 'badge bg-success rounded-pill ms-1.5 narrative-extracted-badge shadow-xs';
                                badge.style.fontSize = '0.68rem';
                                badge.innerHTML = `<i class="bi bi-check-circle-fill me-0.5"></i> ${narrativeExtractedCount} auto-filled`;
                                tab2Btn.appendChild(badge);
                            }
                        }
                    }

                    // 3. Line-Item Budget (Step 4)
                    if (data.budget_items && data.budget_items.length > 0) {
                        populateExtractedBudgetItems(data.budget_items);
                        const phpFormatted = '₱' + Number(data.budget_total || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        filledList.push(`Line-Item Budget (${data.budget_items.length} items totaling ${phpFormatted})`);
                    }

                    // 4. Raw Extracted Text
                    if (data.extracted_text) {
                        extractedTextInput.value = data.extracted_text;
                    }

                    // Success State: Option A
                    if (smartExtractStatus) {
                        smartSpinner.classList.add('d-none');
                        smartSuccessIcon.classList.remove('d-none');
                        smartBadge.className = 'badge bg-success text-white rounded-pill px-2.5 py-1 small fw-bold';
                        smartBadge.innerText = 'Smart Auto-Populated';
                        smartStatusTitle.innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i> Proposal Read Successfully (${data.word_count.toLocaleString()} words indexed)`;
                        smartStatusMessage.innerHTML = `✨ <strong>Auto-filled:</strong> ${filledList.join(' &bull; ')}. Click below to view the official Form RESU-FM-015 layout or tweak any section.`;
                        if (smartActionRow) smartActionRow.classList.remove('d-none');
                    }

                    // Success State: Step 5 OCR Card
                    if (ocrCard) {
                        ocrSpinner.classList.add('d-none');
                        ocrCard.className = 'mt-3 p-3 rounded-4 border border-success bg-success bg-opacity-10 d-block shadow-xs';
                        ocrTitle.className = 'fw-bold small text-success';
                        ocrTitle.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> Manuscript Processed (${data.word_count.toLocaleString()} words indexed)`;
                        ocrMsg.innerHTML = `✨ Successfully auto-populated: <strong>${filledList.join('</strong>, <strong>')}</strong>. You can review and adjust any field or budget item.`;
                        ocrBadge.className = 'badge bg-success text-white rounded-pill px-2.5 py-1 small fw-bold';
                        ocrBadge.innerText = 'Smart Analyzed';
                        ocrBadge.style.display = 'inline-block';
                    }
                } else {
                    if (smartExtractStatus) {
                        smartSpinner.classList.add('d-none');
                        smartStatusTitle.innerText = 'PDF Attached (Scanned / Image Stream)';
                        smartStatusMessage.innerText = 'No digital text stream detected in PDF. The document has been attached and you can fill out the form fields manually.';
                        smartBadge.className = 'badge bg-secondary text-white rounded-pill px-2.5 py-1 small fw-bold';
                        smartBadge.innerText = 'Attached';
                    }
                    if (ocrCard) {
                        ocrSpinner.classList.add('d-none');
                        ocrCard.className = 'mt-3 p-3 rounded-4 border border-secondary bg-light d-block shadow-xs';
                        ocrTitle.className = 'fw-bold small text-secondary';
                        ocrTitle.innerHTML = '<i class="bi bi-info-circle me-1"></i> PDF Attached';
                        ocrMsg.innerText = 'Document attached. The structured fields above will serve as the primary manuscript.';
                        ocrBadge.style.display = 'none';
                    }
                }
            } catch (err) {
                if (smartExtractStatus) {
                    smartSpinner.classList.add('d-none');
                    smartStatusTitle.innerText = 'Document Attached';
                    smartStatusMessage.innerText = 'File attached successfully. You can review or complete form fields below.';
                    smartBadge.className = 'badge bg-secondary text-white rounded-pill px-2.5 py-1 small fw-bold';
                    smartBadge.innerText = 'Attached';
                }
                if (ocrCard) {
                    ocrSpinner.classList.add('d-none');
                    ocrCard.className = 'mt-3 p-3 rounded-4 border border-light bg-light d-block shadow-xs';
                    ocrTitle.className = 'fw-bold small text-muted';
                    ocrTitle.innerText = 'Document Attached';
                    ocrMsg.innerText = 'PDF attached successfully.';
                    ocrBadge.style.display = 'none';
                }
            }
        }

        // Event listener for Option A Dropzone
        if (smartProposalDoc) {
            smartProposalDoc.addEventListener('change', function(e) {
                if (!e.target.files || !e.target.files[0]) return;
                processUploadedProposal(e.target.files[0], smartProposalDoc);
            });
        }

        // Event listener for Step 5 Dropzone
        if (proposalDoc) {
            proposalDoc.addEventListener('change', function(e) {
                if (!e.target.files || !e.target.files[0]) return;
                processUploadedProposal(e.target.files[0], proposalDoc);
            });
        }

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
        let isSubmitting = false;
        const proposalForm = document.getElementById('proposalCreateForm');
        if (proposalForm) {
            proposalForm.addEventListener('submit', async function(e) {
                if (isSubmitting) return;
                e.preventDefault();
                isSubmitting = true;

                const activeBtn = document.activeElement && document.activeElement.type === 'submit' ? document.activeElement : null;
                if (activeBtn) {
                    let hidden = document.getElementById('submittedActionValue');
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = activeBtn.name || 'action';
                        hidden.id = 'submittedActionValue';
                        proposalForm.appendChild(hidden);
                    }
                    hidden.value = activeBtn.value;
                    activeBtn.disabled = true;
                    activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Submitting...';
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

                localStorage.removeItem('urlc_proposal_draft');
                proposalForm.submit();
            });
        }

        // ==========================================
        // AUTO-SAVE DRAFT TO LOCALSTORAGE
        // ==========================================
        function saveLocalDraft() {
            try {
                const titleVal = document.getElementById('proposalTitle')?.value || '';
                const abstractVal = document.getElementById('proposalAbstract')?.value || '';
                const bgVal = document.getElementById('backgroundOfStudy')?.value || '';

                if (!titleVal && !abstractVal && !bgVal) return;

                const draft = {
                    title: titleVal,
                    research_field: document.getElementById('proposalField')?.value || '',
                    college_affiliation: document.getElementById('proposalCollege')?.value || '',
                    credentials: document.querySelector('textarea[name="credentials"]')?.value || '',
                    sdgs_addressed: document.querySelector('input[name="sdgs_addressed"]')?.value || '',
                    abstract: abstractVal,
                    background_of_study: bgVal,
                    significance_of_study: document.getElementById('significanceOfStudy')?.value || '',
                    definition_of_terms: document.getElementById('definitionOfTerms')?.value || '',
                    literature_review: document.getElementById('literatureReview')?.value || '',
                    methodology: document.getElementById('methodologyField')?.value || '',
                    references: document.getElementById('referencesField')?.value || '',
                    saved_at: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                };

                localStorage.setItem('urlc_proposal_draft', JSON.stringify(draft));
            } catch (e) {}
        }

        setInterval(saveLocalDraft, 15000);

        window.addEventListener('DOMContentLoaded', () => {
            try {
                const saved = localStorage.getItem('urlc_proposal_draft');
                if (saved) {
                    const draft = JSON.parse(saved);
                    const currentTitle = document.getElementById('proposalTitle')?.value;
                    if (!currentTitle || currentTitle.trim() === '') {
                        const banner = document.getElementById('localDraftBanner');
                        const bannerTime = document.getElementById('localDraftBannerTime');
                        if (banner) {
                            if (bannerTime && draft.saved_at) {
                                bannerTime.innerText = `Unsaved Proposal Draft Found (Saved at ${draft.saved_at})`;
                            }
                            banner.classList.remove('d-none');
                        }
                    }
                }
            } catch (e) {}
        });

        function restoreLocalDraft() {
            try {
                const saved = localStorage.getItem('urlc_proposal_draft');
                if (!saved) return;
                const draft = JSON.parse(saved);
                if (draft.title) document.getElementById('proposalTitle').value = draft.title;
                if (draft.research_field) document.getElementById('proposalField').value = draft.research_field;
                if (draft.college_affiliation) document.getElementById('proposalCollege').value = draft.college_affiliation;
                if (draft.credentials) document.querySelector('textarea[name="credentials"]').value = draft.credentials;
                if (draft.sdgs_addressed) document.querySelector('input[name="sdgs_addressed"]').value = draft.sdgs_addressed;
                if (draft.abstract) document.getElementById('proposalAbstract').value = draft.abstract;
                if (draft.background_of_study) document.getElementById('backgroundOfStudy').value = draft.background_of_study;
                if (draft.significance_of_study) document.getElementById('significanceOfStudy').value = draft.significance_of_study;
                if (draft.definition_of_terms) document.getElementById('definitionOfTerms').value = draft.definition_of_terms;
                if (draft.literature_review) document.getElementById('literatureReview').value = draft.literature_review;
                if (draft.methodology) document.getElementById('methodologyField').value = draft.methodology;
                if (draft.references) document.getElementById('referencesField').value = draft.references;

                document.getElementById('localDraftBanner').classList.add('d-none');
                alert('✨ Draft restored successfully into your proposal form!');
            } catch (e) {}
        }

        function dismissLocalDraft() {
            localStorage.removeItem('urlc_proposal_draft');
            document.getElementById('localDraftBanner').classList.add('d-none');
        }
    </script>
</x-app-layout>
