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
                            
                            <!-- Document Header -->
                            <div class="text-center pb-4 mb-4 border-bottom">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-1 rounded-pill mb-2 font-monospace" style="font-size: 0.76rem;">
                                    {{ $proposal->proposal_code ?? 'PROPOSAL MANUSCRIPT' }}
                                </span>
                                <h2 class="fw-bold text-dark mb-2" style="font-size: 1.45rem; line-height: 1.4;">{{ $proposal->title }}</h2>
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
                                
                                <!-- Proponent Credentials & Institutional Alignment -->
                                @if($proposal->credentials || $proposal->sdgs_addressed)
                                <div class="manuscript-section mb-4" data-chapter="Title & Proponent Credentials" id="section-credentials">
                                    <h5 class="fw-bold text-primary border-bottom pb-1 mb-2.5" style="font-size: 1.05rem;">
                                        <i class="bi bi-person-badge me-1"></i> Proponent Credentials &amp; SDGs Addressed
                                    </h5>
                                    @if($proposal->credentials)
                                        <div class="mb-2 text-secondary small">
                                            <strong>Key Proponent Credentials:</strong> {{ $proposal->credentials }}
                                        </div>
                                    @endif
                                    @if($proposal->sdgs_addressed)
                                        <div class="mb-1 text-secondary small">
                                            <strong>Sustainable Development Goals / Thrusts:</strong> <span class="badge bg-light text-dark border">{{ $proposal->sdgs_addressed }}</span>
                                        </div>
                                    @endif
                                </div>
                                @endif

                                <!-- Abstract Section -->
                                <div class="manuscript-section mb-4" data-chapter="Abstract" id="section-abstract">
                                    <h5 class="fw-bold text-primary border-bottom pb-1 mb-2.5" style="font-size: 1.05rem;">
                                        <i class="bi bi-card-text me-1"></i> Executive Abstract
                                    </h5>
                                    <div class="section-body text-secondary small lh-lg" style="font-size: 0.92rem; text-align: justify;">
                                        {{ $proposal->abstract ?? 'No abstract provided yet for this research proposal.' }}
                                    </div>
                                </div>

                                <!-- Background of the Study & Problem Statement -->
                                @if($proposal->background_of_study || $proposal->rationale)
                                <div class="manuscript-section mb-4" data-chapter="Background of the Study & Problem" id="section-background">
                                    <h5 class="fw-bold text-primary border-bottom pb-1 mb-2.5" style="font-size: 1.05rem;">
                                        <i class="bi bi-bookmark-fill me-1"></i> Background of the Study &amp; Framework
                                    </h5>
                                    <div class="section-body text-secondary small" style="font-size: 0.92rem; text-align: justify; line-height: 1.7;">
                                        {!! nl2br(e($proposal->background_of_study ?: $proposal->rationale)) !!}
                                    </div>
                                </div>
                                @endif

                                <!-- Significance of the Study -->
                                @if($proposal->significance_of_study)
                                <div class="manuscript-section mb-4" data-chapter="Significance of the Study" id="section-significance">
                                    <h5 class="fw-bold text-primary border-bottom pb-1 mb-2.5" style="font-size: 1.05rem;">
                                        <i class="bi bi-award-fill me-1"></i> Significance of the Study
                                    </h5>
                                    <div class="section-body text-secondary small" style="font-size: 0.92rem; text-align: justify; line-height: 1.7;">
                                        {!! nl2br(e($proposal->significance_of_study)) !!}
                                    </div>
                                </div>
                                @endif

                                <!-- Definition of Terms -->
                                @if($proposal->definition_of_terms)
                                <div class="manuscript-section mb-4" data-chapter="Definition of Terms" id="section-terms">
                                    <h5 class="fw-bold text-primary border-bottom pb-1 mb-2.5" style="font-size: 1.05rem;">
                                        <i class="bi bi-spellcheck me-1"></i> Definition of Terms
                                    </h5>
                                    <div class="section-body text-secondary small" style="font-size: 0.92rem; text-align: justify; line-height: 1.7;">
                                        {!! nl2br(e($proposal->definition_of_terms)) !!}
                                    </div>
                                </div>
                                @endif

                                <!-- Review of Related Literature -->
                                @if($proposal->literature_review)
                                <div class="manuscript-section mb-4" data-chapter="Review of Related Literature" id="section-rrl">
                                    <h5 class="fw-bold text-primary border-bottom pb-1 mb-2.5" style="font-size: 1.05rem;">
                                        <i class="bi bi-book-half me-1"></i> Review of Related Literature (RRL)
                                    </h5>
                                    <div class="section-body text-secondary small" style="font-size: 0.92rem; text-align: justify; line-height: 1.7;">
                                        {!! nl2br(e($proposal->literature_review)) !!}
                                    </div>
                                </div>
                                @endif

                                <!-- Methodology & Research Design -->
                                @if($proposal->methodology)
                                <div class="manuscript-section mb-4" data-chapter="Methodology & Research Design" id="section-methodology">
                                    <h5 class="fw-bold text-primary border-bottom pb-1 mb-2.5" style="font-size: 1.05rem;">
                                        <i class="bi bi-diagram-3-fill me-1"></i> Methodology &amp; Research Design
                                    </h5>
                                    <div class="section-body text-secondary small" style="font-size: 0.92rem; text-align: justify; line-height: 1.7;">
                                        {!! nl2br(e($proposal->methodology)) !!}
                                    </div>
                                </div>
                                @endif

                                <!-- Extracted Full Text from Uploaded PDF (if present and not already broken down) -->
                                @if($proposal->extracted_text && !$proposal->background_of_study && !$proposal->methodology)
                                <div class="manuscript-section mb-4" data-chapter="General / Whole Document" id="section-full-manuscript">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-3">
                                        <h5 class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">
                                            <i class="bi bi-journal-text me-1"></i> Uploaded Manuscript Content
                                        </h5>
                                        <span class="badge bg-light text-secondary border px-2 py-1 small fw-normal">
                                            <i class="bi bi-file-earmark-check me-1 text-success"></i> Continuous Reading View
                                        </span>
                                    </div>
                                    <div class="section-body text-secondary small" style="font-size: 0.92rem; text-align: justify;">
                                        {!! \App\Services\PdfTextExtractor::formatTextToHtml($proposal->extracted_text) !!}
                                    </div>
                                </div>
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
                                @if($proposal->references)
                                <div class="manuscript-section mb-4" data-chapter="References & Citations" id="section-references">
                                    <h5 class="fw-bold text-primary border-bottom pb-1 mb-2.5" style="font-size: 1.05rem;">
                                        <i class="bi bi-link-45deg me-1"></i> References &amp; Citations (APA 7th)
                                    </h5>
                                    <div class="section-body text-secondary small lh-lg font-monospace" style="font-size: 0.85rem; text-align: justify;">
                                        {!! nl2br(e($proposal->references)) !!}
                                    </div>
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
    </script>
</x-app-layout>
