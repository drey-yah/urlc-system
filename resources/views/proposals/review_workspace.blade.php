<x-app-layout>
    <!-- Top Action & Navigation Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
        <div class="p-3 p-md-4 text-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3"
             style="background: linear-gradient(135deg, #1f080c 0%, #3e1218 50%, #791d1d 100%);">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-outline-light rounded-pill px-2.5 py-1 text-nowrap d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                        <i class="bi bi-arrow-left"></i> Proposal Details
                    </a>
                    <span class="badge bg-white bg-opacity-20 text-white border border-white border-opacity-25 px-2.5 py-1 rounded-pill font-monospace" style="font-size: 0.75rem;">
                        {{ $proposal->proposal_code ?? 'PROPOSAL #'.$proposal->id }}
                    </span>
                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 px-2.5 py-1 rounded-pill" style="font-size: 0.75rem;">
                        Phase {{ $proposal->current_phase }} ({{ min(100, max(0, round(($proposal->current_phase / 5) * 100))) }}%)
                    </span>
                </div>
                <h1 class="h5 fw-bold mb-0 text-white text-truncate" style="max-width: 650px;" title="{{ $proposal->title }}">
                    <i class="bi bi-pencil-square me-1.5 text-warning"></i> {{ $proposal->title }}
                </h1>
                <div class="text-white text-opacity-75 small mt-1" style="font-size: 0.8rem;">
                    Lead Author: <strong>{{ $proposal->user->name }}</strong> &bull; Dept: {{ $proposal->user->department ?? 'General' }}
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($isAssignedReviewer || $isAdminOrStaff || auth()->user()->role === 'reviewer')
                <button type="button" class="btn btn-sm btn-warning text-dark rounded-pill px-3 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;" data-bs-toggle="modal" data-bs-target="#reviewerEvaluationModal">
                    <i class="bi bi-star-fill text-dark"></i> Score Rubric (FM-001)
                </button>
                @endif
                <a href="{{ route('proposal.evaluationPdf', $proposal->id) }}" target="_blank" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;" title="View / Print Official Criteria on Approval Sheet">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Form RESU-FM-001 PDF
                </a>
                @if($canEditManuscript)
                <a href="{{ route('proposal.edit', $proposal->id) }}" target="_blank" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;" title="Edit proposal in full 5-step form (adjust budget, work plan, or re-upload file)">
                    <i class="bi bi-pencil-square"></i> Full Form Editor
                </a>
                @if(in_array($proposal->status, ['revision_required', 'returned_for_revision', 'approved_with_revisions']))
                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;" onclick="confirmResubmitRevisions()">
                    <i class="bi bi-send-check-fill"></i> Resubmit Revisions
                </button>
                @endif
                @endif
                <a href="{{ route('proposal.resu_fm015', $proposal->id) }}" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                    <i class="bi bi-printer"></i> Official RESU-FM-015
                </a>
                <a href="{{ route('proposal.reviewSummary', $proposal->id) }}" target="_blank" class="btn btn-sm btn-light rounded-pill px-3 py-1.5 fw-bold text-dark shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                    <i class="bi bi-file-earmark-ruled text-primary"></i> Review Summary Sheet
                </a>
                @if($proposal->document_path)
                <a href="{{ route('file.serve', ['path' => $proposal->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                    <i class="bi bi-file-earmark-pdf"></i> Original PDF
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Workspace Split Screen Layout -->
    <div class="row g-3">
        <!-- ======================================================== -->
        <!-- LEFT COLUMN: In-Browser Document Viewer (Manuscript Text) -->
        <!-- ======================================================== -->
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" id="document-viewer-card">
                <!-- Viewer Tab Controls -->
                <div class="card-header bg-white border-bottom py-2.5 px-3 px-md-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <ul class="nav nav-pills card-header-pills gap-1" id="docViewerTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-3 py-1.5 fw-bold text-dark small" id="text-view-tab" data-bs-toggle="pill" data-bs-target="#text-view-pane" type="button" role="tab">
                                <i class="bi bi-file-text me-1 text-primary"></i> Interactive Manuscript
                            </button>
                        </li>
                        @if($proposal->document_path)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-3 py-1.5 fw-semibold text-muted small" id="pdf-view-tab" data-bs-toggle="pill" data-bs-target="#pdf-view-pane" type="button" role="tab">
                                <i class="bi bi-file-earmark-pdf me-1 text-danger"></i> PDF Preview Embed
                            </button>
                        </li>
                        @endif
                    </ul>

                    <div class="small text-muted d-flex align-items-center gap-2">
                        <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1">
                            <i class="bi bi-cursor-fill text-primary me-1"></i> Highlight text to comment
                        </span>
                    </div>
                </div>

                <div class="tab-content" id="docViewerTabsContent">
                    <!-- Tab 1: Interactive Manuscript Text (Supports Highlighting & Annotating) -->
                    <div class="tab-pane fade show active p-3 p-md-4 p-lg-5" id="text-view-pane" role="tabpanel" style="background-color: #faf9f6; min-height: 800px;">
                        <!-- Paper Canvas (Google Docs style page container) -->
                        <div class="manuscript-paper bg-white mx-auto shadow-sm rounded-3 p-4 p-md-5 border" style="max-width: 860px; line-height: 1.8; color: #222;" id="manuscript-canvas">
                            
                            @if($canEditManuscript)
                            <div class="alert alert-primary bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3 py-2 px-3 small mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-pencil-square text-primary fs-5"></i>
                                    <div>
                                        <strong class="text-primary">Inline Manuscript Editing Active:</strong>
                                        <span class="text-secondary">Click the <strong>Edit Section</strong> button on any section below to update your text directly while reading reviewer notes.</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if(!empty($proposal->extracted_text))
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold" onclick="toggleFullManuscriptEditor()" id="btn-toggle-full-manuscript" style="font-size: 0.78rem;">
                                            <i class="bi bi-file-earmark-text me-1"></i> Edit Entire Manuscript
                                        </button>
                                    @endif
                                    <span class="badge bg-primary text-white rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">Live In-Place Editor</span>
                                </div>
                            </div>

                            @if(!empty($proposal->extracted_text))
                            <div class="section-edit-pane d-none mb-4 p-3.5 bg-light rounded-3 border shadow-sm" id="full-manuscript-editor-pane">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 0.95rem;">
                                        <i class="bi bi-pencil-square me-1"></i> Full Manuscript Continuous Editor
                                    </h6>
                                    <span class="badge bg-white text-secondary border px-2 py-0.5 small">All Chapters &amp; Sections</span>
                                </div>
                                <div class="alert alert-info border-0 rounded-2 py-1.5 px-2.5 small mb-2" style="font-size: 0.78rem;">
                                    <i class="bi bi-info-circle me-1"></i> Edit the complete manuscript text here. Saving will automatically update all chapter sections below.
                                </div>
                                <textarea class="form-control mb-2 rounded-2 bg-white font-monospace" id="input-full-manuscript" rows="18" style="font-size: 0.86rem; line-height: 1.65;">{{ $proposal->extracted_text }}</textarea>
                                <div class="d-flex justify-content-end gap-1.5">
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 small" onclick="toggleFullManuscriptEditor()">Cancel</button>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1 fw-bold small" id="btn-save-full-manuscript" onclick="saveFullManuscriptEdit()">
                                        <i class="bi bi-check-lg me-1"></i> Save Full Manuscript
                                    </button>
                                </div>
                            </div>
                            @endif
                            @endif

                            <!-- Document Header -->
                            <div class="text-center pb-4 mb-4 border-bottom">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-1 rounded-pill mb-2 font-monospace" style="font-size: 0.76rem;">
                                    {{ $proposal->proposal_code ?? 'PROPOSAL MANUSCRIPT' }}
                                </span>
                                <h2 class="fw-bold text-dark mb-2" style="font-size: 1.45rem; line-height: 1.4;">
                                    <span id="section-display-title">{{ $proposal->title }}</span>
                                    @if($canEditManuscript)
                                        <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 ms-1.5 align-middle" onclick="toggleSectionEdit('title')" title="Edit Title" style="font-size: 0.72rem;">
                                            <i class="bi bi-pencil-square"></i> Edit Title
                                        </button>
                                    @endif
                                </h2>
                                @if($canEditManuscript)
                                    <div class="section-edit-pane d-none mb-3 text-start mx-auto p-3 bg-light rounded-3 border" id="section-editor-title" style="max-width: 650px;">
                                        <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">PROPOSAL TITLE:</label>
                                        <input type="text" class="form-control form-control-sm mb-2 rounded-2 fw-semibold bg-white" id="input-section-title" value="{{ $proposal->title }}">
                                        <div class="d-flex justify-content-end gap-1.5">
                                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-0.5 small" onclick="cancelSectionEdit('title')">Cancel</button>
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-0.5 fw-bold small" id="btn-save-title" onclick="saveSectionEdit('title')">
                                                <i class="bi bi-check-lg me-0.5"></i> Save Title
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                <p class="text-muted small mb-0">
                                    Lead Proponent: <strong>{{ $proposal->user->name }}</strong>
                                    @if($proposal->collaborators && $proposal->collaborators->count() > 0)
                                        &bull; Collaborators: {{ $proposal->collaborators->pluck('name')->implode(', ') }}
                                    @endif
                                </p>
                                <div class="text-muted small" style="font-size: 0.78rem;">
                                    Field: {{ $proposal->research_field ?? 'Information & Communications Technology' }} &bull; Department: {{ $proposal->user->department ?? 'General' }}
                                </div>
                            </div>

                            <!-- Interactive Text Stream -->
                            <div id="manuscript-text-stream" class="selectable-document-content">
                                
                                @if(!empty($parsedSections) && count($parsedSections) > 0)
                                    <!-- Modular Editable Manuscript Sections (Detected from Uploaded Document) -->
                                    @foreach($parsedSections as $sec)
                                    <div class="manuscript-section mb-4" data-chapter="{{ $sec['title'] }}" id="section-part-{{ $sec['index'] }}">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2.5">
                                            <div class="d-flex align-items-center gap-2">
                                                <h5 class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">
                                                    <i class="bi {{ $sec['icon'] ?? 'bi-bookmark-fill' }} me-1.5 text-primary opacity-75"></i> {{ $sec['title'] }}
                                                </h5>
                                                @if(!empty($sec['field']))
                                                    <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.68rem;">
                                                        <i class="bi bi-check2-circle text-success me-0.5"></i> Form RESU-FM-015
                                                    </span>
                                                @endif
                                            </div>
                                            @if($canEditManuscript)
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 fw-semibold" onclick="toggleParsedSectionEdit({{ $sec['index'] }})" id="btn-edit-sec-{{ $sec['index'] }}" style="font-size: 0.75rem;">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit Section
                                                </button>
                                            @endif
                                        </div>
                                        <div class="section-body text-secondary small" id="section-display-sec-{{ $sec['index'] }}" style="font-size: 0.92rem; text-align: justify; line-height: 1.7;">
                                            {!! \App\Services\PdfTextExtractor::formatSectionContentHtml($sec['content']) !!}
                                        </div>
                                        @if($canEditManuscript)
                                            <div class="section-edit-pane d-none mt-2 p-3 bg-light rounded-3 border shadow-sm" id="section-editor-sec-{{ $sec['index'] }}">
                                                <div class="d-flex justify-content-between align-items-center mb-1.5">
                                                    <label class="form-label text-muted small fw-bold mb-0" style="font-size: 0.72rem; text-transform: uppercase;">
                                                        EDIT {{ $sec['title'] }}:
                                                    </label>
                                                    <span class="text-muted small" style="font-size: 0.7rem;">Live In-Place Section Editor</span>
                                                </div>
                                                <textarea class="form-control mb-2 rounded-2 bg-white" id="input-section-sec-{{ $sec['index'] }}" rows="8" style="font-size: 0.88rem; line-height: 1.65;">{{ $sec['content'] }}</textarea>
                                                <div class="d-flex justify-content-end gap-1.5">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-0.5 small" onclick="cancelParsedSectionEdit({{ $sec['index'] }})">Cancel</button>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3.5 py-0.5 fw-bold small" id="btn-save-sec-{{ $sec['index'] }}" onclick="saveParsedSectionEdit({{ $sec['index'] }})">
                                                        <i class="bi bi-check-lg me-0.5"></i> Save Changes
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @endforeach
                                @else
                                    <!-- Fallback for Form Wizard / Structured Proposals -->
                                    @if($proposal->credentials || $proposal->sdgs_addressed || $canEditManuscript)
                                    <div class="manuscript-section mb-4" data-chapter="Title & Proponent Credentials" id="section-credentials">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2.5">
                                            <h5 class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">
                                                <i class="bi bi-person-badge me-1"></i> Proponent Credentials &amp; SDGs Addressed
                                            </h5>
                                            @if($canEditManuscript)
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 fw-semibold" onclick="toggleSectionEdit('credentials')" style="font-size: 0.75rem;">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>
                                            @endif
                                        </div>
                                        <div class="section-body text-secondary small" id="section-display-credentials">
                                            <div class="mb-2">
                                                <strong>Key Proponent Credentials:</strong> <span id="text-display-credentials">{{ $proposal->credentials ?: 'None provided' }}</span>
                                            </div>
                                            <div class="mb-1">
                                                <strong>Sustainable Development Goals / Thrusts:</strong> <span class="badge bg-light text-dark border" id="text-display-sdgs">{{ $proposal->sdgs_addressed ?: 'None specified' }}</span>
                                            </div>
                                        </div>
                                        @if($canEditManuscript)
                                            <div class="section-edit-pane d-none mt-2 p-3 bg-light rounded-3 border" id="section-editor-credentials">
                                                <div class="mb-2">
                                                    <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">KEY PROPONENT CREDENTIALS:</label>
                                                    <input type="text" class="form-control form-control-sm rounded-2 bg-white" id="input-section-credentials" value="{{ $proposal->credentials }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">SDGs / INSTITUTIONAL THRUSTS:</label>
                                                    <input type="text" class="form-control form-control-sm rounded-2 bg-white" id="input-section-sdgs_addressed" value="{{ $proposal->sdgs_addressed }}">
                                                </div>
                                                <div class="d-flex justify-content-end gap-1.5">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-0.5 small" onclick="cancelSectionEdit('credentials')">Cancel</button>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-0.5 fw-bold small" id="btn-save-credentials" onclick="saveCredentialsEdit()">
                                                        <i class="bi bi-check-lg me-0.5"></i> Save Changes
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Abstract Section -->
                                    <div class="manuscript-section mb-4" data-chapter="Abstract" id="section-abstract">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2.5">
                                            <h5 class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">
                                                <i class="bi bi-card-text me-1"></i> Executive Abstract
                                            </h5>
                                            @if($canEditManuscript)
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 fw-semibold" onclick="toggleSectionEdit('abstract')" style="font-size: 0.75rem;">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>
                                            @endif
                                        </div>
                                        <div class="section-body text-secondary small lh-lg" id="section-display-abstract" style="font-size: 0.92rem; text-align: justify;">
                                            {!! nl2br(e($proposal->abstract ?? 'No abstract provided yet for this research proposal.')) !!}
                                        </div>
                                        @if($canEditManuscript)
                                            <div class="section-edit-pane d-none mt-2 p-3 bg-light rounded-3 border" id="section-editor-abstract">
                                                <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">EXECUTIVE ABSTRACT:</label>
                                                <textarea class="form-control mb-2 rounded-2 bg-white" id="input-section-abstract" rows="6" style="font-size: 0.88rem; line-height: 1.6;">{{ $proposal->abstract }}</textarea>
                                                <div class="d-flex justify-content-end gap-1.5">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-0.5 small" onclick="cancelSectionEdit('abstract')">Cancel</button>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-0.5 fw-bold small" id="btn-save-abstract" onclick="saveSectionEdit('abstract')">
                                                        <i class="bi bi-check-lg me-0.5"></i> Save Changes
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Background of the Study & Problem Statement -->
                                    @if($proposal->background_of_study || $proposal->rationale || $canEditManuscript)
                                    <div class="manuscript-section mb-4" data-chapter="Background of the Study & Problem" id="section-background">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2.5">
                                            <h5 class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">
                                                <i class="bi bi-bookmark-fill me-1"></i> Background of the Study &amp; Framework
                                            </h5>
                                            @if($canEditManuscript)
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 fw-semibold" onclick="toggleSectionEdit('background_of_study')" style="font-size: 0.75rem;">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>
                                            @endif
                                        </div>
                                        <div class="section-body text-secondary small" id="section-display-background_of_study" style="font-size: 0.92rem; text-align: justify; line-height: 1.7;">
                                            {!! nl2br(e($proposal->background_of_study ?: $proposal->rationale)) !!}
                                        </div>
                                        @if($canEditManuscript)
                                            <div class="section-edit-pane d-none mt-2 p-3 bg-light rounded-3 border" id="section-editor-background_of_study">
                                                <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">BACKGROUND &amp; THEORETICAL / CONCEPTUAL FRAMEWORK:</label>
                                                <textarea class="form-control mb-2 rounded-2 bg-white" id="input-section-background_of_study" rows="8" style="font-size: 0.88rem; line-height: 1.6;">{{ $proposal->background_of_study ?: $proposal->rationale }}</textarea>
                                                <div class="d-flex justify-content-end gap-1.5">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-0.5 small" onclick="cancelSectionEdit('background_of_study')">Cancel</button>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-0.5 fw-bold small" id="btn-save-background_of_study" onclick="saveSectionEdit('background_of_study')">
                                                        <i class="bi bi-check-lg me-0.5"></i> Save Changes
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Significance of the Study -->
                                    @if($proposal->significance_of_study || $canEditManuscript)
                                    <div class="manuscript-section mb-4" data-chapter="Significance of the Study" id="section-significance">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2.5">
                                            <h5 class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">
                                                <i class="bi bi-award-fill me-1"></i> Significance of the Study
                                            </h5>
                                            @if($canEditManuscript)
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 fw-semibold" onclick="toggleSectionEdit('significance_of_study')" style="font-size: 0.75rem;">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>
                                            @endif
                                        </div>
                                        <div class="section-body text-secondary small" id="section-display-significance_of_study" style="font-size: 0.92rem; text-align: justify; line-height: 1.7;">
                                            {!! nl2br(e($proposal->significance_of_study)) !!}
                                        </div>
                                        @if($canEditManuscript)
                                            <div class="section-edit-pane d-none mt-2 p-3 bg-light rounded-3 border" id="section-editor-significance_of_study">
                                                <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">SIGNIFICANCE OF THE STUDY:</label>
                                                <textarea class="form-control mb-2 rounded-2 bg-white" id="input-section-significance_of_study" rows="6" style="font-size: 0.88rem; line-height: 1.6;">{{ $proposal->significance_of_study }}</textarea>
                                                <div class="d-flex justify-content-end gap-1.5">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-0.5 small" onclick="cancelSectionEdit('significance_of_study')">Cancel</button>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-0.5 fw-bold small" id="btn-save-significance_of_study" onclick="saveSectionEdit('significance_of_study')">
                                                        <i class="bi bi-check-lg me-0.5"></i> Save Changes
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Definition of Terms -->
                                    @if($proposal->definition_of_terms || $canEditManuscript)
                                    <div class="manuscript-section mb-4" data-chapter="Definition of Terms" id="section-terms">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2.5">
                                            <h5 class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">
                                                <i class="bi bi-spellcheck me-1"></i> Definition of Terms
                                            </h5>
                                            @if($canEditManuscript)
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 fw-semibold" onclick="toggleSectionEdit('definition_of_terms')" style="font-size: 0.75rem;">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>
                                            @endif
                                        </div>
                                        <div class="section-body text-secondary small" id="section-display-definition_of_terms" style="font-size: 0.92rem; text-align: justify; line-height: 1.7;">
                                            {!! nl2br(e($proposal->definition_of_terms)) !!}
                                        </div>
                                        @if($canEditManuscript)
                                            <div class="section-edit-pane d-none mt-2 p-3 bg-light rounded-3 border" id="section-editor-definition_of_terms">
                                                <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">DEFINITION OF TERMS:</label>
                                                <textarea class="form-control mb-2 rounded-2 bg-white" id="input-section-definition_of_terms" rows="6" style="font-size: 0.88rem; line-height: 1.6;">{{ $proposal->definition_of_terms }}</textarea>
                                                <div class="d-flex justify-content-end gap-1.5">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-0.5 small" onclick="cancelSectionEdit('definition_of_terms')">Cancel</button>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-0.5 fw-bold small" id="btn-save-definition_of_terms" onclick="saveSectionEdit('definition_of_terms')">
                                                        <i class="bi bi-check-lg me-0.5"></i> Save Changes
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Review of Related Literature -->
                                    @if($proposal->literature_review || $canEditManuscript)
                                    <div class="manuscript-section mb-4" data-chapter="Review of Related Literature" id="section-rrl">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2.5">
                                            <h5 class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">
                                                <i class="bi bi-book-half me-1"></i> Review of Related Literature (RRL)
                                            </h5>
                                            @if($canEditManuscript)
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 fw-semibold" onclick="toggleSectionEdit('literature_review')" style="font-size: 0.75rem;">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>
                                            @endif
                                        </div>
                                        <div class="section-body text-secondary small" id="section-display-literature_review" style="font-size: 0.92rem; text-align: justify; line-height: 1.7;">
                                            {!! nl2br(e($proposal->literature_review)) !!}
                                        </div>
                                        @if($canEditManuscript)
                                            <div class="section-edit-pane d-none mt-2 p-3 bg-light rounded-3 border" id="section-editor-literature_review">
                                                <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">REVIEW OF RELATED LITERATURE (RRL):</label>
                                                <textarea class="form-control mb-2 rounded-2 bg-white" id="input-section-literature_review" rows="8" style="font-size: 0.88rem; line-height: 1.6;">{{ $proposal->literature_review }}</textarea>
                                                <div class="d-flex justify-content-end gap-1.5">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-0.5 small" onclick="cancelSectionEdit('literature_review')">Cancel</button>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-0.5 fw-bold small" id="btn-save-literature_review" onclick="saveSectionEdit('literature_review')">
                                                        <i class="bi bi-check-lg me-0.5"></i> Save Changes
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Methodology & Research Design -->
                                    @if($proposal->methodology || $canEditManuscript)
                                    <div class="manuscript-section mb-4" data-chapter="Methodology & Research Design" id="section-methodology">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2.5">
                                            <h5 class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">
                                                <i class="bi bi-diagram-3-fill me-1"></i> Methodology &amp; Research Design
                                            </h5>
                                            @if($canEditManuscript)
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 fw-semibold" onclick="toggleSectionEdit('methodology')" style="font-size: 0.75rem;">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>
                                            @endif
                                        </div>
                                        <div class="section-body text-secondary small" id="section-display-methodology" style="font-size: 0.92rem; text-align: justify; line-height: 1.7;">
                                            {!! nl2br(e($proposal->methodology)) !!}
                                        </div>
                                        @if($canEditManuscript)
                                            <div class="section-edit-pane d-none mt-2 p-3 bg-light rounded-3 border" id="section-editor-methodology">
                                                <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">METHODOLOGY &amp; RESEARCH DESIGN:</label>
                                                <textarea class="form-control mb-2 rounded-2 bg-white" id="input-section-methodology" rows="8" style="font-size: 0.88rem; line-height: 1.6;">{{ $proposal->methodology }}</textarea>
                                                <div class="d-flex justify-content-end gap-1.5">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-0.5 small" onclick="cancelSectionEdit('methodology')">Cancel</button>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-0.5 fw-bold small" id="btn-save-methodology" onclick="saveSectionEdit('methodology')">
                                                        <i class="bi bi-check-lg me-0.5"></i> Save Changes
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- References & Citations -->
                                    @if($proposal->references || $canEditManuscript)
                                    <div class="manuscript-section mb-4" data-chapter="References & Citations" id="section-references">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2.5">
                                            <h5 class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">
                                                <i class="bi bi-quote me-1"></i> References &amp; Citations
                                            </h5>
                                            @if($canEditManuscript)
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 fw-semibold" onclick="toggleSectionEdit('references')" style="font-size: 0.75rem;">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>
                                            @endif
                                        </div>
                                        <div class="section-body text-secondary small" id="section-display-references" style="font-size: 0.92rem; text-align: justify; line-height: 1.7;">
                                            {!! nl2br(e($proposal->references)) !!}
                                        </div>
                                        @if($canEditManuscript)
                                            <div class="section-edit-pane d-none mt-2 p-3 bg-light rounded-3 border" id="section-editor-references">
                                                <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">REFERENCES &amp; CITATIONS:</label>
                                                <textarea class="form-control mb-2 rounded-2 bg-white" id="input-section-references" rows="8" style="font-size: 0.88rem; line-height: 1.6;">{{ $proposal->references }}</textarea>
                                                <div class="d-flex justify-content-end gap-1.5">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-0.5 small" onclick="cancelSectionEdit('references')">Cancel</button>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-0.5 fw-bold small" id="btn-save-references" onclick="saveSectionEdit('references')">
                                                        <i class="bi bi-check-lg me-0.5"></i> Save Changes
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                @endif

                                <!-- Work Plan (Gantt Chart Matrix) -->
                                @if(!empty($proposal->work_plan_matrix) && is_array($proposal->work_plan_matrix))
                                <div class="manuscript-section mb-4" data-chapter="Work Plan & Gantt Matrix" id="section-workplan">
                                    <h5 class="fw-bold text-primary border-bottom pb-1 mb-2.5" style="font-size: 1.05rem;">
                                        <i class="bi bi-calendar3-range me-1"></i> Work Plan (Gantt Chart Matrix)
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm small align-middle text-center">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-start">Activity / Milestone</th>
                                                    @for($m = 1; $m <= 12; $m++)
                                                        <th style="width: 32px;">M{{ $m }}</th>
                                                    @endfor
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($proposal->work_plan_matrix as $wpRow)
                                                    @php
                                                        $wpAct = is_array($wpRow) ? ($wpRow['activity'] ?? '') : ($wpRow->activity ?? '');
                                                        $wpMonths = is_array($wpRow) ? ($wpRow['months'] ?? []) : ($wpRow->months ?? []);
                                                    @endphp
                                                    <tr>
                                                        <td class="text-start fw-medium">{{ $wpAct }}</td>
                                                        @for($m = 1; $m <= 12; $m++)
                                                            <td class="p-1 {{ in_array($m, $wpMonths) ? 'bg-primary bg-opacity-25' : '' }}">
                                                                @if(in_array($m, $wpMonths))
                                                                    <i class="bi bi-check-lg text-primary fw-bold"></i>
                                                                @endif
                                                            </td>
                                                        @endfor
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @elseif($proposal->milestones && $proposal->milestones->count() > 0)
                                <div class="manuscript-section mb-4" data-chapter="Work Plan & Gantt Matrix" id="section-ch4">
                                    <h5 class="fw-bold text-primary border-bottom pb-1 mb-2.5" style="font-size: 1.05rem;">
                                        <i class="bi bi-calendar-check me-1"></i> Work Plan &amp; Milestones
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm small">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Milestone / Activity</th>
                                                    <th>Timeline</th>
                                                    <th>Deliverables</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($proposal->milestones as $ms)
                                                    <tr>
                                                        <td class="fw-semibold">{{ $ms->title }}</td>
                                                        <td>{{ $ms->start_date ? \Carbon\Carbon::parse($ms->start_date)->format('M Y') : 'Start' }} - {{ $ms->target_date ? \Carbon\Carbon::parse($ms->target_date)->format('M Y') : 'M12' }}</td>
                                                        <td>{{ $ms->description }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                <!-- Line-Item Budget -->
                                @if($proposal->budgetItems && $proposal->budgetItems->count() > 0)
                                <div class="manuscript-section mb-4" data-chapter="Line-Item Budget (LIB)" id="section-ch5">
                                    <h5 class="fw-bold text-primary border-bottom pb-1 mb-2.5" style="font-size: 1.05rem;">
                                        <i class="bi bi-cash-coin me-1"></i> Line-Item Budget (LIB) Breakdown
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm small">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Category</th>
                                                    <th>Item Description &amp; Specifications</th>
                                                    <th>Existing Resources</th>
                                                    <th class="text-end">Proposed Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $totCalc = 0; @endphp
                                                @foreach($proposal->budgetItems as $item)
                                                    @php $totCalc += $item->amount; @endphp
                                                    <tr>
                                                        <td class="text-uppercase text-muted fw-semibold" style="font-size: 0.75rem;">
                                                            {{ ucfirst(str_replace('_', ' ', $item->category_group ?? ($item->category ?? 'MOOE'))) }}
                                                        </td>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td><span class="badge bg-light text-dark border">{{ $item->existing_resources ?? 'RESU' }}</span></td>
                                                        <td class="text-end fw-bold">&#8369;{{ number_format($item->amount, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light fw-bold">
                                                <tr>
                                                    <td colspan="3" class="text-end text-dark">GRAND TOTAL ESTIMATED BUDGET:</td>
                                                    <td class="text-end text-dark fw-bold">&#8369;{{ number_format($proposal->total_budget ?: $totCalc, 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                <!-- References & Citations -->
                                @if($proposal->references || $canEditManuscript)
                                <div class="manuscript-section mb-4" data-chapter="References & Citations" id="section-references">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2.5">
                                        <h5 class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">
                                            <i class="bi bi-link-45deg me-1"></i> References &amp; Citations (APA 7th)
                                        </h5>
                                        @if($canEditManuscript)
                                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 fw-semibold" onclick="toggleSectionEdit('references')" style="font-size: 0.75rem;">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </button>
                                        @endif
                                    </div>
                                    <div class="section-body text-secondary small lh-lg font-monospace" id="section-display-references" style="font-size: 0.85rem; text-align: justify;">
                                        {!! nl2br(e($proposal->references)) !!}
                                    </div>
                                    @if($canEditManuscript)
                                        <div class="section-edit-pane d-none mt-2 p-3 bg-light rounded-3 border" id="section-editor-references">
                                            <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">REFERENCES &amp; CITATIONS (APA 7th):</label>
                                            <textarea class="form-control mb-2 rounded-2 bg-white font-monospace" id="input-section-references" rows="6" style="font-size: 0.85rem; line-height: 1.6;">{{ $proposal->references }}</textarea>
                                            <div class="d-flex justify-content-end gap-1.5">
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-0.5 small" onclick="cancelSectionEdit('references')">Cancel</button>
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-0.5 fw-bold small" id="btn-save-references" onclick="saveSectionEdit('references')">
                                                    <i class="bi bi-check-lg me-0.5"></i> Save Changes
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: PDF Preview Embed -->
                    @if($proposal->document_path)
                    <div class="tab-pane fade" id="pdf-view-pane" role="tabpanel" style="height: 850px;">
                        <iframe src="{{ route('file.serve', ['path' => $proposal->document_path]) }}#toolbar=1" class="w-100 h-100 border-0" title="PDF Viewer"></iframe>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- RIGHT COLUMN: Google Docs-Style Comments & Revisions Stream -->
        <!-- ======================================================== -->
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 80px;" id="comments-sidebar-card">
                
                <!-- Sidebar Header -->
                <div class="card-header bg-white border-bottom py-3 px-3 px-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1rem;">
                            <i class="bi bi-chat-quote-fill text-warning"></i> Revision Notes
                        </h5>
                        @php
                            $unresolvedCount = $proposal->annotations->where('is_resolved', false)->count();
                            $resolvedCount = $proposal->annotations->where('is_resolved', true)->count();
                        @endphp
                        <span class="badge {{ $unresolvedCount > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill px-2.5 py-1" id="unresolved-badge">
                            {{ $unresolvedCount }} Open
                        </span>
                    </div>

                    <!-- Filter Pills -->
                    <div class="d-flex gap-1.5 pt-1">
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 filter-annotation-btn active fw-semibold" style="font-size: 0.74rem;" data-filter="all">
                            All ({{ $proposal->annotations->count() }})
                        </button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 filter-annotation-btn fw-semibold" style="font-size: 0.74rem;" data-filter="open">
                            Pending ({{ $unresolvedCount }})
                        </button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 filter-annotation-btn fw-semibold" style="font-size: 0.74rem;" data-filter="resolved">
                            Resolved ({{ $resolvedCount }})
                        </button>
                    </div>
                </div>

                <!-- Add Comment Form Card (Google Docs Popover Style) -->
                <div class="p-3 border-bottom bg-light bg-opacity-75" id="new-comment-composer">
                    <form id="annotation-create-form">
                        @csrf
                        <input type="hidden" name="highlighted_text" id="composer-highlighted-text" value="">
                        <input type="hidden" name="highlight_anchor" id="composer-highlight-anchor" value="">

                        <!-- Referenced Passage Quote Box (Shown when reviewer highlights text) -->
                        <div id="composer-quote-preview" class="p-2 mb-2 bg-warning bg-opacity-10 border-start border-4 border-warning rounded-end small d-none position-relative">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-1.5" style="font-size: 0.65rem;" onclick="clearHighlightedSelection()" title="Clear quote"></button>
                            <small class="text-muted fw-bold d-block mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">
                                <i class="bi bi-highlighter text-warning"></i> REFERENCED TEXT PASSAGE:
                            </small>
                            <div class="fst-italic text-dark text-truncate-3 lh-sm" id="composer-quote-text" style="font-size: 0.8rem;"></div>
                        </div>

                        <!-- Chapter/Section Selector -->
                        <div class="mb-2">
                            <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.04em;">CHAPTER / SECTION:</label>
                            <select name="chapter_section" id="composer-chapter-select" class="form-select form-select-sm bg-white rounded-3 py-1.5" style="font-size: 0.8rem;">
                                @foreach($chapters as $chKey => $chVal)
                                    <option value="{{ $chKey }}">{{ $chVal }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quick Tagging Dropdown -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label text-muted small fw-bold mb-0" style="font-size: 0.7rem; letter-spacing: 0.04em;">
                                    <i class="bi bi-at text-primary"></i> TAG RESEARCHER:
                                </label>
                                <span class="text-muted small" style="font-size: 0.68rem;">Sends notification &amp; email</span>
                            </div>
                            <select name="tagged_user_id" id="composer-tagged-user" class="form-select form-select-sm bg-white rounded-3 py-1.5" style="font-size: 0.8rem;">
                                <option value="">-- No specific tag (Notify Lead Author) --</option>
                                @foreach($taggableUsers as $tUser)
                                    <option value="{{ $tUser->id }}">
                                        {{ $tUser->name }} ({{ $tUser->email }}) {{ $tUser->id === $proposal->user_id ? '— [Lead Author]' : '— [Collaborator]' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Comment / Suggestion Textarea -->
                        <div class="mb-2">
                            <textarea name="comment" id="composer-comment-text" class="form-control bg-white rounded-3 py-2 px-2.5" rows="3" placeholder="Input your review comment or suggestion here... (Type @ to tag)" style="font-size: 0.84rem;" required></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-sm btn-link text-muted p-0 text-decoration-none" style="font-size: 0.75rem;" onclick="clearHighlightedSelection()">
                                Reset
                            </button>
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1.5" id="composer-submit-btn" style="font-size: 0.8rem;">
                                <i class="bi bi-send-fill"></i> Post Comment
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Comments Thread Stream List -->
                <div class="card-body p-2 p-md-3 overflow-y-auto" style="max-height: 580px;" id="annotations-stream-container">
                    @forelse($proposal->annotations as $annotation)
                        <div class="card border rounded-3 mb-2.5 shadow-2xs annotation-thread-card {{ $annotation->is_resolved ? 'resolved-card border-success border-opacity-25' : 'border-light-subtle' }}"
                             id="annotation-card-{{ $annotation->id }}"
                             data-annotation-id="{{ $annotation->id }}"
                             data-is-resolved="{{ $annotation->is_resolved ? '1' : '0' }}">
                            <div class="card-body p-2.5">
                                
                                <!-- Card Header -->
                                <div class="d-flex justify-content-between align-items-start gap-1 mb-1.5">
                                    <div class="d-flex align-items-center gap-1.5 min-w-0">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 26px; height: 26px; font-size: 0.72rem;">
                                            {{ strtoupper(substr($annotation->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="fw-bold text-dark small text-truncate d-block" style="font-size: 0.8rem; line-height: 1.2;">
                                                {{ $annotation->user->name ?? 'Reviewer' }}
                                            </span>
                                            <small class="text-muted d-block" style="font-size: 0.68rem; line-height: 1.1;">
                                                {{ $annotation->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Status / Chapter Badge -->
                                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                        @if($annotation->is_resolved)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                                <i class="bi bi-check-circle-fill"></i> Resolved
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                                Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Chapter Pill -->
                                <div class="mb-1.5">
                                    <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill" style="font-size: 0.67rem;">
                                        {{ $annotation->chapter_section }}
                                    </span>
                                </div>

                                <!-- Highlighted Quote (if any) -->
                                @if($annotation->highlighted_text)
                                    <div class="p-2 mb-2 bg-warning bg-opacity-10 border-start border-3 border-warning rounded-end text-dark small cursor-pointer highlight-quote-box transition-all"
                                         style="font-size: 0.78rem; line-height: 1.4;"
                                         onclick="jumpToAnnotation({{ $annotation->id }})"
                                         title="Click to jump and highlight this quote in document">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-warning text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">
                                                <i class="bi bi-highlighter"></i> Referenced Quote
                                            </span>
                                            <span class="badge bg-warning bg-opacity-25 text-dark border border-warning px-1.5 py-0.5 rounded-pill" style="font-size: 0.65rem;">
                                                <i class="bi bi-box-arrow-up-right me-0.5"></i> Jump to text
                                            </span>
                                        </div>
                                        <div class="fst-italic highlight-quote-text">
                                            &ldquo;{{ $annotation->highlighted_text }}&rdquo;
                                        </div>
                                    </div>
                                @endif

                                <!-- Comment Body -->
                                <div class="text-dark mb-2 small lh-sm" style="font-size: 0.84rem;">
                                    {{ $annotation->comment }}
                                </div>

                                <!-- Tagged Proponent -->
                                @if($annotation->taggedUser)
                                    <div class="mb-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                                            <i class="bi bi-at"></i>{{ $annotation->taggedUser->name }}
                                        </span>
                                    </div>
                                @endif

                                <!-- Action Buttons Row -->
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light-subtle">
                                    <button type="button" 
                                            class="btn btn-xs rounded-pill px-2.5 py-1 fw-bold toggle-resolve-btn {{ $annotation->is_resolved ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                            style="font-size: 0.72rem;"
                                            onclick="toggleResolveAnnotation({{ $annotation->id }})">
                                        <i class="bi {{ $annotation->is_resolved ? 'bi-arrow-counterclockwise' : 'bi-check-lg' }} me-0.5"></i>
                                        {{ $annotation->is_resolved ? 'Reopen' : 'Mark Resolved' }}
                                    </button>

                                    <div class="d-flex align-items-center gap-1.5">
                                        <button type="button" class="btn btn-xs btn-link text-primary p-0 text-decoration-none" style="font-size: 0.72rem;" onclick="toggleReplyBox({{ $annotation->id }})">
                                            <i class="bi bi-reply"></i> Reply
                                        </button>
                                        @if(auth()->id() === $annotation->user_id || in_array(auth()->user()->role, ['admin', 'super_admin']))
                                        <button type="button" class="btn btn-xs btn-link text-danger p-0 text-decoration-none ms-1" style="font-size: 0.72rem;" onclick="deleteAnnotation({{ $annotation->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Replies List (Threaded) -->
                                <div id="replies-list-{{ $annotation->id }}" class="mt-2 pt-2 border-top {{ $annotation->replies && $annotation->replies->count() > 0 ? '' : 'd-none' }}">
                                    @if($annotation->replies)
                                        @foreach($annotation->replies as $reply)
                                            <div class="p-2 mb-1.5 rounded-2 bg-light border-start border-2 border-primary small" style="font-size: 0.78rem;">
                                                <div class="d-flex justify-content-between align-items-center mb-0.5">
                                                    <strong class="text-dark" style="font-size: 0.75rem;">{{ $reply->user->name }}</strong>
                                                    <span class="text-muted" style="font-size: 0.65rem;">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <div class="text-secondary">{{ $reply->comment }}</div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Inline Reply Composer (Hidden by default) -->
                                <div id="reply-box-{{ $annotation->id }}" class="mt-2 pt-2 border-top d-none">
                                    <form onsubmit="submitReply(event, {{ $annotation->id }})">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="comment" class="form-control rounded-start-pill py-1 px-2.5" placeholder="Write a reply..." style="font-size: 0.76rem;" required>
                                            <button type="submit" class="btn btn-primary rounded-end-pill px-2.5 fw-bold" style="font-size: 0.75rem;">
                                                Reply
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted small" id="no-annotations-notice">
                            <i class="bi bi-chat-left-quote fs-2 text-muted opacity-50 d-block mb-2"></i>
                            <p class="mb-1 fw-bold text-dark">No review notes yet</p>
                            <p class="text-muted mb-0" style="font-size: 0.75rem;">Highlight any text on the manuscript to add your first contextual comment.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Highlight Tooltip (Appears when text is selected) -->
    <div id="floating-highlight-popover" class="position-absolute d-none shadow-lg rounded-pill bg-dark text-white px-3 py-1.5" style="z-index: 1050; cursor: pointer;">
        <span class="d-flex align-items-center gap-1.5 small fw-bold" onclick="attachSelectedTextToComposer()">
            <i class="bi bi-chat-quote-fill text-warning"></i> Add Revision Note
        </span>
    </div>

    <style>
        .selectable-document-content {
            user-select: text;
        }

        /* Google Docs-style Document Inline Highlights */
        .doc-comment-highlight {
            background-color: #fef08a !important; /* Authentic Google Docs yellow */
            color: #1c1917 !important;
            border-bottom: 2px solid #eab308;
            border-radius: 3px;
            padding: 1px 3px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            display: inline;
        }

        .doc-comment-highlight:hover {
            background-color: #fde047 !important;
            box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.45);
            border-bottom-color: #ca8a04;
        }

        /* Resolved Highlights - Soft calm blue/green */
        .doc-comment-highlight.is-resolved {
            background-color: #e0f2fe !important;
            border-bottom: 2px dashed #0284c7;
            color: #0369a1 !important;
            opacity: 0.85;
        }

        .doc-comment-highlight.is-resolved:hover {
            background-color: #bae6fd !important;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.35);
        }

        /* Radiating Pulse Highlight when jumped to from card */
        @keyframes pulseGlowTarget {
            0% {
                background-color: #f59e0b !important;
                color: #ffffff !important;
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.85);
            }
            50% {
                background-color: #fde047 !important;
                color: #1c1917 !important;
                box-shadow: 0 0 0 10px rgba(245, 158, 11, 0);
            }
            100% {
                background-color: #fef08a !important;
                color: #1c1917 !important;
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
            }
        }

        .pulse-target-highlight {
            animation: pulseGlowTarget 1.5s ease-in-out 3;
            outline: 3px solid #d97706 !important;
            outline-offset: 3px;
            border-radius: 4px;
        }

        /* Active Comment Card Glow */
        .active-card-glow {
            border-color: #f59e0b !important;
            background-color: #fffdf5 !important;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.35) !important;
            transform: translateY(-2px);
            transition: all 0.25s ease;
        }

        .highlight-quote-box:hover {
            background-color: #fef08a !important;
            border-color: #d97706 !important;
        }

        .text-truncate-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .shadow-2xs {
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
    </style>

    <script>
        let currentSelectedRange = null;
        let currentSelectedText = '';
        const annotationsData = @json($proposal->annotations);

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Immediately apply persistent highlights to the document
            applyDocumentHighlights();

            const manuscriptStream = document.getElementById('manuscript-text-stream');
            const floatingPopover = document.getElementById('floating-highlight-popover');

            // Text Selection Event Listener for Floating Tooltip
            if (manuscriptStream && floatingPopover) {
                document.addEventListener('mouseup', function(e) {
                    const selection = window.getSelection();
                    const text = selection.toString().trim();

                    if (text.length >= 3 && manuscriptStream.contains(selection.anchorNode)) {
                        currentSelectedText = text;
                        currentSelectedRange = selection.getRangeAt(0).cloneRange();

                        const rect = selection.getRangeAt(0).getBoundingClientRect();
                        floatingPopover.style.top = `${window.scrollY + rect.top - 42}px`;
                        floatingPopover.style.left = `${window.scrollX + rect.left + (rect.width / 2) - 80}px`;
                        floatingPopover.classList.remove('d-none');
                    } else if (!floatingPopover.contains(e.target)) {
                        floatingPopover.classList.add('d-none');
                    }
                });
            }

            // Filter button events
            document.querySelectorAll('.filter-annotation-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-annotation-btn').forEach(b => b.classList.remove('active', 'btn-dark'));
                    document.querySelectorAll('.filter-annotation-btn').forEach(b => b.classList.add('btn-light'));
                    this.classList.remove('btn-light');
                    this.classList.add('active', 'btn-dark');

                    const filter = this.getAttribute('data-filter');
                    document.querySelectorAll('.annotation-thread-card').forEach(card => {
                        const isResolved = card.getAttribute('data-is-resolved') === '1';
                        if (filter === 'all') {
                            card.classList.remove('d-none');
                        } else if (filter === 'open') {
                            if (isResolved) card.classList.add('d-none');
                            else card.classList.remove('d-none');
                        } else if (filter === 'resolved') {
                            if (isResolved) card.classList.remove('d-none');
                            else card.classList.add('d-none');
                        }
                    });
                });
            });

            // Form Submit for New Annotation
            const form = document.getElementById('annotation-create-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const submitBtn = document.getElementById('composer-submit-btn');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Posting...';

                    const formData = new FormData(form);

                    fetch("{{ route('proposal.annotations.store', $proposal->id) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            form.reset();
                            clearHighlightedSelection();
                            location.reload(); // Refresh to render card & highlights cleanly
                        } else {
                            alert(data.message || 'Error saving comment.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Failed to save comment.');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-send-fill"></i> Post Comment';
                    });
                });
            }
        });

        // Search document text and wrap every annotated passage with persistent Google Docs yellow highlight
        function applyDocumentHighlights() {
            const canvas = document.getElementById('manuscript-canvas');
            if (!canvas || !annotationsData || !annotationsData.length) return;

            annotationsData.forEach(a => {
                if (!a.highlighted_text || !a.highlighted_text.trim()) return;
                const rawQuote = a.highlighted_text.trim();
                const id = a.id;
                const isResolved = Boolean(a.is_resolved);
                const authorName = a.user ? a.user.name : 'Reviewer';
                const commentShort = (a.comment || '').replace(/"/g, '&quot;').substring(0, 100);

                highlightQuoteInCanvas(canvas, rawQuote, id, isResolved, authorName, commentShort);
            });
        }

        function highlightQuoteInCanvas(canvas, rawQuote, annotationId, isResolved, authorName, commentShort) {
            if (document.getElementById(`doc-hl-${annotationId}`)) return;

            // Split into clean words ignoring varied linebreaks/spaces
            const words = rawQuote.split(/\s+/).filter(w => w.length > 0);
            if (words.length === 0) return;

            // Try full phrase first
            let success = matchAndWrap(canvas, words, annotationId, isResolved, authorName, commentShort);

            // If full phrase didn't match (e.g. edge punctuation or hyphen variations), try with prominent subphrase
            if (!success && words.length > 4) {
                const subCount = Math.min(words.length - 1, Math.max(4, Math.floor(words.length * 0.75)));
                const subWords = words.slice(0, subCount);
                matchAndWrap(canvas, subWords, annotationId, isResolved, authorName, commentShort);
            }
        }

        function matchAndWrap(canvas, words, annotationId, isResolved, authorName, commentShort) {
            function escapeWord(w) {
                let esc = w.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                esc = esc.replace(/['"“”‘’]/g, "['\"“”‘’]");
                esc = esc.replace(/[-–—]/g, "[-–—]");
                esc = esc.replace(/&/g, '(&|&amp;)');
                return esc;
            }

            const pattern = words.map(escapeWord).join('[\\s\\r\\n]+');
            let regex;
            try {
                regex = new RegExp(`(${pattern})`, 'i');
            } catch(e) {
                return false;
            }

            const sections = canvas.querySelectorAll('.section-body, p, td');
            for (let el of sections) {
                if (el.querySelector('.section-body')) continue; // Skip parent containers

                const rawText = el.textContent || '';
                const firstCleanWord = words[0].replace(/[^a-zA-Z0-9]/g, '');
                if (firstCleanWord && !rawText.toLowerCase().includes(firstCleanWord.toLowerCase())) {
                    continue;
                }

                if (regex.test(el.innerHTML)) {
                    let replaced = false;
                    el.innerHTML = el.innerHTML.replace(regex, function(match) {
                        if (replaced) return match;
                        replaced = true;
                        const statusClass = isResolved ? 'is-resolved' : 'is-pending';
                        return `<mark class="doc-comment-highlight ${statusClass}" id="doc-hl-${annotationId}" data-annotation-id="${annotationId}" onclick="handleHighlightClick(${annotationId})" title="Revision Note (${authorName}): &quot;${commentShort}&quot;"><i class="bi bi-chat-quote-fill me-1 small opacity-75"></i>${match}</mark>`;
                    });
                    if (replaced) return true;
                }
            }
            return false;
        }

        // Jump smoothly from comment card directly to highlighted text in manuscript
        function jumpToAnnotation(id) {
            // Ensure interactive text tab is active
            const textTabBtn = document.getElementById('text-view-tab');
            if (textTabBtn && !textTabBtn.classList.contains('active')) {
                bootstrap.Tab.getOrCreateInstance(textTabBtn).show();
            }

            // Find highlight element in document
            const highlightEl = document.getElementById(`doc-hl-${id}`);
            if (highlightEl) {
                highlightEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

                document.querySelectorAll('.doc-comment-highlight').forEach(el => el.classList.remove('pulse-target-highlight'));
                highlightEl.classList.add('pulse-target-highlight');
                setTimeout(() => {
                    highlightEl.classList.remove('pulse-target-highlight');
                }, 3500);
            } else {
                // Fallback search
                const card = document.getElementById(`annotation-card-${id}`);
                if (card) {
                    const quoteEl = card.querySelector('.highlight-quote-text');
                    const quoteText = quoteEl ? quoteEl.innerText.trim() : '';
                    if (quoteText) {
                        const words = quoteText.split(/\s+/).slice(0, 4);
                        const sections = document.querySelectorAll('#manuscript-canvas .section-body, #manuscript-canvas p');
                        for (let s of sections) {
                            if (words[0] && s.innerText.toLowerCase().includes(words[0].toLowerCase())) {
                                s.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                s.classList.add('pulse-target-highlight');
                                setTimeout(() => s.classList.remove('pulse-target-highlight'), 3000);
                                break;
                            }
                        }
                    }
                }
            }

            // Highlight the active card on the right
            document.querySelectorAll('.annotation-thread-card').forEach(c => c.classList.remove('active-card-glow'));
            const activeCard = document.getElementById(`annotation-card-${id}`);
            if (activeCard) {
                activeCard.classList.add('active-card-glow');
                setTimeout(() => activeCard.classList.remove('active-card-glow'), 2500);
            }
        }

        // Clicking a highlight in the document jumps to and focuses the comment card on the right
        function handleHighlightClick(id) {
            const allFilterBtn = document.querySelector('.filter-annotation-btn[data-filter="all"]');
            if (allFilterBtn && !allFilterBtn.classList.contains('active')) {
                allFilterBtn.click();
            }

            const card = document.getElementById(`annotation-card-${id}`);
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });

                document.querySelectorAll('.annotation-thread-card').forEach(c => c.classList.remove('active-card-glow'));
                card.classList.add('active-card-glow');
                setTimeout(() => card.classList.remove('active-card-glow'), 2500);
            }

            const highlightEl = document.getElementById(`doc-hl-${id}`);
            if (highlightEl) {
                highlightEl.classList.add('pulse-target-highlight');
                setTimeout(() => highlightEl.classList.remove('pulse-target-highlight'), 2000);
            }
        }

        // Attach highlighted text into comment composer
        function attachSelectedTextToComposer() {
            const floatingPopover = document.getElementById('floating-highlight-popover');
            if (floatingPopover) floatingPopover.classList.add('d-none');

            if (!currentSelectedText) return;

            document.getElementById('composer-highlighted-text').value = currentSelectedText;
            document.getElementById('composer-quote-text').innerText = currentSelectedText;
            document.getElementById('composer-quote-preview').classList.remove('d-none');

            // Find closest chapter
            if (currentSelectedRange) {
                let node = currentSelectedRange.commonAncestorContainer;
                while (node && node !== document.body) {
                    if (node.getAttribute && node.getAttribute('data-chapter')) {
                        const chapter = node.getAttribute('data-chapter');
                        const selectEl = document.getElementById('composer-chapter-select');
                        for (let opt of selectEl.options) {
                            if (opt.value === chapter) {
                                selectEl.value = chapter;
                                break;
                            }
                        }
                        break;
                    }
                    node = node.parentNode;
                }
            }

            // Scroll to composer & focus textarea
            const commentInput = document.getElementById('composer-comment-text');
            if (commentInput) {
                commentInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => commentInput.focus(), 250);
            }
        }

        function clearHighlightedSelection() {
            document.getElementById('composer-highlighted-text').value = '';
            document.getElementById('composer-quote-text').innerText = '';
            document.getElementById('composer-quote-preview').classList.add('d-none');
            const popover = document.getElementById('floating-highlight-popover');
            if (popover) popover.classList.add('d-none');
            currentSelectedText = '';
        }

        // Toggle Resolve/Reopen
        function toggleResolveAnnotation(id) {
            fetch(`/annotations/${id}/toggle-resolve`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(err => console.error(err));
        }

        // Delete Annotation
        function deleteAnnotation(id) {
            if (!confirm('Are you sure you want to delete this comment?')) return;

            fetch(`/annotations/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById(`annotation-card-${id}`);
                    if (card) card.remove();
                    const highlightEl = document.getElementById(`doc-hl-${id}`);
                    if (highlightEl) {
                        highlightEl.outerHTML = highlightEl.innerHTML;
                    }
                }
            })
            .catch(err => console.error(err));
        }

        // Toggle Inline Reply Box
        function toggleReplyBox(id) {
            const replyBox = document.getElementById(`reply-box-${id}`);
            if (replyBox) {
                replyBox.classList.toggle('d-none');
                if (!replyBox.classList.contains('d-none')) {
                    const input = replyBox.querySelector('input[name="comment"]');
                    if (input) input.focus();
                }
            }
        }

        // Submit Reply
        function submitReply(e, parentId) {
            e.preventDefault();
            const form = e.target;
            const input = form.querySelector('input[name="comment"]');
            const comment = input.value.trim();
            if (!comment) return;

            const formData = new FormData();
            formData.append('parent_id', parentId);
            formData.append('comment', comment);

            fetch("{{ route('proposal.annotations.store', $proposal->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(err => console.error(err));
        }

        // ==========================================
        // INLINE MANUSCRIPT EDITING (Approach 1)
        // ==========================================
        function toggleSectionEdit(key) {
            const editor = document.getElementById(`section-editor-${key}`);
            const display = document.getElementById(`section-display-${key}`);
            if (!editor) return;

            const isOpening = editor.classList.contains('d-none');
            if (isOpening) {
                editor.classList.remove('d-none');
                if (display) display.classList.add('d-none');
                const input = document.getElementById(`input-section-${key}`);
                if (input) {
                    input.focus();
                }
            } else {
                editor.classList.add('d-none');
                if (display) display.classList.remove('d-none');
            }
        }

        function cancelSectionEdit(key) {
            const editor = document.getElementById(`section-editor-${key}`);
            const display = document.getElementById(`section-display-${key}`);
            if (editor) editor.classList.add('d-none');
            if (display) display.classList.remove('d-none');
        }

        function toggleParsedSectionEdit(index) {
            const editor = document.getElementById(`section-editor-sec-${index}`);
            const display = document.getElementById(`section-display-sec-${index}`);
            const btn = document.getElementById(`btn-edit-sec-${index}`);
            if (!editor) return;

            const isOpening = editor.classList.contains('d-none');
            if (isOpening) {
                editor.classList.remove('d-none');
                if (display) display.classList.add('d-none');
                if (btn) btn.innerHTML = '<i class="bi bi-x-lg me-1"></i> Close';
                const input = document.getElementById(`input-section-sec-${index}`);
                if (input) input.focus();
            } else {
                editor.classList.add('d-none');
                if (display) display.classList.remove('d-none');
                if (btn) btn.innerHTML = '<i class="bi bi-pencil-square me-1"></i> Edit Section';
            }
        }

        function cancelParsedSectionEdit(index) {
            const editor = document.getElementById(`section-editor-sec-${index}`);
            const display = document.getElementById(`section-display-sec-${index}`);
            const btn = document.getElementById(`btn-edit-sec-${index}`);
            if (editor) editor.classList.add('d-none');
            if (display) display.classList.remove('d-none');
            if (btn) btn.innerHTML = '<i class="bi bi-pencil-square me-1"></i> Edit Section';
        }

        function saveParsedSectionEdit(index) {
            const input = document.getElementById(`input-section-sec-${index}`);
            const btn = document.getElementById(`btn-save-sec-${index}`);
            if (!input) return;

            const content = input.value;
            const originalBtnHtml = btn ? btn.innerHTML : 'Save';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
            }

            fetch("{{ route('proposal.updateSection', $proposal->id) }}", {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    section: 'manuscript_section',
                    section_index: index,
                    content: content
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const display = document.getElementById(`section-display-sec-${index}`);
                    if (display) {
                        display.innerHTML = data.rendered_html;
                        display.classList.remove('d-none');
                    }
                    const editor = document.getElementById(`section-editor-sec-${index}`);
                    if (editor) editor.classList.add('d-none');
                    const editBtn = document.getElementById(`btn-edit-sec-${index}`);
                    if (editBtn) editBtn.innerHTML = '<i class="bi bi-pencil-square me-1"></i> Edit Section';

                    showWorkspaceToast(data.message || 'Section updated successfully.', 'success');

                    // Clean and re-run document highlights so matching quotes re-highlight
                    removeExistingHighlightSpans();
                    applyDocumentHighlights();
                } else {
                    alert(data.message || 'Failed to update section.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while saving section changes.');
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                }
            });
        }

        function toggleFullManuscriptEditor() {
            const pane = document.getElementById('full-manuscript-editor-pane');
            const btn = document.getElementById('btn-toggle-full-manuscript');
            if (!pane) return;

            const isOpening = pane.classList.contains('d-none');
            if (isOpening) {
                pane.classList.remove('d-none');
                if (btn) btn.innerHTML = '<i class="bi bi-x-lg me-1"></i> Close Full Editor';
                const input = document.getElementById('input-full-manuscript');
                if (input) input.focus();
            } else {
                pane.classList.add('d-none');
                if (btn) btn.innerHTML = '<i class="bi bi-file-earmark-text me-1"></i> Edit Entire Manuscript';
            }
        }

        function saveFullManuscriptEdit() {
            const input = document.getElementById('input-full-manuscript');
            const btn = document.getElementById('btn-save-full-manuscript');
            if (!input) return;

            const content = input.value;
            const originalBtnHtml = btn ? btn.innerHTML : 'Save';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving Entire Manuscript...';
            }

            fetch("{{ route('proposal.updateSection', $proposal->id) }}", {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    section: 'extracted_text',
                    content: content
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showWorkspaceToast('Full manuscript saved successfully. Refreshing workspace sections...', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 600);
                } else {
                    alert(data.message || 'Failed to save full manuscript.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while saving full manuscript.');
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                }
            });
        }

        function saveSectionEdit(key) {
            const input = document.getElementById(`input-section-${key}`);
            const btn = document.getElementById(`btn-save-${key}`);
            if (!input) return;

            const content = input.value;
            const originalBtnHtml = btn ? btn.innerHTML : 'Save';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
            }

            fetch("{{ route('proposal.updateSection', $proposal->id) }}", {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    section: key,
                    content: content
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const display = document.getElementById(`section-display-${key}`);
                    if (display) {
                        display.innerHTML = data.rendered_html;
                        display.classList.remove('d-none');
                    }
                    const editor = document.getElementById(`section-editor-${key}`);
                    if (editor) editor.classList.add('d-none');

                    showWorkspaceToast(data.message || 'Section updated successfully.', 'success');

                    // Clean and re-run document highlights so matching quotes re-highlight
                    removeExistingHighlightSpans();
                    applyDocumentHighlights();
                } else {
                    alert(data.message || 'Failed to update section.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while saving changes.');
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                }
            });
        }

        function saveCredentialsEdit() {
            const credsInput = document.getElementById('input-section-credentials');
            const sdgsInput = document.getElementById('input-section-sdgs_addressed');
            const btn = document.getElementById('btn-save-credentials');
            const originalBtnHtml = btn ? btn.innerHTML : 'Save';

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
            }

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            Promise.all([
                fetch("{{ route('proposal.updateSection', $proposal->id) }}", {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: JSON.stringify({ section: 'credentials', content: credsInput ? credsInput.value : '' })
                }).then(r => r.json()),
                fetch("{{ route('proposal.updateSection', $proposal->id) }}", {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: JSON.stringify({ section: 'sdgs_addressed', content: sdgsInput ? sdgsInput.value : '' })
                }).then(r => r.json())
            ])
            .then(([res1, res2]) => {
                if (res1.success && res2.success) {
                    const credsDisplay = document.getElementById('text-display-credentials');
                    const sdgsDisplay = document.getElementById('text-display-sdgs');
                    if (credsDisplay) credsDisplay.textContent = (credsInput && credsInput.value) ? credsInput.value : 'None provided';
                    if (sdgsDisplay) sdgsDisplay.textContent = (sdgsInput && sdgsInput.value) ? sdgsInput.value : 'None specified';

                    const editor = document.getElementById('section-editor-credentials');
                    const display = document.getElementById('section-display-credentials');
                    if (editor) editor.classList.add('d-none');
                    if (display) display.classList.remove('d-none');

                    showWorkspaceToast('Credentials and SDGs updated successfully.', 'success');
                } else {
                    alert('Error saving credentials or SDGs.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Failed to save changes.');
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                }
            });
        }

        function removeExistingHighlightSpans() {
            const canvas = document.getElementById('manuscript-canvas');
            if (!canvas) return;
            const marks = canvas.querySelectorAll('mark.doc-comment-highlight');
            marks.forEach(m => {
                const parent = m.parentNode;
                if (!parent) return;
                while (m.firstChild) {
                    if (m.firstChild.nodeType === Node.ELEMENT_NODE && m.firstChild.tagName.toLowerCase() === 'i') {
                        m.removeChild(m.firstChild);
                    } else {
                        parent.insertBefore(m.firstChild, m);
                    }
                }
                parent.removeChild(m);
            });
        }

        function confirmResubmitRevisions() {
            if (!confirm("Are you sure you want to resubmit your proposal revisions back for review? Reviewers and coordinators will be notified immediately.")) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch("{{ route('proposal.resubmitRevisions', $proposal->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Revisions resubmitted successfully.');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to resubmit revisions.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while resubmitting revisions.');
            });
        }

        function showWorkspaceToast(msg, type = 'success') {
            let toast = document.getElementById('workspace-toast-container');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'workspace-toast-container';
                toast.className = 'position-fixed bottom-0 end-0 p-3';
                toast.style.zIndex = '9999';
                document.body.appendChild(toast);
            }
            const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
            const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-octagon-fill';
            toast.innerHTML = `
                <div class="toast show align-items-center text-white ${bgClass} border-0 shadow-lg rounded-3" role="alert" style="min-width: 280px;">
                    <div class="d-flex align-items-center justify-content-between p-2.5">
                        <div class="toast-body d-flex align-items-center gap-2 p-0" style="font-size: 0.85rem;">
                            <i class="bi ${icon} fs-5"></i> <span>${msg}</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white p-0 m-0" onclick="this.closest('.toast').remove()"></button>
                    </div>
                </div>
            `;
            setTimeout(() => {
                if (toast) toast.innerHTML = '';
            }, 3500);
        }

        // ==========================================
        // WORKSPACE REVIEWER EVALUATION (RESU-FM-001)
        // ==========================================
        const wsScoreA = document.getElementById('ws-eval-score-a');
        const wsScoreB = document.getElementById('ws-eval-score-b');
        const wsScoreC = document.getElementById('ws-eval-score-c');
        const wsScoreD = document.getElementById('ws-eval-score-d');
        const wsScoreE = document.getElementById('ws-eval-score-e');
        const wsTotalDisplay = document.getElementById('ws-eval-total-display');
        const wsBadge = document.getElementById('ws-eval-threshold-badge');

        function recalculateWorkspaceTotal() {
            if (!wsScoreA) return;
            const a = parseFloat(wsScoreA.value || 0);
            const b = parseFloat(wsScoreB.value || 0);
            const c = parseFloat(wsScoreC.value || 0);
            const d = parseFloat(wsScoreD.value || 0);
            const e = parseFloat(wsScoreE.value || 0);

            const total = Math.min(100, Math.max(0, a + b + c + d + e));
            if (wsTotalDisplay) wsTotalDisplay.textContent = total.toFixed(2) + '%';

            if (wsBadge) {
                if (total >= 80) {
                    wsBadge.className = 'alert py-2 px-3 mb-3 rounded-3 text-center small fw-semibold alert-success text-success border-success border-opacity-25';
                    wsBadge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Total score: <strong>' + total.toFixed(2) + '%</strong>. Meets the <strong>80%</strong> approval threshold.';
                } else if (total > 0) {
                    wsBadge.className = 'alert py-2 px-3 mb-3 rounded-3 text-center small fw-semibold alert-warning text-dark border-warning border-opacity-50';
                    wsBadge.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Total score: <strong>' + total.toFixed(2) + '%</strong>. Below the 80% passing threshold.';
                } else {
                    wsBadge.className = 'alert py-2 px-3 mb-3 rounded-3 text-center small fw-semibold alert-secondary text-muted';
                    wsBadge.innerHTML = '<i class="bi bi-info-circle me-1"></i> A rating of at least <strong>80%</strong> shall be the basis for approving R &amp; D proposals.';
                }
            }
        }

        [wsScoreA, wsScoreB, wsScoreC, wsScoreD, wsScoreE].forEach(input => {
            if (input) input.addEventListener('input', recalculateWorkspaceTotal);
        });
        recalculateWorkspaceTotal();

        function submitWorkspaceEvaluation() {
            const btn = document.getElementById('ws-eval-submit-btn');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

            const payload = {
                score_a: wsScoreA ? wsScoreA.value : 0,
                score_b: wsScoreB ? wsScoreB.value : 0,
                score_c: wsScoreC ? wsScoreC.value : 0,
                score_d: wsScoreD ? wsScoreD.value : 0,
                score_e: wsScoreE ? wsScoreE.value : 0,
                status: document.getElementById('ws-eval-status').value,
                review_comments: document.getElementById('ws-eval-comments').value,
                review_suggestions: document.getElementById('ws-eval-suggestions').value,
            };

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('proposal.evaluate', $proposal->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showWorkspaceToast(data.message || 'Evaluation saved successfully!', 'success');
                    const modalEl = document.getElementById('reviewerEvaluationModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    }
                } else {
                    alert(data.message || 'Error saving evaluation.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while saving evaluation.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        }
    </script>

    <!-- Reviewer Evaluation Modal (RESU-FM-001) -->
    <div class="modal fade" id="reviewerEvaluationModal" tabindex="-1" aria-labelledby="reviewerEvaluationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom py-3 px-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary rounded-pill px-2.5 py-1 font-monospace" style="font-size: 0.72rem;">FORM RESU-FM-001</span>
                            <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Peer Review Rubric</span>
                        </div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="reviewerEvaluationModalLabel">Criteria on Approval of Research Proposals</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light bg-opacity-50">
                    <form id="workspace-evaluation-form">
                        @csrf
                        <div class="bg-white p-3 rounded-3 border mb-3 shadow-2xs">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.68rem; letter-spacing: 0.05em;">Proposal Title:</small>
                            <h6 class="fw-bold text-primary mb-1">{{ $proposal->title }}</h6>
                            <div class="small text-muted" style="font-size: 0.78rem;">
                                Proponent: <strong>{{ $proposal->user->name }}</strong> &bull; Dept: {{ $proposal->college_affiliation ?: ($proposal->user->department ?? 'General') }}
                            </div>
                        </div>

                        <!-- Rubric Table -->
                        <div class="table-responsive bg-white rounded-3 border mb-3 shadow-2xs">
                            <table class="table table-bordered table-sm small mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 78%;">Evaluation Criteria</th>
                                        <th style="width: 22%;" class="text-center">Score (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="p-2.5">
                                            <strong class="text-dark">A. Innovativeness / Novelty / Creativity</strong>
                                            <div class="text-muted" style="font-size: 0.72rem;">Originality of research concept, innovation value. Max: 15%</div>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" step="0.5" min="0" max="15" name="score_a" id="ws-eval-score-a" class="form-control form-control-sm text-center fw-bold ws-rubric-input" value="{{ $reviewerEvaluation->score_a ?? '' }}" placeholder="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-2.5">
                                            <strong class="text-dark">B. Time Schedule and Cost</strong>
                                            <div class="text-muted" style="font-size: 0.72rem;">Work plan feasibility and budget realism. Max: 5%</div>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" step="0.5" min="0" max="5" name="score_b" id="ws-eval-score-b" class="form-control form-control-sm text-center fw-bold ws-rubric-input" value="{{ $reviewerEvaluation->score_b ?? '' }}" placeholder="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-2.5">
                                            <strong class="text-dark">C. Applicability</strong>
                                            <div class="text-muted" style="font-size: 0.72rem;">Results applicable for research, extension, CPE and potential IPSs. Max: 25%</div>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" step="0.5" min="0" max="25" name="score_c" id="ws-eval-score-c" class="form-control form-control-sm text-center fw-bold ws-rubric-input" value="{{ $reviewerEvaluation->score_c ?? '' }}" placeholder="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-2.5">
                                            <strong class="text-dark">D. Relevance</strong>
                                            <div class="text-muted" style="font-size: 0.72rem;">Contribution to new knowledge and S&amp;T advancement. Max: 25%</div>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" step="0.5" min="0" max="25" name="score_d" id="ws-eval-score-d" class="form-control form-control-sm text-center fw-bold ws-rubric-input" value="{{ $reviewerEvaluation->score_d ?? '' }}" placeholder="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-2.5">
                                            <strong class="text-dark">E. Effectiveness</strong>
                                            <div class="text-muted" style="font-size: 0.72rem;">Responsiveness to the needs of stakeholders and beneficiaries. Max: 30%</div>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" step="0.5" min="0" max="30" name="score_e" id="ws-eval-score-e" class="form-control form-control-sm text-center fw-bold ws-rubric-input" value="{{ $reviewerEvaluation->score_e ?? '' }}" placeholder="0">
                                        </td>
                                    </tr>
                                    <tr class="table-light">
                                        <td class="p-2.5 fw-bold text-dark text-uppercase">TOTAL RATING (%)</td>
                                        <td class="p-2 text-center fw-bold fs-6" id="ws-eval-total-display">0.00%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Threshold alert -->
                        <div id="ws-eval-threshold-badge" class="alert py-2 px-3 mb-3 rounded-3 text-center small fw-semibold alert-secondary" style="font-size: 0.78rem;">
                            <i class="bi bi-info-circle me-1"></i> A rating of at least <strong>80%</strong> shall be the basis for approving R &amp; D proposals for implementation.
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">RECOMMENDED STATUS / DECISION:</label>
                                <select name="status" id="ws-eval-status" class="form-select form-select-sm bg-white rounded-3 py-1.5" style="font-size: 0.82rem;">
                                    <option value="pending" {{ $proposal->status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                                    <option value="approved" {{ $proposal->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="approved_with_revisions" {{ $proposal->status === 'approved_with_revisions' ? 'selected' : '' }}>Approved with Revisions</option>
                                    <option value="revision_required" {{ $proposal->status === 'revision_required' ? 'selected' : '' }}>Revision Required</option>
                                    <option value="rejected" {{ $proposal->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-end">
                                <a href="{{ route('proposal.evaluationPdf', $proposal->id) }}" target="_blank" class="btn btn-outline-danger btn-sm w-100 rounded-pill py-1.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-1.5" style="font-size: 0.82rem;">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> Download / Print Current RESU-FM-001 PDF
                                </a>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">EVALUATOR REMARKS / COMMENTS (FORM RESU-FM-001):</label>
                            <textarea name="review_comments" id="ws-eval-comments" class="form-control form-control-sm bg-white rounded-3" rows="3" placeholder="Enter evaluator remarks and qualitative findings">{{ $reviewerEvaluation->comments ?? $proposal->review_comments }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold mb-1" style="font-size: 0.72rem;">SUGGESTIONS FOR IMPROVEMENT:</label>
                            <textarea name="review_suggestions" id="ws-eval-suggestions" class="form-control form-control-sm bg-white rounded-3" rows="2" placeholder="Actionable suggestions for the lead proponent">{{ $reviewerEvaluation->suggestions ?? $proposal->review_suggestions }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" id="ws-eval-submit-btn" onclick="submitWorkspaceEvaluation()">
                                <i class="bi bi-check-circle-fill"></i> Save Evaluation &amp; Ratings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </script>
</x-app-layout>
