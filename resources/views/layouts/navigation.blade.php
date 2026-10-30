<aside class="sidebar" id="sidebarNav">
    <!-- Brand & Mobile Close -->
    <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom border-warning border-opacity-25">
        <a class="sidebar-brand m-0 p-0 border-0" href="{{ route('dashboard') }}">
            <img src="{{ asset('logo.png') }}" alt="University of Antique Seal" class="sidebar-brand-seal">
            <div class="lh-sm">
                <span class="fw-bold d-block sidebar-brand-title">URLC Portal</span>
                <span class="sidebar-brand-sub">University of Antique</span>
            </div>
        </a>
        <button type="button" class="btn btn-sm text-white-50 border-0 p-1 d-flex align-items-center justify-content-center rounded-circle" id="sidebarCloseBtn" aria-label="Close sidebar" title="Close sidebar" style="width: 32px; height: 32px; background: rgba(255,255,255,0.06); transition: all 0.2s ease;">
            <i class="bi bi-x-lg fs-6 text-white d-lg-none"></i>
            <i class="bi bi-chevron-left fs-6 text-white-50 d-none d-lg-inline"></i>
        </button>
    </div>

    <!-- Navigation -->
    @php
        $currentRole = auth()->user()->activeRole();
    @endphp
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>

        @if(auth()->user()->isSuperAdmin() && $currentRole === 'super_admin')
            <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Super Admin</span>
            </a>
            <a href="{{ route('superadmin.users') }}" class="sidebar-link {{ request()->routeIs('superadmin.users') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>User Management</span>
            </a>
            <a href="{{ route('superadmin.settings') }}" class="sidebar-link {{ request()->routeIs('superadmin.settings') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i>
                <span>System Settings</span>
            </a>
            <a href="{{ route('superadmin.logs') }}" class="sidebar-link {{ request()->routeIs('superadmin.logs') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Activity Logs</span>
            </a>
        @endif

        @if($currentRole === 'admin')
            <a href="{{ route('admin.proposals') }}" class="sidebar-link {{ request()->routeIs('admin.proposals') ? 'active' : '' }}">
                <i class="bi bi-collection-fill"></i>
                <span>All Proposals</span>
            </a>
            <a href="{{ route('superadmin.logs') }}" class="sidebar-link {{ request()->routeIs('superadmin.logs') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Activity Logs</span>
            </a>
        @endif

        @if($currentRole === 'reviewer')
            <a href="{{ route('reviewer.proposals') }}" class="sidebar-link {{ request()->routeIs('reviewer.proposals') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check-fill"></i>
                <span>Review Queue</span>
            </a>
        @endif

        @if($currentRole === 'coordinator')
            <a href="{{ route('coordinator.proposals') }}" class="sidebar-link {{ request()->routeIs('coordinator.proposals') ? 'active' : '' }}">
                <i class="bi bi-check-circle-fill"></i>
                <span>Endorsements</span>
            </a>
        @endif

        @if($currentRole === 'staff')
            <a href="{{ route('staff.proposals') }}" class="sidebar-link {{ request()->routeIs('staff.proposals') ? 'active' : '' }}">
                <i class="bi bi-inbox-fill"></i>
                <span>Receiving Queue</span>
            </a>
        @endif

        @if($currentRole === 'recording_staff')
            <a href="{{ route('recording_staff.dashboard') }}" class="sidebar-link {{ request()->routeIs('recording_staff.dashboard') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Tracking Dashboard</span>
            </a>
            <a href="{{ route('recording_staff.budget_allocations.index') }}" class="sidebar-link {{ request()->routeIs('recording_staff.budget_allocations.*') ? 'active' : '' }}">
                <i class="bi bi-pie-chart-fill"></i>
                <span>Budget Allocations</span>
            </a>
        @endif

        @if($currentRole === 'dean')
            <a href="{{ route('dean.dashboard') }}" class="sidebar-link {{ request()->routeIs('dean.dashboard') ? 'active' : '' }}">
                <i class="bi bi-bank2"></i>
                <span>Dean Noting</span>
            </a>
        @endif

        @if($currentRole === 'vprei')
            <a href="{{ route('vprei.dashboard') }}" class="sidebar-link {{ request()->routeIs('vprei.dashboard') ? 'active' : '' }}">
                <i class="bi bi-award-fill"></i>
                <span>VPREI Approvals</span>
            </a>
        @endif

        @if($currentRole === 'president')
            <a href="{{ route('president.dashboard') }}" class="sidebar-link {{ request()->routeIs('president.dashboard') ? 'active' : '' }}">
                <i class="bi bi-person-workspace"></i>
                <span>President Authorization</span>
            </a>
        @endif

        @if($currentRole === 'budget_officer')
            <a href="{{ route('budget.dashboard') }}" class="sidebar-link {{ request()->routeIs('budget.dashboard') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Budget Certification</span>
            </a>
        @endif

        @if($currentRole === 'sao_finance')
            <a href="{{ route('finance.dashboard') }}" class="sidebar-link {{ request()->routeIs('finance.dashboard') ? 'active' : '' }}">
                <i class="bi bi-bank"></i>
                <span>Procurement PRs</span>
            </a>
        @endif

        @if($currentRole === 'researcher')
            <a href="{{ route('proposal.index') }}" class="sidebar-link {{ request()->routeIs('proposal.index') ? 'active' : '' }}">
                <i class="bi bi-folder-fill"></i>
                <span>My Researches</span>
            </a>
            <a href="{{ route('proposal.create') }}" class="sidebar-link {{ request()->routeIs('proposal.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Submit Proposal</span>
            </a>
        @endif

        @if(in_array($currentRole, ['researcher', 'vprei', 'president', 'admin', 'funding_agency', 'super_admin']))
            <a href="{{ route('external-funding.index') }}" class="sidebar-link {{ request()->routeIs('external-funding.*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i>
                <span>{{ $currentRole === 'funding_agency' ? 'Agency Grant Reviews' : 'External Funding' }}</span>
            </a>
        @endif

        <a href="{{ route('announcements.index') }}" class="sidebar-link {{ request()->routeIs('announcements.index') ? 'active' : '' }}">
            <i class="bi bi-megaphone-fill"></i>
            <span>Announcements</span>
        </a>

        <button type="button" class="sidebar-link w-100 border-0 bg-transparent text-start" data-bs-toggle="modal" data-bs-target="#submissionGuidelinesModal">
            <i class="bi bi-book-half"></i>
            <span>Submission Guidelines</span>
        </button>

        <a href="{{ route('repository.index') }}" class="sidebar-link {{ request()->routeIs('repository.index') ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark-fill"></i>
            <span>Research Repository</span>
        </a>

        <a href="{{ route('messages.index') }}" class="sidebar-link {{ request()->routeIs('messages.index') ? 'active' : '' }}">
            <i class="bi bi-envelope-fill"></i>
            <span>Messages</span>
        </a>

    </nav>
</aside>

<!-- Global Submission Guidelines Modal -->
<div class="modal fade" id="submissionGuidelinesModal" tabindex="-1" aria-labelledby="submissionGuidelinesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header border-0 bg-light p-3 p-md-4 pb-2">
                <div>
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 mb-1" id="submissionGuidelinesModalLabel">
                        <i class="bi bi-book-half text-primary fs-4"></i> Research Proposal Submission Guidelines
                    </h5>
                    <p class="text-muted small mb-0">Official lifecycle stages, procedural workflows, and institutional review policies (URLC / VPREI).</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-3 p-md-4" style="max-height: 75vh; overflow-y: auto;">
                
                <!-- Section 1: The 5 Proposal Lifecycle Phases -->
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold text-dark text-uppercase small mb-0" style="letter-spacing: 0.05em;">
                            <i class="bi bi-diagram-3-fill text-primary me-1"></i> Research Lifecycle (Phases 1 to 5)
                        </h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 small fw-semibold">5 Official Phases</span>
                    </div>

                    <div class="d-flex flex-column gap-2 gap-md-3">
                        <!-- Phase 1 Card -->
                        <div class="card border border-primary border-opacity-25 rounded-3 shadow-xs bg-white">
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                            <i class="bi bi-file-earmark-text-fill small"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Phase 1: Proposal Formulation & Approvals</h6>
                                    </div>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">Appendix A</span>
                                </div>
                                <p class="small text-muted mb-0" style="line-height: 1.55;">
                                    Submission of proposal manuscript, collaborators, Gantt chart milestones, and Line Item Budget (LIB). Undergoes formal endorsement pipeline from College Coordinator up to University President authorization and Notice to Proceed.
                                </p>
                            </div>
                        </div>

                        <!-- Phase 2 Card -->
                        <div class="card border border-success border-opacity-25 rounded-3 shadow-xs bg-white">
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                            <i class="bi bi-gear-wide-connected small"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Phase 2: Implementation, Monitoring & Evaluation</h6>
                                    </div>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">Execution</span>
                                </div>
                                <p class="small text-muted mb-0" style="line-height: 1.55;">
                                    Project execution through Activity Designs, Purchase Requests (PRs), milestone progress monitoring, budget liquidation, and terminal report evaluation for Certificate of Project Completion.
                                </p>
                            </div>
                        </div>

                        <!-- Phase 3 Card -->
                        <div class="card border rounded-3 shadow-xs bg-white" style="border-color: rgba(139, 92, 246, 0.3) !important;">
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background-color: rgba(139, 92, 246, 0.12); color: #7C3AED;">
                                            <i class="bi bi-easel-fill small"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Phase 3: Research Dissemination & Presentation</h6>
                                    </div>
                                    <span class="badge border px-2 py-1 rounded-pill fw-bold" style="font-size: 0.72rem; background-color: rgba(139, 92, 246, 0.1); color: #7C3AED; border-color: rgba(139, 92, 246, 0.25) !important;">Appendix B</span>
                                </div>
                                <p class="small text-muted mb-0" style="line-height: 1.55;">
                                    Oral or poster presentation in local, regional, national, or international conferences/symposia with university presentation funding endorsements, travel orders, and certificate liquidation.
                                </p>
                            </div>
                        </div>

                        <!-- Phase 4 Card -->
                        <div class="card border rounded-3 shadow-xs bg-white" style="border-color: rgba(14, 165, 233, 0.3) !important;">
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background-color: rgba(14, 165, 233, 0.12); color: #0284C7;">
                                            <i class="bi bi-journal-check small"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Phase 4: Publication in Indexed Journals</h6>
                                    </div>
                                    <span class="badge border px-2 py-1 rounded-pill fw-bold" style="font-size: 0.72rem; background-color: rgba(14, 165, 233, 0.1); color: #0284C7; border-color: rgba(14, 165, 233, 0.25) !important;">Appendix C</span>
                                </div>
                                <p class="small text-muted mb-0" style="line-height: 1.55;">
                                    Publication in CHED-recognized, Scopus, Web of Science (WoS), or reputable peer-reviewed academic journals, qualifying the research team for official university publication incentives.
                                </p>
                            </div>
                        </div>

                        <!-- Phase 5 Card -->
                        <div class="card border rounded-3 shadow-xs bg-white" style="border-color: rgba(229, 169, 60, 0.4) !important;">
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background-color: rgba(229, 169, 60, 0.18); color: #B45309;">
                                            <i class="bi bi-people-fill small"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Phase 5: Local In-House Forum & Archiving</h6>
                                    </div>
                                    <span class="badge border px-2 py-1 rounded-pill fw-bold text-dark" style="font-size: 0.72rem; background-color: rgba(229, 169, 60, 0.15); border-color: rgba(229, 169, 60, 0.35) !important;">Appendix D</span>
                                </div>
                                <p class="small text-muted mb-0" style="line-height: 1.55;">
                                    Presentation at the Annual University SUC In-House Research Forum, evaluation for Best Paper & Best Poster honors, and permanent archiving in the Centralized Research Document Repository.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Step-by-Step Submission & Review Pipeline -->
                <div>
                    <h6 class="fw-bold text-dark text-uppercase small mb-3" style="letter-spacing: 0.05em;">
                        <i class="bi bi-signpost-split-fill text-primary me-1"></i> Proposal Submission & Review Pipeline
                    </h6>

                    <div class="d-flex flex-column gap-2">
                        <!-- Step 1 -->
                        <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3 border">
                            <div class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center p-0 fw-bold flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.78rem;">1</div>
                            <div class="min-w-0" style="min-width: 0;">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <strong class="text-dark small fw-bold">Draft & Submit Proposal</strong>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">Researcher</span>
                                </div>
                                <p class="small text-muted mb-0" style="line-height: 1.5; font-size: 0.82rem;">
                                    Go to <strong>Submit Proposal</strong>, enter title, abstract, research agenda alignment, co-investigators, Line Item Budget (LIB), Gantt chart, and upload the proposal PDF.
                                </p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3 border">
                            <div class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center p-0 fw-bold flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.78rem;">2</div>
                            <div class="min-w-0" style="min-width: 0;">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <strong class="text-dark small fw-bold">College Endorsement & Dean Noting</strong>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">Coordinator &bull; Dean</span>
                                </div>
                                <p class="small text-muted mb-0" style="line-height: 1.5; font-size: 0.82rem;">
                                    The College Research Coordinator reviews alignment and endorses the proposal, which is then formally noted by the College Dean and transmitted to the Research Unit.
                                </p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3 border">
                            <div class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center p-0 fw-bold flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.78rem;">3</div>
                            <div class="min-w-0" style="min-width: 0;">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <strong class="text-dark small fw-bold">Technical In-House Review & Evaluation</strong>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">Research Unit &bull; Reviewers</span>
                                </div>
                                <p class="small text-muted mb-0" style="line-height: 1.5; font-size: 0.82rem;">
                                    The Research Director assigns technical peer Reviewers to evaluate methodology and budget. Researchers make necessary revisions and submit the revised copy.
                                </p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3 border">
                            <div class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center p-0 fw-bold flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.78rem;">4</div>
                            <div class="min-w-0" style="min-width: 0;">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <strong class="text-dark small fw-bold">Budget Certification & Executive Approvals</strong>
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">Budget &bull; VPREI &bull; President</span>
                                </div>
                                <p class="small text-muted mb-0" style="line-height: 1.5; font-size: 0.82rem;">
                                    The Budget Officer certifies funding availability, the VPREI recommends approval, and the University President grants final executive authorization and Notice to Proceed.
                                </p>
                            </div>
                        </div>

                        <!-- Step 5 -->
                        <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3 border">
                            <div class="badge bg-success rounded-circle d-flex align-items-center justify-content-center p-0 fw-bold flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.78rem;">5</div>
                            <div class="min-w-0" style="min-width: 0;">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <strong class="text-dark small fw-bold">Project Execution & Output Dissemination</strong>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">Phases 2 to 5 Active</span>
                                </div>
                                <p class="small text-muted mb-0" style="line-height: 1.5; font-size: 0.82rem;">
                                    Real-time portal tracking guides the project through Phase 2 execution, Phase 3 presentations, Phase 4 publications, and Phase 5 institutional archiving.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0 p-3 p-md-4 pt-2 bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted small d-none d-sm-inline" style="font-size: 0.78rem;">University Research &amp; Development Unit (URLC)</span>
                <button type="button" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg"></i> Understood &amp; Close
                </button>
            </div>
        </div>
    </div>
</div>
